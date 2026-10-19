<?php

use Symfony\Component\Yaml\Yaml;

/**
 * @return array<string, mixed>
 */
function ciWorkflow(?string $file = null): array
{
    /** @var array<string, mixed> */
    return Yaml::parseFile($file ?? base_path('.github/workflows/ci.yml'));
}

/**
 * @param  array<string, mixed>  $workflow
 * @return list<array<string, mixed>>
 */
function ciSteps(array $workflow): array
{
    /** @var list<array<string, mixed>> */
    return $workflow['jobs']['gate']['steps'];
}

it('pins every action to a full commit sha with a readable version comment', function () {
    preg_match_all('/^\s*(?:-\s+)?uses:\s*(\S+)(.*)$/m', (string) file_get_contents(base_path('.github/workflows/ci.yml')), $found, PREG_SET_ORDER);

    expect($found)->not->toBeEmpty();

    foreach ($found as [, $action, $rest]) {
        expect($action)->toMatch('/^[\w.-]+\/[\w.-]+@[0-9a-f]{40}$/')
            ->and($rest)->toMatch('/^\s+#\s*v?\d/');
    }
});

it('grants the workflow read access to the repository and nothing else', function () {
    $workflow = ciWorkflow();

    expect($workflow['permissions'])->toBe(['contents' => 'read'])
        ->and($workflow['jobs']['gate'])->not->toHaveKey('permissions');
});

it('checks out the whole history without leaving credentials behind', function () {
    $checkout = collect(ciSteps(ciWorkflow()))->first(fn (array $step): bool => str_starts_with($step['uses'] ?? '', 'actions/checkout@'));

    expect($checkout['with']['fetch-depth'])->toBe(0)
        ->and($checkout['with']['persist-credentials'])->toBeFalse();
});

it('runs the one gate script and lists no check of its own', function () {
    $runs = collect(ciSteps(ciWorkflow()))->pluck('run')->filter()->values()->all();

    expect(collect($runs)->contains(fn (string $run): bool => str_contains($run, 'scripts/verify.sh')))->toBeTrue();

    foreach ($runs as $run) {
        expect($run)->not->toMatch('/pint|phpstan|artisan test|vitest|jscpd|tsc|npm run (check|types|build)|npm audit|composer audit/');
    }
});

it('reads the commit range from the event and falls back when its start is unknown', function () {
    $gate = collect(ciSteps(ciWorkflow()))->first(fn (array $step): bool => str_contains($step['run'] ?? '', 'scripts/verify.sh'));

    expect($gate['env']['VERIFY_RANGE'])->toContain('github.event.before')->toContain('pull_request.base.sha')
        ->and($gate['env'])->toHaveKey('PUSHED_COMMITS')
        ->and($gate['run'])->toContain('git cat-file -e')->toContain('PUSHED_COMMITS');
});

it('runs the tests on in-memory sqlite without the Vite CI refusal', function () {
    $env = ciWorkflow()['jobs']['gate']['env'];

    expect($env['DB_CONNECTION'])->toBe('sqlite')
        ->and($env['DB_DATABASE'])->toBe(':memory:')
        ->and((string) $env['LARAVEL_BYPASS_ENV_CHECK'])->toBe('1');
});

it('triggers on pushes to the working branch named in devcore.conf and on pull requests', function () {
    preg_match('/^MAIN_BRANCH=(\S+)$/m', (string) file_get_contents(base_path('devcore.conf')), $branch);
    $on = ciWorkflow()['on'];

    expect($branch[1] ?? null)->not->toBeNull()
        ->and($on['push']['branches'])->toContain($branch[1])
        ->and($on)->toHaveKey('pull_request');
});

it('keeps dependabot watching composer, npm and the actions', function () {
    $updates = Yaml::parseFile(base_path('.github/dependabot.yml'))['updates'];

    expect(collect($updates)->pluck('package-ecosystem')->sort()->values()->all())->toBe(['composer', 'github-actions', 'npm']);
});
