<?php

use Illuminate\Support\Facades\Process;

/*
 * `AGENTS.md` section 5 is the only place that says when a skill is used. The antislop skills live in `.ai/skills/` (taken from their
 * upstream release, never edited by hand); this test keeps every skill folder named in that section, and no row naming a missing skill.
 */

/** @return list<string> skill folder names in `.ai/skills/` */
function vendoredSkills(): array
{
    return array_values(array_map('basename', glob(base_path('.ai/skills/*'), GLOB_ONLYDIR) ?: []));
}

function agentsSkillSection(): string
{
    $agents = (string) file_get_contents(base_path('AGENTS.md'));
    $start = strpos($agents, '## 5. Skills and tools: when to use which');
    $end = strpos($agents, '## 6. Rules: the standard ones and the project\'s own');

    expect($start)->not->toBeFalse('AGENTS.md lost its section 5 heading.')
        ->and($end)->not->toBeFalse('AGENTS.md lost its section 6 heading.');

    return substr($agents, (int) $start, (int) $end - (int) $start);
}

it('gives every skill in .ai/skills a SKILL.md named after its folder', function () {
    expect(vendoredSkills())->not->toBeEmpty();

    foreach (vendoredSkills() as $skill) {
        $file = base_path(".ai/skills/{$skill}/SKILL.md");

        expect(is_file($file))->toBeTrue("{$skill} has no SKILL.md");
        expect((string) file_get_contents($file))->toContain("\nname: {$skill}\n");
    }
});

it('names every skill in .ai/skills with its usage rule in AGENTS.md section 5, and names no missing one', function () {
    $section = agentsSkillSection();

    foreach (vendoredSkills() as $skill) {
        expect(preg_match('/^\|\s*`'.preg_quote($skill, '/').'`/m', $section))->toBe(1, "{$skill} has no row in the table of AGENTS.md section 5");
    }

    preg_match_all('/\|\s*`(antislop[a-z-]*)`/', $section, $named);

    expect(array_values(array_unique($named[1])))->each->toBeIn(vendoredSkills());
});

it('records the antislop release and license next to the core skill, without its installer or MCP server', function () {
    expect(trim((string) file_get_contents(base_path('.ai/skills/antislop/VERSION'))))->toMatch('/^\d+\.\d+\.\d+$/')
        ->and(is_file(base_path('.ai/skills/antislop/LICENSE')))->toBeTrue()
        ->and(glob(base_path('.ai/skills/*/*.mjs')))->toBeEmpty()
        ->and(glob(base_path('.ai/skills/*/contrast-mcp.py')))->toBeEmpty();
});

it('runs the contrast checker shipped with antislop-human with its own self-test passing', function () {
    $python = trim((string) Process::run('command -v python3')->output());

    if ($python === '') {
        $this->markTestSkipped('python3 is not installed.'); // weakening-allowed: without python3 the contrast script cannot run at all
    }

    $result = Process::run([$python, base_path('.ai/skills/antislop-human/contrast-check.py'), '--selftest']);

    expect($result->successful())->toBeTrue($result->output().$result->errorOutput());
});

it('lists every antislop skill in boost.json and no skill there that is not in .ai/skills', function () {
    $boost = json_decode((string) file_get_contents(base_path('boost.json')), true, flags: JSON_THROW_ON_ERROR);
    $listed = array_values(array_filter($boost['skills'], fn (string $skill): bool => str_starts_with($skill, 'antislop')));

    expect(array_diff(vendoredSkills(), $listed))->toBe([])
        ->and(array_diff($listed, vendoredSkills()))->toBe([]);
});

it('keeps every antislop file exactly as released, without hand edits or automatic formatting', function () {
    $lines = array_filter(explode("\n", (string) file_get_contents(base_path('.ai/skills/antislop/MANIFEST.sha256'))));
    $changed = [];

    foreach ($lines as $line) {
        [$hash, $path] = preg_split('/\s+/', trim($line), 2) ?: ['', ''];

        if (! is_file(base_path(".ai/skills/{$path}")) || hash_file('sha256', base_path(".ai/skills/{$path}")) !== $hash) {
            $changed[] = $path;
        }
    }

    expect($lines)->not->toBeEmpty()->and($changed)->toBe([]);
});

it('has no file in .ai/skills that the manifest does not list, and no path in the manifest that leaves the folder', function () {
    $listed = [];

    foreach (array_filter(explode("\n", (string) file_get_contents(base_path('.ai/skills/antislop/MANIFEST.sha256')))) as $line) {
        $path = trim((string) (preg_split('/\s+/', trim($line), 2)[1] ?? ''));
        $listed[] = $path;

        expect($path)->not->toContain('..')->not->toStartWith('/');
    }

    $present = [];

    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('.ai/skills'), FilesystemIterator::SKIP_DOTS)) as $file) {
        if ($file->getFilename() === '.DS_Store') {
            continue; // Finder litter, never part of a skill
        }

        $present[] = substr($file->getPathname(), strlen(base_path('.ai/skills')) + 1);
    }

    $present = array_values(array_diff($present, ['antislop/MANIFEST.sha256']));

    expect(array_values(array_diff($present, $listed)))->toBe([], 'A file under .ai/skills that MANIFEST.sha256 does not list.');
});
