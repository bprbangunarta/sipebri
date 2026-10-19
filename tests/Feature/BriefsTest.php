<?php

/*
 * Work briefs (`docs/briefs/`, AGENTS.md section 3): a running brief must be complete, and a finished one must already be deleted. The folder
 * holds only TEMPLATE.md when no work is running.
 */

/** The sections of a brief: the template is English (devcore); a brief written from the earlier Indonesian template stays valid until it is closed. */
const BRIEF_SECTIONS = [
    ['## Goal', '## Acceptance criteria', '## Out of scope', '## Assumptions and open questions', '## Checklist'],
    ['## Tujuan', '## Kriteria penerimaan', '## Di luar cakupan', '## Asumsi dan pertanyaan terbuka', '## Daftar periksa'],
];

/** @return list<string> the headings of the first section set that the text fully holds, else the English set */
function briefHeadings(string $text): array
{
    foreach (BRIEF_SECTIONS as $headings) {
        if (array_filter($headings, fn (string $heading): bool => ! str_contains($text, $heading)) === []) {
            return $headings;
        }
    }

    return BRIEF_SECTIONS[0];
}

/** @return list<string> */
function activeBriefs(): array
{
    return array_values(array_filter(glob(base_path('docs/briefs/*.md')) ?: [], fn (string $file): bool => basename($file) !== 'TEMPLATE.md'));
}

it('keeps a brief template with every required section and one checklist', function () {
    $template = (string) file_get_contents(base_path('docs/briefs/TEMPLATE.md'));

    expect($template)->toContain("\nStatus: draft\n");

    foreach (BRIEF_SECTIONS[0] as $heading) {
        expect($template)->toContain($heading);
    }

    expect(preg_match_all('/^- \[ \]/m', $template))->toBeGreaterThanOrEqual(15);
});

/**
 * Everything wrong with one brief, as sentences. Kept apart from the file loop so each rule can be proven on a sample text.
 *
 * @return list<string>
 */
function briefProblems(string $name, string $text): array
{
    $problems = [];

    if (preg_match('/^\d{4}-[a-z0-9-]+\.md$/', $name) !== 1) {
        $problems[] = "{$name}: the name must be NNNN-short-title.md";
    }

    if (preg_match('/^Status: (draft|approved)$/m', $text) !== 1) {
        $problems[] = "{$name}: no 'Status: draft' or 'Status: approved' line";
    }

    if (preg_match('/^Status: approved$/m', $text) === 1) {
        $approved = preg_match('/^Approved: (\d{4}-\d{2}-\d{2})$/m', $text, $date) === 1 ? DateTime::createFromFormat('!Y-m-d', $date[1]) : false;

        if ($approved === false || $approved->format('Y-m-d') !== $date[1]) {
            $problems[] = "{$name}: approved brief without a real 'Approved: YYYY-MM-DD' date";
        }
    }

    foreach (briefHeadings($text) as $heading) {
        if (preg_match('/^'.preg_quote($heading, '/').'$/m', $text) !== 1) {
            $problems[] = "{$name}: section '{$heading}' is missing";
        }
    }

    if (preg_match_all('/^- \[[ x]\]/m', $text) < 2) {
        $problems[] = "{$name}: acceptance criteria and checklist must hold '- [ ]' items";
    }

    preg_match_all('/^- \[x\].*N\/A(?!: .{10,})/m', $text, $noReason);

    if ($noReason[0] !== []) {
        $problems[] = "{$name}: N/A item without a reason of 10 characters or more: ".$noReason[0][0];
    }

    return $problems;
}

function sampleBrief(string $replace = '', string $with = ''): string
{
    $brief = "# Sample\n\nStatus: approved\nApproved: 2026-10-20\n\n## Goal\nx\n## Acceptance criteria\n- [ ] a\n## Out of scope\nx\n## Assumptions and open questions\nx\n## Checklist\n- [ ] b\n";

    return $replace === '' ? $brief : str_replace($replace, $with, $brief);
}

it('keeps every running brief complete: name, status, approval date, required sections, and checklist items', function () {
    $problems = [];

    foreach (activeBriefs() as $file) {
        $problems = [...$problems, ...briefProblems(basename($file), (string) file_get_contents($file))];
    }

    expect($problems)->toBe([]);
});

it('accepts a complete brief and refuses each way a brief can be incomplete', function (string $name, string $text, string $expected) {
    expect(briefProblems($name, $text))->toBe($expected === '' ? [] : [$name.': '.$expected]);
})->with([
    'complete' => ['0004-sample.md', sampleBrief(), ''],
    'complete, Indonesian headings' => ['0004-sample.md', str_replace(['## Goal', '## Acceptance criteria', '## Out of scope', '## Assumptions and open questions', '## Checklist'], BRIEF_SECTIONS[1], sampleBrief()), ''],
    'name without a number' => ['sample.md', sampleBrief(), 'the name must be NNNN-short-title.md'],
    'name with capitals' => ['0004-Sample.md', sampleBrief(), 'the name must be NNNN-short-title.md'],
    'no status line' => ['0004-sample.md', sampleBrief("Status: approved\n", ''), "no 'Status: draft' or 'Status: approved' line"],
    'unknown status' => ['0004-sample.md', sampleBrief('Status: approved', 'Status: done'), "no 'Status: draft' or 'Status: approved' line"],
    'approved without a date' => ['0004-sample.md', sampleBrief("Approved: 2026-10-20\n", ''), "approved brief without a real 'Approved: YYYY-MM-DD' date"],
    'approved with a date that does not exist' => ['0004-sample.md', sampleBrief('2026-10-20', '2026-13-45'), "approved brief without a real 'Approved: YYYY-MM-DD' date"],
    'approved with a date in another order' => ['0004-sample.md', sampleBrief('2026-10-20', '20-10-2026'), "approved brief without a real 'Approved: YYYY-MM-DD' date"],
    'a section missing' => ['0004-sample.md', sampleBrief("## Out of scope\n", ''), "section '## Out of scope' is missing"],
    'a section only mentioned inside a deeper heading' => ['0004-sample.md', sampleBrief('## Goal', '### Goal'), "section '## Goal' is missing"],
    'a section indented' => ['0004-sample.md', sampleBrief('## Goal', ' ## Goal'), "section '## Goal' is missing"],
    'no checklist items' => ['0004-sample.md', sampleBrief("- [ ] a\n", '')."\n", "acceptance criteria and checklist must hold '- [ ]' items"],
    'N/A without a reason' => ['0004-sample.md', sampleBrief('- [ ] b', '- [x] b N/A'), 'N/A item without a reason of 10 characters or more: - [x] b N/A'],
    'N/A with a one-word reason' => ['0004-sample.md', sampleBrief('- [ ] b', '- [x] b N/A: nope'), 'N/A item without a reason of 10 characters or more: - [x] b N/A'],
    'N/A with a reason' => ['0004-sample.md', sampleBrief('- [ ] b', '- [x] b N/A: nothing is shown on screen'), ''],
]);

it('does not let briefs whose items are all done pile up: they are deleted in the closing commit', function () {
    $finished = array_values(array_filter(activeBriefs(), function (string $file): bool {
        $text = (string) file_get_contents($file);

        return preg_match_all('/^- \[x\]/m', $text) > 0 && preg_match('/^- \[ \]/m', $text) !== 1;
    }));

    expect(array_map('basename', $finished))->toBe([]);
});
