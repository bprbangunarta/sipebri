<?php

use Symfony\Component\Yaml\Yaml;

/*
 * The gate has one definition, `scripts/verify.sh`. Composer scripts, hooks, and the CI workflow only call it; this test fails when one of
 * them starts listing checks of its own (which could then drift from what the hook guards), or when the hooks are not installed here.
 */
const GATE_CHECK_PATTERN = '/pint|phpstan|artisan test|phpunit|pest|npm (run|test|audit)|composer audit|types:check|lint|vp check|tsc/i';

it('makes composer verify, check, and ci:check only call the gate script, and test run no style or type check of its own', function () {
    $scripts = json_decode((string) file_get_contents(base_path('composer.json')), true)['scripts'];
    $steps = fn (string $name): array => array_values(array_filter((array) $scripts[$name], fn (string $step): bool => ! str_starts_with($step, 'Composer\\Config')));

    expect($steps('verify'))->toBe(['sh scripts/verify.sh'])
        ->and($steps('ci:check'))->toBe(['@verify'])
        ->and($steps('check'))->toBe(['@verify'])
        ->and(implode(' ', $steps('test')))->not->toMatch('/pint|phpstan|lint|types:check|npm/');
});

it('installs the git hooks from composer setup', function () {
    $scripts = json_decode((string) file_get_contents(base_path('composer.json')), true)['scripts'];

    expect(implode("\n", $scripts['setup']))->toContain('git config core.hooksPath .githooks');
});

it('makes the CI workflow only set the application up and call the gate, listing no checks of its own', function () {
    $workflow = Yaml::parseFile(base_path('.github/workflows/tests.yml'));
    $steps = $workflow['jobs']['ci']['steps'];
    $runs = collect($steps)->pluck('run')->filter()->values()->all();

    expect($runs)->toBe(['composer setup', 'composer ci:check']);

    foreach ($runs as $run) {
        expect($run === 'composer ci:check' || preg_match(GATE_CHECK_PATTERN, $run) !== 1)->toBeTrue("CI step '{$run}' lists a check of its own; put it in scripts/verify.sh.");
    }
});

it('runs CI on every push to the working branches and on pull requests, with full history and the pushed range', function () {
    $workflow = Yaml::parseFile(base_path('.github/workflows/tests.yml'));
    $on = $workflow['on'] ?? $workflow[true];
    $gate = collect($workflow['jobs']['ci']['steps'])->firstWhere('run', 'composer ci:check');
    $checkout = collect($workflow['jobs']['ci']['steps'])->first(fn (array $step): bool => str_starts_with((string) ($step['uses'] ?? ''), 'actions/checkout@'));

    expect($on['push']['branches'])->toContain('v3.0.0-alpha')->toContain('main')
        ->and(array_key_exists('pull_request', $on))->toBeTrue()
        ->and($checkout['with']['fetch-depth'])->toBe(0)
        ->and($gate['env']['VERIFY_RANGE'])->toContain('github.event.before')->toContain('github.event.pull_request.base.sha');
});

it('keeps the workflow free of secrets and write permissions, with every action pinned to a commit hash', function () {
    $raw = (string) file_get_contents(base_path('.github/workflows/tests.yml'));
    $workflow = Yaml::parseFile(base_path('.github/workflows/tests.yml'));

    expect($workflow['permissions'])->toBe(['contents' => 'read'])
        ->and($raw)->not->toContain('secrets.')
        ->and($raw)->not->toContain('pull_request_target');

    preg_match_all('/^\s*(?:-\s*)?uses:\s*(\S+)/m', $raw, $matches);

    expect($matches[1])->not->toBeEmpty();

    foreach ($matches[1] as $action) {
        expect($action)->toMatch('/@[0-9a-f]{40}$/', "Action {$action} must be pinned to a commit hash, not a tag.");
    }
});

it('ships executable hooks that call the gate scripts', function (string $hook, string $script) {
    $path = base_path(".githooks/{$hook}");

    expect(is_executable($path))->toBeTrue()->and((string) file_get_contents($path))->toContain($script);
})->with([['commit-msg', 'check-brief.sh'], ['commit-msg', 'check-weakened.sh'], ['pre-push', 'verify.sh']]);

it('ships the gate scripts as executables', function (string $script) {
    expect(is_executable(base_path("scripts/{$script}")))->toBeTrue($script);
})->with(['verify.sh', 'check-brief.sh', 'check-weakened.sh', 'check-dependencies.sh']);

it('flags a clone where the git hooks are not installed, with how to install them', function () {
    if (! is_dir(base_path('.git'))) {
        expect(true)->toBeTrue();

        return;
    }

    $path = trim((string) shell_exec('git -C '.escapeshellarg(base_path()).' config core.hooksPath'));

    expect($path)->toBe('.githooks', 'The git hooks are not installed in this clone, so commits and pushes are not guarded. Run `composer setup` (or `git config core.hooksPath .githooks`).');
});

it('runs the duplicate-code check in the gate, with a zero threshold for blocks of 20 lines or more', function () {
    $config = json_decode((string) file_get_contents(base_path('.jscpd.json')), true);
    $package = json_decode((string) file_get_contents(base_path('package.json')), true);

    expect((string) file_get_contents(base_path('scripts/verify.sh')))->toContain('npm run duplicates')
        ->and($package['scripts'])->toHaveKey('duplicates')
        ->and($config['threshold'])->toBe(0)
        ->and($config['minLines'])->toBeLessThanOrEqual(20);
});

it('defines every npm script the gate calls', function () {
    $package = json_decode((string) file_get_contents(base_path('package.json')), true);
    preg_match_all('/npm run ([\w:-]+)/', (string) file_get_contents(base_path('scripts/verify.sh')), $called);

    expect(array_values(array_diff(array_unique($called[1]), array_keys($package['scripts']))))->toBe([]);
});
