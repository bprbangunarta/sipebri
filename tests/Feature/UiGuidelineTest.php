<?php

use Inertia\Testing\AssertableInertia as Assert;

it('shows the UI guideline to a Super Admin', function () {
    $this->actingAs(superAdmin())
        ->get(route('ui-guideline'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('ui-guideline'));
});

it('refuses the UI guideline to anyone who is not a Super Admin', function () {
    $this->actingAs(userWith(['dashboard.view']))
        ->get(route('ui-guideline'))
        ->assertForbidden();
});
