<?php

use Illuminate\Support\Facades\Process;

/*
 * The agent reads `.ai/rules/index.md` to learn which rule file applies to the path it will touch (AGENTS.md, step 4). Two sources say
 * which paths a rule covers (the index row and the rule file's own `paths:` header), and a source file that no row reaches is edited
 * without its rules being read. This test keeps both honest, and keeps AGENTS.md section 5, `boost.json`, and the generated Boost block
 * of AGENTS.md from drifting apart (`php artisan boost:update` rewrites the block and `boost.json`).
 */

/**
 * @return array<string, list<string>> rule file => the globs its index rows list ("everything" rows list `**`)
 */
function indexedRuleGlobs(): array
{
    $rows = [];

    foreach (explode("\n", (string) file_get_contents(base_path('.ai/rules/index.md'))) as $line) {
        $cells = array_map('trim', explode('|', trim($line, " \t|")));

        if (! str_starts_with(trim($line), '|') || count($cells) < 2 || preg_match('#^\.ai/rules/[\w.-]+\.md$#', $cells[1]) !== 1) {
            continue;
        }

        preg_match_all('/`([^`]+)`/', $cells[0], $found);

        $globs = str_starts_with($cells[0], 'everything') ? ['**'] : $found[1];
        $rows[$cells[1]] = [...($rows[$cells[1]] ?? []), ...$globs];
    }

    return $rows;
}

/**
 * @return list<string> the globs of a rule file's `paths:` header, with "everything" written one way
 */
function headerRuleGlobs(string $ruleFile): array
{
    $text = (string) file_get_contents(base_path($ruleFile));

    if (preg_match('/\A---\npaths:\n((?:[ \t]+- .*\n)+)---\n/', $text, $header) !== 1) {
        return [];
    }

    preg_match_all('/^[ \t]+- [\'"]?(.*?)[\'"]?\s*$/m', $header[1], $paths);

    return array_map(fn (string $glob): string => $glob === '**/*' ? '**' : $glob, $paths[1]);
}

function ruleGlobToRegex(string $glob): string
{
    $regex = '';

    for ($i = 0, $length = strlen($glob); $i < $length; $i++) {
        if ($glob[$i] === '*' && ($glob[$i + 1] ?? '') === '*') {
            $i++;

            if (($glob[$i + 1] ?? '') === '/') {
                $i++;
                $regex .= '(?:.*/)?';
            } else {
                $regex .= '.*';
            }
        } elseif ($glob[$i] === '*') {
            $regex .= '[^/]*';
        } else {
            $regex .= preg_quote($glob[$i], '#');
        }
    }

    return '#^'.$regex.'$#';
}

/**
 * @return list<string>
 */
function trackedFiles(): array
{
    $result = Process::path(base_path())->run(['git', 'ls-files', '-z']);

    expect($result->successful())->toBeTrue('git ls-files failed: '.$result->errorOutput());

    return array_values(array_filter(explode("\0", $result->output()), fn (string $file): bool => $file !== '' && is_file(base_path($file))));
}

/**
 * Files that no path-specific rule row reaches, on purpose. A pattern that matches no uncovered file fails the test (the list cannot
 * rot), and a file a row reaches never needs a pattern.
 *
 * @return array<string, string> pattern => reason
 */
function deliberatelyUncoveredFiles(): array
{
    return [
        '#^(composer\.lock|package-lock\.json)$#' => 'lockfile, written by the package manager',
        '#\.(png|ico|jpe?g|gif|woff2?|ttf)$#' => 'binary file',
        '#^public/#' => 'static files and the framework entry point (index.php, .htaccess), served as they are',
        '#^(storage|bootstrap/cache)/#' => 'generated or placeholder folders',
        '#^\.ai/skills/#' => 'vendored skills, replaced by a newer release and never edited (the registry checks them)',
        '#\.md$#' => 'documents: AGENTS.md, CLAUDE.md, the rule files, the brief template, the PRD',
        '#^(docs|memory)/#' => 'documents',
        '#^(\.githooks|\.github|scripts)/#' => 'gate and CI files, owned by devcore and governed by AGENTS.md sections 3 and 4 and quality.md',
        '#^(devcore\.conf)$#' => 'settings of the gate, same as the scripts it configures',
        '#^(artisan)$#' => 'framework entry point, not edited',
        '#^(\.gitignore|\.gitattributes|\.editorconfig|\.npmrc|\.mcp\.json|boost\.json|pnpm-workspace\.yaml)$#' => 'tooling dotfiles, no code rule applies',
    ];
}

it('lists in the index exactly the paths each rule file declares in its own header', function () {
    $drift = [];

    foreach (indexedRuleGlobs() as $ruleFile => $indexed) {
        $declared = headerRuleGlobs($ruleFile);

        sort($declared);
        sort($indexed);

        if ($declared !== array_values(array_unique($indexed))) {
            $drift[] = "{$ruleFile}: header [".implode(', ', $declared).'] but index [ '.implode(', ', $indexed).' ]';
        }
    }

    expect(indexedRuleGlobs())->not->toBeEmpty()
        ->and($drift)->toBe([]);
});

it('gives every rule file in .ai/rules a header that declares its paths', function () {
    $missing = array_values(array_filter(
        array_keys(indexedRuleGlobs()),
        fn (string $ruleFile): bool => headerRuleGlobs($ruleFile) === [],
    ));

    expect($missing)->toBe([]);
});

it('reaches every tracked source file with at least one rule row besides the two that cover everything', function () {
    $specific = [];

    foreach (indexedRuleGlobs() as $globs) {
        foreach ($globs as $glob) {
            if ($glob !== '**') {
                $specific[] = ruleGlobToRegex($glob);
            }
        }
    }

    $uncovered = array_values(array_filter(
        trackedFiles(),
        fn (string $file): bool => array_filter($specific, fn (string $regex): bool => preg_match($regex, $file) === 1) === [],
    ));

    $unexplained = array_values(array_filter(
        $uncovered,
        fn (string $file): bool => array_filter(array_keys(deliberatelyUncoveredFiles()), fn (string $pattern): bool => preg_match($pattern, $file) === 1) === [],
    ));

    $stale = array_values(array_filter(
        array_keys(deliberatelyUncoveredFiles()),
        fn (string $pattern): bool => array_filter($uncovered, fn (string $file): bool => preg_match($pattern, $file) === 1) === [],
    ));

    expect($unexplained)->toBe([], 'Add a row (or a path to an existing rule file and its row) in .ai/rules/index.md, or list the file with a reason in deliberatelyUncoveredFiles().')
        ->and($stale)->toBe([], 'These exceptions match no uncovered file any more: remove them.');
});

it('matches the glob forms the index uses against real paths', function () {
    expect(preg_match(ruleGlobToRegex('app/**'), 'app/Http/Controllers/A.php'))->toBe(1)
        ->and(preg_match(ruleGlobToRegex('app/**'), 'application/A.php'))->toBe(0)
        ->and(preg_match(ruleGlobToRegex('**/*.test.*'), 'resources/js/lib/mask.test.ts'))->toBe(1)
        ->and(preg_match(ruleGlobToRegex('**/*.test.*'), 'mask.test.ts'))->toBe(1)
        ->and(preg_match(ruleGlobToRegex('**/*.test.*'), 'resources/js/lib/mask.ts'))->toBe(0)
        ->and(preg_match(ruleGlobToRegex('database/migrations/**'), 'database/seeders/A.php'))->toBe(0)
        ->and(preg_match(ruleGlobToRegex('composer.json'), 'composer.json'))->toBe(1)
        ->and(preg_match(ruleGlobToRegex('composer.json'), 'composerXjson'))->toBe(0);
});

/**
 * @return list<string> skill names that AGENTS.md section 5 gives a row, in its first column
 */
function agentsSkillRows(): array
{
    $agents = (string) file_get_contents(base_path('AGENTS.md'));
    $start = (int) strpos($agents, '## 5. Skills and tools');
    $end = (int) strpos($agents, '## 6. Rules');

    preg_match_all('/^\|\s*`([a-z0-9-]+)`/m', substr($agents, $start, $end - $start), $rows);

    return array_values(array_unique($rows[1]));
}

it('keeps the skills of boost.json and the rows of AGENTS.md section 5 the same set', function () {
    $boost = json_decode((string) file_get_contents(base_path('boost.json')), true);
    $listed = $boost['skills'] ?? [];
    $vendored = array_map('basename', glob(base_path('.ai/skills/*'), GLOB_ONLYDIR) ?: []);
    $rows = agentsSkillRows();

    expect($listed)->not->toBeEmpty()
        ->and(array_values(array_diff($listed, $rows)))->toBe([], 'boost.json names a skill that AGENTS.md section 5 has no row for.')
        ->and(array_values(array_diff($rows, $listed)))->toBe([], 'AGENTS.md section 5 has a row for a skill that boost.json does not list, so Boost never publishes it.')
        ->and(array_values(array_diff($vendored, $listed)))->toBe([], 'A vendored skill in .ai/skills is not listed in boost.json, so the agent folder never links it.');
});

it('keeps the generated Boost block of AGENTS.md once, after the project part, with no mention of its tags before it', function () {
    $agents = (string) file_get_contents(base_path('AGENTS.md'));
    $open = '<laravel-boost-guidelines>';
    $close = '</laravel-boost-guidelines>';
    $start = strpos($agents, $open);
    $project = substr($agents, 0, (int) $start);

    expect(substr_count($agents, $open))->toBe(1, 'A mention of the opening tag in the project part makes `php artisan boost:update` swallow everything from the mention to the real block.')
        ->and(substr_count($agents, $close))->toBe(1)
        ->and(strpos($agents, $close))->toBeGreaterThan((int) $start)
        ->and($project)->toContain('# Bagian 2: Pedoman Laravel Boost')
        ->and(trim((string) substr($agents, strpos($agents, $close) + strlen($close))))->toBe('', 'Nothing follows the Boost block: boost:update would drop it.');
});

it('points no document at the backlog file that was merged into the PRD', function () {
    expect(is_file(base_path('BACKLOG.md')))->toBeFalse();

    $agents = (string) file_get_contents(base_path('AGENTS.md'));
    $texts = [
        'AGENTS.md' => substr($agents, 0, (int) strpos($agents, '<laravel-boost-guidelines>')),
        'CLAUDE.md' => (string) file_get_contents(base_path('CLAUDE.md')),
    ];

    foreach (glob(base_path('.ai/rules/*.md')) ?: [] as $rule) {
        $texts['.ai/rules/'.basename($rule)] = (string) file_get_contents($rule);
    }

    $pointers = array_keys(array_filter($texts, fn (string $text): bool => str_contains($text, 'BACKLOG')));

    expect($pointers)->toBe([]);
});
