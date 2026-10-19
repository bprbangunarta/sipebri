<?php

use Illuminate\Support\Facades\Process;

/*
 * The dependency vulnerability gate (`scripts/check-dependencies.sh`) runs for real with fake `composer` and `npm` first on the PATH, so
 * all three states (clean, finding, advisory list unreachable) are proven without a network.
 */

/**
 * Runs the script with fake programs: each pair is [exit code, output].
 *
 * @param  array{0: int, 1: string}  $composer
 * @param  array{0: int, 1: string}  $npm
 * @return array{0: int, 1: string, 2: string} exit code, standard output, error output
 */
function runDependencyGate(array $composer, array $npm): array
{
    $dir = sys_get_temp_dir().'/dep-'.bin2hex(random_bytes(4));
    mkdir($dir.'/bin', 0777, true);
    mkdir($dir.'/scripts', 0777, true);
    copy(base_path('scripts/check-dependencies.sh'), $dir.'/scripts/check-dependencies.sh');
    chmod($dir.'/scripts/check-dependencies.sh', 0755);

    foreach (['composer' => $composer, 'npm' => $npm] as $name => [$code, $output]) {
        file_put_contents($dir.'/bin/'.$name, "#!/bin/sh\ncat <<'EOT'\n{$output}\nEOT\nexit {$code}\n");
        chmod($dir.'/bin/'.$name, 0755);
    }

    Process::path($dir)->run('git init -q')->throw();
    $result = Process::path($dir)->env(['PATH' => $dir.'/bin:'.getenv('PATH')])->run(['bash', 'scripts/check-dependencies.sh']);

    return [$result->exitCode(), $result->output(), $result->errorOutput()];
}

const DEP_ADVISORY_COMPOSER = "Found 1 security vulnerability advisory affecting 1 package:\nPackage: vendor/package";
const DEP_ADVISORY_NPM = "package  <1.2.3\nSeverity: high\n1 high severity vulnerability\n\nTo address all issues, run:\n  npm audit fix";

it('passes when both audits are clean', function () {
    [$code, $out] = runDependencyGate([0, 'No security vulnerability advisories found.'], [0, 'found 0 vulnerabilities']);

    expect($code)->toBe(0)->and($out)->toContain('composer audit: tidak ada temuan')->and($out)->toContain('npm audit: tidak ada temuan');
});

it('stops when composer finds a vulnerability', function () {
    [$code, , $error] = runDependencyGate([1, DEP_ADVISORY_COMPOSER], [0, 'found 0 vulnerabilities']);

    expect($code)->toBe(1)->and($error)->toContain('vendor/package')->and($error)->toContain('composer audit menemukan kerentanan');
});

it('stops when npm finds a vulnerability', function () {
    [$code, , $error] = runDependencyGate([0, 'No security vulnerability advisories found.'], [1, DEP_ADVISORY_NPM]);

    expect($code)->toBe(1)->and($error)->toContain('npm audit menemukan kerentanan');
});

it('warns and continues when the advisory list cannot be reached', function () {
    [$code, , $error] = runDependencyGate([100, 'Failed to audit: curl error 6 while downloading'], [1, 'npm error code ENOTFOUND']);

    expect($code)->toBe(0)
        ->and($error)->toContain('composer audit tidak bisa memeriksa')
        ->and($error)->toContain('npm audit tidak bisa memeriksa')
        ->and($error)->toContain('TIDAK dilakukan');
});
