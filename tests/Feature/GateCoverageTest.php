<?php

use Illuminate\Support\Facades\Process;

/*
 * The gates (scripts/verify.sh, check-weakened.sh, check-brief.sh) refuse a weaker check only when the change is in the diff they read,
 * and some loosenings (a lower PHPStan level, a narrower CODE_PATHS, a step that always succeeds) are plain edits to a setting. These tests
 * read the real project files and fail when such a setting is loosened, so the loosening shows up in the suite as well as in a review.
 * They read files and `git ls-files` only; they never run the gate.
 */

/** Top-level folders whose files are documents or data, not code: a commit that only touches them needs no brief. */
const GATE_NOT_CODE_DIRS = [
    '.ai' => 'rule and skill documents, kept honest by scripts/check-registry.sh and the skill manifests',
    'docs' => 'briefs and guides, read by people',
    'memory' => 'the PRD, read by people and agents',
    'storage' => 'only .gitignore placeholders of the runtime folders',
];

/** Top-level files that are documents or editor hints, not code or a check setting. */
const GATE_NOT_CODE_FILES = [
    'AGENTS.md' => 'agent guide, guarded by DocsMatchCodeTest and check-registry.sh',
    'CLAUDE.md' => 'project notes for agents, guarded by DocsMatchCodeTest',
    '.gitignore' => 'ignore list, not a check',
    '.editorconfig' => 'editor hints, the formatter is the check',
];

/**
 * Top-level files that hold code or a check setting but are not in CODE_PATHS yet, so a commit that only changes them needs no brief
 * (audit 25, finding A-11, waiting for the owner). An entry that CODE_PATHS now covers must be deleted from here.
 */
const GATE_KNOWN_UNCOVERED = [];

/** Test files that are placeholders from the framework skeleton and assert nothing (audit 25, finding A-12, to be deleted). */
const GATE_KNOWN_VACUOUS_TESTS = [];

/** @return list<string> tracked paths from the repository root */
function gateTrackedFiles(): array
{
    $result = Process::path(base_path())->run(['git', 'ls-files', '-z']);

    expect($result->successful())->toBeTrue($result->errorOutput());

    return array_values(array_filter(explode("\0", $result->output()), fn (string $path): bool => $path !== ''));
}

function gateRead(string $path): string
{
    $content = file_get_contents(base_path($path));

    expect($content)->not->toBeFalse("{$path} cannot be read.");

    return (string) $content;
}

/** The value of a single-quoted or bare setting of devcore.conf. */
function gateConf(string $key): string
{
    preg_match('/^'.preg_quote($key, '/')."=(?:'(.*)'|(\\S*))\\s*$/m", gateRead('devcore.conf'), $match);

    expect($match)->not->toBeEmpty("devcore.conf has no {$key} setting.");

    return ($match[1] ?? '') !== '' ? $match[1] : ($match[2] ?? '');
}

/** devcore.conf holds POSIX extended regular expressions; the ones used here read the same as PCRE. */
function gateMatches(string $setting, string $path): bool
{
    return preg_match('~'.str_replace('~', '\~', gateConf($setting)).'~', $path) === 1;
}

/** @return array<string, string> label => command of every step of scripts/verify.steps, in order */
function gateSteps(): array
{
    $steps = [];

    foreach (preg_split('/\R/', gateRead('scripts/verify.steps')) ?: [] as $line) {
        if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
            continue;
        }

        expect(str_contains($line, '|'))->toBeTrue("verify.steps line without label|command: {$line}");
        [$label, $command] = explode('|', $line, 2);
        $steps[trim($label)] = trim($command);
    }

    return $steps;
}

/** @return array<string, mixed> */
function gateDecode(string $path): array
{
    $decoded = json_decode(gateRead($path), true);

    expect($decoded)->toBeArray("{$path} is not valid JSON.");

    return $decoded;
}

it('keeps every check in scripts/verify.steps, and none of them can pass by itself', function () {
    $commands = gateSteps();
    $joined = implode("\n", $commands);

    $required = [
        'registry check' => 'scripts/check-registry.sh',
        'gate self-test' => 'scripts/selftest.sh',
        'composer audit' => 'composer audit',
        'npm audit' => 'npm audit',
        'env check' => 'php artisan env:check',
        'Pint that fails on a difference' => '/vendor\/bin\/pint\b[^\n]*--test/',
        'PHPStan' => '/vendor\/bin\/phpstan analyse/',
        'build' => 'npm run build',
        'tests' => 'php artisan test',
        'front-end standards' => 'scripts/check-standards.mjs',
        'front-end checks' => 'npm run check',
        'copied code' => 'jscpd',
        'types' => 'npm run types:check',
    ];

    $missing = [];

    foreach ($required as $name => $needle) {
        $found = str_starts_with($needle, '/') ? preg_match($needle, $joined) === 1 : str_contains($joined, $needle);

        if (! $found) {
            $missing[] = $name;
        }
    }

    expect($missing)->toBe([], 'verify.steps lost: '.implode(', ', $missing));

    $defused = [];

    foreach ($commands as $label => $command) {
        if (preg_match('/(\|\||;|&&)\s*(true|:|exit\s+0)(?![\w:-])|^\s*(true|:|exit\s+0|echo)(?![\w:-])/', $command) === 1) {
            $defused[] = "{$label}: {$command}";
        }
    }

    expect($defused)->toBe([], 'a step that cannot fail: '.implode('; ', $defused));
});

it('builds before it runs the tests, because the tests render pages through the Vite manifest', function () {
    $labels = array_keys(gateSteps());
    $commands = array_values(gateSteps());

    $build = array_search(true, array_map(fn (string $c): bool => str_contains($c, 'npm run build'), $commands), true);
    $tests = array_search(true, array_map(fn (string $c): bool => str_contains($c, 'php artisan test'), $commands), true);

    expect($build)->not->toBeFalse()->and($tests)->not->toBeFalse()->and($build)->toBeLessThan($tests, 'steps: '.implode(' > ', $labels));
});

it('keeps CODE_PATHS covering every tracked code file, so a commit that touches code needs a brief', function () {
    $uncovered = [];

    foreach (gateTrackedFiles() as $path) {
        $top = explode('/', $path)[0];

        if (array_key_exists($top, GATE_NOT_CODE_DIRS) || array_key_exists($path, GATE_NOT_CODE_FILES)) {
            continue;
        }

        if (! gateMatches('CODE_PATHS', $path)) {
            $uncovered[$top] = true;
        }
    }

    $unexpected = array_values(array_diff(array_keys($uncovered), GATE_KNOWN_UNCOVERED));

    expect($unexpected)->toBe([], 'not covered by CODE_PATHS in devcore.conf: '.implode(', ', $unexpected));

    $nowCovered = array_values(array_diff(GATE_KNOWN_UNCOVERED, array_keys($uncovered)));

    expect($nowCovered)->toBe([], 'CODE_PATHS covers these now: delete them from GATE_KNOWN_UNCOVERED: '.implode(', ', $nowCovered));
});

it('has a reason for every top-level folder and file that CODE_PATHS does not cover on purpose', function () {
    $tracked = array_map(fn (string $path): string => explode('/', $path)[0], gateTrackedFiles());

    foreach (array_keys(GATE_NOT_CODE_DIRS) as $dir) {
        expect(in_array($dir, $tracked, true))->toBeTrue("GATE_NOT_CODE_DIRS names {$dir}, which is no longer tracked: delete the entry.");
    }

    foreach (array_keys(GATE_NOT_CODE_FILES) as $file) {
        expect(in_array($file, $tracked, true))->toBeTrue("GATE_NOT_CODE_FILES names {$file}, which is no longer tracked: delete the entry.");
        expect(gateMatches('CODE_PATHS', $file))->toBeFalse("{$file} is in CODE_PATHS now: delete it from GATE_NOT_CODE_FILES.");
    }
});

it('reads every tracked source file for weakened checks, and every test file as a test', function () {
    $notScanned = [];
    $notTest = [];

    foreach (gateTrackedFiles() as $path) {
        $isCode = preg_match('/\.(php|tsx?|mjs|js|sh)$/', $path) === 1;
        $isTest = str_starts_with($path, 'tests/') || preg_match('/\.test\.tsx?$/', $path) === 1;

        // public/index.php is the framework entry file; the gate scripts' own logic is read by hand, check-weakened.sh skips itself.
        if ($isCode && ! in_array($path, ['public/index.php'], true) && ! gateMatches('WEAKENED_SCAN', $path)) {
            $notScanned[] = $path;
        }

        if ($isTest && (! gateMatches('TEST_PATHS', $path) || ! gateMatches('WEAKENED_SCAN', $path))) {
            $notTest[] = $path;
        }
    }

    expect($notScanned)->toBe([], 'not read by WEAKENED_SCAN: '.implode(', ', $notScanned))
        ->and($notTest)->toBe([], 'a test file outside TEST_PATHS or WEAKENED_SCAN: '.implode(', ', $notTest));

    // The settings the checks live in are read too, so a suppression in them is refused.
    foreach (['devcore.conf', 'phpstan.neon', 'pint.json', 'tsconfig.json', 'vite.config.ts'] as $setting) {
        expect(gateMatches('WEAKENED_SCAN', $setting))->toBeTrue("{$setting} is not in WEAKENED_SCAN.");
    }
});

it('runs every test file: each one sits in a phpunit test suite folder or matches the Vitest include pattern', function () {
    $phpunit = gateRead('phpunit.xml');
    preg_match_all('~<directory[^>]*>([^<]+)</directory>~', (string) preg_replace('~<source>.*?</source>~s', '', $phpunit), $dirs);
    $suiteDirs = array_map(fn (string $dir): string => trim($dir, '/').'/', $dirs[1]);

    expect($suiteDirs)->toContain('tests/Feature/')->and($suiteDirs)->toContain('tests/Unit/');
    expect($phpunit)->not->toContain('<exclude')->and($phpunit)->not->toContain('stopOnSkipped="true"');

    $stray = [];

    foreach (gateTrackedFiles() as $path) {
        if (str_starts_with($path, 'tests/') && str_ends_with($path, 'Test.php') && ! array_filter($suiteDirs, fn (string $dir): bool => str_starts_with($path, $dir))) {
            $stray[] = "{$path} (outside every suite folder)";
        }

        $isTestSource = (str_starts_with($path, 'tests/') && str_ends_with($path, '.php')) || preg_match('/^resources\/js\/.*\.tsx?$/', $path) === 1;

        if ($isTestSource && ! in_array($path, ['tests/Pest.php', 'tests/TestCase.php'], true) && ! str_ends_with($path, 'Test.php') && preg_match('/\.test\.tsx?$/', $path) !== 1
            && preg_match('/^\s*(it|test|describe)\(/m', gateRead($path)) === 1) {
            $stray[] = "{$path} (defines tests but its name is not matched by a runner)";
        }
    }

    expect($stray)->toBe([], implode('; ', $stray));
    expect(gateRead('vite.config.ts'))->toContain("include: ['resources/js/**/*.test.{ts,tsx}']");
});

it('has no test that is marked as to-do, skipped by environment, focused, or asserts nothing', function () {
    // check-weakened.sh refuses a skip or an only; these are the ways of switching a test off or emptying it that it does not read.
    $markers = [
        '->'.'todo(', 'markTest'.'Incomplete', '->skipOn', '->skip'.'If(', '->skip'.'Unless(', 'it.'.'todo(', 'test.'.'todo(', 'it.skip'.'If(',
        '@ts-'.'nocheck', 'prettier-'.'ignore', 'expect(true)->'.'toBeTrue()', 'expect(true)->'.'toBe(true)', 'assertTrue('.'true)', '->only'.'()',
    ];

    $found = [];

    foreach (gateTrackedFiles() as $path) {
        $isTest = (str_starts_with($path, 'tests/') && str_ends_with($path, '.php')) || preg_match('/\.test\.tsx?$/', $path) === 1;

        if (! $isTest || $path === 'tests/Feature/GateCoverageTest.php' || in_array($path, GATE_KNOWN_VACUOUS_TESTS, true)) {
            continue;
        }

        foreach (preg_split('/\R/', gateRead($path)) ?: [] as $number => $line) {
            foreach ($markers as $marker) {
                if (str_contains($line, $marker) && preg_match('/weakening-allowed: \S.{8,}/', $line) !== 1) {
                    $found[] = $path.':'.($number + 1).'  '.$marker;
                }
            }
        }
    }

    expect($found)->toBe([], implode("\n", $found));

    foreach (GATE_KNOWN_VACUOUS_TESTS as $path) {
        expect(base_path($path))->toBeFile("{$path} is gone: delete it from GATE_KNOWN_VACUOUS_TESTS.");
    }
});

it('keeps the static analysis at the recorded level, with nothing ignored and no baseline', function () {
    $neon = gateRead('phpstan.neon');

    expect(preg_match('/^\s+level:\s*(\d+|max)\s*$/m', $neon, $level))->toBe(1);

    // Level 7 is the level the project recorded in memory/PRD.md; raising it is fine, lowering it needs the owner.
    expect($level[1] === 'max' || (int) $level[1] >= 7)->toBeTrue("PHPStan level is {$level[1]}, the recorded level is 7.");
    expect($neon)->not->toContain('ignoreErrors')->and($neon)->not->toContain('baseline')->and($neon)->not->toContain('reportUnmatchedIgnoredErrors: false')
        ->and($neon)->not->toContain('treatPhpDocTypesAsCertain: false');

    foreach (['app/', 'bootstrap/app.php', 'config/', 'database/', 'routes/'] as $path) {
        expect(preg_match('/^\s+- '.preg_quote($path, '/').'\s*$/m', $neon))->toBe(1, "phpstan.neon no longer analyses {$path}");
    }

    expect(glob(base_path('phpstan-baseline*')))->toBe([])->and(glob(base_path('*baseline.neon')))->toBe([]);
});

it('keeps the copied-code check strict', function () {
    $jscpd = gateDecode('.jscpd.json');

    expect($jscpd['threshold'] ?? null)->toBe(0)
        ->and($jscpd['minLines'] ?? 999)->toBeLessThanOrEqual(20)
        ->and($jscpd['minTokens'] ?? 999)->toBeLessThanOrEqual(150)
        ->and($jscpd['path'] ?? [])->toBe(['app', 'resources/js']);

    $allowedIgnores = ['**/*.test.ts', '**/*.test.tsx', '**/node_modules/**', 'resources/js/actions/**', 'resources/js/routes/**', 'resources/js/wayfinder/**'];

    expect(array_diff($jscpd['ignore'] ?? [], $allowedIgnores))->toBe([], 'jscpd ignores more than the tests and generated folders.');
});

it('keeps the lint, type and format settings strict', function () {
    $vite = gateRead('vite.config.ts');

    expect($vite)->toContain('denyWarnings: true')->and($vite)->toContain('typeAware: true')->and($vite)->not->toContain('denyWarnings: false');

    preg_match("/ignorePatterns: \[(.*?)\]/s", $vite, $ignores);
    $allowedIgnores = ['vendor/**', 'node_modules/**', 'public/**', 'bootstrap/ssr/**', 'tailwind.config.js', 'resources/js/actions/**', 'resources/js/routes/**', 'resources/js/wayfinder/**'];
    preg_match_all("/'([^']+)'/", $ignores[1] ?? '', $listed);

    expect(array_diff($listed[1], $allowedIgnores))->toBe([], 'the linter ignores more than vendor, build and generated folders.');

    // Only these two rules are switched off, with the reason written next to them.
    preg_match("/\brules: \{(.*?)\}/s", $vite, $rules);
    preg_match_all("/'([^']+)':\s*'off'/", $rules[1] ?? '', $off);

    expect($off[1])->toBe(['typescript/no-misused-spread', 'unicorn/no-useless-spread']);

    $tsconfig = gateRead('tsconfig.json');

    expect(preg_match('/^\s*"strict":\s*true\b/m', $tsconfig))->toBe(1)
        ->and($tsconfig)->not->toMatch('/^\s*"(noImplicitAny|strictNullChecks|skipLibCheck)":\s*false/m')
        ->and($tsconfig)->not->toMatch('/^\s*"(checkJs|allowJs)":\s*false/m');

    $pint = gateDecode('pint.json');

    expect(array_keys($pint))->toBe(['preset'])->and($pint['preset'])->toBe('laravel');
});

it('runs the front-end and PHP checks through the package and composer scripts the gate calls', function () {
    $package = gateDecode('package.json')['scripts'] ?? [];

    expect($package['check'] ?? '')->toBe('vp check && npm run test')
        ->and($package['test'] ?? '')->toBe('vp test run')
        ->and($package['types:check'] ?? '')->toBe('tsc --noEmit')
        ->and($package['build'] ?? '')->toBe('vp build');

    $composer = gateDecode('composer.json')['scripts'] ?? [];

    // composer verify, check and ci:check are the gate and nothing else: a second list of checks would drift from verify.steps.
    foreach (['verify' => ['Composer\Config::disableProcessTimeout', 'sh scripts/verify.sh'], 'check' => ['Composer\Config::disableProcessTimeout', '@verify'], 'ci:check' => ['Composer\Config::disableProcessTimeout', '@verify']] as $name => $expected) {
        expect($composer[$name] ?? null)->toBe($expected, "composer {$name} must only call the gate.");
    }

    expect($composer['lint:check'] ?? [])->toBe(['pint --parallel --test'])->and($composer['types:check'] ?? [])->toBe(['phpstan analyse']);

    // composer setup installs the hooks path: without it nothing guards the commits of a fresh clone.
    expect(implode("\n", $composer['setup'] ?? []))->toContain('git config core.hooksPath .githooks');
    expect(gateRead('.npmrc'))->toContain('ignore-scripts=true');
});

it('lets CI run the gate and nothing else, with the whole history', function () {
    $ci = gateRead('.github/workflows/ci.yml');

    $code = implode("\n", array_filter(preg_split('/\R/', $ci) ?: [], fn (string $line): bool => ! str_starts_with(ltrim($line), '#')));

    expect($code)->toContain('scripts/verify.sh');
    expect(preg_match('~vendor/bin/(pint|phpstan)|artisan test|npm (run )?(check|test|run types:check)|\bvp (check|test)|jscpd|\btsc\b~', $code))->toBe(0, 'CI lists a check of its own: it belongs in scripts/verify.steps.');
    expect($ci)->toContain('fetch-depth: 0')->and($ci)->toContain('VERIFY_RANGE:')->and($ci)->not->toContain('continue-on-error')->and($ci)->not->toMatch('/^\s+if:\s*false/m');
});

it('keeps the hooks and gate scripts executable, wired to each other, and the line endings as LF', function () {
    $modes = [];

    foreach (explode("\n", trim(Process::path(base_path())->run(['git', 'ls-files', '-s', '.githooks', 'scripts'])->output())) as $line) {
        [$meta, $path] = explode("\t", $line, 2);
        $modes[$path] = substr($meta, 0, 6);
    }

    foreach (['.githooks/commit-msg', '.githooks/pre-push', 'scripts/verify.sh', 'scripts/check-brief.sh', 'scripts/check-weakened.sh', 'scripts/check-registry.sh', 'scripts/selftest.sh'] as $path) {
        expect($modes[$path] ?? null)->toBe('100755', "{$path} must be tracked as executable: git ignores a hook that is not.");
    }

    expect(gateRead('.githooks/commit-msg'))->toContain('scripts/check-brief.sh')->and(gateRead('.githooks/commit-msg'))->toMatch('~scripts/check-weakened\.sh"? --staged~');
    expect(gateRead('.githooks/pre-push'))->toContain('VERIFY_RANGE')->and(gateRead('.githooks/pre-push'))->toContain('scripts/verify.sh');

    $verify = gateRead('scripts/verify.sh');

    expect($verify)->toContain('scripts/check-weakened.sh --range')->and($verify)->toContain('scripts/check-brief.sh --range')->and($verify)->toContain('scripts/verify.steps')
        ->and($verify)->not->toMatch('/^\s*exit 0\s*$/m');

    expect(gateRead('.gitattributes'))->toContain('eol=lf');
});

it('sets the branch that CI compares a new branch with to a branch that exists', function () {
    $main = gateConf('MAIN_BRANCH');
    $branches = Process::path(base_path())->run(['git', 'branch', '--list', '--all', '--format=%(refname:short)'])->output();

    $known = preg_split('/\R/', $branches) ?: [];

    expect(in_array($main, $known, true) || in_array("origin/{$main}", $known, true))->toBeTrue("MAIN_BRANCH={$main} is not a branch of this repository.");
});

it('lets only the pre-push hook reuse a green run, for whole hours, and never CI or a manual run', function () {
    expect(gateConf('VERIFY_STAMP_HOURS'))->toMatch('/^[1-9][0-9]*$/')
        ->and(gateRead('.githooks/pre-push'))->toContain('VERIFY_REUSE_SHAS=')
        ->and(gateRead('.github/workflows/ci.yml'))->not->toContain('VERIFY_REUSE_SHAS')
        ->and(gateRead('scripts/verify.sh'))->toContain('VERIFY_REUSE_SHAS')->and(gateRead('composer.json'))->not->toContain('VERIFY_REUSE_SHAS');
});
