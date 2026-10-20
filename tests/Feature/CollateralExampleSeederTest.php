<?php

use App\Audit\AuditLog;
use App\Models\AppraisalEntry;
use App\Models\Collateral;
use App\Models\CollateralAppraisal;
use App\Support\Appraisal\AppraisalRules;
use App\Support\Appraisal\AppraisalTemplate;
use App\Support\Appraisal\AppraisalTemplates;
use App\Support\Appraisal\LandBuildingTemplate;
use App\Support\Appraisal\VehicleTemplate;
use Database\Seeders\CollateralExampleSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Examples\LandExamples;
use Database\Seeders\Examples\VehicleExamples;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Validator;

function seedExamples(): void
{
    superAdmin();
    test()->seed(CollateralExampleSeeder::class);
}

function appraisalOf(string $cbsId): CollateralAppraisal
{
    return Collateral::where('cbs_id', $cbsId)->firstOrFail()->appraisals()->firstOrFail();
}

it('seeds six collaterals once however often it runs, and leaves what was edited alone', function () {
    seedExamples();
    Collateral::where('cbs_id', '01.3.002238')->update(['owner_name' => 'EDITED BY AN OWNER']);
    $this->seed(CollateralExampleSeeder::class);

    expect(Collateral::count())->toBe(6)->and(CollateralAppraisal::count())->toBe(4)
        ->and(Collateral::where('cbs_id', '01.3.002238')->value('owner_name'))->toBe('EDITED BY AN OWNER');
});

it('keeps the shape of the screens: types, bindings, amounts, and several collaterals on one credit account', function () {
    seedExamples();

    expect(Collateral::where('credit_account', '01.301.028020')->pluck('collateral_type_code')->sort()->values()->all())->toBe(['05', '10'])
        ->and(Collateral::where('credit_account', '01.302.000929')->pluck('collateral_type_code')->sort()->values()->all())->toBe(['10', '14', '14'])
        ->and(Collateral::where('cbs_id', '01.3.002194')->first(['collateral_type_code', 'binding_type_code', 'guarantee_value', 'fair_value', 'njop_value', 'appraisal_value'])->toArray())
        ->toBe(['collateral_type_code' => '05', 'binding_type_code' => '01', 'guarantee_value' => 437_500_000, 'fair_value' => 437_500_000, 'njop_value' => 437_500_000, 'appraisal_value' => 129_826_667])
        ->and(Collateral::where('cbs_id', '01.3.002238')->first(['collateral_type_code', 'binding_type_code', 'guarantee_value', 'fair_value', 'njop_value', 'appraisal_value'])->toArray())
        ->toBe(['collateral_type_code' => '06', 'binding_type_code' => '06', 'guarantee_value' => 13_000_000, 'fair_value' => 100_000_000, 'njop_value' => 13_000_000, 'appraisal_value' => 87_100_000]);
});

it('gives each sample collateral the payload that core banking would be sent', function () {
    seedExamples();

    $car = Collateral::where('cbs_id', '01.3.002195')->firstOrFail()->toCbsPayload();

    expect($car)->toMatchArray(['jenis' => '10', 'jenis_pengikat' => '03', 'lokasi' => '0121', 'asuransi' => 'Y', 'startdate' => '2026-10-07', 'pemilik_nama' => 'NURHALIMAH', 'ppap' => 1])
        ->and($car['nilai'])->toBe(['njop' => 0, 'jaminan' => 90_000_000, 'adjust' => 0, 'wajar' => 90_000_000, 'taksasi' => 90_000_000, 'independen' => 0])
        ->and($car['penaksir']['taksasi'])->toBe(['penaksir' => 'PAJAR', 'tanggal' => '2026-10-07']);
});

it('fills four appraisals through every section, finalised with the figures the system works out', function (string $cbsId, string $template, int $market, int $liquidation, string $haircut, string $decision) {
    seedExamples();
    $appraisal = appraisalOf($cbsId);

    expect(fieldsOf($appraisal, ['template', 'status', 'market_value', 'liquidation_value', 'decision', 'appraiser_name', 'finalized_by']))
        ->toBe(['template' => $template, 'status' => 'final', 'market_value' => $market, 'liquidation_value' => $liquidation, 'decision' => $decision, 'appraiser_name' => 'PENILAI CONTOH', 'finalized_by' => 'PENILAI CONTOH'])
        ->and((float) $appraisal->haircut_percent)->toBe((float) $haircut);
})->with([
    'land with customary papers (refused)' => ['01.3.002194', 'land_building', 447_600_000, 335_700_000, '25', 'rejected'],
    'land with a certificate and no binding (on conditions)' => ['01.3.002238', 'land_building', 86_500_000, 64_875_000, '25', 'conditional'],
    'the car (on conditions)' => ['01.3.002195', 'vehicle', 84_000_000, 67_200_000, '20', 'conditional'],
    'the motorcycle (accepted)' => ['01.3.001872', 'vehicle', 14_100_000, 11_280_000, '20', 'accepted'],
]);

it('says on every sample appraisal that it is a sample and not a real appraisal', function () {
    seedExamples();

    foreach (CollateralAppraisal::all() as $appraisal) {
        expect(AppraisalEntry::where('collateral_appraisal_id', $appraisal->id)->where('item', 'conclusion.summary')->value('value'))->toStartWith('[CONTOH PENGISIAN]');
    }
});

it('refuses the land with customary papers because it cannot carry a mortgage right', function () {
    seedExamples();
    $appraisal = appraisalOf('01.3.002194');

    expect(AppraisalEntry::where('collateral_appraisal_id', $appraisal->id)->where('item', 'legal.can_be_encumbered')->value('value'))->toBe('no')
        ->and($appraisal->decision)->toBe('rejected');
});

it('does not appraise the two supporting documents, because the general form cannot accept a collateral that has no value', function () {
    seedExamples();

    expect(Collateral::where('collateral_type_code', '14')->count())->toBe(2)->and(CollateralAppraisal::whereIn('collateral_id', Collateral::where('collateral_type_code', '14')->pluck('id'))->count())->toBe(0);
});

it('holds only answers that are items of its form and that pass the rules of their item', function (AppraisalTemplate $template, array $answers) {
    $items = collect(AppraisalTemplates::items($template))->filter(fn (array $item): bool => AppraisalRules::answerable($item))->keyBy('key');
    $unknown = array_values(array_diff(array_keys($answers), $items->keys()->all()));
    $invalid = [];

    foreach ($answers as $key => $answer) {
        $item = $items[$key] ?? null;
        $value = is_array($answer) ? $answer[0] : $answer;
        $note = is_array($answer) ? $answer[1] : '';

        if ($item !== null && (Validator::make(['value' => $value], ['value' => AppraisalRules::for($item)])->fails() || mb_strlen($note) > AppraisalRules::NOTE_MAX)) {
            $invalid[] = $key;
        }
    }

    expect($unknown)->toBe([])->and($invalid)->toBe([]);
})->with([
    'land with customary papers' => [fn () => new LandBuildingTemplate, fn () => LandExamples::customaryPapers()],
    'land with a certificate' => [fn () => new LandBuildingTemplate, fn () => LandExamples::certificateWithoutBinding()],
    'the car' => [fn () => new VehicleTemplate('wheel4'), fn () => VehicleExamples::car()],
    'the motorcycle' => [fn () => new VehicleTemplate('wheel2'), fn () => VehicleExamples::motorcycle()],
]);

it('writes nothing to the audit trail, because seeding is not user activity', function () {
    superAdmin();
    AuditLog::query()->getQuery()->delete();

    $this->seed(CollateralExampleSeeder::class);

    expect(AuditLog::query()->count())->toBe(0);
});

it('does not seed samples in production, neither alone nor through the main seeder', function () {
    superAdmin();
    app()->detectEnvironment(fn (): string => 'production');

    app(CollateralExampleSeeder::class)->run();
    app(DatabaseSeeder::class)->run();

    expect(Collateral::count())->toBe(0);
});

it('lets two collaterals carry the same credit account while the collateral id stays unique', function () {
    $this->actingAs(superAdmin());
    Collateral::create(['collateral_type_code' => '05', 'cbs_id' => 'AG-1', 'credit_account' => '01.301.000001']);
    Collateral::create(['collateral_type_code' => '10', 'cbs_id' => 'AG-2', 'credit_account' => '01.301.000001']);

    expect(Collateral::where('credit_account', '01.301.000001')->count())->toBe(2)
        ->and(fn () => Collateral::create(['collateral_type_code' => '10', 'cbs_id' => 'AG-2']))->toThrow(UniqueConstraintViolationException::class);
});
