<?php

use App\Audit\Audit;
use App\Audit\AuditLog;
use App\Audit\AuditVerifier;
use App\Models\Collateral;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    config(['services.codex.endpoint' => 'https://codex.test', 'services.codex.token' => 'test-token']);
});

it('records who changed what, with the values before and after', function () {
    $admin = superAdmin();
    AuditLog::query()->getQuery()->delete();

    $this->actingAs($admin)->post('/master-data/products', ['code' => 'ZZ', 'alias' => 'ZZ', 'name' => 'Test Product', 'is_active' => true]);
    $product = Product::query()->where('code', 'ZZ')->firstOrFail();
    $this->actingAs($admin)->put("/master-data/products/{$product->id}", ['code' => 'ZZ', 'alias' => 'ZZ', 'name' => 'Renamed Product', 'is_active' => true]);

    $created = AuditLog::query()->where('event', 'products.created')->firstOrFail();
    $updated = AuditLog::query()->where('event', 'products.updated')->firstOrFail();

    expect($created->user_id)->toBe($admin->id)
        ->and($created->new_values)->toContain('TEST PRODUCT')
        ->and($updated->decoded('old_values'))->toBe(['name' => 'TEST PRODUCT'])
        ->and($updated->decoded('new_values'))->toBe(['name' => 'RENAMED PRODUCT'])
        ->and($updated->subject_label)->toBe('ZZ')
        ->and($updated->request_id)->not->toBe($created->request_id);
});

it('never stores secrets', function () {
    Audit::record('test.secret', 'test', 'x', new: ['password' => 'hunter2', 'nested' => ['token' => 'abc', 'ok' => 1]], context: ['otp' => '123456']);
    $row = AuditLog::query()->where('event', 'test.secret')->firstOrFail();

    expect($row->decoded('new_values'))->toBe(['password' => '[redacted]', 'nested' => ['token' => '[redacted]', 'ok' => 1]])
        ->and($row->decoded('context'))->toBe(['otp' => '[redacted]'])
        ->and(json_encode($row->toArray()))->not->toContain('hunter2');
});

it('chains entries and verifies an intact trail', function () {
    Audit::record('a.one', 'a', 'one');
    Audit::record('a.two', 'a', 'two');
    $rows = AuditLog::query()->orderBy('id')->get();

    expect($rows[1]->previous_hash)->toBe($rows[0]->hash)
        ->and(app(AuditVerifier::class)->verify())->toMatchArray(['ok' => true, 'checked' => 2]);
});

it('detects a changed or removed entry', function () {
    foreach (['one', 'two', 'three'] as $name) {
        Audit::record("a.{$name}", 'a', $name);
    }
    $ids = AuditLog::query()->orderBy('id')->pluck('id');

    DB::table('audit_logs')->where('id', $ids[1])->update(['outcome' => 'failure']);
    expect(app(AuditVerifier::class)->verify())->toMatchArray(['ok' => false, 'broken_at' => $ids[1]]);

    DB::table('audit_logs')->where('id', $ids[1])->update(['outcome' => 'success']);
    DB::table('audit_logs')->where('id', $ids[1])->delete();
    expect(app(AuditVerifier::class)->verify())->toMatchArray(['ok' => false, 'broken_at' => $ids[2]]);
});

it('refuses to update or delete an entry through the model', function () {
    $log = Audit::record('a.one', 'a', 'one');

    expect(fn () => $log->update(['outcome' => 'failure']))->toThrow(LogicException::class)
        ->and(fn () => $log->delete())->toThrow(LogicException::class);
});

it('records the audit:verify command result', function () {
    Audit::record('a.one', 'a', 'one');
    $this->artisan('audit:verify')->assertSuccessful();

    DB::table('audit_logs')->update(['module' => 'tampered']);
    $this->artisan('audit:verify')->assertFailed();
});

it('records successful and failed sign-ins and sign-outs', function () {
    $this->seed(RoleSeeder::class);
    fakeCodex();

    $this->post(route('login.store'), ['username' => '309011221', 'password' => 'x']);
    $this->post(route('logout'));

    expect(AuditLog::query()->where('event', 'auth.login')->where('username', '309011221')->exists())->toBeTrue()
        ->and(AuditLog::query()->where('event', 'auth.logout')->exists())->toBeTrue();

    Http::swap(new Factory);
    Http::fake(['codex.test/*' => Http::response(['success' => false, 'message' => 'bad'], 401)]);
    $this->post(route('login.store'), ['username' => 'nobody', 'password' => 'wrong']);

    $failed = AuditLog::query()->where('event', 'auth.login_failed')->firstOrFail();
    expect($failed->outcome)->toBe('failure')
        ->and($failed->decoded('context'))->toBe(['username' => 'nobody', 'reason' => 'invalid credentials'])
        ->and($failed->getRawOriginal('context'))->not->toContain('wrong');
});

it('records refused access', function () {
    $user = userWith(['dashboard.view']);
    $this->actingAs($user)->get('/users')->assertForbidden();

    $row = AuditLog::query()->where('event', 'access.denied')->firstOrFail();
    expect($row->user_id)->toBe($user->id)->and($row->outcome)->toBe('denied');
});

it('records role permission changes', function () {
    $admin = superAdmin();
    $role = Role::findOrCreate('Tester', 'web');
    $this->actingAs($admin)->put("/roles/{$role->id}/permissions", ['permissions' => ['loan-applications.view']]);

    $row = AuditLog::query()->where('event', 'roles.permissions_changed')->firstOrFail();
    expect($row->decoded('new_values')['added'])->toBe(['loan-applications.view']);
});

it('keeps seeding out of the trail and lets callers pause auditing', function () {
    Audit::withoutAuditing(fn () => Collateral::query()->getQuery()->count());
    expect(Audit::withoutAuditing(fn () => Audit::record('a.b', 'a', 'b')))->toBeNull();
});

it('lists the trail for permitted users only, with filters', function () {
    $admin = superAdmin();
    Audit::record('a.one', 'alpha', 'one', outcome: 'failure');
    Audit::record('b.one', 'beta', 'one');

    $this->actingAs(userWith(['dashboard.view']))->get('/audit-logs')->assertForbidden();

    $this->actingAs($admin)->get('/audit-logs?module=alpha&outcome=failure')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('audit-logs/index')
            ->has('logs.data', 1)
            ->where('logs.data.0.event', 'a.one'));
});

it('exports the filtered trail as CSV and records the export', function () {
    $admin = superAdmin();
    Audit::record('a.one', 'alpha', 'one', label: '=SUM(1)');

    $response = $this->actingAs($admin)->get('/audit-logs/export?module=alpha');
    $csv = $response->streamedContent();

    expect($csv)->toContain('a.one')->toContain("'=SUM(1)")
        ->and(AuditLog::query()->where('event', 'audit_logs.exported')->exists())->toBeTrue();
});

it('verifies the chain from the page', function () {
    $admin = superAdmin();
    Audit::record('a.one', 'a', 'one');

    $this->actingAs($admin)->post('/audit-logs/verify')->assertRedirect();
    expect(AuditLog::query()->where('event', 'audit_logs.verified')->firstOrFail()->outcome)->toBe('success');
});

it('does not audit users hidden fields', function () {
    $user = User::factory()->create();
    $user->forceFill(['remember_token' => 'abc'])->save();

    expect(AuditLog::query()->where('subject_type', 'like', '%User')->where('event', 'users.updated')->exists())->toBeFalse();
});

it('prunes entries past the retention period and keeps the rest verifiable', function () {
    config(['security.audit_retention_years' => 5]);

    $this->travelTo(now()->subYears(6));
    Audit::record('old.one', 'old', 'one');
    Audit::record('old.two', 'old', 'two');
    $this->travelBack();
    Audit::record('new.one', 'new', 'one');

    $this->artisan('audit:prune', ['--dry-run' => true])->assertSuccessful();
    expect(AuditLog::query()->count())->toBe(3);

    $this->artisan('audit:prune')->assertSuccessful();

    expect(AuditLog::query()->orderBy('id')->pluck('event')->all())->toBe(['new.one', 'audit_logs.pruned'])
        ->and(DB::table('audit_anchors')->value('pruned_count'))->toBe(2)
        ->and(app(AuditVerifier::class)->verify())->toMatchArray(['ok' => true, 'checked' => 2]);
});

it('still detects tampering after a prune, and a removed anchor', function () {
    $this->travelTo(now()->subYears(6));
    Audit::record('old.one', 'old', 'one');
    $this->travelBack();
    Audit::record('new.one', 'new', 'one');
    Audit::record('new.two', 'new', 'two');
    $this->artisan('audit:prune')->assertSuccessful();

    DB::table('audit_anchors')->delete();
    expect(app(AuditVerifier::class)->verify()['ok'])->toBeFalse();
});

it('does not prune when nothing is old enough', function () {
    Audit::record('new.one', 'new', 'one');
    $this->artisan('audit:prune')->assertSuccessful();

    expect(AuditLog::query()->count())->toBe(1)->and(DB::table('audit_anchors')->count())->toBe(0);
});
