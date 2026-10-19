<?php

use App\Support\Navigation;
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

it('refuses with the Indonesian wording of the error screen, never with the English text of the permission package', function () {
    $this->actingAs(userWith(['dashboard.view']))
        ->get(route('ui-guideline'))
        ->assertForbidden()
        ->assertInertia(fn (Assert $page) => $page->component('error')->where('status', 403)->where('message', ''));
});

it('sends a guest to the sign-in page instead of showing the UI guideline', function () {
    $this->get(route('ui-guideline'))->assertRedirect(route('login'));
});

it('shows the UI guideline to a Super Admin with no data of its own, only what every page carries', function () {
    $this->actingAs(superAdmin())
        ->get(route('ui-guideline'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('ui-guideline')->missing('loans')->missing('stats'));
});

it('refuses every method other than GET on the UI guideline address', function () {
    $this->actingAs(superAdmin())->post('/ui-guideline')->assertStatus(405);
    $this->actingAs(superAdmin())->delete('/ui-guideline')->assertStatus(405);
});

it('lists the UI guideline in the menu of a Super Admin only', function () {
    $labels = fn ($user) => collect(Navigation::for($user))->flatMap(fn (array $section) => $section['items'])->pluck('label')->all();

    expect($labels(superAdmin()))->toContain('UI Guideline')
        ->and($labels(userWith(['dashboard.view'])))->not->toContain('UI Guideline');
});
