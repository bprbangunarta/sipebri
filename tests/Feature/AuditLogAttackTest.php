<?php

use App\Audit\Audit;
use App\Audit\AuditLog;
use App\Audit\AuditVerifier;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/** A trail whose entry #2 has every column filled (subject, before and after, request, address, agent). */
function richTrail(): array
{
    $admin = superAdmin();
    test()->actingAs($admin);
    test()->withHeaders(['User-Agent' => 'Mozilla/5.0 (Uji)'])->post('/references/products', ['code' => 'ZZ', 'alias' => 'ZZ', 'name' => 'Satu', 'is_active' => true]);
    $product = Product::query()->where('code', 'ZZ')->firstOrFail();
    test()->withHeaders(['User-Agent' => 'Mozilla/5.0 (Uji)'])->put("/references/products/{$product->id}", ['code' => 'ZZ', 'alias' => 'ZZ', 'name' => 'Dua', 'is_active' => true]);
    Audit::record('akhir.satu', 'akhir', 'satu');
    Audit::record('akhir.dua', 'akhir', 'dua');

    return [$admin, AuditLog::query()->where('event', 'products.updated')->firstOrFail()];
}

dataset('hashed columns', [
    'created_at' => ['created_at', '2001-01-01 00:00:00.000000'], 'request_id' => ['request_id', 'bukan-aslinya'], 'user_id' => ['user_id', 999], 'user_name' => ['user_name', 'Orang Lain'],
    'username' => ['username', 'orang-lain'], 'event' => ['event', 'x.y'], 'module' => ['module', 'xmod'], 'action' => ['action', 'xact'], 'outcome' => ['outcome', 'failure'],
    'subject_type' => ['subject_type', 'App\\Models\\Lain'], 'subject_id' => ['subject_id', 424242], 'subject_label' => ['subject_label', 'LAIN'],
    'old_values' => ['old_values', '{"name":"PALSU"}'], 'new_values' => ['new_values', '{"name":"PALSU"}'], 'context' => ['context', '{"alasan":"palsu"}'],
    'ip_address' => ['ip_address', '10.9.8.7'], 'user_agent' => ['user_agent', 'Peramban Lain'], 'http_method' => ['http_method', 'DELETE'], 'url' => ['url', 'http://x.test/lain'],
]);

it('notices a change in any column of an entry, however small', function (string $column, mixed $forged) {
    [, $row] = richTrail();
    $original = DB::table('audit_logs')->where('id', $row->id)->value($column);

    DB::table('audit_logs')->where('id', $row->id)->update([$column => $forged]);

    expect(app(AuditVerifier::class)->verify())->toMatchArray(['ok' => false, 'broken_at' => $row->id]);

    DB::table('audit_logs')->where('id', $row->id)->update([$column => $original]);
    expect(app(AuditVerifier::class)->verify()['ok'])->toBeTrue();
})->with('hashed columns');

it('notices an entry that was slipped in, one that was removed, and two that were swapped', function () {
    richTrail();
    $rows = AuditLog::query()->orderBy('id')->get();

    // Slipped in: a row with a believable link and a made-up hash.
    $copy = (array) DB::table('audit_logs')->where('id', $rows[2]->id)->first();
    unset($copy['id']);
    DB::table('audit_logs')->insert([...$copy, 'hash' => str_repeat('a', 64), 'event' => 'sisipan.palsu']);
    expect(app(AuditVerifier::class)->verify()['ok'])->toBeFalse();
    DB::table('audit_logs')->where('event', 'sisipan.palsu')->delete();
    expect(app(AuditVerifier::class)->verify()['ok'])->toBeTrue();

    // Swapped: the events of two entries that differ trade places.
    $created = $rows->firstWhere('event', 'products.created');
    $updated = $rows->firstWhere('event', 'products.updated');
    DB::table('audit_logs')->where('id', $created->id)->update(['event' => 'products.updated']);
    DB::table('audit_logs')->where('id', $updated->id)->update(['event' => 'products.created']);
    expect(app(AuditVerifier::class)->verify())->toMatchArray(['ok' => false, 'broken_at' => $created->id]);
});

it('notices the newest entries being deleted, also when new ones were written after', function () {
    richTrail();
    expect(app(AuditVerifier::class)->verify()['ok'])->toBeTrue();

    DB::table('audit_logs')->orderByDesc('id')->limit(2)->delete();
    expect(app(AuditVerifier::class)->verify())->toMatchArray(['ok' => false])
        ->and(app(AuditVerifier::class)->verify()['reason'])->toContain('terbaru');

    Audit::record('sesudah.potong', 'x', 'y');
    expect(app(AuditVerifier::class)->verify()['ok'])->toBeFalse();
});

it('notices an entry that kept its number but not its content, at the end of the chain', function () {
    richTrail();
    app(AuditVerifier::class)->verify();
    $last = AuditLog::query()->orderByDesc('id')->firstOrFail();

    DB::table('audit_logs')->where('id', $last->id)->delete();
    $copy = (array) DB::table('audit_logs')->orderByDesc('id')->first();
    unset($copy['id']);
    DB::table('audit_logs')->insert([...$copy, 'id' => $last->id, 'event' => 'pengganti', 'hash' => str_repeat('b', 64), 'previous_hash' => $copy['hash']]);

    expect(app(AuditVerifier::class)->verify()['ok'])->toBeFalse();
});

it('does not mind the oldest entries being removed with audit:prune, however many times it is verified', function () {
    $this->travelTo(now()->subYears(6));
    Audit::record('lama.satu', 'x', 'y');
    $this->travelBack();
    Audit::record('baru.satu', 'x', 'y');
    expect(app(AuditVerifier::class)->verify()['ok'])->toBeTrue();

    $this->artisan('audit:prune', ['--force' => true])->assertSuccessful();

    expect(app(AuditVerifier::class)->verify()['ok'])->toBeTrue()->and(app(AuditVerifier::class)->verify()['ok'])->toBeTrue();
});

it('records the last verified entry only when it moved, and never lets that record be updated', function () {
    Audit::record('a.satu', 'a', 'b');
    app(AuditVerifier::class)->verify();
    app(AuditVerifier::class)->verify();

    expect(DB::table('audit_verified_heads')->count())->toBe(1);

    Audit::record('a.dua', 'a', 'b');
    app(AuditVerifier::class)->verify();

    expect(DB::table('audit_verified_heads')->count())->toBe(2)->and((int) DB::table('audit_verified_heads')->orderByDesc('id')->value('entry_id'))->toBe((int) AuditLog::query()->max('id'));
});

it('records that the audit log was opened, once in ten minutes for the same person', function () {
    $admin = superAdmin();
    $other = superAdmin2();
    AuditLog::query()->getQuery()->delete();

    $this->actingAs($admin)->get('/audit-logs')->assertOk();
    $this->get('/audit-logs?module=a')->assertOk();
    $this->get('/audit-logs?page=2')->assertOk();
    expect(AuditLog::query()->where('event', 'audit_logs.viewed')->count())->toBe(1);

    $this->actingAs($other)->get('/audit-logs')->assertOk();
    expect(AuditLog::query()->where('event', 'audit_logs.viewed')->count())->toBe(2);

    $this->travel(11)->minutes();
    $this->actingAs($admin)->get('/audit-logs')->assertOk();
    expect(AuditLog::query()->where('event', 'audit_logs.viewed')->where('user_id', $admin->id)->count())->toBe(2);

    $row = AuditLog::query()->where('event', 'audit_logs.viewed')->where('user_id', $admin->id)->orderBy('id')->firstOrFail();
    expect($row->username)->toBe($admin->username)->and($row->decoded('context'))->toHaveKey('filters');
});

function superAdmin2(): User
{
    return User::factory()->create()->assignRole('Super Admin');
}

it('does not record a view for somebody who was refused', function () {
    AuditLog::query()->getQuery()->delete();

    $this->actingAs(userWith(['dashboard.view']))->get('/audit-logs')->assertForbidden();

    expect(AuditLog::query()->where('event', 'audit_logs.viewed')->count())->toBe(0)->and(AuditLog::query()->where('event', 'access.denied')->count())->toBe(1);
});

it('refuses every action of the audit log to people who are not Super Admin, and to guests', function (string $method, string $uri) {
    $this->{$method}($uri)->assertRedirect(route('login'));

    $this->actingAs(userWith(['dashboard.view', 'loan-applications.view']));
    $this->{$method}($uri)->assertForbidden();
})->with([['get', '/audit-logs'], ['get', '/audit-logs/export'], ['post', '/audit-logs/verify']]);

it('exports every column, respects the filters, and neutralises a formula in any text column', function (string $first) {
    superAdmin();
    $this->actingAs(User::query()->first());
    Audit::record('x.uji', 'ekspor', 'uji', label: $first.'1+1', context: ['catatan' => $first.'cmd']);
    Audit::record('x.lain', 'lain', 'uji');

    $csv = $this->get('/audit-logs/export?module=ekspor')->streamedContent();
    $rows = array_map('str_getcsv', array_filter(explode("\n", trim($csv))));
    $header = array_shift($rows);

    expect($header)->toHaveCount(19)->and($rows)->toHaveCount(1)
        ->and(array_combine($header, $rows[0])['subject'])->toBe("'".$first.'1+1')
        ->and($csv)->not->toContain('x.lain');
})->with(['=', '+', '-', '@', "\t"]);

it('keeps commas, quotes, new lines, and non-latin letters in an exported cell as they were written', function () {
    $this->actingAs(superAdmin());
    $label = "Satu, \"dua\"\ntiga ~ 日本語 ünï";
    Audit::record('x.uji', 'ekspor', 'uji', label: $label);

    $csv = $this->get('/audit-logs/export?module=ekspor')->streamedContent();
    $handle = fopen('php://memory', 'r+');
    fwrite($handle, $csv);
    rewind($handle);
    $header = fgetcsv($handle);
    $row = array_combine($header, fgetcsv($handle));

    expect($row['subject'])->toBe($label);
});

it('filters by date inclusive of both ends, and answers a clear error for a date that does not exist', function () {
    $this->actingAs(superAdmin());
    AuditLog::query()->getQuery()->delete();
    $this->travelTo(Carbon::parse('2026-03-10 23:59:59'));
    Audit::record('t.tepi', 'tanggal', 'a');
    $this->travelTo(Carbon::parse('2026-03-11 00:00:00'));
    Audit::record('t.hari', 'tanggal', 'b');
    $this->travelBack();

    $events = fn (array $query): array => collect($this->get('/audit-logs?'.http_build_query([...$query, 'module' => 'tanggal']))->assertOk()->inertiaProps('logs.data'))->pluck('event')->all();

    expect($events(['from' => '2026-03-10', 'to' => '2026-03-10']))->toBe(['t.tepi'])
        ->and($events(['from' => '2026-03-11', 'to' => '2026-03-11']))->toBe(['t.hari'])
        ->and($events(['from' => '2026-03-10', 'to' => '2026-03-11']))->toBe(['t.hari', 't.tepi'])
        ->and($events(['from' => '2026-03-12']))->toBe([])
        ->and($events(['from' => '2026-03-11', 'to' => '2026-03-10']))->toBe([]);

    $this->get('/audit-logs?from=2026-02-31')->assertSessionHasErrors('from');
    $this->get('/audit-logs?to=kemarin')->assertSessionHasErrors('to');
    $this->get('/audit-logs/export?from=2026-13-01')->assertSessionHasErrors('from');
});

it('refuses a filter the list does not know, and finds exactly what a search names', function () {
    $this->actingAs(superAdmin());
    Audit::record('cari.satu', 'cari', 'a', label: '100% Resmi');
    Audit::record('cari.dua', 'cari', 'b', label: 'Biasa saja');

    $this->get('/audit-logs?outcome=hilang')->assertSessionHasErrors('outcome');
    $this->get('/audit-logs?per_page=abc')->assertSessionHasErrors('per_page');
    $this->get('/audit-logs?search='.str_repeat('x', 101))->assertSessionHasErrors('search');

    $found = fn (string $term): array => collect($this->get('/audit-logs?'.http_build_query(['search' => $term, 'module' => 'cari']))->inertiaProps('logs.data'))->pluck('event')->all();

    // SQLite has no default escape character (MySQL does), so only what is the same on both is asserted: a wildcard never matches every row.
    expect($found('Resmi'))->toBe(['cari.satu'])->and($found('%'))->not->toContain('cari.dua')->and($found('zzz-tidak-ada'))->toBe([]);
});

it('says when a chain is broken, in the audit log and on the page', function () {
    $this->actingAs(superAdmin());
    Audit::record('a.satu', 'a', 'b');
    Audit::record('a.dua', 'a', 'b');
    DB::table('audit_logs')->where('event', 'a.satu')->update(['outcome' => 'failure']);

    $this->post('/audit-logs/verify')->assertRedirect()->assertSessionHas('audit_verification', fn (array $result): bool => $result['ok'] === false && $result['broken_at'] !== null);

    expect(AuditLog::query()->where('event', 'audit_logs.verified')->firstOrFail()->outcome)->toBe('failure');
});
