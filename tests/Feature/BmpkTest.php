<?php

use App\Audit\AuditLog;
use App\Models\LoanApplication;
use App\Models\ProductParameter;
use App\Models\Setting;
use App\Support\LendingLimit;
use Database\Seeders\CreditReferenceSeeder;
use Inertia\Testing\AssertableInertia as Assert;

it('starts at Rp 2 billion from the seeder and never resets a later change', function () {
    $this->seed(CreditReferenceSeeder::class);
    expect(LendingLimit::bmpk())->toBe(2_000_000_000);

    LendingLimit::setBmpk(3_000_000_000);
    $this->seed(CreditReferenceSeeder::class);

    expect(LendingLimit::bmpk())->toBe(3_000_000_000);
});

it('has no limit when none is set', function () {
    expect(LendingLimit::bmpk())->toBeNull();

    Setting::create(['key' => 'bmpk', 'value' => '0']);
    expect(LendingLimit::bmpk())->toBeNull();
});

it('refuses a loan above the BMPK even when the product allows more', function () {
    $s = loanSetup();
    ProductParameter::where('product_id', $s['product']->id)->update(['max_amount' => 999_999_999_999]);
    LendingLimit::setBmpk(2_000_000_000);
    $this->actingAs($officer = loanOfficer());
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => $officer->id]);

    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 2_000_000_001]))
        ->assertSessionHasErrors(['requested_amount' => 'The loan amount exceeds the legal lending limit (BMPK) of Rp2.000.000.000.']);
    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 2_000_000_000]))->assertSessionHasNoErrors();
});

it('keeps the stricter product limit when it is below the BMPK', function () {
    $s = loanSetup();
    LendingLimit::setBmpk(2_000_000_000);
    $this->actingAs($officer = loanOfficer());
    $loan = LoanApplication::create(['application_code' => '00700001', 'application_date' => now(), 'status' => 'draft', 'nik' => '1', 'full_name' => 'A', 'created_by' => $officer->id]);

    $this->put(route('loan-applications.update', $loan), loanPayload($s, ['requested_amount' => 60_000_000]))
        ->assertSessionHasErrors(['requested_amount' => 'The maximum loan amount is Rp50.000.000 for this product.']);
});

it('lets only Super Admin change the BMPK, and records the change', function () {
    $this->seed(CreditReferenceSeeder::class);

    $this->actingAs(userWith(['dashboard.view', 'loan-applications.manage']))->put(route('bmpk.update'), ['amount' => 1])->assertForbidden();
    expect(LendingLimit::bmpk())->toBe(2_000_000_000);

    $admin = superAdmin();
    $this->actingAs($admin)->get(route('bmpk.show'))->assertInertia(fn (Assert $page) => $page->component('master-data/bmpk')->where('amount', 2_000_000_000));
    $this->put(route('bmpk.update'), ['amount' => 0])->assertSessionHasErrors('amount');
    $this->put(route('bmpk.update'), ['amount' => 2_500_000_000])->assertSessionHasNoErrors();

    $row = AuditLog::query()->where('event', 'settings.updated')->firstOrFail();
    expect(LendingLimit::bmpk())->toBe(2_500_000_000)
        ->and($row->user_id)->toBe($admin->id)
        ->and($row->decoded('old_values'))->toBe(['value' => '2000000000'])
        ->and($row->decoded('new_values'))->toBe(['value' => '2500000000']);
});
