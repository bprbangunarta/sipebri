<?php

use App\Audit\AuditLog;
use App\Models\LoanApplication;
use App\Models\Office;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

function loanAt(Office $office): LoanApplication
{
    return LoanApplication::create([
        'application_code' => '0070000'.random_int(1, 9), 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A',
        'created_by' => auth()->id(), 'office_id' => $office->id,
    ]);
}

it('refuses to delete an office that an active person or application uses, with the count', function () {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    User::factory()->create(['office_id' => $office->id]);
    loanAt($office);

    $this->delete(route('references.destroy', ['offices', $office->id]))
        ->assertSessionHas('error', fn (string $message): bool => str_contains($message, '1 pengajuan kredit') || str_contains($message, '1 pengguna'));

    expect(Office::count())->toBe(1);
});

it('refuses to delete an office that only deactivated people or deleted applications still hold', function (string $holder) {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    if ($holder === 'user') {
        User::factory()->create(['office_id' => $office->id])->delete();
    } else {
        loanAt($office)->delete();
    }

    $this->delete(route('references.destroy', ['offices', $office->id]))->assertSessionHas('error');

    expect(Office::count())->toBe(1);
})->with(['deactivated user' => 'user', 'deleted application' => 'loan']);

it('shows the same count in the Dipakai column as the delete refusal uses', function () {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    User::factory()->create(['office_id' => $office->id])->delete();
    loanAt($office)->delete();

    $this->get(route('references.index', 'offices'))->assertInertia(fn (Assert $page) => $page->where('items.data.0.usage_count', 2));
});

it('answers one validation sentence, not a server error, for hostile values', function (array $input, string $field) {
    Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    $this->post(route('references.store', 'offices'), $input)->assertSessionHasErrors($field);

    expect(Office::count())->toBe(1);
})->with([
    'code over 8' => [['code' => '123456789', 'alias' => 'AAA', 'name' => 'X'], 'code'],
    'alias over 8' => [['code' => '11', 'alias' => '123456789', 'name' => 'X'], 'alias'],
    'name over 100' => [['code' => '11', 'alias' => 'AAA', 'name' => str_repeat('x', 101)], 'name'],
    'code as array' => [['code' => ['11'], 'alias' => 'AAA', 'name' => 'X'], 'code'],
    'name as array' => [['code' => '11', 'alias' => 'AAA', 'name' => ['x']], 'name'],
    'code only spaces' => [['code' => '   ', 'alias' => 'AAA', 'name' => 'X'], 'code'],
    'code taken in other case' => [['code' => '00', 'alias' => 'AAA', 'name' => 'X'], 'code'],
    'alias taken in other case' => [['code' => '11', 'alias' => 'pmk', 'name' => 'X'], 'alias'],
]);

it('stores markup and wildcard characters as plain text', function () {
    $this->post(route('references.store', 'offices'), ['code' => '11', 'alias' => 'A%_', 'name' => '<script>alert(1)</script>'])->assertSessionHasNoErrors();

    expect(Office::query()->where('code', '11')->first(['alias', 'name'])->toArray())->toBe(['alias' => 'A%_', 'name' => '<script>alert(1)</script>']);
});

it('searches % and _ as the characters they are', function () {
    Office::create(['code' => '11', 'alias' => 'AB', 'name' => 'Plain']);
    Office::create(['code' => '12', 'alias' => 'CD', 'name' => '100% Real']);

    $codes = fn (string $term): array => collect($this->get(route('references.index', ['offices', 'search' => $term]))->inertiaProps('items.data'))->pluck('code')->sort()->values()->all();

    // SQLite has no default escape character (MySQL does), so only what is the same on both is asserted: a wildcard never matches every row.
    expect($codes('%'))->not->toContain('11')->and($codes('_'))->toBe([])->and($codes('P_ain'))->toBe([]);
});

it('answers not found, not a server error, for an id that does not exist or is not a number', function () {
    $this->put(route('references.update', ['offices', 999]), ['code' => '11', 'alias' => 'AAA', 'name' => 'X'])->assertNotFound();
    $this->delete(route('references.destroy', ['offices', 999]))->assertNotFound();
    $this->put('/references/offices/abc', [])->assertNotFound();
    $this->get('/references/nothing')->assertNotFound();
});

it('turns a code taken at the same moment into a validation sentence and saves nothing', function () {
    Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    Office::creating(function (Office $office): void {
        if ($office->code === '11') {
            throw new UniqueConstraintViolationException('sqlite', 'insert into offices', [], new Exception('UNIQUE constraint failed: offices.code'));
        }
    });

    $this->post(route('references.store', 'offices'), ['code' => '11', 'alias' => 'AAA', 'name' => 'X'])->assertSessionHasErrors('code');

    expect(Office::count())->toBe(1);
});

it('keeps every action for Super Admin only', function (string $method, string $uri) {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view', 'loan-applications.manage']));

    $this->{$method}(str_replace('{id}', (string) $office->id, $uri), ['code' => '11', 'alias' => 'AAA', 'name' => 'X'])->assertForbidden();

    expect(Office::count())->toBe(1);
})->with([['get', '/references/offices'], ['post', '/references/offices'], ['put', '/references/offices/{id}'], ['delete', '/references/offices/{id}']]);

it('writes the audit entry of every change with the office as subject', function () {
    AuditLog::query()->getQuery()->delete();

    $this->post(route('references.store', 'offices'), ['code' => '11', 'alias' => 'AAA', 'name' => 'First']);
    $office = Office::firstWhere('code', '11');
    $this->put(route('references.update', ['offices', $office->id]), ['code' => '11', 'alias' => 'AAA', 'name' => 'Second']);
    $this->delete(route('references.destroy', ['offices', $office->id]));

    $events = AuditLog::query()->orderBy('id')->pluck('event')->all();
    $updated = AuditLog::query()->where('event', 'offices.updated')->firstOrFail();

    expect($events)->toBe(['offices.created', 'offices.updated', 'offices.deleted'])
        ->and(json_decode((string) $updated->old_values, true))->toBe(['name' => 'First'])
        ->and(json_decode((string) $updated->new_values, true))->toBe(['name' => 'Second']);
});

it('does not write an audit entry when a save changes nothing', function () {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    AuditLog::query()->getQuery()->delete();

    $this->put(route('references.update', ['offices', $office->id]), ['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan'])->assertSessionHasNoErrors();

    expect(AuditLog::query()->count())->toBe(0);
});

it('refuses an office from Codex whose code, alias or name does not fit the columns, and keeps the person out', function (array $office) {
    Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    Http::swap(new Factory);
    config(['services.codex.endpoint' => 'https://codex.test', 'services.codex.token' => 'test-token']);
    Http::fake(['codex.test/*' => Http::response(['success' => true, 'data' => [
        'user' => ['id' => 70, 'username' => 'u70', 'name' => 'U', 'role' => 'Guest', 'is_active' => true],
        'office' => $office,
    ]])]);

    $this->post(route('logout'));
    $this->post(route('login.store'), ['username' => 'u70', 'password' => 'x'])->assertSessionHasErrors();

    expect(User::find(70))->toBeNull()->and(Office::count())->toBe(1);
})->with([
    'code over 8' => [['code' => '123456789', 'name' => 'Long']],
    'alias over 8' => [['code' => '71', 'alias' => '123456789', 'name' => 'Long']],
    'name over 100' => [['code' => '72', 'name' => str_repeat('x', 101)]],
]);

it('shows an office without alias with its name, not with an empty label', function () {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    $office->update(['alias' => null]);

    $loan = loanAt($office);

    $this->get(route('loan-applications.show', $loan))
        ->assertInertia(fn (Assert $page) => $page->where('references.offices.0.label', 'Pamanukan'));
});

it('keeps the limits of the form and of what Codex may send in step', function () {
    $fields = collect(config('references.offices.fields'))->keyBy('name');

    expect($fields['code']['max'])->toBe(Office::CODE_MAX)
        ->and($fields['alias']['max'])->toBe(Office::ALIAS_MAX)
        ->and($fields['name']['max'])->toBe(Office::NAME_MAX);
});

it('keeps the code of an office that people or applications use, and lets an unused one change', function (string $holder) {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    if ($holder === 'user') {
        User::factory()->create(['office_id' => $office->id]);
    } elseif ($holder === 'deactivated user') {
        User::factory()->create(['office_id' => $office->id])->delete();
    } else {
        loanAt($office);
    }

    $this->put(route('references.update', ['offices', $office->id]), ['code' => '99', 'alias' => 'PMK', 'name' => 'Pamanukan'])->assertSessionHasErrors('code');
    expect($office->fresh()->code)->toBe('00');

    $this->put(route('references.update', ['offices', $office->id]), ['code' => '00', 'alias' => 'PMK', 'name' => 'Renamed'])->assertSessionHasNoErrors();
    expect($office->fresh()->name)->toBe('Renamed');
})->with(['active user' => 'user', 'deactivated user' => 'deactivated user', 'application' => 'loan']);

it('changes the code of an office nobody uses', function () {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    $this->put(route('references.update', ['offices', $office->id]), ['code' => '99', 'alias' => 'PMK', 'name' => 'Pamanukan'])->assertSessionHasNoErrors();

    expect($office->fresh()->code)->toBe('99');
});

it('keeps the name as written and tells on the form that Codex refreshes name and alias', function () {
    $office = Office::create(['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan']);
    $this->put(route('references.update', ['offices', $office->id]), ['code' => '00', 'alias' => 'pmk', 'name' => 'Pamanukan Baru'])->assertSessionHasNoErrors();

    expect(Office::query()->find($office->id, ['alias', 'name'])->toArray())->toBe(['alias' => 'PMK', 'name' => 'Pamanukan Baru']);

    $this->get(route('references.index', 'offices'))->assertInertia(fn (Assert $page) => $page
        ->where('resource.fields.1.hint', fn (string $hint): bool => str_contains($hint, 'Codex'))
        ->where('resource.fields.2.hint', fn (string $hint): bool => str_contains($hint, 'Codex'))
        ->missing('resource.fields.2.uppercase'));
});
