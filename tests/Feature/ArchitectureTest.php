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
/**
 * The source of a PHP file without its comments, so a word in a comment never counts as code.
 */
function phpCodeOnly(string $source): string
{
    $code = '';

    foreach (token_get_all($source) as $token) {
        $code .= is_array($token) ? (in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true) ? ' ' : $token[1]) : $token;
    }

    return $code;
}

/**
 * @return list<string> files of `app/` (relative to the project root)
 */
function appPhpFiles(): array
{
    $files = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('app'), FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->getExtension() === 'php') {
            $files[] = str_replace(base_path().'/', '', $file->getPathname());
        }
    }

    sort($files);

    return $files;
}

it('records writes that bypass model events', function () {
    $bypass = '/->(syncWithoutDetaching|attach|detach|sync|toggle|updateExistingPivot)\(|DB::(table\([^)]*\)->(insert|insertOrIgnore|update|delete|upsert|truncate|increment|decrement)|insert|update|delete|statement|unprepared)\(|->(where|whereIn|whereKey|whereNull|whereNotNull)\([^;{}]*\)->(update|delete|forceDelete|increment|decrement)\(|::query\(\)->[^;{}]*->(update|delete)\(|->(upsert|insertOrIgnore|truncate)\(|Quietly\(|Storage::[^;]*(delete|deleteDirectory)\(|->delete\([\'"][^)]/';
    $exempt = [
        'app/Http/Controllers/NotificationController.php' => 'read markers of the signed-in person, not business data',
        'app/Audit/AuditVerifier.php' => 'the bookmark of the audit trail itself (the last entry found intact); the caller records the result of the verification',
    ];
    $offenders = [];
    $needless = [];

    foreach (appPhpFiles() as $relative) {
        $code = phpCodeOnly((string) file_get_contents(base_path($relative)));
        $bypasses = preg_match($bypass, $code) === 1 && ! str_contains($code, 'Audit::record');

        if ($bypasses && ! array_key_exists($relative, $exempt)) {
            $offenders[] = $relative;
        }

        if (! $bypasses && array_key_exists($relative, $exempt)) {
            $needless[] = $relative;
        }
    }

    expect($offenders)->toBe([])
        ->and(array_diff(array_keys($exempt), appPhpFiles()))->toBe([], 'An exemption for a file that no longer exists: remove it.')
        ->and($needless)->toBe([], 'An exemption for a file that records its writes or bypasses nothing: remove it.');
});

it('leaves no debugging call in app, however the function is called', function () {
    $debug = ['dd', 'ddd', 'dump', 'ray', 'var_dump', 'phpinfo', 'error_log', 'die', 'exit'];
    $offenders = [];

    foreach (appPhpFiles() as $relative) {
        $tokens = array_values(array_filter(token_get_all((string) file_get_contents(base_path($relative))), fn ($token): bool => ! is_array($token) || ! in_array($token[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)));

        foreach ($tokens as $i => $token) {
            $isCall = is_array($token) && in_array($token[0], [T_STRING, T_EXIT], true) && in_array(strtolower($token[1]), $debug, true);
            $before = $tokens[$i - 1] ?? null;
            $isMember = is_array($before) && in_array($before[0], [T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR, T_DOUBLE_COLON, T_FUNCTION, T_NEW], true);

            if ($isCall && ! $isMember && (($tokens[$i + 1] ?? null) === '(' || $token[0] === T_EXIT)) {
                $offenders[] = "{$relative}: {$token[1]}";
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

it('names the device position only through the shared geolocation helper, never in the photo upload flow', function () {
    $offenders = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('js'))) as $file) {
        $path = $file->getPathname();

        if (! in_array($file->getExtension(), ['ts', 'tsx', 'js', 'jsx'], true) || str_ends_with($path, 'lib/geolocation.ts')) {
            continue;
        }

        // The word itself, so `navigator['geolocation']` and `const { geolocation } = navigator` are caught too; the helper is only imported.
        if (preg_match('/\bgeolocation\b/i', str_replace('lib/geolocation', '', (string) file_get_contents($path))) === 1) {
            $offenders[] = str_replace(base_path().'/', '', $path);
        }
    }

    expect($offenders)->toBe([]);
});

it('keeps a helper that two test files share in tests/Pest.php, so each file passes when run alone', function () {
    $sources = [];

    foreach (glob(base_path('tests/Feature/*Test.php')) ?: [] as $path) {
        $sources[$path] = (string) file_get_contents($path);
    }

    $offenders = [];

    foreach ($sources as $path => $source) {
        preg_match_all('/^function (\w+)\(/m', $source, $defined);

        foreach ($defined[1] as $name) {
            foreach ($sources as $other => $otherSource) {
                if ($other !== $path && preg_match('/(?<![\w>:$])'.$name.'\(/', $otherSource) === 1) {
                    $offenders[] = basename($other).' calls '.$name.'() defined in '.basename($path);
                }
            }
        }
    }

    expect($offenders)->toBe([]);
});
