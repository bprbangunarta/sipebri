<?php

use App\Audit\AuditLog;
use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\ProductParameter;
use App\Models\Setting;
use App\Support\LendingLimit;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->actingAs(superAdmin());
});

function changeBmpk(mixed $amount, mixed $reason = 'Penyesuaian aturan'): TestResponse
{
    return test()->put(route('bmpk.update'), ['amount' => $amount, 'reason' => $reason]);
}

it('refuses a limit that is not a whole positive amount within twelve digits, and keeps the old one', function (mixed $amount) {
    LendingLimit::setBmpk(LendingLimit::DEFAULT_BMPK);
    changeBmpk($amount)->assertSessionHasErrors('amount');

    expect(LendingLimit::bmpk())->toBe(2_000_000_000);
})->with(['zero' => 0, 'negative' => -1, 'text' => 'abc', 'fraction' => 1.5, 'too large' => 1_000_000_000_000, 'empty' => null, 'array' => [[5]], 'scientific' => '1e9']);

it('accepts the largest amount of twelve digits and the smallest of one rupiah', function () {
    changeBmpk(999_999_999_999)->assertSessionHasNoErrors();
    expect(LendingLimit::bmpk())->toBe(999_999_999_999);

    changeBmpk(1)->assertSessionHasNoErrors();
    expect(LendingLimit::bmpk())->toBe(1);
});

it('wants a reason for every change, and keeps it with the audit entry', function (mixed $reason) {
    LendingLimit::setBmpk(LendingLimit::DEFAULT_BMPK);
    changeBmpk(2_500_000_000, $reason)->assertSessionHasErrors('reason');

    expect(LendingLimit::bmpk())->toBe(2_000_000_000)->and(AuditLog::query()->where('event', 'settings.updated')->count())->toBe(0);
})->with(['missing' => null, 'blank' => '   ', 'too short' => 'ya', 'too long' => str_repeat('x', 256), 'array' => [['x']]]);

it('records who changed the limit, from what to what, and why', function () {
    $admin = auth()->user();
    LendingLimit::setBmpk(LendingLimit::DEFAULT_BMPK);
    AuditLog::query()->getQuery()->delete();

    changeBmpk(2_500_000_000, 'Penyesuaian modal inti')->assertSessionHas('success');

    $row = AuditLog::query()->where('event', 'settings.updated')->firstOrFail();

    expect($row->user_id)->toBe($admin->id)
        ->and($row->decoded('old_values'))->toBe(['value' => '2000000000'])
        ->and($row->decoded('new_values'))->toBe(['value' => '2500000000'])
        ->and($row->decoded('context'))->toMatchArray(['reason' => 'Penyesuaian modal inti']);
});

it('saves and records nothing when the limit does not change, and says so', function () {
    LendingLimit::setBmpk(LendingLimit::DEFAULT_BMPK);
    AuditLog::query()->getQuery()->delete();

    changeBmpk(2_000_000_000)->assertSessionHasNoErrors()->assertSessionHas('success', 'BMPK tidak berubah.');

    expect(AuditLog::query()->count())->toBe(0);
});

it('shows the current limit, and nothing when none is set', function () {
    LendingLimit::setBmpk(LendingLimit::DEFAULT_BMPK);
    $this->get(route('bmpk.show'))->assertInertia(fn (Assert $page) => $page->where('amount', 2_000_000_000));

    Setting::query()->where('key', 'bmpk')->delete();

    $this->get(route('bmpk.show'))->assertInertia(fn (Assert $page) => $page->where('amount', null));
});

it('keeps the BMPK for Super Admin only, and for people who are signed in', function () {
    LendingLimit::setBmpk(LendingLimit::DEFAULT_BMPK);
    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view', 'loan-applications.manage']));
    $this->get(route('bmpk.show'))->assertForbidden();
    changeBmpk(1)->assertForbidden();

    auth()->logout();
    $this->put(route('bmpk.update'), ['amount' => 1, 'reason' => 'Penyesuaian'])->assertRedirect(route('login'));

    expect(LendingLimit::bmpk())->toBe(2_000_000_000);
});

it('leaves the amount of every loan alone when the limit is lowered, and refuses to submit a draft that is now above it', function () {
    $admin = auth()->user();
    $s = loanSetup();
    $this->actingAs($officer = loanOfficer());
    fakeCustomers();
    $this->post(route('loan-applications.store'), ['nik' => '3201000000000001']);
    $loan = LoanApplication::firstOrFail();
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 40_000_000]))->assertSessionHasNoErrors();

    $this->actingAs($admin);
    changeBmpk(30_000_000)->assertSessionHasNoErrors();

    expect($loan->fresh()->requested_amount)->toBe(40_000_000);

    $this->actingAs($officer)->post(route('loan-applications.confirm', $loan))->assertSessionHas('error', fn (string $message): bool => str_contains($message, 'BMPK') && str_contains($message, 'Rp30.000.000'));
    expect($loan->fresh()->status)->toBe(LoanStatus::Draft);

    $this->actingAs($admin);
    changeBmpk(40_000_000)->assertSessionHasNoErrors();

    $this->actingAs($officer)->post(route('loan-applications.confirm', $loan))->assertSessionHas('success');
    expect($loan->fresh()->status)->toBe(LoanStatus::Submitted);
});

it('refuses to submit a draft that is above the limit of its product too', function () {
    $s = loanSetup();
    $this->actingAs($officer = loanOfficer());
    fakeCustomers();
    $this->post(route('loan-applications.store'), ['nik' => '3201000000000001']);
    $loan = LoanApplication::firstOrFail();
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 40_000_000]))->assertSessionHasNoErrors();

    ProductParameter::query()->where('product_id', $s['product']->id)->update(['max_amount' => 20_000_000]);

    $this->post(route('loan-applications.confirm', $loan))->assertSessionHas('error');
    expect($loan->fresh()->status)->toBe(LoanStatus::Draft);
});
