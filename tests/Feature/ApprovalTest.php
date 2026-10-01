<?php

use Inertia\Testing\AssertableInertia as Assert;

it('shows the approvals placeholder to people with the permission and lists it in the menu', function () {
    $user = userWith(['dashboard.view', 'approvals.view']);

    $this->actingAs($user)->get('/approvals')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('approvals/index')
        ->where('navigation.0.items', fn ($items) => collect($items)->contains('label', 'Persetujuan')));
});

it('keeps the approvals page away from people without the permission', function () {
    $this->actingAs(userWith(['dashboard.view']))->get('/approvals')->assertForbidden();
});
