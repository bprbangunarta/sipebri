<?php

use Illuminate\Support\Facades\Process;

/*
 * `scripts/check-standards.mjs` runs for real in a temporary copy whose path holds spaces and non-ASCII letters, with broken or missing
 * files, so what comes out is always a sentence someone can act on. The shell gates get the same treatment in their own tests
 * (BriefGateTest, WeakenedCheckGateTest: file names with quotes, spaces, non-ASCII letters, and a leading dash).
 */

const CLEAN_NAVIGATION = "<?php\n\nreturn [['label' => 'Pengajuan', 'href' => '/loan-applications']];\n";

/**
 * Temporary copy holding the script, a small resources/js tree, and a menu file, inside a folder with a difficult name.
 *
 * @param  array<string, string>  $files  paths under resources/js -> content
 */
function scriptWorkspace(array $files = ['pages/page.tsx' => "export default function Page() {\n    return null;\n}\n"], string $navigation = CLEAN_NAVIGATION): string
{
    $dir = sys_get_temp_dir().'/work space é '.bin2hex(random_bytes(3));
    mkdir($dir.'/scripts', 0777, true);
    mkdir($dir.'/resources/js', 0777, true);
    mkdir($dir.'/app/Support', 0777, true);
    copy(base_path('scripts/check-standards.mjs'), "{$dir}/scripts/check-standards.mjs");
    file_put_contents($dir.'/app/Support/Navigation.php', $navigation);

    foreach ($files as $path => $content) {
        @mkdir(dirname("{$dir}/resources/js/{$path}"), 0777, true);
        file_put_contents("{$dir}/resources/js/{$path}", $content);
    }

    return $dir;
}

it('checks the standards in a folder whose name holds spaces and non-ASCII letters, and passes a clean tree', function () {
    $dir = scriptWorkspace();

    $result = Process::path($dir)->run(['node', 'scripts/check-standards.mjs']);

    expect($result->successful())->toBeTrue($result->errorOutput())->and($result->output())->toContain('Standar kode terpenuhi (');
});

it('finds a violation in a folder with a difficult name, with the file name and line', function () {
    $dir = scriptWorkspace(['pages/page.tsx' => "export default function Page() {\n    console.log('x');\n    return null;\n}\n"]);

    $result = Process::path($dir)->run(['node', 'scripts/check-standards.mjs']);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain('page.tsx:2')->toContain('console.log');
});

it('skips the generated Wayfinder folders', function () {
    $dir = scriptWorkspace([
        'pages/page.tsx' => "export default function Page() {\n    return null;\n}\n",
        'actions/App/Example.ts' => "export default function x(): any {\n    console.log('generated');\n}\n",
    ]);

    $result = Process::path($dir)->run(['node', 'scripts/check-standards.mjs']);

    expect($result->successful())->toBeTrue($result->errorOutput());
});

it('applies the menu label rule to the navigation file', function () {
    $dir = scriptWorkspace(navigation: "<?php\n\nreturn [['label' => 'Label yang sangat panjang sekali', 'href' => '/x']];\n");

    $result = Process::path($dir)->run(['node', 'scripts/check-standards.mjs']);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain('Navigation.php:3')->toContain('terlalu panjang');
});

it('stops with a sentence, not a stack trace, when there are no files at all or the menu file is missing', function () {
    $none = Process::path(scriptWorkspace([]))->run(['node', 'scripts/check-standards.mjs']);

    expect($none->successful())->toBeFalse()->and($none->errorOutput())->toContain('tidak memeriksa apa pun')->not->toContain('    at ');

    $dir = scriptWorkspace();
    unlink($dir.'/app/Support/Navigation.php');
    $noMenu = Process::path($dir)->run(['node', 'scripts/check-standards.mjs']);

    expect($noMenu->successful())->toBeFalse()->and($noMenu->errorOutput())->toContain('Tidak bisa membaca')->not->toContain('    at ');
});

it('stops with a sentence, not a stack trace, when a folder cannot be read', function () {
    $dir = scriptWorkspace(['pages/page.tsx' => "export {};\n", 'locked/inner.tsx' => "export {};\n"]);
    chmod($dir.'/resources/js/locked', 0000);

    try {
        $locked = Process::path($dir)->run(['node', 'scripts/check-standards.mjs']);
    } finally {
        chmod($dir.'/resources/js/locked', 0755);
    }

    expect($locked->successful())->toBeFalse()->and($locked->errorOutput())->toContain('Tidak bisa membaca')->not->toContain('    at ');
})->skip(fn () => function_exists('posix_geteuid') && posix_geteuid() === 0, 'root reads a locked folder'); // weakening-allowed: root ignores folder permissions, so the failure cannot be produced
