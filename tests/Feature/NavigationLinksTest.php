<?php

use App\Support\Navigation;

/** Every menu entry must open: a renamed URL or route that the sidebar still points to would show up here. */
function menuHrefs($user): array
{
    $hrefs = [];

    foreach (Navigation::for($user) as $section) {
        foreach ($section['items'] as $item) {
            foreach ($item['children'] ?? [$item] as $link) {
                $hrefs[] = $link['href'];
            }
        }
    }

    return $hrefs;
}

it('opens every menu link for a Super Admin', function () {
    $admin = superAdmin();
    $hrefs = menuHrefs($admin);

    expect($hrefs)->not->toBeEmpty();

    foreach ($hrefs as $href) {
        $response = $this->actingAs($admin)->get($href);

        // Menu entries that redirect (the committee hub) must land on a page that opens.
        if ($response->isRedirect()) {
            $response = $this->actingAs($admin)->get($response->headers->get('Location'));
        }

        expect($response->status())->toBe(200, "{$href} does not open");
    }
});

it('opens every menu link for the workflow roles', function (string $role, array $permissions) {
    $user = userWith($permissions, $role);

    foreach (menuHrefs($user) as $href) {
        expect($this->actingAs($user)->get($href)->status())->toBeIn([200, 302], "{$href} does not open for {$role}");
    }
})->with([
    'analyst' => ['Staff Analis & Appraisal', ['dashboard.view', 'loan-applications.view', 'surveys.view', 'surveys.manage', 'credit-analysis.view', 'approvals.view']],
    'section head' => ['Kepala Seksi Analis', ['dashboard.view', 'loan-applications.view', 'scheduling.view', 'scheduling.manage', 'surveys.view', 'surveys.manage', 'approvals.view']],
    'account officer' => ['AO Kredit', ['dashboard.view', 'loan-applications.view', 'loan-applications.manage', 'collaterals.view', 'collaterals.manage']],
]);
