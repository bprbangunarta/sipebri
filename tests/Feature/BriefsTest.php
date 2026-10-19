<?php

/*
 * Work briefs (`docs/briefs/`, AGENTS.md section 3): a running brief must be complete, and a finished one must already be deleted. The folder
 * holds only TEMPLATE.md when no work is running.
 */

const BRIEF_HEADINGS = ['## Tujuan', '## Kriteria penerimaan', '## Di luar cakupan', '## Asumsi dan pertanyaan terbuka', '## Daftar periksa'];

/** @return list<string> */
function activeBriefs(): array
{
    return array_values(array_filter(glob(base_path('docs/briefs/*.md')) ?: [], fn (string $file): bool => basename($file) !== 'TEMPLATE.md'));
}

it('keeps a brief template with every required section and one checklist', function () {
    $template = (string) file_get_contents(base_path('docs/briefs/TEMPLATE.md'));

    expect($template)->toContain("\nStatus: draft\n");

    foreach (BRIEF_HEADINGS as $heading) {
        expect($template)->toContain($heading);
    }

    expect(preg_match_all('/^- \[ \]/m', $template))->toBeGreaterThanOrEqual(15);
});

it('keeps every running brief complete: name, status, approval date, required sections, and checklist items', function () {
    $problems = [];

    foreach (activeBriefs() as $file) {
        $name = basename($file);
        $text = (string) file_get_contents($file);

        if (preg_match('/^\d{4}-[a-z0-9-]+\.md$/', $name) !== 1) {
            $problems[] = "{$name}: the name must be NNNN-short-title.md";
        }

        if (preg_match('/^Status: (draft|approved)$/m', $text) !== 1) {
            $problems[] = "{$name}: no 'Status: draft' or 'Status: approved' line";
        }

        if (str_contains($text, "\nStatus: approved\n") && preg_match('/^Approved: \d{4}-\d{2}-\d{2}$/m', $text) !== 1) {
            $problems[] = "{$name}: approved brief without an 'Approved: YYYY-MM-DD' line";
        }

        foreach (BRIEF_HEADINGS as $heading) {
            if (! str_contains($text, $heading)) {
                $problems[] = "{$name}: section '{$heading}' is missing";
            }
        }

        if (preg_match_all('/^- \[[ x]\]/m', $text) < 2) {
            $problems[] = "{$name}: acceptance criteria and checklist must hold '- [ ]' items";
        }

        preg_match_all('/^- \[x\].*N\/A(?!: .{5,})/m', $text, $noReason);

        if ($noReason[0] !== []) {
            $problems[] = "{$name}: N/A item without a reason: ".$noReason[0][0];
        }
    }

    expect($problems)->toBe([]);
});

it('does not let briefs whose items are all done pile up: they are deleted in the closing commit', function () {
    $finished = array_values(array_filter(activeBriefs(), function (string $file): bool {
        $text = (string) file_get_contents($file);

        return preg_match_all('/^- \[x\]/m', $text) > 0 && preg_match('/^- \[ \]/m', $text) !== 1;
    }));

    expect(array_map('basename', $finished))->toBe([]);
});
