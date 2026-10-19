<?php

use App\Models\AppNotification;
use App\Models\User;
use App\Support\Navigation;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * The titles and descriptions written in `resources/js/lib/error-pages.ts`, by status.
 *
 * @return array<int, array{title: string, description: string}>
 */
function clientErrorWording(): array
{
    $source = (string) file_get_contents(resource_path('js/lib/error-pages.ts'));
    preg_match_all("/status: (\d+),\s+title: '((?:[^'\\\\]|\\\\.)*)',\s+description:\s+'((?:[^'\\\\]|\\\\.)*)'/", $source, $found, PREG_SET_ORDER);

    return collect($found)->mapWithKeys(fn (array $m): array => [(int) $m[1] => ['title' => stripslashes($m[2]), 'description' => stripslashes($m[3])]])->all();
}

function note(User $user, array $attributes = []): AppNotification
{
    return AppNotification::create([...['user_id' => $user->id, 'title' => 'Judul', 'module' => 'test', 'level' => 'info'], ...$attributes]);
}

it('says the same words on the server-rendered error pages as on the screens of the app, for every status they share', function (int $status) {
    $wording = clientErrorWording();

    $html = html_entity_decode(view("errors.$status")->render());

    expect($wording)->toHaveKey($status)
        ->and($html)->toContain($wording[$status]['title'])
        ->and($html)->toContain($wording[$status]['description']);
})->with([401, 403, 404, 419, 429, 500, 503]);

it('shows nothing of the cause on a server error', function () {
    config(['app.debug' => false]);
    Route::get('/_boom', fn () => throw new RuntimeException('secret detail from /var/www/app/Secret.php'));

    $html = $this->get('/_boom')->assertStatus(500)->getContent();

    expect($html)->not->toContain('secret detail')->not->toContain('RuntimeException')->not->toContain('/var/www');
});

it('tells search engines to stay away on every kind of answer', function () {
    Route::get('/_missing', fn () => abort(404));

    $this->get('/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    $this->get('/_missing')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    expect(file_get_contents(public_path('robots.txt')))->toContain('Disallow: /');
});

it('hands a person only their own notifications, the latest first, with the unread count', function () {
    $me = superAdmin();
    $other = User::factory()->create();
    foreach (range(1, 12) as $i) {
        note($me, ['title' => "Saya $i", 'read_at' => $i <= 4 ? now() : null]);
    }
    note($other, ['title' => 'Orang lain']);

    $this->actingAs($me)->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page
        ->where('notifications.unread', 8)
        ->has('notifications.items', 10)
        ->where('notifications.items.0.title', 'Saya 12')
        ->where('notifications.items', fn ($items) => collect($items)->every(fn ($n) => str_starts_with($n['title'], 'Saya'))));
});

it('marks one notification read once, only for its owner', function () {
    $me = superAdmin();
    $other = User::factory()->create();
    $mine = note($me);
    $theirs = note($other);

    $this->actingAs($me)->post(route('notifications.read', $mine))->assertRedirect();
    $firstRead = $mine->fresh()->read_at;
    $this->travel(5)->minutes();
    $this->post(route('notifications.read', $mine))->assertRedirect();
    $this->post(route('notifications.read', $theirs))->assertForbidden();
    $this->post('/notifications/99999/read')->assertNotFound();

    expect($mine->fresh()->read_at->equalTo($firstRead))->toBeTrue()->and($theirs->fresh()->read_at)->toBeNull();
});

it('marks all of my notifications read and leaves everybody else alone', function () {
    $me = superAdmin();
    $other = User::factory()->create();
    note($me);
    note($me);
    $theirs = note($other);

    $this->actingAs($me)->post(route('notifications.read-all'))->assertRedirect();

    expect(AppNotification::query()->where('user_id', $me->id)->whereNull('read_at')->count())->toBe(0)->and($theirs->fresh()->read_at)->toBeNull();
});

it('keeps notifications for people who are signed in', function () {
    $this->post('/notifications/read-all')->assertRedirect(route('login'));
});

it('shows each person only the menu their permissions allow, and nothing for a role without any', function () {
    $labels = fn (User $user): array => collect(Navigation::for($user))->flatMap(fn (array $section) => $section['items'])->pluck('label')->all();

    $loans = userWith(['loan-applications.view'], 'Pengaju');
    $nobody = userWith([], 'Kosong');
    $admin = superAdmin();

    expect($labels($loans))->toContain('Pengajuan')->not->toContain('Jaminan')->not->toContain('Data Kantor')->not->toContain('Data Pengguna')
        ->and($labels($nobody))->toBe([])
        ->and($labels($admin))->toContain('Pengajuan')->toContain('Dashboard');
});

it('lets a person close the two-step banner until they sign out, and brings it back at the next sign-in', function () {
    $me = superAdmin();
    $this->actingAs($me)->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page->where('security.reminderDismissed', false));

    $this->post(route('profile.two-factor.reminder.dismiss'))->assertRedirect();
    $this->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page->where('security.reminderDismissed', true));

    $this->post(route('logout'));
    $this->actingAs($me)->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page->where('security.reminderDismissed', false));
});

it('keeps the banner closing to people who are signed in', function () {
    $this->post(route('profile.two-factor.reminder.dismiss'))->assertRedirect(route('login'));
});
