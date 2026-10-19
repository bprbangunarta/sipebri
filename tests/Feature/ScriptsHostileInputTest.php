<?php

use Illuminate\Support\Facades\File;
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
    // Enough random bytes that a leftover from another run can never collide, and every folder is removed after its test.
    $dir = sys_get_temp_dir().'/work space é '.bin2hex(random_bytes(8));
    $GLOBALS['scriptWorkspaces'][] = $dir;
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

/*
 * Every rule marked [mesin] in `.ai/rules/js.md` must fail when broken, and the neighbouring code that is allowed must pass: the script is
 * run for real on a temporary tree with one broken line, then with its allowed twin.
 */
dataset('broken front-end rules', [
    'a raw table' => ['pages/p.tsx', "export default function P() {\n    return <table><tbody /></table>;\n}\n", 'Tabel mentah dilarang'],
    'a colour written out (hex)' => ['pages/p.tsx', "export default function P() {\n    return <div className=\"bg-[#ff0000]\" />;\n}\n", 'Warna ditulis langsung'],
    'a colour written out (rgb)' => ['pages/p.tsx', "export default function P() {\n    return <div style={{ color: 'rgb(1, 2, 3)' }} />;\n}\n", 'Warna ditulis langsung'],
    'a title and action row pinned to the first line' => ['pages/p.tsx', "export default function P() {\n    return <div className=\"flex items-start justify-between\" />;\n}\n", 'items-center'],
    'a date formatted on the screen (Intl)' => ['pages/p.tsx', "export default function P() {\n    return <p>{new Intl.NumberFormat('id-ID').format(1)}</p>;\n}\n", 'lib/format.ts'],
    'a date formatted on the screen (toLocaleDateString)' => ['pages/p.tsx', "export default function P() {\n    return <p>{new Date().toLocaleDateString('id')}</p>;\n}\n", 'lib/format.ts'],
    'a console.log' => ['pages/p.tsx', "export default function P() {\n    console.log('x');\n    return null;\n}\n", 'console.log'],
    'a Tailwind palette colour for a status' => ['pages/p.tsx', "export default function P() {\n    return <p className=\"text-red-500\" />;\n}\n", 'Palet Tailwind'],
    'an arbitrary text size' => ['pages/p.tsx', "export default function P() {\n    return <p className=\"text-[11px]\" />;\n}\n", 'Ukuran teks sembarang'],
    'a home-made full-screen layer' => ['pages/p.tsx', "export default function P() {\n    return <div className=\"fixed inset-0\" />;\n}\n", 'Lapisan layar penuh'],
    'a number input' => ['pages/p.tsx', "export default function P() {\n    return <input type=\"number\" />;\n}\n", 'type="number"'],
    'a tel input' => ['pages/p.tsx', "export default function P() {\n    return <input type=\"tel\" />;\n}\n", 'type="number"'],
    'a native text area' => ['pages/p.tsx', "export default function P() {\n    return <textarea />;\n}\n", 'Kontrol native'],
    'a native select' => ['pages/p.tsx', "export default function P() {\n    return <select />;\n}\n", 'Kontrol native'],
    'a native checkbox' => ['pages/p.tsx', "export default function P() {\n    return <input type=\"checkbox\" />;\n}\n", 'Kontrol native'],
    'a type any' => ['pages/p.tsx', "export default function P(props: any) {\n    return null;\n}\n", 'Tipe any'],
    'a cast to any' => ['pages/p.tsx', "export default function P() {\n    return (1 as any) && null;\n}\n", 'Tipe any'],
    'a ts-ignore' => ['pages/p.tsx', "// @ts-ignore\nexport default function P() {\n    return null;\n}\n", '@ts-ignore'], // weakening-allowed: sample code for the standards check, not a suppression
    'a direct fetch' => ['pages/p.tsx', "export default function P() {\n    void fetch('/x');\n    return null;\n}\n", 'fetch langsung'],
    'dangerouslySetInnerHTML' => ['pages/p.tsx', "export default function P() {\n    return <div dangerouslySetInnerHTML={{ __html: 'x' }} />;\n}\n", 'dangerouslySetInnerHTML'],
    'a secret in the browser storage' => ['pages/p.tsx', "export default function P() {\n    localStorage.setItem('token', 'x');\n    return null;\n}\n", 'localStorage'],
    'a default export outside pages and layouts' => ['components/x.tsx', "export default function X() {\n    return null;\n}\n", 'Ekspor bawaan'],
    'a list key that is an index' => ['pages/p.tsx', "export function P({ rows }: { rows: string[] }) {\n    return rows.map((row, index) => <p key={index}>{row}</p>);\n}\n", 'key memakai indeks'],
    'an icon in a dialog footer' => ['pages/p.tsx', "export default function P() {\n    return (\n        <DialogFooter>\n            <Button>\n                <Save /> Simpan\n            </Button>\n        </DialogFooter>\n    );\n}\n", 'footer dialog/kartu hanya teks'],
    'an icon in a card footer' => ['pages/p.tsx', "export default function P() {\n    return (\n        <CardFooter>\n            <Button>\n                <Save /> Simpan\n            </Button>\n        </CardFooter>\n    );\n}\n", 'footer dialog/kartu hanya teks'],
    'a modal without a dialog body' => ['pages/p.tsx', "export default function P() {\n    return <Modal open title=\"x\" onOpenChange={() => undefined}>isi</Modal>;\n}\n", 'DialogBody'],
    'a wrapper around the page header buttons' => ['pages/p.tsx', "export default function P() {\n    return <PageHeader title=\"x\" actions={<div><Button>Simpan</Button></div>} />;\n}\n", 'PageHeader: actions'],
]);

dataset('allowed front-end code', [
    'status tokens and a standard size' => ['pages/p.tsx', "export default function P() {\n    return <p className=\"bg-success-soft text-sm text-success-ink\" />;\n}\n"],
    'a colour named only in a comment' => ['pages/p.tsx', "// bg-[#ff0000] and console.log are banned\nexport default function P() {\n    return null;\n}\n"],
    'a title and action row centred' => ['pages/p.tsx', "export default function P() {\n    return <div className=\"flex items-center justify-between\" />;\n}\n"],
    'a text input' => ['pages/p.tsx', "export default function P() {\n    return <input type=\"text\" />;\n}\n"],
    'a list key that is an id' => ['pages/p.tsx', "export function P({ rows }: { rows: { id: number }[] }) {\n    return rows.map((row) => <p key={row.id} />);\n}\n"],
    'a text-only dialog footer' => ['pages/p.tsx', "export default function P() {\n    return (\n        <DialogFooter>\n            <Button>Simpan</Button>\n        </DialogFooter>\n    );\n}\n"],
    'a modal with its body' => ['pages/p.tsx', "export default function P() {\n    return (\n        <Modal open title=\"x\" onOpenChange={() => undefined}>\n            <DialogBody>isi</DialogBody>\n        </Modal>\n    );\n}\n"],
    'page header buttons as a fragment' => ['pages/p.tsx', "export default function P() {\n    return <PageHeader title=\"x\" actions={<><Button>Simpan</Button></>} />;\n}\n"],
    'a named export in a component' => ['components/x.tsx', "export function X() {\n    return null;\n}\n"],
]);

it('refuses front-end code that breaks a rule of js.md, and says which rule', function (string $path, string $code, string $message) {
    $result = Process::path(scriptWorkspace([$path => $code]))->run(['node', 'scripts/check-standards.mjs']);

    expect($result->successful())->toBeFalse()->and($result->errorOutput())->toContain($message);
})->with('broken front-end rules');

it('lets front-end code through that only looks like it breaks a rule', function (string $path, string $code) {
    $result = Process::path(scriptWorkspace([$path => $code]))->run(['node', 'scripts/check-standards.mjs']);

    expect($result->successful())->toBeTrue($result->errorOutput());
})->with('allowed front-end code');

afterEach(function () {
    foreach ($GLOBALS['scriptWorkspaces'] ?? [] as $dir) {
        File::deleteDirectory($dir);
    }

    $GLOBALS['scriptWorkspaces'] = [];
});
