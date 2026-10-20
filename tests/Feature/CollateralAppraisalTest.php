<?php

use App\Audit\AuditLog;
use App\Models\AppraisalEntry;
use App\Models\AppraisalPhoto;
use App\Models\Collateral;
use App\Models\CollateralAppraisal;
use App\Models\CollateralCondition;
use App\Models\CollateralType;
use App\Models\LoanApplication;
use App\Models\Product;
use App\Models\Region;
use App\Support\Appraisal\AppraisalContext;
use App\Support\Appraisal\AppraisalStarter;
use App\Support\Appraisal\AppraisalTemplates;
use App\Support\Appraisal\GeneralTemplate;
use App\Support\Appraisal\LandBuildingTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

/** The named attributes of a model, in the order asked. */
function fieldsOf(Model $model, array $keys): array
{
    return collect($keys)->mapWithKeys(fn (string $key): array => [$key => $model->getAttribute($key)])->all();
}

function landCollateral(string $type = '05', array $attributes = []): Collateral
{
    return Collateral::create(['collateral_type_code' => $type, 'owner_name' => 'BUDI', 'document_number' => 'SHM 1', 'certificate_land_area' => 100, ...$attributes]);
}

function startAppraisal(Collateral $collateral, array $payload = []): CollateralAppraisal
{
    test()->post(route('collaterals.appraisals.store', $collateral), ['purpose' => 'new', ...$payload])->assertSessionHasNoErrors();

    return $collateral->appraisals()->reorder('id', 'desc')->firstOrFail();
}

/** Saves answers through the screen's endpoint, a request for each section the keys belong to. */
function fillAppraisal(CollateralAppraisal $appraisal, array $answers): void
{
    $sections = collect(AppraisalTemplates::items(AppraisalTemplates::of($appraisal)))->pluck('section', 'key');

    foreach (collect($answers)->groupBy(fn ($value, string $key) => $sections[$key], preserveKeys: true) as $section => $group) {
        test()->put(route('appraisals.sections.update', [$appraisal, $section]), [
            'answers' => $group->map(fn ($value, string $key): array => ['key' => $key, 'value' => $value])->values()->all(),
        ])->assertSessionHasNoErrors();
    }
}

/** What an appraiser must have filled in for a land appraisal that is accepted. */
function acceptableLandAnswers(array $overrides = []): array
{
    return [
        'h:effective_date' => '2026-10-20', 'h:appraiser_name' => 'Rina', 'h:credit_ceiling' => '200000000',
        'adjustments.selected' => '1950000', 'land_physical.area' => '100', 'reconciliation.land.selected' => '195000000',
        'reconciliation.building.selected' => '190000000', 'reconciliation.facilities.selected' => '5000000', 'reconciliation.deductions.selected' => '10000000', 'h:haircut_percent' => '20', 'h:decision' => 'accepted',
        ...array_fill_keys(array_map(fn (string $key): string => $key, (new LandBuildingTemplate)->hardStops()), 'yes'),
        ...$overrides,
    ];
}

function finalize(CollateralAppraisal $appraisal)
{
    return test()->post(route('appraisals.finalize', $appraisal));
}

it('picks the land and building form for the land types and the general form for everything else', function (string $type, string $form) {
    expect(AppraisalTemplates::for(landCollateral($type))->key())->toBe($form);
})->with([
    'certificate with a mortgage right' => ['05', LandBuildingTemplate::KEY],
    'certificate without' => ['06', LandBuildingTemplate::KEY],
    'customary papers' => ['07', LandBuildingTemplate::KEY],
    'place of business' => ['08', LandBuildingTemplate::KEY],
    'gold' => ['03', GeneralTemplate::KEY],
    'savings or deposit' => ['02', GeneralTemplate::KEY],
    'vehicle' => ['10', GeneralTemplate::KEY],
    'no collateral' => ['99', GeneralTemplate::KEY],
]);

it('keeps every item key of a form unique, every hard stop a yes/no check, and a figure for every computed item', function (string $form) {
    $template = collect([new LandBuildingTemplate, new GeneralTemplate])->first(fn ($template) => $template->key() === $form);
    $items = collect(AppraisalTemplates::items($template));
    $cells = $template->compute([], new AppraisalContext(0, null))->cells;

    expect($items->pluck('key')->duplicates()->all())->toBe([])
        ->and($items->whereIn('key', $template->hardStops())->pluck('type')->unique()->all())->toBe(['yesno'])
        ->and($items->whereIn('key', $template->hardStops())->count())->toBe(count($template->hardStops()))
        ->and($items->where('type', 'computed')->pluck('key')->reject(fn (string $key): bool => array_key_exists($key, $cells))->values()->all())->toBe([]);
})->with([LandBuildingTemplate::KEY, GeneralTemplate::KEY]);

it('starts an appraisal as draft number one with the form and the person who starts it', function () {
    $collateral = landCollateral();

    $appraisal = startAppraisal($collateral, ['purpose' => 'top_up']);

    expect(fieldsOf($appraisal, ['sequence', 'status', 'purpose', 'template', 'template_version', 'appraiser_name', 'created_by']))
        ->toBe(['sequence' => 1, 'status' => 'draft', 'purpose' => 'top_up', 'template' => 'land_building', 'template_version' => 1, 'appraiser_name' => auth()->user()->name, 'created_by' => auth()->id()]);
});

it('allows one open draft per collateral and numbers the next appraisal after the finished one', function () {
    $collateral = landCollateral();
    $first = startAppraisal($collateral);

    $this->post(route('collaterals.appraisals.store', $collateral), ['purpose' => 'review'])->assertSessionHas('error');
    expect($collateral->appraisals()->count())->toBe(1);

    fillAppraisal($first, acceptableLandAnswers());
    finalize($first)->assertSessionHasNoErrors();
    $second = startAppraisal($collateral, ['purpose' => 'review']);

    expect($second->sequence)->toBe(2)->and($collateral->appraisals()->pluck('sequence')->all())->toBe([2, 1]);
});

it('does not start two appraisals when two people ask at the same moment', function () {
    $collateral = landCollateral();
    $user = auth()->user();
    AppraisalStarter::start($collateral, $user, 'new', null, null);

    expect(fn () => AppraisalStarter::start($collateral, $user, 'new', null, null))->toThrow(RuntimeException::class, 'masih punya penilaian');
    expect(CollateralAppraisal::count())->toBe(1);
});

it('takes the debtor, product, and ceiling from the loan file the collateral is attached to', function () {
    $collateral = landCollateral();
    $product = Product::create(['code' => 'P1', 'alias' => 'KRU', 'name' => 'GENERAL', 'is_active' => true]);
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'SITI', 'cif_number' => '0012345', 'product_id' => $product->id, 'requested_amount' => 75_000_000]);
    $loan->collaterals()->attach($collateral->id);

    $appraisal = startAppraisal($collateral, ['loan_application_id' => $loan->id]);

    expect(fieldsOf($appraisal, ['loan_application_id', 'debtor_name', 'debtor_cif', 'product_label', 'credit_ceiling']))
        ->toBe(['loan_application_id' => $loan->id, 'debtor_name' => 'SITI', 'debtor_cif' => '0012345', 'product_label' => 'KRU : GENERAL', 'credit_ceiling' => 75_000_000]);
});

it('refuses a loan file or an earlier appraisal that does not belong to the collateral', function () {
    $collateral = landCollateral();
    $other = landCollateral('05', ['document_number' => 'SHM 2']);
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A']);
    $foreign = startAppraisal($other);

    $this->post(route('collaterals.appraisals.store', $collateral), ['purpose' => 'new', 'loan_application_id' => $loan->id])->assertSessionHasErrors('loan_application_id');
    $this->post(route('collaterals.appraisals.store', $collateral), ['purpose' => 'new', 'copy_from' => $foreign->id])->assertSessionHasErrors('copy_from');
    $this->post(route('collaterals.appraisals.store', $collateral), ['purpose' => 'forever'])->assertSessionHasErrors('purpose');

    expect($collateral->appraisals()->count())->toBe(0);
});

it('locks the debtor data of an appraisal that is tied to a loan file', function () {
    $collateral = landCollateral();
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'SITI', 'requested_amount' => 75_000_000]);
    $loan->collaterals()->attach($collateral->id);
    $appraisal = startAppraisal($collateral, ['loan_application_id' => $loan->id]);

    $this->put(route('appraisals.sections.update', [$appraisal, 'identity']), ['answers' => [['key' => 'h:credit_ceiling', 'value' => '1']]])->assertSessionHasErrors('values.h:credit_ceiling');

    expect($appraisal->fresh()->credit_ceiling)->toBe(75_000_000);
});

it('saves answers of a section as entries and as columns of the appraisal, with the note of each', function () {
    $appraisal = startAppraisal(landCollateral());

    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), ['answers' => [
        ['key' => 'legal.no_dispute', 'value' => 'yes', 'note' => 'Surat keterangan RT'], ['key' => 'legal.no_blockade', 'value' => 'no'],
    ]])->assertSessionHas('success', 'Bagian penilaian disimpan.');
    $this->put(route('appraisals.sections.update', [$appraisal, 'identity']), ['answers' => [
        ['key' => 'h:appraiser_name', 'value' => 'Rina'], ['key' => 'h:effective_date', 'value' => '2026-10-20'], ['key' => 'h:credit_ceiling', 'value' => '200000000'],
    ]]);

    $entry = AppraisalEntry::where('item', 'legal.no_dispute')->firstOrFail();
    expect(fieldsOf($entry, ['section', 'value', 'note']))->toBe(['section' => 'legal', 'value' => 'yes', 'note' => 'Surat keterangan RT'])
        ->and(AppraisalEntry::where('item', 'legal.no_blockade')->value('value'))->toBe('no')
        ->and(fieldsOf($appraisal->fresh(), ['appraiser_name', 'credit_ceiling']))->toBe(['appraiser_name' => 'Rina', 'credit_ceiling' => 200_000_000])
        ->and($appraisal->fresh()->effective_date->toDateString())->toBe('2026-10-20');
});

it('removes an answer that is emptied and leaves answers that are not sent alone', function () {
    $appraisal = startAppraisal(landCollateral());
    fillAppraisal($appraisal, ['legal.no_dispute' => 'yes', 'legal.no_blockade' => 'yes']);

    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), ['answers' => [['key' => 'legal.no_dispute', 'value' => '']]]);

    expect(AppraisalEntry::where('item', 'legal.no_dispute')->exists())->toBeFalse()->and(AppraisalEntry::where('item', 'legal.no_blockade')->value('value'))->toBe('yes');
});

it('writes one audit entry for a saved section and nothing for a section that did not change', function () {
    $appraisal = startAppraisal(landCollateral());
    AuditLog::query()->getQuery()->delete();
    $answers = ['answers' => [['key' => 'legal.no_dispute', 'value' => 'yes'], ['key' => 'legal.no_blockade', 'value' => 'yes'], ['key' => 'legal.identity', 'value' => 'no']]];

    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), $answers);
    $entries = AuditLog::query()->where('event', 'collateral_appraisals.section_saved')->get();

    expect($entries)->toHaveCount(1)
        ->and(json_decode((string) $entries->first()->new_values, true))->toMatchArray(['legal.no_dispute' => 'yes', 'legal.identity' => 'no'])
        ->and(json_decode((string) $entries->first()->context, true))->toBe(['section' => 'legal'])
        ->and(AuditLog::query()->where('event', 'like', 'collateral_appraisals.created')->count())->toBe(0);

    AuditLog::query()->getQuery()->delete();
    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), $answers)->assertSessionHas('success', 'Tidak ada perubahan.');

    expect(AuditLog::query()->count())->toBe(0);
});

it('refuses answers that do not fit their item, each with the message of its item', function (string $key, mixed $value, string $section) {
    $appraisal = startAppraisal(landCollateral());

    $this->put(route('appraisals.sections.update', [$appraisal, $section]), ['answers' => [['key' => $key, 'value' => $value]]])->assertSessionHasErrors("values.$key");

    expect(AppraisalEntry::count())->toBe(0);
})->with([
    'money with letters' => ['reconciliation.land.selected', '12abc', 'reconciliation'],
    'negative money' => ['reconciliation.land.selected', '-5', 'reconciliation'],
    'percent with three decimals' => ['h:haircut_percent', '12.345', 'reconciliation'],
    'percent above 100' => ['h:haircut_percent', '101', 'reconciliation'],
    'adjustment below -100' => ['adjustments.time.c1', '-101', 'adjustments'],
    'a date that is not a date' => ['h:effective_date', '20-10-2026', 'identity'],
    'a choice that does not exist' => ['h:purpose', 'forever', 'identity'],
    'yes or no that is neither' => ['legal.no_dispute', 'maybe', 'legal'],
    'a risk that is not on the scale' => ['risks.legal', 'extreme', 'risks'],
    'text too long' => ['land_documents.tax', str_repeat('x', 256), 'land_documents'],
]);

it('refuses a note that is too long, a key of another section, a figure the system works out, and an unknown section', function () {
    $appraisal = startAppraisal(landCollateral());

    $this->put(route('appraisals.sections.update', [$appraisal, 'reconciliation']), ['answers' => [['key' => 'reconciliation.market_value', 'value' => '1']]])->assertSessionHasErrors('answers.0.key');

    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), ['answers' => [['key' => 'legal.no_dispute', 'value' => 'yes', 'note' => str_repeat('x', 501)]]])->assertSessionHasErrors('answers.0.note');
    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), ['answers' => [['key' => 'risks.legal', 'value' => 'low']]])->assertSessionHasErrors('answers.0.key');
    $this->put(route('appraisals.sections.update', [$appraisal, 'nonsense']), ['answers' => [['key' => 'x', 'value' => 'y']]])->assertNotFound();
    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), [])->assertSessionHasErrors('answers');
});

it('works out and keeps the market and liquidation value on the appraisal as answers are saved', function () {
    $appraisal = startAppraisal(landCollateral());

    fillAppraisal($appraisal, acceptableLandAnswers());

    expect(fieldsOf($appraisal->fresh(), ['market_value', 'liquidation_value']))->toBe(['market_value' => 380_000_000, 'liquidation_value' => 304_000_000]);
});

it('says what stops an appraisal from being finalised, and finalises it when nothing does', function () {
    $appraisal = startAppraisal(landCollateral());

    $this->post(route('appraisals.finalize', $appraisal))->assertSessionHasErrors('finalize');
    $message = session('errors')->first('finalize');
    expect($message)->toContain('tanggal efektif')->toContain('status agunan');

    fillAppraisal($appraisal, acceptableLandAnswers());
    finalize($appraisal)->assertSessionHasNoErrors()->assertSessionHas('success', 'Penilaian difinalkan.');

    $final = $appraisal->fresh();
    expect($final->status)->toBe('final')->and($final->finalized_by)->toBe(auth()->user()->name)->and($final->finalized_at)->not->toBeNull()->and($final->decision)->toBe('accepted');
});

it('refuses to accept an appraisal while a legal check that cannot be waived is not answered yes', function (string $answer) {
    $appraisal = startAppraisal(landCollateral());
    $answers = acceptableLandAnswers(['legal.no_dispute' => $answer]);
    $answers = $answer === '' ? array_diff_key($answers, ['legal.no_dispute' => 1]) : $answers;
    fillAppraisal($appraisal, $answers);

    finalize($appraisal)->assertSessionHasErrors('finalize');

    expect(session('errors')->first('finalize'))->toContain('Tidak terdapat sengketa')->and($appraisal->fresh()->status)->toBe('draft');
})->with(['no' => ['no'], 'not answered' => ['']]);

it('lets an appraisal be rejected without the legal checks, and requires conditions for a conditional acceptance', function () {
    $appraisal = startAppraisal(landCollateral());
    fillAppraisal($appraisal, ['h:effective_date' => '2026-10-20', 'h:appraiser_name' => 'Rina', 'h:decision' => 'conditional', 'legal.no_dispute' => 'no']);

    finalize($appraisal)->assertSessionHasErrors('finalize');
    expect(session('errors')->first('finalize'))->toContain('syarat utama');

    fillAppraisal($appraisal, ['h:decision' => 'rejected']);
    finalize($appraisal)->assertSessionHasNoErrors();

    expect($appraisal->fresh()->decision)->toBe('rejected');
});

it('needs a market value and a liquidation value before an acceptance', function () {
    $appraisal = startAppraisal(landCollateral());
    fillAppraisal($appraisal, ['h:effective_date' => '2026-10-20', 'h:appraiser_name' => 'Rina', 'h:decision' => 'accepted', ...array_fill_keys((new LandBuildingTemplate)->hardStops(), 'yes')]);

    finalize($appraisal)->assertSessionHasErrors('finalize');

    expect(session('errors')->first('finalize'))->toContain('Nilai pasar belum terhitung')->toContain('isi haircut');
});

it('never changes or deletes a finalised appraisal', function () {
    Storage::fake('vault');
    config(['filesystems.attachments' => 'vault']);
    $collateral = landCollateral();
    $appraisal = startAppraisal($collateral);
    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image('a.jpg')]);
    fillAppraisal($appraisal, acceptableLandAnswers());
    finalize($appraisal);
    $photo = AppraisalPhoto::firstOrFail();
    $before = $appraisal->fresh()->toArray();

    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), ['answers' => [['key' => 'legal.no_dispute', 'value' => 'no']]])->assertSessionHas('error');
    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image('b.jpg')])->assertSessionHas('error');
    $this->delete(route('appraisals.photos.destroy', [$appraisal, $photo]))->assertSessionHas('error');
    $this->delete(route('appraisals.destroy', $appraisal))->assertSessionHas('error');
    $this->post(route('appraisals.finalize', $appraisal))->assertSessionHas('error');

    expect($appraisal->fresh()->toArray())->toBe($before)->and(AppraisalEntry::where('item', 'legal.no_dispute')->value('value'))->toBe('yes')
        ->and(AppraisalPhoto::count())->toBe(1)->and(Storage::disk('vault')->exists($photo->path))->toBeTrue();
});

it('copies the answers of an earlier appraisal for a review, except the checks that must be made again and the photos', function () {
    Storage::fake('vault');
    config(['filesystems.attachments' => 'vault']);
    $collateral = landCollateral();
    $first = startAppraisal($collateral);
    $this->post(route('appraisals.photos.store', $first), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image('a.jpg')]);
    fillAppraisal($first, acceptableLandAnswers(['land_documents.tax' => 'PBB lunas', 'h:owner_relation' => 'Suami', 'h:reviewer_name' => 'Dodi']));
    finalize($first);
    AuditLog::query()->getQuery()->delete();

    $second = startAppraisal($collateral, ['purpose' => 'review', 'copy_from' => $first->id]);

    expect(AppraisalEntry::where('collateral_appraisal_id', $second->id)->where('item', 'land_documents.tax')->value('value'))->toBe('PBB lunas')
        ->and(AppraisalEntry::where('collateral_appraisal_id', $second->id)->where('item', 'like', 'legal.%')->count())->toBe(0)
        ->and(fieldsOf($second->fresh(), ['owner_relation', 'reviewer_name', 'decision', 'effective_date']))->toBe(['owner_relation' => 'Suami', 'reviewer_name' => 'Dodi', 'decision' => null, 'effective_date' => null])
        ->and($second->photos()->count())->toBe(0)
        ->and(AuditLog::query()->where('event', 'collateral_appraisals.copied')->count())->toBe(1);
});

it('keeps a draft out of history by deleting it with its photos, and refuses to delete the collateral that has history', function () {
    Storage::fake('vault');
    config(['filesystems.attachments' => 'vault']);
    $collateral = landCollateral();
    $draft = startAppraisal($collateral);
    $this->post(route('appraisals.photos.store', $draft), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image('a.jpg')]);
    $path = AppraisalPhoto::firstOrFail()->path;

    $this->delete(route('collaterals.destroy', $collateral))->assertSessionHas('error');
    $this->delete(route('appraisals.destroy', $draft))->assertRedirect(route('collaterals.appraisals.index', $collateral));

    expect(CollateralAppraisal::count())->toBe(0)->and(AppraisalPhoto::count())->toBe(0)->and(Storage::disk('vault')->exists($path))->toBeFalse();

    $this->delete(route('collaterals.destroy', $collateral))->assertSessionHas('success');
});

it('stores photos under the places of the form only, as images up to five per place', function () {
    Storage::fake('vault');
    config(['filesystems.attachments' => 'vault']);
    $appraisal = startAppraisal(landCollateral());

    foreach (range(1, 5) as $i) {
        $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image("front-$i.jpg")])->assertSessionHas('success');
    }

    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image('six.jpg')])->assertSessionHas('error');
    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.nowhere', 'photo' => UploadedFile::fake()->image('a.jpg')])->assertSessionHasErrors('item');
    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.access', 'photo' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')])->assertSessionHasErrors('photo');
    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.access', 'photo' => UploadedFile::fake()->image('big.jpg')->size(5121)])->assertSessionHasErrors('photo');
    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.access', 'photo' => UploadedFile::fake()->image('a.gif')])->assertSessionHasErrors('photo');

    $photo = AppraisalPhoto::where('item', 'photos.front')->firstOrFail();
    expect(AppraisalPhoto::count())->toBe(5)->and($photo->path)->toStartWith("appraisals/{$appraisal->collateral_id}/{$appraisal->id}/")->and(Storage::disk('vault')->exists($photo->path))->toBeTrue();

    $this->delete(route('appraisals.photos.destroy', [$appraisal, $photo]))->assertSessionHas('success');
    expect(Storage::disk('vault')->exists($photo->path))->toBeFalse();
});

it('does not delete a photo through the appraisal of another collateral', function () {
    Storage::fake('vault');
    config(['filesystems.attachments' => 'vault']);
    $mine = startAppraisal(landCollateral());
    $theirs = startAppraisal(landCollateral('05', ['document_number' => 'SHM 2']));
    $this->post(route('appraisals.photos.store', $theirs), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image('a.jpg')]);

    $this->delete(route('appraisals.photos.destroy', [$mine, AppraisalPhoto::firstOrFail()]))->assertNotFound();

    expect(AppraisalPhoto::count())->toBe(1);
});

it('keeps reading to collaterals.view and writing to collaterals.manage, and shows nothing to anyone else', function () {
    $collateral = landCollateral();
    $appraisal = startAppraisal($collateral);
    $reader = userWith(['collaterals.view'], 'Pembaca');
    $stranger = userWith(['dashboard.view'], 'Orang lain');

    $this->actingAs($reader);
    $this->get(route('collaterals.appraisals.index', $collateral))->assertOk();
    $this->get(route('appraisals.show', $appraisal))->assertOk();
    $this->post(route('collaterals.appraisals.store', $collateral), ['purpose' => 'new'])->assertForbidden();
    $this->put(route('appraisals.sections.update', [$appraisal, 'legal']), ['answers' => [['key' => 'legal.no_dispute', 'value' => 'yes']]])->assertForbidden();
    $this->post(route('appraisals.finalize', $appraisal))->assertForbidden();
    $this->post(route('appraisals.photos.store', $appraisal), ['item' => 'photos.front', 'photo' => UploadedFile::fake()->image('a.jpg')])->assertForbidden();
    $this->delete(route('appraisals.destroy', $appraisal))->assertForbidden();

    $this->actingAs($stranger);
    $this->get(route('collaterals.appraisals.index', $collateral))->assertForbidden();
    $this->get(route('appraisals.show', $appraisal))->assertForbidden();

    expect(AppraisalEntry::count())->toBe(0)->and($appraisal->fresh()->status)->toBe('draft');
});

it('gives the history of a collateral newest first, with the figures that management reads', function () {
    $collateral = landCollateral();
    $first = startAppraisal($collateral);
    fillAppraisal($first, acceptableLandAnswers());
    finalize($first);
    startAppraisal($collateral, ['purpose' => 'top_up']);

    $this->get(route('collaterals.appraisals.index', $collateral))->assertInertia(fn (Assert $page) => $page
        ->component('collaterals/appraisals/index')
        ->where('appraisals.0.sequence', 2)->where('appraisals.0.status', 'draft')->where('appraisals.0.purpose_label', 'Top-up')
        ->where('appraisals.1.sequence', 1)->where('appraisals.1.market_value', 380_000_000)->where('appraisals.1.decision_label', 'Dapat diterima')
        ->where('hasDraft', true)->where('canManage', true));
});

it('gives the appraisal page its form, answers, figures, progress, and what still blocks it', function () {
    $appraisal = startAppraisal(landCollateral());
    fillAppraisal($appraisal, ['legal.no_dispute' => 'yes', 'legal.no_blockade' => 'yes', 'reconciliation.land.selected' => '195000000']);

    $this->get(route('appraisals.show', $appraisal))->assertInertia(fn (Assert $page) => $page
        ->component('collaterals/appraisals/show')
        ->where('template.key', 'land_building')->where('values', fn ($values) => ($values['legal.no_dispute'] ?? null) === 'yes')->where('progress.legal', [2, 14])
        ->where('computed', fn ($computed) => ($computed['reconciliation.total.selected'] ?? null) === 195_000_000)->where('canEdit', true)->has('blockers', 2)
        ->has('template.sections', 18));

    AuditLog::query()->getQuery()->delete();
    $this->get(route('appraisals.show', $appraisal));
    expect(AuditLog::query()->where('event', 'collateral_appraisals.viewed')->count())->toBe(1);
});

it('shows a finalised appraisal as read only', function () {
    $appraisal = startAppraisal(landCollateral());
    fillAppraisal($appraisal, acceptableLandAnswers());
    finalize($appraisal);

    $this->get(route('appraisals.show', $appraisal))->assertInertia(fn (Assert $page) => $page->where('canEdit', false)->where('blockers', [])->where('appraisal.status', 'final'));
});

it('records the appraisal, its finalisation, and the removal of a draft on the audit trail', function () {
    $collateral = landCollateral();
    $appraisal = startAppraisal($collateral);
    fillAppraisal($appraisal, acceptableLandAnswers());
    finalize($appraisal);
    $draft = startAppraisal($collateral, ['purpose' => 'review']);
    $this->delete(route('appraisals.destroy', $draft));

    $events = AuditLog::query()->where('module', 'collateral_appraisals')->pluck('event')->all();
    $finalized = AuditLog::query()->where('event', 'collateral_appraisals.finalized')->firstOrFail();

    expect($events)->toContain('collateral_appraisals.created', 'collateral_appraisals.section_saved', 'collateral_appraisals.finalized', 'collateral_appraisals.deleted')
        ->and(json_decode((string) $finalized->context, true))->toMatchArray(['decision' => 'accepted', 'market_value' => 380_000_000, 'liquidation_value' => 304_000_000]);
});

it('saves the facts of the land and its building on the collateral and refuses ones that do not fit', function () {
    $collateral = landCollateral();
    $payload = ['collateral_type_code' => '05', 'document_number' => 'SHM 1', 'description' => 'RUMAH', 'owner_name' => 'BUDI', 'owner_address' => 'JL X', 'region_code' => '3201'];
    CollateralType::create(['code' => '05', 'name' => 'LAND']);
    CollateralCondition::create(['code' => '9', 'name' => 'OK']);
    Region::create(['code' => '3201', 'regency' => 'BOGOR', 'district' => 'X', 'village' => 'Y']);

    $this->put(route('collaterals.update', $collateral), [...$payload, 'condition_code' => '9', 'condition_date' => '2026-01-01', 'insurance_code' => 'T', 'insurance_date' => '2026-01-01', 'right_kind' => 'VILLA'])
        ->assertSessionHasErrors('right_kind');
    $this->put(route('collaterals.update', $collateral), [...$payload, 'condition_code' => '9', 'condition_date' => '2026-01-01', 'insurance_code' => 'T', 'insurance_date' => '2026-01-01',
        'right_issued_at' => '2026-02-01', 'right_expires_at' => '2026-01-01'])->assertSessionHasErrors('right_expires_at');
    $this->put(route('collaterals.update', $collateral), [...$payload, 'condition_code' => '9', 'condition_date' => '2026-01-01', 'insurance_code' => 'T', 'insurance_date' => '2026-01-01',
        'right_kind' => 'SHM', 'right_holder_name' => 'BUDI', 'right_issued_at' => '2020-02-01', 'certificate_land_area' => 120, 'parcel_number' => '01234', 'permitted_building_area' => 60])->assertSessionHasNoErrors();

    expect(fieldsOf($collateral->fresh(), ['right_kind', 'right_holder_name', 'certificate_land_area', 'parcel_number', 'permitted_building_area']))
        ->toBe(['right_kind' => 'SHM', 'right_holder_name' => 'BUDI', 'certificate_land_area' => 120, 'parcel_number' => '01234', 'permitted_building_area' => 60])
        ->and($collateral->fresh()->right_issued_at->toDateString())->toBe('2020-02-01');
});

it('uses the area on the certificate for the land value when the appraiser did not measure it', function () {
    $appraisal = startAppraisal(landCollateral('05', ['certificate_land_area' => 80]));

    fillAppraisal($appraisal, ['adjustments.selected' => '1000000', 'reconciliation.land.selected' => '80000000', 'h:haircut_percent' => '0']);
    $this->get(route('appraisals.show', $appraisal))->assertInertia(fn (Assert $page) => $page->where('computed', fn ($computed) => ($computed['reconciliation.land.market'] ?? null) === 80_000_000));
});

it('appraises collaterals of other types with the general form', function () {
    $collateral = landCollateral('03');
    $appraisal = startAppraisal($collateral);
    fillAppraisal($appraisal, ['h:effective_date' => '2026-10-20', 'h:appraiser_name' => 'Rina', 'h:decision' => 'accepted', 'h:credit_ceiling' => '40000000',
        'value.market_value' => '50000000', 'h:haircut_percent' => '10', ...array_fill_keys((new GeneralTemplate)->hardStops(), 'yes')]);

    finalize($appraisal)->assertSessionHasNoErrors();

    expect(fieldsOf($appraisal->fresh(), ['template', 'market_value', 'liquidation_value', 'status']))->toBe(['template' => 'general', 'market_value' => 50_000_000, 'liquidation_value' => 45_000_000, 'status' => 'final']);
});
