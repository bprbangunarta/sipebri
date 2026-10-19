<?php

/*
 * The machine-checkable rules of the `antislop-code` skill and antislop R-02, so they cannot regress: comments without content (banners,
 * "Step 1", end markers, emoji, empty labels, TODOs without a task) and the long dash in text people read. Whether a comment adds
 * information is still judged in review, with the `antislop-code` skill.
 */

/**
 * Files that already used the long dash in screen text before 2026-10-19. Changing them changes what people read, so each is fixed by the
 * owner of that screen; an entry that no longer matches fails the test so the list cannot rot.
 *
 * @return array<string, string> file -> reason
 */
function longDashDebt(): array
{
    return [];
}

/**
 * @return list<string> source files from the project's code folders (generated Wayfinder output excluded)
 */
function sourceFilesForComments(): array
{
    $files = [];

    foreach (['app', 'bootstrap', 'config', 'database', 'routes', 'tests', 'resources/js', 'scripts'] as $folder) {
        if (! is_dir(base_path($folder))) {
            continue;
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($folder), FilesystemIterator::SKIP_DOTS)) as $file) {
            $path = $file->getPathname();
            $relative = substr($path, strlen(base_path()) + 1);

            if (! in_array($file->getExtension(), ['php', 'ts', 'tsx', 'mjs', 'sh'], true)
                || preg_match('#^resources/js/(actions|routes|wayfinder)/#', $relative) === 1
                || str_contains($path, '/node_modules/')
                || $file->getFilename() === 'CommentHygieneTest.php') {
                continue;
            }

            $files[] = $path;
        }
    }

    // Comments in `.env.example` are read by whoever sets up a server: the same rules as code comments apply.
    $files[] = base_path('.env.example');
    $files[] = base_path('.githooks/commit-msg');
    $files[] = base_path('.githooks/pre-push');

    sort($files);

    return $files;
}

/**
 * The text of every comment line, without its comment marker.
 *
 * @return list<array{0: string, 1: int, 2: string}> file, line number, text
 */
function commentLines(): array
{
    $found = [];

    foreach (sourceFilesForComments() as $file) {
        foreach (file($file) ?: [] as $number => $line) {
            $trimmed = trim($line);

            if (str_starts_with($trimmed, '#!')) {
                continue;
            }

            if (preg_match('#^(//+|/\*+|\*+(?!/)|\#(?!\[))\s?(.*)$#', $trimmed, $match) === 1) {
                $found[] = [substr($file, strlen(base_path()) + 1), $number + 1, trim(rtrim($match[2], '*/ '))];
            }
        }
    }

    return $found;
}

/**
 * @param  callable(string): bool  $isSlop
 * @return list<string>
 */
function commentViolations(callable $isSlop): array
{
    $violations = [];

    foreach (commentLines() as [$file, $line, $text]) {
        if ($text !== '' && $isSlop($text)) {
            $violations[] = "{$file}:{$line}: {$text}";
        }
    }

    return $violations;
}

it('uses no banner separators made of repeated characters in comments', function () {
    expect(commentViolations(fn (string $text): bool => preg_match('/([=_~#*-])\1{3,}/', $text) === 1 || preg_match('/^-{2,}\s*\S.*\s*-{2,}$/', $text) === 1))->toBe([]);
});

it('does not narrate the flow step by step in comments', function () {
    expect(commentViolations(fn (string $text): bool => preg_match('/^(step|phase)\s+\d+\b/i', $text) === 1))->toBe([]);
});

it('puts no block end markers, emoji, or empty labels in comments', function () {
    $endMarker = fn (string $text): bool => preg_match('/^end\s+(of\s+)?(if|for|foreach|while|switch|function|class|method)\b/i', $text) === 1;
    $emoji = fn (string $text): bool => preg_match('/\p{Extended_Pictographic}/u', $text) === 1;
    $emptyLabel = fn (string $text): bool => preg_match('/^(MAIN LOGIC|CORE LOGIC|BUSINESS LOGIC|HELPER FUNCTIONS?|ERROR HANDLING|ENTRY POINT)\.?$/i', $text) === 1;

    expect(commentViolations(fn (string $text): bool => $endMarker($text) || $emoji($text) || $emptyLabel($text)))->toBe([]);
});

it('leaves no TODO without a clear task', function () {
    expect(commentViolations(fn (string $text): bool => preg_match('/^TODO:?\s*$/i', $text) === 1 || preg_match('/^TODO:?\s*(improve( this)?|refactor( this)?|fix( this)?|add more( validation)?|clean ?up)\.?$/i', $text) === 1))->toBe([]);
});

it('uses no long dash in text the project writes: code, translations, docs, and screens', function () {
    $dash = "\u{2014}";
    $files = [
        ...sourceFilesForComments(),
        ...glob(base_path('lang/*/*.php')) ?: [],
        base_path('CLAUDE.md'),
        base_path('memory/PRD.md'),
        ...glob(base_path('.ai/rules/*.md')) ?: [],
        ...glob(base_path('docs/briefs/*.md')) ?: [],
        ...glob(base_path('resources/views/**/*.blade.php')) ?: [],
        ...glob(base_path('resources/views/*.blade.php')) ?: [],
    ];
    $agents = (string) file_get_contents(base_path('AGENTS.md'));
    $violations = [];

    // AGENTS.md: only the project part; the Boost block below "# Bagian 2" is generated and never edited by hand.
    if (str_contains(substr($agents, 0, (int) strpos($agents, '# Bagian 2')), $dash)) {
        $violations[] = 'AGENTS.md';
    }

    foreach ($files as $file) {
        if (is_file($file) && str_contains((string) file_get_contents($file), $dash)) {
            $violations[] = substr($file, strlen(base_path()) + 1);
        }
    }

    expect(array_values(array_diff($violations, array_keys(longDashDebt()))))->toBe([], 'Long dash in project text; use a colon, comma, or parentheses (antislop R-02).')
        ->and(array_values(array_diff(array_keys(longDashDebt()), $violations)))->toBe([], 'Long-dash debt entry no longer matches: remove it from longDashDebt().');
});

it('gives every long-dash debt entry a real reason', function () {
    foreach (longDashDebt() as $file => $reason) {
        expect(mb_strlen($reason))->toBeGreaterThanOrEqual(10, "Debt entry {$file} has no reason.");
    }
});
