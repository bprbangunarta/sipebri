<?php

/*
 * The "Mutu desain" rules of `.ai/rules/php.md` that a machine can guard: file and method size, nesting depth, meaningless names, layers
 * that must not depend on each other, and direct table reads in controllers. Code that already broke a limit before 2026-10-19 is listed
 * below with a reason: honest debt, paid when the code is touched. The list cannot rot: an entry that no longer matches fails the test.
 */
const DESIGN_MAX_METHOD_LINES = 80;
const DESIGN_MAX_NESTING = 4;
const DESIGN_MAX_PHP_FILE_LINES = 400;
const DESIGN_MAX_TS_FILE_LINES = 500;

/**
 * Existing exceptions: key -> reason. A key is a file, `file::method`, or `file::DB::table`.
 *
 * @return array<string, string>
 */
function designExceptions(): array
{
    return [
        'app/Http/Controllers/LoanApplicationController.php' => 'application form, collateral links, product limits, and checklist in one controller; move the checklist and limit messages to App\\Support when touched',
        'app/Http/Controllers/ApprovalController.php::show' => 'approval screen assembles steps, figures, repayment capacity, and permissions inline; extract a presenter when touched',
        'app/Http/Controllers/DashboardController.php::DB::table' => 'grouped counts per reference table for the dashboard charts; move to a dashboard query class when touched',
        'resources/js/pages/loan-applications/show.tsx' => 'application detail with collateral, checklist, and conflict panels; split the panels out when touched',
        'resources/js/pages/surveys/show.tsx' => 'survey screen with locations, photos, and result form in one page; split per panel when touched',
        'resources/js/pages/ui-guideline.tsx' => 'sample page showing every shared component, intentionally one long file',
    ];
}

/**
 * @param  list<string>  $extensions
 * @return list<string> files relative to the project root (tests and generated Wayfinder output excluded)
 */
function designFiles(string $folder, array $extensions): array
{
    $files = [];

    if (! is_dir(base_path($folder))) {
        return $files;
    }

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($folder), FilesystemIterator::SKIP_DOTS)) as $file) {
        $relative = substr($file->getPathname(), strlen(base_path()) + 1);

        if (in_array($file->getExtension(), $extensions, true)
            && ! str_contains($relative, '/node_modules/')
            && ! str_contains($file->getFilename(), '.test.')
            && preg_match('#^resources/js/(actions|routes|wayfinder)/#', $relative) !== 1) {
            $files[] = $relative;
        }
    }

    sort($files);

    return $files;
}

/**
 * Length and nesting depth of every named method (not closures) in a PHP file.
 *
 * @return list<array{0: string, 1: int, 2: int}> name, line count, depth of control blocks inside the method body
 */
function designMethods(string $source): array
{
    $tokens = token_get_all($source);
    $count = count($tokens);
    $methods = [];

    for ($i = 0; $i < $count; $i++) {
        if (! is_array($tokens[$i]) || $tokens[$i][0] !== T_FUNCTION) {
            continue;
        }

        $name = null;

        for ($j = $i + 1; $j < $count; $j++) {
            if (is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                continue;
            }

            $name = is_array($tokens[$j]) && $tokens[$j][0] === T_STRING ? $tokens[$j][1] : null;

            break;
        }

        if ($name === null) {
            continue;
        }

        $open = $j;

        while ($open < $count && $tokens[$open] !== '{' && $tokens[$open] !== ';') {
            $open++;
        }

        if ($open >= $count || $tokens[$open] === ';') {
            continue;
        }

        $depth = 0;
        $deepest = 0;
        $text = '';

        for ($k = $i; $k < $count; $k++) {
            $token = $tokens[$k];
            $text .= is_array($token) ? $token[1] : $token;

            if ($k < $open) {
                continue;
            }

            if ($token === '{' || (is_array($token) && in_array($token[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES], true))) {
                $depth++;
                $deepest = max($deepest, $depth);
            } elseif ($token === '}') {
                $depth--;

                if ($depth === 0) {
                    break;
                }
            }
        }

        $methods[] = [$name, substr_count($text, "\n") + 1, $deepest - 1];
    }

    return $methods;
}

function designLineCount(string $file): int
{
    return substr_count((string) file_get_contents(base_path($file)), "\n") + 1;
}

/**
 * Fails on a file-level violation without a listed reason, and on a file-level entry under `$prefix` that no longer matches.
 *
 * @param  list<string>  $violations
 */
function expectOnlyListedDesignDebt(array $violations, string $prefix): void
{
    $exceptions = designExceptions();
    $scoped = array_filter(array_keys($exceptions), fn (string $key): bool => ! str_contains($key, '::') && str_starts_with($key, $prefix));

    expect(array_values(array_diff($violations, array_keys($exceptions))))->toBe([], 'Design limit broken without a listed reason (see `.ai/rules/php.md`, "Mutu desain").');
    expect(array_values(array_diff($scoped, $violations)))->toBe([], 'Exception no longer matches: remove it from designExceptions().');
}

it('requires a clear reason for every design exception', function () {
    foreach (designExceptions() as $key => $reason) {
        expect(preg_match_all('/\p{L}/u', $reason))->toBeGreaterThanOrEqual(10, "Exception {$key} has no reason.");
    }
});

it('keeps PHP files within 400 lines', function () {
    $violations = array_values(array_filter(designFiles('app', ['php']), fn (string $file): bool => designLineCount($file) > DESIGN_MAX_PHP_FILE_LINES));

    expectOnlyListedDesignDebt($violations, 'app/');
});

it('keeps every method within 80 lines and four levels of nested blocks', function () {
    $violations = [];

    foreach (designFiles('app', ['php']) as $file) {
        foreach (designMethods((string) file_get_contents(base_path($file))) as [$name, $lines, $depth]) {
            if ($lines > DESIGN_MAX_METHOD_LINES || $depth > DESIGN_MAX_NESTING) {
                $violations[] = "{$file}::{$name}";
            }
        }
    }

    $exceptions = array_filter(array_keys(designExceptions()), fn (string $key): bool => str_contains($key, '.php::') && ! str_ends_with($key, '::DB::table'));

    expect(array_values(array_diff($violations, array_keys(designExceptions()))))->toBe([], 'Method too long or too deeply nested without a listed reason.');
    expect(array_values(array_diff($exceptions, $violations)))->toBe([], 'Method exception no longer matches: remove it from designExceptions().');
});

it('keeps TypeScript files within 500 lines', function () {
    $violations = array_values(array_filter(designFiles('resources/js', ['ts', 'tsx']), fn (string $file): bool => designLineCount($file) > DESIGN_MAX_TS_FILE_LINES));

    expectOnlyListedDesignDebt($violations, 'resources/js/');
});

it('uses no meaningless class names: Helper, Util, Manager, Misc, Common', function () {
    $generic = array_values(array_filter(designFiles('app', ['php']), fn (string $file): bool => preg_match('/(Helpers?|Utils?|Utilities|Managers?|Misc|Common)\.php$/', $file) === 1));

    expect($generic)->toBe([]);
});

it('does not read tables directly with DB::table in controllers', function () {
    $violations = [];

    foreach (designFiles('app/Http/Controllers', ['php']) as $file) {
        if (str_contains((string) file_get_contents(base_path($file)), 'DB::table(')) {
            $violations[] = "{$file}::DB::table";
        }
    }

    $exceptions = array_filter(array_keys(designExceptions()), fn (string $key): bool => str_ends_with($key, '::DB::table'));

    expect(array_values(array_diff($violations, array_keys(designExceptions()))))->toBe([], 'A controller reads a table directly; move it to a model, a query class, or a Support class.');
    expect(array_values(array_diff($exceptions, $violations)))->toBe([], 'DB::table exception no longer matches: remove it from designExceptions().');
});

// One rule per layer and per dependency: arch() given a list at once reads it in a way that lets violations through.
foreach (['App\Models', 'App\Support', 'App\Enums', 'App\Services', 'App\Audit', 'App\Security'] as $layer) {
    foreach (['App\Http\Controllers', 'App\Http\Middleware'] as $http) {
        arch("{$layer} does not depend on {$http}")->expect($layer)->not->toUse($http);
    }
}

foreach (['dd', 'dump', 'var_dump', 'ray', 'die', 'exit'] as $debug) {
    arch("no {$debug} call is left in app")->expect('App')->not->toUse($debug);
}
