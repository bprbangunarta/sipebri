<?php

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use Inertia\Testing\AssertableInertia as Assert;

function dashboardLoan(int $number, int $creator, LoanStatus $status, array $extra = []): LoanApplication
{
    return LoanApplication::create([
        'application_code' => sprintf('%08d', 700000 + $number), 'application_date' => now(), 'status' => $status->value,
        'nik' => '3201000000000001', 'full_name' => "Person {$number}", 'requested_amount' => 1_000_000, 'created_by' => $creator, ...$extra,
    ]);
}

it('renders the dashboard with no files', function () {
    $this->actingAs(superAdmin())->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->component('dashboard')->where('stats.total', 0)->where('scope', 'all'));
});

it('summarises the credit files by status and month', function () {
    $admin = superAdmin();
    dashboardLoan(1, $admin->id, LoanStatus::Draft);
    dashboardLoan(2, $admin->id, LoanStatus::Submitted);
    dashboardLoan(3, $admin->id, LoanStatus::Survey);
    dashboardLoan(4, $admin->id, LoanStatus::Approved);

    $this->actingAs($admin)->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('stats.total', 4)
            ->where('stats.in_process', 2)
            ->where('stats.drafts', 1)
            ->where('stats.this_month', 4)
            ->where('stats.amount_in_process', 2_000_000)
            ->where('byMonth.11.total', 4)
            ->has('recent', 4));
});

it('shows only their own files to people without a later-stage permission', function () {
    $owner = userWith(['dashboard.view', 'loan-applications.view'], 'Owner');
    $other = userWith(['dashboard.view'], 'Other');
    dashboardLoan(1, $owner->id, LoanStatus::Draft);
    dashboardLoan(2, $other->id, LoanStatus::Draft);

    $this->actingAs($owner)->get(route('dashboard'))
        ->assertInertia(fn (Assert $page) => $page->where('scope', 'mine')->where('stats.total', 1));
});
