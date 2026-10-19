<?php

use Illuminate\Support\Facades\Process;

/*
 * The weakened-check detector (`scripts/check-weakened.sh`) runs for real in a temporary git repository, for each rejected pattern, both
 * legitimate ways through, and the paths that must not be caught.
 */

const WEAKENED_BASE_TEST = "<?php\n\nit('one', function () {\n    expect(1)->toBe(1);\n});\n\nit('two', function () {\n    expect(2)->toBe(2);\n});\n";

/**
 * Temporary repository with one initial commit holding a test file with two cases and one application file.
 */
function weakenedRepo(): string
{
    $dir = sys_get_temp_dir().'/weak-'.bin2hex(random_bytes(4));
    mkdir($dir.'/scripts', 0777, true);
    copy(base_path('scripts/check-weakened.sh'), $dir.'/scripts/check-weakened.sh');
    chmod($dir.'/scripts/check-weakened.sh', 0755);

    foreach ([['git', 'init', '-q'], ['git', 'config', 'user.email', 't@example.test'], ['git', 'config', 'user.name', 'Test']] as $command) {
        Process::path($dir)->run($command)->throw();
    }

    weakenedWrite($dir, 'tests/Feature/ExampleTest.php', WEAKENED_BASE_TEST);
    weakenedWrite($dir, 'app/Example.php', "<?php\n\nreturn 1;\n");
    Process::path($dir)->run('git add -A && git commit -q -m start')->throw();

    return $dir;
}

function weakenedWrite(string $dir, string $path, string $content): void
{
    @mkdir(dirname($dir.'/'.$path), 0777, true);
    file_put_contents($dir.'/'.$path, $content);
}

/**
 * Runs the detector on the staged change.
 *
 * @return array{0: bool, 1: string}
 */
function weakenedStaged(string $dir, string $message = 'Change something'): array
{
    file_put_contents($dir.'/MSG', $message);
    Process::path($dir)->run('git add -A')->throw();
    $result = Process::path($dir)->run(['bash', 'scripts/check-weakened.sh', '--staged', 'MSG']);

    return [$result->successful(), $result->errorOutput()];
}

it('lets a change through that weakens nothing', function () {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'app/Example.php', "<?php\n\nreturn 2;\n");
    weakenedWrite($dir, 'tests/Feature/ExampleTest.php', WEAKENED_BASE_TEST."\nit('three', function () {\n    expect(3)->toBe(3);\n});\n");

    expect(weakenedStaged($dir)[0])->toBeTrue();
});

it('rejects skipped or focused tests', function (string $line) {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'tests/Feature/ExampleTest.php', WEAKENED_BASE_TEST."\n".$line."\n");

    [$ok, $error] = weakenedStaged($dir);

    expect($ok)->toBeFalse()->and($error)->toContain('tests/Feature/ExampleTest.php')->and($error)->toContain('tes dilewati atau difokuskan');
})->with([
    "it('x', fn () => 1)->skip();",
    '$this->markTestSkipped();',
    "it.skip('x', () => {});",
    "test.only('x', () => {});",
    "xit('x', () => {});",
]);

it('rejects checker suppressions in application code', function (string $line) {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'app/Example.php', "<?php\n\n".$line."\nreturn 1;\n");

    [$ok, $error] = weakenedStaged($dir);

    expect($ok)->toBeFalse()->and($error)->toContain('app/Example.php:3')->and($error)->toContain('penekanan pemeriksa');
})->with([
    '// @phpstan-ignore-next-line',
    '// @ts-ignore',
    '// eslint-disable-next-line',
    '// oxlint-disable-next-line',
    '/** @codeCoverageIgnore */',
]);

it('does not mistake a query skip for a skipped test', function () {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'app/Example.php', "<?php\n\nreturn User::query()->skip(5)->take(5);\n");

    expect(weakenedStaged($dir)[0])->toBeTrue();
});

it('passes a per-line marker with a reason and rejects a short reason', function () {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'app/Example.php', "<?php\n\n// @ts-ignore weakening-allowed: third-party types are still wrong\nreturn 1;\n");

    [$ok, $error] = weakenedStaged($dir);
    expect($ok)->toBeTrue()->and($error)->toContain('third-party types are still wrong');

    weakenedWrite($dir, 'app/Example.php', "<?php\n\n// @ts-ignore weakening-allowed: later\nreturn 1;\n");
    expect(weakenedStaged($dir)[0])->toBeFalse();
});

it('rejects a deleted test file and a test file that loses cases', function () {
    $dir = weakenedRepo();
    unlink($dir.'/tests/Feature/ExampleTest.php');
    [$ok, $error] = weakenedStaged($dir);
    expect($ok)->toBeFalse()->and($error)->toContain('berkas tes dihapus');

    $dir = weakenedRepo();
    weakenedWrite($dir, 'tests/Feature/ExampleTest.php', "<?php\n\nit('one', function () {\n    expect(1)->toBe(1);\n});\n");
    [$ok, $error] = weakenedStaged($dir);
    expect($ok)->toBeFalse()->and($error)->toContain('kehilangan kasus tes: 1 dihapus, 0 ditambah');
});

it('lets test cases be moved or renamed as long as the count does not drop', function () {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'tests/Feature/ExampleTest.php', "<?php\n\nit('one renamed', function () {\n    expect(1)->toBe(1);\n});\n\nit('two renamed', function () {\n    expect(2)->toBe(2);\n});\n");

    expect(weakenedStaged($dir)[0])->toBeTrue();
});

it('rejects a new PHPStan baseline', function () {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'phpstan-baseline.neon', "parameters:\n    ignoreErrors: []\n");

    [$ok, $error] = weakenedStaged($dir);

    expect($ok)->toBeFalse()->and($error)->toContain('baseline PHPStan');
});

it('passes a commit marked Weakened with a reason and rejects a short reason', function () {
    $dir = weakenedRepo();
    unlink($dir.'/tests/Feature/ExampleTest.php');

    [$ok, $error] = weakenedStaged($dir, "Drop the example feature\n\nWeakened: example feature removed together with its tests");
    expect($ok)->toBeTrue()->and($error)->toContain('example feature removed together with its tests');

    expect(weakenedStaged($dir, "Drop the example feature\n\nWeakened: drop")[0])->toBeFalse();
});

it('does not scan docs, skills, or vendor', function () {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'docs/notes.md', "// @phpstan-ignore\n");
    weakenedWrite($dir, '.ai/skills/x/SKILL.md', "// @ts-ignore\n");
    weakenedWrite($dir, 'vendor/x/y.php', "<?php // @phpstan-ignore\n");

    expect(weakenedStaged($dir)[0])->toBeTrue();
});

it('does not let a message that pretends to be a merge or revert skip the check', function (string $message) {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'app/Example.php', "<?php\n\n// @phpstan-ignore-next-line\nreturn 1;\n");

    [$ok, $error] = weakenedStaged($dir, $message);

    expect($ok)->toBeFalse()->and($error)->toContain('app/Example.php:3');
})->with(["Merge branch 'x'", 'Revert "New feature"']);

it('lets a real merge through, both as a staged commit and in a push range', function () {
    $dir = weakenedRepo();
    Process::path($dir)->run(['git', 'branch', '-m', 'trunk'])->throw();
    Process::path($dir)->run(['git', 'checkout', '-q', '-b', 'side'])->throw();
    weakenedWrite($dir, 'app/Other.php', "<?php\n\n// @phpstan-ignore-next-line\nreturn 1;\n");
    Process::path($dir)->run(['git', 'add', '-A'])->throw();
    Process::path($dir)->run(['git', 'commit', '-q', '--no-verify', '-m', "Weakens with a reason\n\nWeakened: third-party analysis has wrong types"])->throw();
    Process::path($dir)->run(['git', 'checkout', '-q', 'trunk'])->throw();
    weakenedWrite($dir, 'app/Example.php', "<?php\n\nreturn 5;\n");
    Process::path($dir)->run('git add -A && git commit -q -m "change trunk"')->throw();
    Process::path($dir)->run(['git', 'merge', '-q', '--no-ff', '--no-verify', '-m', 'merge', 'side'])->throw();

    $range = Process::path($dir)->run(['bash', 'scripts/check-weakened.sh', '--range', 'HEAD~2..HEAD']);

    expect($range->successful())->toBeTrue($range->errorOutput());

    file_put_contents($dir.'/.git/MERGE_HEAD', trim(Process::path($dir)->run('git rev-parse HEAD')->output())."\n");
    weakenedWrite($dir, 'app/Other.php', "<?php\n\n// @phpstan-ignore-next-line\nreturn 2;\n");

    expect(weakenedStaged($dir, 'Merge branch side')[0])->toBeTrue();
});

it('accepts a range given as several arguments and checks every commit not on a remote for a branch without upstream', function () {
    $dir = weakenedRepo();
    $remote = sys_get_temp_dir().'/weak-remote-'.bin2hex(random_bytes(4));
    Process::run(['git', 'init', '-q', '--bare', $remote])->throw();
    Process::path($dir)->run(['git', 'remote', 'add', 'origin', $remote])->throw();
    Process::path($dir)->run(['git', 'push', '-q', 'origin', 'HEAD:refs/heads/base'])->throw();
    Process::path($dir)->run(['git', 'fetch', '-q', 'origin'])->throw();

    weakenedWrite($dir, 'app/Example.php', "<?php\n\n// @phpstan-ignore-next-line\nreturn 1;\n");
    Process::path($dir)->run('git add -A && git commit -q --no-verify -m "Weakens quietly"')->throw();
    weakenedWrite($dir, 'app/Other.php', "<?php\n\nreturn 3;\n");
    Process::path($dir)->run('git add -A && git commit -q --no-verify -m "Clean afterwards"')->throw();
    weakenedWrite($dir, 'app/More.php', "<?php\n\nreturn 4;\n");
    Process::path($dir)->run('git add -A && git commit -q --no-verify -m "Clean again"')->throw();

    $result = Process::path($dir)->run(['bash', 'scripts/check-weakened.sh', '--range', 'HEAD', '--not', '--remotes']);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain('app/Example.php:3');
});

it('checks files whose names hold spaces, quotes, non-ASCII letters, or a leading dash', function (string $name) {
    $dir = weakenedRepo();
    weakenedWrite($dir, "tests/Feature/{$name}", WEAKENED_BASE_TEST."\nit('x', fn () => 1)->skip();\n");

    [$ok, $error] = weakenedStaged($dir);

    expect($ok)->toBeFalse()->and($error)->toContain('tes dilewati atau difokuskan');
})->with(['has space Test.php', "quote'double\"Test.php", 'é-ñTest.php', '-leading-dash.test.ts', 'back\\slashTest.php']);

it('checks every commit in a push range against its own message', function () {
    $dir = weakenedRepo();
    weakenedWrite($dir, 'app/Example.php', "<?php\n\n// @phpstan-ignore-next-line\nreturn 1;\n");
    Process::path($dir)->run(['git', 'add', '-A'])->throw();
    Process::path($dir)->run(['git', 'commit', '-q', '--no-verify', '-m', 'Weakens quietly'])->throw();

    $rejected = Process::path($dir)->run(['bash', 'scripts/check-weakened.sh', '--range', 'HEAD~1..HEAD']);
    expect($rejected->successful())->toBeFalse()->and($rejected->errorOutput())->toContain('app/Example.php:3');

    Process::path($dir)->run(['git', 'commit', '-q', '--amend', '--no-verify', '-m', "Weakens with a reason\n\nWeakened: third-party analysis has wrong types"])->throw();
    $passed = Process::path($dir)->run(['bash', 'scripts/check-weakened.sh', '--range', 'HEAD~1..HEAD']);
    expect($passed->successful())->toBeTrue();
});
