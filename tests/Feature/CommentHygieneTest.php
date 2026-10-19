<?php

/*
 * The machine-checkable rules of the `antislop-code` skill and antislop R-02, so they cannot regress: comments without content (banners,
 * "Step 1", end markers, emoji, empty labels, TODOs without a task) and the long dash in text people read. Whether a comment adds
 * information is still judged in review, with the `antislop-code` skill.
 */

/**
 * Where comments are read from: the code folders of the project (generated Wayfinder output excluded) and the files at the root that
 * hold comments people read.
 *
 * @return list<string>
 */
function sourceFilesForComments(): array
{
    $files = [];

    foreach (['app', 'bootstrap', 'config', 'database', 'routes', 'tests', 'resources/js', 'resources/css', 'resources/views', 'scripts', '.github'] as $folder) {
        if (! is_dir(base_path($folder))) {
            continue;
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($folder), FilesystemIterator::SKIP_DOTS)) as $file) {
            $path = $file->getPathname();
            $relative = substr($path, strlen(base_path()) + 1);

            if (! in_array($file->getExtension(), ['php', 'ts', 'tsx', 'mjs', 'sh', 'css', 'yml', 'yaml', 'steps'], true)
                || preg_match('#^resources/js/(actions|routes|wayfinder)/#', $relative) === 1
                || preg_match('#^bootstrap/cache/#', $relative) === 1
                || str_contains($path, '/node_modules/')
                || $file->getFilename() === 'CommentHygieneTest.php') {
                continue;
            }

            $files[] = $path;
        }
    }

    // Comments in `.env.example` are read by whoever sets up a server: the same rules as code comments apply.
    foreach (['.env.example', '.githooks/commit-msg', '.githooks/pre-push', 'devcore.conf', 'phpstan.neon', 'vite.config.ts'] as $file) {
        if (is_file(base_path($file))) {
            $files[] = base_path($file);
        }
    }

    sort($files);

    return $files;
}

/**
 * Every comment in a source text: whole-line comments (PHP, TypeScript, shell, CSS, YAML, Blade), JSX comments, doc-block lines, and
 * comments that trail a statement.
 *
 * @return list<array{0: int, 1: string, 2: string}> line number, text without its comment marker, the whole comment as written
 */
function commentsInSource(string $source): array
{
    $found = [];

    foreach (preg_split('/\r\n|\n|\r/', $source) ?: [] as $index => $line) {
        $trimmed = trim($line);

        if (str_starts_with($trimmed, '#!')) {
            continue;
        }

        if (preg_match('#^(//+|/\*+|\{\s*/\*+|\{\{--|<!--|\*+(?!/)|\#(?!\[))\s?(.*)$#', $trimmed, $match) === 1) {
            [$raw, $text] = [$trimmed, $match[2]];
        } elseif (preg_match('#[;{},)]\s+((?://+|\#(?!\[))\s?(.*))$#', $trimmed, $match) === 1) {
            [$raw, $text] = [$match[1], $match[2]];
        } else {
            continue;
        }

        $found[] = [$index + 1, trim((string) preg_replace('#\s*(\*/\}?|--\}\}|-->)\s*$#', '', $text)), $raw];
    }

    return $found;
}

/**
 * @return list<array{0: string, 1: int, 2: string, 3: string}> file, line number, text, comment as written
 */
function commentLines(): array
{
    static $found = null;

    if ($found !== null) {
        return $found;
    }

    $found = [];

    foreach (sourceFilesForComments() as $file) {
        foreach (commentsInSource((string) file_get_contents($file)) as [$line, $text, $raw]) {
            $found[] = [substr($file, strlen(base_path()) + 1), $line, $text, $raw];
        }
    }

    return $found;
}

/**
 * The rules, one place for the tests below and for the samples that prove each rule can fire.
 *
 * @return array<string, callable(string, string): bool> rule name => whether the comment (text, as written) breaks it
 */
function commentRules(): array
{
    return [
        'banner' => fn (string $text, string $raw): bool => preg_match('/([=_~#*-])\1{3,}/', $raw) === 1 || preg_match('/^-{2,}\s*\S.*\s*-{2,}$/', $text) === 1,
        'step narration' => fn (string $text): bool => preg_match('/^(step|phase)\s+\d+\b/i', $text) === 1,
        'end marker' => fn (string $text): bool => preg_match('/^end\s+(of\s+)?(if|for|foreach|while|switch|function|class|method)\b/i', $text) === 1,
        'emoji' => fn (string $text): bool => preg_match('/\p{Extended_Pictographic}/u', $text) === 1,
        'empty label' => fn (string $text): bool => preg_match('/^(MAIN LOGIC|CORE LOGIC|BUSINESS LOGIC|HELPER FUNCTIONS?|ERROR HANDLING|ENTRY POINT)\.?$/i', $text) === 1,
        'vague TODO' => fn (string $text): bool => preg_match('/^(TODO|FIXME|XXX|HACK):?\s*$/i', $text) === 1 || preg_match('/^(TODO|FIXME|XXX|HACK):?\s*(improve( this)?|refactor( this)?|fix( this)?|add more( validation)?|clean ?up)\.?$/i', $text) === 1,
    ];
}

/**
 * @param  list<string>  $rules  names from commentRules()
 * @return list<string>
 */
function commentViolations(array $rules): array
{
    $violations = [];

    foreach (commentLines() as [$file, $line, $text, $raw]) {
        foreach ($rules as $rule) {
            if (($text !== '' || $rule === 'banner') && commentRules()[$rule]($text, $raw)) {
                $violations[] = "{$file}:{$line}: {$raw}";
            }
        }
    }

    return $violations;
}

it('uses no banner separators made of repeated characters in comments', function () {
    expect(commentViolations(['banner']))->toBe([]);
});

it('does not narrate the flow step by step in comments', function () {
    expect(commentViolations(['step narration']))->toBe([]);
});

it('puts no block end markers, emoji, or empty labels in comments', function () {
    expect(commentViolations(['end marker', 'emoji', 'empty label']))->toBe([]);
});

it('leaves no TODO without a clear task', function () {
    expect(commentViolations(['vague TODO']))->toBe([]);
});

it('finds each kind of comment in the places a source can hold one', function (string $source, string $text) {
    expect(array_values(array_filter(array_column(commentsInSource($source), 1))))->toBe([$text]);
})->with([
    'a PHP line comment' => ["<?php\n// why this is so\n", 'why this is so'],
    'a hash comment' => ["# why this is so\n", 'why this is so'],
    'a doc block line' => ["/**\n * why this is so\n */\n", 'why this is so'],
    'a one-line block comment' => ["/* why this is so */\n", 'why this is so'],
    'a JSX comment' => ["<div>\n    {/* why this is so */}\n</div>\n", 'why this is so'],
    'a Blade comment' => ["{{-- why this is so --}}\n", 'why this is so'],
    'an HTML comment' => ["<!-- why this is so -->\n", 'why this is so'],
    'a comment after a statement' => ["\$a = 1; // why this is so\n", 'why this is so'],
    'a comment after a brace' => ["if (\$a) { // why this is so\n", 'why this is so'],
]);

it('does not mistake a URL, an attribute, a colour, or a shebang for a comment', function (string $source) {
    expect(commentsInSource($source))->toBe([]);
})->with([
    'a URL in a string' => ["\$url = 'https://example.test/a';\n"],
    'a PHP attribute' => ["#[Test]\n"],
    'a hex colour' => ["color: #fff;\n"],
    'a shebang' => ["#!/usr/bin/env bash\n"],
]);

it('has a rule that fires on each kind of empty comment, and lets a real reason through', function (string $rule, string $text, string $raw) {
    expect(commentRules()[$rule]($text, $raw))->toBeTrue();
})->with([
    'a row of equals signs' => ['banner', '======', '// ======'],
    'a row of dashes around a title' => ['banner', '---- Helpers ----', '// ---- Helpers ----'],
    'a block comment made of stars' => ['banner', '', '/**********/'],
    'a doc block line made of stars' => ['banner', '********', '* ********'],
    'Step 1' => ['step narration', 'Step 1: load', '// Step 1: load'],
    'phase 2' => ['step narration', 'phase 2', '// phase 2'],
    'end if' => ['end marker', 'end if', '// end if'],
    'an emoji' => ['emoji', 'done ✅', '// done ✅'],
    'MAIN LOGIC' => ['empty label', 'MAIN LOGIC', '// MAIN LOGIC'],
    'a bare TODO' => ['vague TODO', 'TODO', '// TODO'],
    'a bare FIXME' => ['vague TODO', 'FIXME:', '// FIXME:'],
    'TODO fix this' => ['vague TODO', 'TODO: fix this', '// TODO: fix this'],
    'HACK cleanup' => ['vague TODO', 'HACK cleanup', '// HACK cleanup'],
]);

it('lets a comment that says why, and a TODO with a task, through every rule', function (string $text) {
    foreach (commentRules() as $rule) {
        expect($rule($text, '// '.$text))->toBeFalse();
    }
})->with([
    'a reason' => ['The bank closes the file after 30 days, so the date is kept as given.'],
    'a TODO with a task' => ['TODO: move the limit to the product table once the owner has chosen the rule.'],
    'a sentence with a dash in it' => ['Route-model binding is skipped here on purpose.'],
]);

/**
 * Text files whose content people read or that hold comments: the project's folders and root files, never the installed packages, the
 * build output, or the vendored skills (released as they are and checked by their manifest).
 *
 * @return list<string>
 */
function textFilesForLongDash(): array
{
    $files = [];
    $skip = '#^(vendor|node_modules|\.git|storage|public/build|public/hot|bootstrap/cache|\.claude|\.ai/skills|resources/js/(actions|routes|wayfinder))/#';
    $text = ['php', 'ts', 'tsx', 'js', 'mjs', 'css', 'md', 'json', 'yml', 'yaml', 'xml', 'sh', 'py', 'txt', 'neon', 'conf', 'steps', 'svg', 'html', 'example'];

    // Pruned folder by folder, so the installed packages are never walked.
    $walk = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator(base_path(), FilesystemIterator::SKIP_DOTS),
        fn (SplFileInfo $entry): bool => preg_match($skip, substr($entry->getPathname(), strlen(base_path()) + 1).'/') !== 1,
    ));

    foreach ($walk as $file) {
        $relative = substr($file->getPathname(), strlen(base_path()) + 1);

        if (! $file->isFile() || $file->isLink()) {
            continue;
        }

        $name = $file->getFilename();

        if (in_array($file->getExtension(), $text, true) || in_array($name, ['commit-msg', 'pre-push'], true)) {
            $files[] = $relative;
        }
    }

    sort($files);

    return $files;
}

it('uses no long dash in text the project writes: code, translations, docs, configuration, and screens', function () {
    $dash = "\u{2014}";
    $violations = [];

    foreach (textFilesForLongDash() as $relative) {
        $text = (string) file_get_contents(base_path($relative));

        // AGENTS.md: only the project part; the Boost block is generated by `php artisan boost:update` and never edited by hand.
        if ($relative === 'AGENTS.md') {
            $boost = strpos($text, '<laravel-boost-guidelines>');
            $text = $boost === false ? $text : substr($text, 0, $boost);
        }

        if (str_contains($text, $dash)) {
            $violations[] = $relative;
        }
    }

    expect(textFilesForLongDash())->toContain('AGENTS.md', 'CLAUDE.md', 'memory/PRD.md', 'composer.json', 'devcore.conf', '.github/workflows/ci.yml', 'resources/css/app.css', '.env.example')
        ->and($violations)->toBe([], 'Long dash in project text; use a colon, comma, or parentheses (antislop R-02).');
});
