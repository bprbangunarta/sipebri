<?php

use Illuminate\Support\Facades\Process;

/*
 * The brief gate (`scripts/check-brief.sh`, installed as the commit-msg hook) runs for real in a temporary git repository, so the rule is
 * proven to hold for any commit and not only written down in AGENTS.md.
 */

const APPROVED_BRIEF = "# Example\n\nStatus: approved\nApproved: 2026-10-17\n\n## Daftar periksa\n\n- [x] one\n";

/**
 * Temporary repository with one initial commit and the gate script inside.
 */
function gateRepo(): string
{
    $dir = sys_get_temp_dir().'/gate-'.bin2hex(random_bytes(4));
    mkdir($dir.'/scripts', 0777, true);
    copy(base_path('scripts/check-brief.sh'), $dir.'/scripts/check-brief.sh');
    chmod($dir.'/scripts/check-brief.sh', 0755);

    foreach ([['git', 'init', '-q'], ['git', 'config', 'user.email', 't@example.test'], ['git', 'config', 'user.name', 'Test']] as $command) {
        Process::path($dir)->run($command)->throw();
    }

    gateWrite($dir, 'README.md', "start\n");
    Process::path($dir)->run('git add -A && git commit -q -m start')->throw();

    return $dir;
}

function gateWrite(string $dir, string $path, string $content): void
{
    @mkdir(dirname($dir.'/'.$path), 0777, true);
    file_put_contents($dir.'/'.$path, $content);
}

/**
 * Runs the gate for the staged change with the given commit message.
 *
 * @return array{0: bool, 1: string} whether it passed, and its error output
 */
function runGate(string $dir, string $message): array
{
    file_put_contents($dir.'/MSG', $message);
    Process::path($dir)->run('git add -A')->throw();
    $result = Process::path($dir)->run(['bash', 'scripts/check-brief.sh', 'MSG']);

    return [$result->successful(), $result->errorOutput()];
}

it('lets a change that is not code through without a brief', function () {
    $dir = gateRepo();
    gateWrite($dir, 'memory/PRD.md', "notes\n");

    expect(runGate($dir, 'PRD notes')[0])->toBeTrue();
});

it('rejects a code commit without an approved brief and without an exemption line', function () {
    $dir = gateRepo();
    gateWrite($dir, 'app/Example.php', "<?php\n");

    [$ok, $error] = runGate($dir, 'New feature');

    expect($ok)->toBeFalse()->and($error)->toContain('BRIEF')->toContain('Brief: none');
});

it('treats translations, CI workflows, and root tool configs as code', function (string $path) {
    $dir = gateRepo();
    gateWrite($dir, $path, "x\n");

    expect(runGate($dir, 'Change')[0])->toBeFalse($path);
})->with(['lang/id/validation.php', '.github/workflows/tests.yml', 'phpstan.neon', 'composer.json', 'vite.config.ts']);

it('passes a code commit when an approved brief with its approval date exists', function () {
    $dir = gateRepo();
    gateWrite($dir, 'docs/briefs/0001-example.md', APPROVED_BRIEF);
    gateWrite($dir, 'app/Example.php', "<?php\n");

    expect(runGate($dir, 'New feature')[0])->toBeTrue();
});

it('rejects a brief that is still a draft or that has no approval date', function (string $brief) {
    $dir = gateRepo();
    gateWrite($dir, 'docs/briefs/0001-example.md', $brief);
    gateWrite($dir, 'app/Example.php', "<?php\n");

    expect(runGate($dir, 'New feature')[0])->toBeFalse();
})->with([
    'draft' => ["# Example\n\nStatus: draft\nApproved:\n\n- [ ] one\n"],
    'no date' => ["# Example\n\nStatus: approved\n\n- [x] one\n"],
]);

it('accepts a Brief: none line with a reason and rejects a reason that is too short', function () {
    $dir = gateRepo();
    gateWrite($dir, 'app/Example.php', "<?php\n");

    expect(runGate($dir, "Fix a typo\n\nBrief: none: one misspelled word in an error message")[0])->toBeTrue()
        ->and(runGate($dir, "Fix\n\nBrief: none: small")[0])->toBeFalse();
});

it('lets a real merge or revert through, but not a message that only pretends to be one', function () {
    $dir = gateRepo();
    gateWrite($dir, 'app/Example.php', "<?php\n");

    // The first word of a message does not open the gate: anyone can write "Merge" or "Revert".
    foreach (["Merge branch 'x'", 'Revert "New feature"', 'fixup! something', 'squash! something'] as $message) {
        expect(runGate($dir, $message)[0])->toBeFalse($message);
    }

    foreach (['MERGE_HEAD', 'REVERT_HEAD'] as $state) {
        file_put_contents($dir.'/.git/'.$state, trim(Process::path($dir)->run('git rev-parse HEAD')->output())."\n");

        expect(runGate($dir, 'Real merge')[0])->toBeTrue($state);

        unlink($dir.'/.git/'.$state);
    }
});

it('refuses to close a brief with unfinished items and allows it once every item is done', function () {
    $dir = gateRepo();
    gateWrite($dir, 'docs/briefs/0001-example.md', "# Example\n\nStatus: approved\nApproved: 2026-10-17\n\n- [x] one\n- [ ] two\n");
    Process::path($dir)->run('git add -A && git commit -q -m brief')->throw();

    unlink($dir.'/docs/briefs/0001-example.md');
    [$ok, $error] = runGate($dir, 'Done');

    expect($ok)->toBeFalse()->and($error)->toContain('belum selesai');

    Process::path($dir)->run('git reset -q')->throw();
    gateWrite($dir, 'docs/briefs/0001-example.md', "# Example\n\nStatus: approved\nApproved: 2026-10-17\n\n- [x] one\n- [x] two N/A: does not apply here\n");
    Process::path($dir)->run('git add -A && git commit -q -m partly-done')->throw();

    unlink($dir.'/docs/briefs/0001-example.md');
    gateWrite($dir, 'app/Example.php', "<?php\n");

    expect(runGate($dir, 'Closing')[0])->toBeTrue();
});

it('counts code files whose names hold non-ASCII letters, quotes, or spaces instead of letting them through without a brief', function (string $name) {
    $dir = gateRepo();
    gateWrite($dir, "app/{$name}", "<?php\n");

    [$ok, $error] = runGate($dir, 'No brief');

    expect($ok)->toBeFalse()->and($error)->toContain('tidak ada brief yang disetujui');
})->with(['é-ñ.php', 'double"quote.php', "single'quote.php", 'has space.php', 'back\\slash.php', '-leading-dash.php']);

it('still sees an unfinished brief being closed when another file with an odd name is deleted alongside', function () {
    $dir = gateRepo();
    gateWrite($dir, 'docs/briefs/0001-example.md', "# Example\n\nStatus: approved\nApproved: 2026-10-17\n\n- [ ] two\n");
    gateWrite($dir, 'docs/é odd.md', "content\n");
    Process::path($dir)->run('git add -A && git commit -q -m brief')->throw();

    unlink($dir.'/docs/briefs/0001-example.md');
    unlink($dir.'/docs/é odd.md');

    [$ok, $error] = runGate($dir, 'Done');

    expect($ok)->toBeFalse()->and($error)->toContain('belum selesai');
});
