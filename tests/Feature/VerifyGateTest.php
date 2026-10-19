<?php

use Illuminate\Support\Facades\Process;

/*
 * `scripts/verify.sh` and the `pre-push` hook run for real in a temporary repository, with the heavy tools (php, npm, composer, pint,
 * phpstan) replaced by stubs that always pass: what is tested is the commit range checked, the order of the steps, and the requirement that
 * the hooks are installed, not the tools themselves.
 */

const VERIFY_ZERO_SHA = '0000000000000000000000000000000000000000';

/**
 * Temporary repository holding copies of the gate scripts, tool stubs, one initial commit, and (when asked) a remote with a base branch.
 * Every stub appends its arguments to `<stubs>/calls.log`.
 *
 * @return array{dir: string, stubs: string, remote: string}
 */
function verifyRepo(bool $withRemote = true, bool $hooks = true): array
{
    $dir = sys_get_temp_dir().'/verify-'.bin2hex(random_bytes(4));
    $stubs = $dir.'-stubs';
    mkdir($stubs, 0777, true);
    $log = "echo \"\$(basename \"\$0\") \$*\" >> '{$stubs}/calls.log'\n";

    foreach (['php', 'npm', 'composer'] as $tool) {
        // `php artisan env:check` fails when ENV_CHECK_FAIL is set; everything else always passes.
        $body = $tool === 'php' ? "case \"\$*\" in *env:check*) [ -z \"\$ENV_CHECK_FAIL\" ] || exit 1 ;; esac\nexit 0\n" : "exit 0\n";
        file_put_contents("{$stubs}/{$tool}", "#!/bin/sh\n{$log}{$body}");
        chmod("{$stubs}/{$tool}", 0755);
    }

    mkdir($dir.'/scripts', 0777, true);
    mkdir($dir.'/.githooks', 0777, true);
    mkdir($dir.'/vendor/bin', 0777, true);

    foreach (['verify.sh', 'check-weakened.sh', 'check-brief.sh', 'check-dependencies.sh'] as $script) {
        copy(base_path("scripts/{$script}"), "{$dir}/scripts/{$script}");
        chmod("{$dir}/scripts/{$script}", 0755);
    }

    copy(base_path('.githooks/pre-push'), $dir.'/.githooks/pre-push');
    chmod($dir.'/.githooks/pre-push', 0755);

    foreach (['pint', 'phpstan'] as $tool) {
        file_put_contents("{$dir}/vendor/bin/{$tool}", "#!/bin/sh\n{$log}exit 0\n");
        chmod("{$dir}/vendor/bin/{$tool}", 0755);
    }

    foreach ([['git', 'init', '-q', '-b', 'trunk'], ['git', 'config', 'user.email', 't@example.test'], ['git', 'config', 'user.name', 'Test']] as $command) {
        Process::path($dir)->run($command)->throw();
    }

    verifyWrite($dir, 'tests/Feature/ExampleTest.php', "<?php\n\nit('one', function () {\n    expect(1)->toBe(1);\n});\n");
    verifyCommit($dir, "start\n\nBrief: none: sample repository for the gate test");

    $remote = $dir.'-remote.git';

    if ($withRemote) {
        Process::run(['git', 'init', '-q', '--bare', $remote])->throw();
        Process::path($dir)->run(['git', 'remote', 'add', 'origin', $remote])->throw();
        Process::path($dir)->run(['git', 'push', '-q', 'origin', 'trunk'])->throw();
    }

    // Hooks are installed after the setup push, so that push does not run them.
    if ($hooks) {
        Process::path($dir)->run(['git', 'config', 'core.hooksPath', '.githooks'])->throw();
    }

    return ['dir' => $dir, 'stubs' => $stubs, 'remote' => $remote];
}

function verifyWrite(string $dir, string $path, string $content): void
{
    @mkdir(dirname($dir.'/'.$path), 0777, true);
    file_put_contents($dir.'/'.$path, $content);
}

function verifyCommit(string $dir, string $message): void
{
    Process::path($dir)->run(['git', 'add', '-A'])->throw();
    Process::path($dir)->run(['git', 'commit', '-q', '--no-verify', '-m', $message])->throw();
}

/**
 * @param  array{dir: string, stubs: string, remote: string}  $repo
 * @param  array<string, string>  $env
 */
function verifyRun(array $repo, array $env = [], ?string $input = null, string $command = 'scripts/verify.sh')
{
    $process = Process::path($repo['dir'])->env(['PATH' => $repo['stubs'].':'.getenv('PATH'), ...$env]);

    return ($input === null ? $process : $process->input($input))->run(['sh', $command]);
}

// Written in two pieces so this test line itself is not read by the weakened-check detector as a suppression.
const VERIFY_WEAKENING = "<?php\n\n// @phpstan"."-ignore-next-line\nreturn 1;\n";

it('passes on a clean repository and checks the commits that are not upstream yet', function () {
    $repo = verifyRepo();
    verifyWrite($repo['dir'], 'app/Clean.php', "<?php\n\nreturn 1;\n");
    verifyCommit($repo['dir'], "Good\n\nBrief: none: small change for the gate test");

    $result = verifyRun($repo);

    expect($result->successful())->toBeTrue($result->errorOutput())->and($result->output())->toContain('Semua pemeriksaan hijau');
});

it('builds the assets before the test suite and the type check, and runs every gate step', function () {
    $repo = verifyRepo();

    expect(verifyRun($repo)->successful())->toBeTrue();

    $calls = array_values(array_filter(explode("\n", (string) file_get_contents($repo['stubs'].'/calls.log'))));
    $position = fn (string $call): int|false => array_search($call, $calls, true);

    expect($calls)->toContain('php artisan env:check')
        ->toContain('pint --test --format agent')
        ->toContain('phpstan analyse --memory-limit=1G --no-progress')
        ->toContain('npm run build')
        ->toContain('php artisan test --compact')
        ->toContain('npm run check')
        ->toContain('npm run duplicates')
        ->toContain('npm run types:check')
        ->and($position('npm run build'))->toBeLessThan($position('php artisan test --compact'))
        ->and($position('npm run build'))->toBeLessThan($position('npm run types:check'));
});

it('refuses to run when the git hooks are not installed, with a hint on installing them', function () {
    $repo = verifyRepo(hooks: false);

    $result = verifyRun($repo);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain('hook git belum dipasang')->toContain('composer setup');
});

it('checks every commit not on a remote for a branch without upstream, not only the last one', function () {
    $repo = verifyRepo();
    Process::path($repo['dir'])->run(['git', 'checkout', '-q', '-b', 'fresh'])->throw();
    verifyWrite($repo['dir'], 'app/First.php', VERIFY_WEAKENING);
    verifyCommit($repo['dir'], "Weakens quietly\n\nBrief: none: change for the gate test");
    verifyWrite($repo['dir'], 'app/Second.php', "<?php\n\nreturn 2;\n");
    verifyCommit($repo['dir'], "Second\n\nBrief: none: change for the gate test");
    verifyWrite($repo['dir'], 'app/Third.php', "<?php\n\nreturn 3;\n");
    verifyCommit($repo['dir'], "Third\n\nBrief: none: change for the gate test");

    $result = verifyRun($repo);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain('app/First.php:3');
});

it('checks every commit in a repository without any remote', function () {
    $repo = verifyRepo(withRemote: false);
    verifyWrite($repo['dir'], 'app/First.php', VERIFY_WEAKENING);
    verifyCommit($repo['dir'], "Weakens quietly\n\nBrief: none: change for the gate test");
    verifyWrite($repo['dir'], 'app/Second.php', "<?php\n\nreturn 2;\n");
    verifyCommit($repo['dir'], "Second\n\nBrief: none: change for the gate test");

    $result = verifyRun($repo);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain('app/First.php:3');
});

it('rejects a code commit that names no brief, which shows when the commit-msg hook was skipped', function () {
    $repo = verifyRepo();
    verifyWrite($repo['dir'], 'app/NoBrief.php', "<?php\n\nreturn 1;\n");
    verifyCommit($repo['dir'], 'No brief line');

    $result = verifyRun($repo);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain('No brief line')->toContain('--no-verify');
});

it('does not reject a docs-only commit for lacking a brief line', function () {
    $repo = verifyRepo();
    verifyWrite($repo['dir'], 'docs/notes.md', "content\n");
    verifyCommit($repo['dir'], 'Docs only');

    expect(verifyRun($repo)->successful())->toBeTrue();
});

it('uses the range actually being pushed from the pre-push hook, whatever branch is checked out', function () {
    $repo = verifyRepo();
    $base = trim(Process::path($repo['dir'])->run(['git', 'rev-parse', 'HEAD'])->output());
    verifyWrite($repo['dir'], 'app/First.php', VERIFY_WEAKENING);
    verifyCommit($repo['dir'], "Weakens quietly\n\nBrief: none: change for the gate test");
    $weakened = trim(Process::path($repo['dir'])->run(['git', 'rev-parse', 'HEAD'])->output());
    verifyWrite($repo['dir'], 'app/Second.php', "<?php\n\nreturn 2;\n");
    verifyCommit($repo['dir'], "Second\n\nBrief: none: change for the gate test");
    $tip = trim(Process::path($repo['dir'])->run(['git', 'rev-parse', 'HEAD'])->output());

    // A new remote branch: every commit not on the remote, including the weakening one.
    $new = verifyRun($repo, input: "refs/heads/fresh {$tip} refs/heads/fresh ".VERIFY_ZERO_SHA."\n", command: '.githooks/pre-push');
    expect($new->successful())->toBeFalse()->and($new->errorOutput())->toContain('app/First.php:3');

    // An existing remote branch, up to and including the weakening commit: it is checked.
    $existing = verifyRun($repo, input: "refs/heads/trunk {$tip} refs/heads/trunk {$base}\n", command: '.githooks/pre-push');
    expect($existing->successful())->toBeFalse();

    // Only the later commit is sent (the weakening one is already on the remote): not checked again.
    $later = verifyRun($repo, input: "refs/heads/trunk {$tip} refs/heads/trunk {$weakened}\n", command: '.githooks/pre-push');
    expect($later->successful())->toBeTrue($later->errorOutput());

    // Deleting a remote branch carries no commits.
    $delete = verifyRun($repo, input: 'refs/heads/old '.VERIFY_ZERO_SHA.' refs/heads/old '.$base."\n", command: '.githooks/pre-push');
    expect($delete->successful())->toBeTrue($delete->errorOutput());
});

it('uses the range from CI, and falls back to the main branch on the first push of a branch', function () {
    $repo = verifyRepo();
    Process::path($repo['dir'])->run(['git', 'fetch', '-q', 'origin'])->throw();
    Process::path($repo['dir'])->run(['git', 'update-ref', 'refs/remotes/origin/main', 'HEAD'])->throw();
    $base = trim(Process::path($repo['dir'])->run(['git', 'rev-parse', 'HEAD'])->output());
    verifyWrite($repo['dir'], 'app/First.php', VERIFY_WEAKENING);
    verifyCommit($repo['dir'], "Weakens quietly\n\nBrief: none: change for the gate test");
    verifyWrite($repo['dir'], 'app/Second.php', "<?php\n\nreturn 2;\n");
    verifyCommit($repo['dir'], "Second\n\nBrief: none: change for the gate test");

    expect(verifyRun($repo, ['VERIFY_RANGE' => "{$base}..HEAD"])->successful())->toBeFalse()
        ->and(verifyRun($repo, ['VERIFY_RANGE' => VERIFY_ZERO_SHA.'..HEAD'])->successful())->toBeFalse();
});

it('stops when .env lacks a variable from .env.example, before the longer checks', function () {
    $repo = verifyRepo();

    $result = verifyRun($repo, ['ENV_CHECK_FAIL' => '1']);

    expect($result->successful())->toBeFalse()->and($result->output())->toContain('.env memuat semua variabel')->not->toContain('Pint');
});
