<?php

use App\Audit\Auditable;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;

/*
| Project rules that used to live only in documents. When one of these fails, either fix the code or
| (if the rule itself changed) change it here and in CLAUDE.md / .ai/rules in the same commit.
*/

arch('no debugging leftovers')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'die', 'exit'])
    ->not->toBeUsed();

arch('configuration is read through config(), never env(), outside config files')
    ->expect('env')->not->toBeUsed();

arch('every business model is audited')
    ->expect('App\Models')
    ->toUseTrait(Auditable::class)
    ->ignoring([AppNotification::class]);

arch('controllers are suffixed and extend the base controller')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller')
    ->toExtend(Controller::class)
    ->ignoring(Controller::class);

arch('enums live in App\Enums and are enums')
    ->expect('App\Enums')->toBeEnums();

arch('the audit trail is only written through the Audit service')
    ->expect('App\Audit\AuditLog')
    ->toOnlyBeUsedIn(['App\Audit', 'App\Http\Controllers\AuditLogController']);

arch('models do not reach into HTTP')
    ->expect('App\Models')
    ->not->toUse(['Illuminate\Http\Request', 'Illuminate\Support\Facades\Request']);

/*
| Writes that raise no model events (pivot changes, bulk updates, raw table updates, file deletes) leave no trace in the
| audit trail by themselves. Any class that does one must also call Audit::record(), or be listed below with the reason.
*/
it('records writes that bypass model events', function () {
    $bypass = '/->(syncWithoutDetaching|attach|detach|sync)\(|DB::table\([^)]*\)->(insert|update|delete|upsert)|::query\(\)->[^;]*->(update|delete)\(|Quietly\(/';
    $exempt = [
        'app/Http/Controllers/NotificationController.php' => 'read markers of the signed-in person, not business data',
        'app/Audit/Audit.php' => 'the audit writer itself',
        'app/Audit/Auditable.php' => 'the audit hook itself',
        'app/Console/Commands/AuditPrune.php' => 'retention pruning records itself',
    ];
    $offenders = [];

    foreach (['app/Http', 'app/Auth', 'app/Support', 'app/Security', 'app/Services', 'app/Audit', 'app/Console'] as $dir) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($dir))) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace(base_path().'/', '', $file->getPathname());
            $source = (string) file_get_contents($file->getPathname());

            if (! array_key_exists($relative, $exempt) && preg_match($bypass, $source) && ! str_contains($source, 'Audit::record')) {
                $offenders[] = $relative;
            }
        }
    }

    expect($offenders)->toBe([]);
});

it('renders every table through the shared DataTable component', function () {
    $offenders = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('js'))) as $file) {
        if ($file->getExtension() !== 'tsx' || str_ends_with($file->getPathname(), 'components/ui/data-table.tsx')) {
            continue;
        }

        if (preg_match('/<table[\s>]/', (string) file_get_contents($file->getPathname()))) {
            $offenders[] = str_replace(base_path().'/', '', $file->getPathname());
        }
    }

    expect($offenders)->toBe([]);
});

it('keeps the test suite away from real external storage', function () {
    $phpunit = (string) file_get_contents(base_path('phpunit.xml'));

    expect($phpunit)->toContain('<env name="ATTACHMENTS_DISK" value="public"/>')
        ->and($phpunit)->toContain('<env name="AWS_BUCKET" value=""/>')
        ->and(config('filesystems.attachments'))->toBe('public');
});

it('centres the actions of title-and-action rows instead of pinning them to the first line', function () {
    $offenders = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('js'))) as $file) {
        // Combobox options are the exception: their check mark belongs next to the first line of a multi-line option.
        if ($file->getExtension() !== 'tsx' || str_ends_with($file->getPathname(), 'components/ui/combobox.tsx')) {
            continue;
        }

        if (preg_match('/items-start[^"\'`]*justify-between|justify-between[^"\'`]*items-start/', (string) file_get_contents($file->getPathname()))) {
            $offenders[] = str_replace(base_path().'/', '', $file->getPathname());
        }
    }

    expect($offenders)->toBe([]);
});

it('reads the device position only through the shared geolocation helper, never in the photo upload flow', function () {
    $offenders = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('js'))) as $file) {
        $path = $file->getPathname();

        if (! in_array($file->getExtension(), ['ts', 'tsx'], true) || str_ends_with($path, 'lib/geolocation.ts')) {
            continue;
        }

        if (str_contains((string) file_get_contents($path), 'navigator.geolocation')) {
            $offenders[] = str_replace(base_path().'/', '', $path);
        }
    }

    expect($offenders)->toBe([]);
});
