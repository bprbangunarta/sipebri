#!/usr/bin/env bash
# Proves that the gates really refuse, with real git, in a throwaway repository: it copies the hooks, the scripts, and the settings of
# this project into a temporary folder and tries to break each rule. Run it after installing devcore, and after changing any gate.
#   scripts/selftest.sh
# Exit status 0 only when every case behaves as the rules say. It never touches the project's own repository.
set -uo pipefail

source_root="$(git rev-parse --show-toplevel)"
work="$(mktemp -d)"
trap 'rm -rf "$work"' EXIT
passed=0
failed=0

ok() { passed=$((passed + 1)); printf '  PASS  %s\n' "$1"; }
bad() { failed=$((failed + 1)); printf '  FAIL  %s\n' "$1"; [ -n "${2:-}" ] && printf '        %s\n' "$2"; }

# refuses <description> <expected first word of the message> <command...>: the command must fail and its message must start with the word.
refuses() {
    local description="$1" word="$2"
    shift 2
    local output
    output="$("$@" 2>&1)"
    local status=$?

    if [ "$status" -ne 0 ] && printf '%s\n' "$output" | grep -q "$word"; then ok "$description"; else bad "$description" "expected a refusal that says $word, got status $status: $(printf '%s' "$output" | head -n 3 | tr '\n' ' ')"; fi
}

# accepts <description> <command...>: the command must succeed.
accepts() {
    local description="$1"
    shift
    local output
    output="$("$@" 2>&1)"
    local status=$?

    if [ "$status" -eq 0 ]; then ok "$description"; else bad "$description" "status $status: $(printf '%s' "$output" | head -n 3 | tr '\n' ' ')"; fi
}

new_repo() {
    local dir="$work/$1"
    mkdir -p "$dir/scripts/lib" "$dir/.githooks" "$dir/docs/briefs"
    cp "$source_root/devcore.conf" "$dir/"
    cp "$source_root/scripts/check-brief.sh" "$source_root/scripts/check-weakened.sh" "$source_root/scripts/verify.sh" "$source_root/scripts/check-registry.sh" "$dir/scripts/"
    cp "$source_root/scripts/lib/msg.sh" "$dir/scripts/lib/"
    cp "$source_root/.githooks/commit-msg" "$source_root/.githooks/pre-push" "$dir/.githooks/"
    cp "$source_root/docs/briefs/TEMPLATE.md" "$dir/docs/briefs/"
    printf 'Passing|true\n' > "$dir/scripts/verify.steps"
    # The scaffolding is committed before the hooks are installed, so the first commit is not judged as a change of the project.
    ( cd "$dir" && git init -q -b main . && git config user.email t@example.com && git config user.name Test && git add -A \
        && git commit -q -m "$(printf 'Scaffolding\n\nBrief: none: scaffolding of the throwaway repository')" && git config core.hooksPath .githooks )
    echo "$dir"
}

commit() { git commit -q -m "$1" 2>&1; }

# Back to the last commit, so a refused case leaves nothing staged for the next one.
reset_all() { git reset -q --hard && git clean -fdq; }

echo "Brief gate"
repo="$(new_repo brief)"
cd "$repo" || exit 1
echo '# readme' > README.md
git add -A
accepts 'a commit that touches no code needs no brief' commit 'Add readme'

mkdir -p src && echo 'a = 1' > src/a.txt
git add -A
refuses 'a code commit without a brief is refused' 'BRIEF:' commit 'Add a'
accepts 'a code commit with "Brief: none: <reason>" passes' commit "$(printf 'Add a\n\nBrief: none: tiny first file for the test')"

echo 'b = 2' > src/b.txt
git add -A
refuses 'a reason shorter than ten letters does not count' 'BRIEF:' commit "$(printf 'Add b\n\nBrief: none: short')"
reset_all

cat > docs/briefs/0001-example.md <<'BRIEF'
# Example

Status: approved
Approved: 2026-01-01

## Checklist

- [ ] one item left
BRIEF
git add docs/briefs/0001-example.md
accepts 'adding a brief is not a code commit' commit 'Add brief'
echo 'b = 2' > src/b.txt
git add -A
accepts 'a code commit passes while an approved brief exists' commit 'Add b'

git rm -q docs/briefs/0001-example.md
refuses 'closing a brief that still has "- [ ]" is refused' 'BRIEF:' commit 'Close brief'
git reset -q --hard
sed -i.bak 's/- \[ \]/- [x]/' docs/briefs/0001-example.md && rm -f docs/briefs/0001-example.md.bak
git add -A && git commit -q -m 'Tick the last item'
git rm -q docs/briefs/0001-example.md
accepts 'closing a fully ticked brief passes' commit 'Close brief'

echo 'Rules and skills registry'
repo="$(new_repo registry)"
cd "$repo" || exit 1
mkdir -p .ai/rules .ai/skills/demo
printf '# Rules\n\n| Applies to | Rule file |\n| --- | --- |\n| everything | .ai/rules/quality.md |\n' > .ai/rules/index.md
printf '# Quality\n' > .ai/rules/quality.md
printf 'The guide.\n' > AGENTS.md
printf -- '---\nname: demo\n---\n# Demo skill\n' > .ai/skills/demo/SKILL.md
printf 'The guide names `demo`.\n' > AGENTS.md
accepts 'registered rules and a named skill pass' scripts/check-registry.sh

printf '# A custom rule\n' > .ai/rules/custom.md
refuses 'a rule file that is not in the index is refused' 'REGISTRY:' scripts/check-registry.sh
printf '| custom things | .ai/rules/custom.md |\n' >> .ai/rules/index.md
accepts 'a custom rule with a row in the index passes, like a standard one' scripts/check-registry.sh

printf '| ghost | .ai/rules/ghost.md |\n' >> .ai/rules/index.md
refuses 'an index row for a file that does not exist is refused' 'REGISTRY:' scripts/check-registry.sh
sed -i.bak '/ghost/d' .ai/rules/index.md && rm -f .ai/rules/index.md.bak

printf 'The guide names nothing.\n' > AGENTS.md
refuses 'a skill that the guide does not name is refused' 'REGISTRY:' scripts/check-registry.sh
printf 'The guide names `demo`.\n' > AGENTS.md

printf -- '---\nname: other\n---\n' > .ai/skills/demo/SKILL.md
refuses 'a skill whose name differs from its folder is refused' 'REGISTRY:' scripts/check-registry.sh
printf -- '---\nname: demo\n---\n# Demo skill\n' > .ai/skills/demo/SKILL.md

( cd .ai/skills && if command -v sha256sum >/dev/null 2>&1; then sha256sum demo/SKILL.md; else shasum -a 256 demo/SKILL.md; fi > demo/MANIFEST.sha256 )
accepts 'a vendored skill that matches its manifest passes' scripts/check-registry.sh
printf 'edited by hand\n' >> .ai/skills/demo/SKILL.md
refuses 'a vendored skill edited by hand is refused' 'REGISTRY:' scripts/check-registry.sh

echo 'Weakened-check gate'
repo="$(new_repo weak)"
cd "$repo" || exit 1
mkdir -p tests && printf "it('one', () => {});\nit('two', () => {});\n" > tests/a.test.js
git add -A
accepts 'a new test file passes' commit "$(printf 'Add tests\n\nBrief: none: first tests for the gate check')"

printf "it('one', () => {});\n" > tests/a.test.js
git add -A
refuses 'removing a test case is refused' 'WEAKENED:' commit "$(printf 'Drop a test\n\nBrief: none: pretend cleanup of tests')"
accepts 'removing a test case with a "Weakened:" reason passes' commit "$(printf 'Drop a test\n\nBrief: none: pretend cleanup of tests\nWeakened: the feature it covered was removed')"

printf "it.skip('one', () => {});\n" > tests/a.test.js
git add -A
refuses 'skipping a test is refused' 'WEAKENED:' commit "$(printf 'Skip a test\n\nBrief: none: pretend flaky test fix')"
reset_all

printf "// eslint-disable-next-line no-undef\nlet x = 1;\n" > tests/b.test.js
git add -A
refuses 'suppressing a linter rule is refused' 'WEAKENED:' commit "$(printf 'Silence the linter\n\nBrief: none: pretend lint fix here')"
reset_all
printf "// eslint-disable-next-line no-undef -- weakening-allowed: generated global\nlet x = 1;\n" > tests/b.test.js
git add -A
accepts 'a "weakening-allowed:" marker with a reason passes' commit "$(printf 'Silence the linter\n\nBrief: none: pretend lint fix here')"

git rm -q tests/a.test.js
refuses 'deleting a test file is refused' 'WEAKENED:' commit "$(printf 'Delete tests\n\nBrief: none: pretend cleanup of files')"
reset_all

echo 'Steps cannot be taken out of the gate'
repo="$(new_repo steps)"
cd "$repo" || exit 1
printf 'Passing|true\nSecond|true\n' > scripts/verify.steps
git add -A && git commit -q --no-verify -m 'Two steps'
printf 'Passing|true\n' > scripts/verify.steps
git add -A
refuses 'removing a step from verify.steps is refused' 'WEAKENED:' commit "$(printf 'Drop a step\n\nBrief: none: pretend the step was slow')"
accepts 'removing a step with a "Weakened:" reason passes' commit "$(printf 'Drop a step\n\nBrief: none: the step moved elsewhere\nWeakened: the same check now runs in the build job')"
printf '# only comments now\n' > scripts/verify.steps
git add -A
refuses 'emptying verify.steps is refused too' 'WEAKENED:' commit "$(printf 'Empty it\n\nBrief: none: pretend cleanup of the file')"
reset_all

echo 'The gate (verify.sh and the pre-push hook)'
repo="$(new_repo gate)"
cd "$repo" || exit 1
mkdir -p src && echo 'a = 1' > src/a.txt && git add -A
commit "$(printf 'First\n\nBrief: none: first commit of the gate test')" >/dev/null
accepts 'verify.sh passes when every step passes' scripts/verify.sh

printf 'Passing|true\nFailing|false\n' > scripts/verify.steps
refuses 'verify.sh stops at a failing step' 'VERIFY:' scripts/verify.sh
printf '# nothing\n' > scripts/verify.steps
refuses 'verify.sh refuses to check nothing' 'VERIFY:' scripts/verify.sh
printf 'Passing|true\n' > scripts/verify.steps

git config --unset core.hooksPath
refuses 'verify.sh refuses when the hooks are not installed' 'HOOK:' scripts/verify.sh
git config core.hooksPath .githooks

echo 'b = 2' > src/b.txt && git add -A
git commit -q --no-verify -m 'Sneaky commit without a brief'
refuses 'a commit made with --no-verify is caught later by the gate' 'BRIEF:' env VERIFY_RANGE='HEAD~1..HEAD' scripts/verify.sh

echo 'Push'
bare="$work/remote.git"
git init -q --bare -b main "$bare"
git remote add origin "$bare"
git reset -q --hard HEAD~1
git push -q origin main 2>&1
accepts 'the pre-push hook lets a push through when the gate is green' git push origin main

echo 'c = 3' > src/c.txt && git add -A
commit "$(printf 'Add c\n\nBrief: none: another file for the push test')" >/dev/null
printf 'Failing|false\n' > scripts/verify.steps
refuses 'the pre-push hook stops a push when the gate is red' 'VERIFY:' git push origin main

echo 'Language'
sed -i.bak 's/^DEVCORE_LANG=.*/DEVCORE_LANG=id/' devcore.conf && rm -f devcore.conf.bak
git rm -q --cached src/c.txt 2>/dev/null
echo 'd = 4' > src/d.txt && git add src/d.txt
refuses 'the messages can be Indonesian, and the first word stays the same' 'BRIEF:' commit 'Add d'

echo 'The stamp: a push does not repeat a gate that just passed on the same content'
repo="$(new_repo stamp)"
cd "$repo" || exit 1
ran="$work/stamp-ran"
: > "$ran"
runs() { wc -c < "$ran" | tr -d ' '; }
# steps_ran <description> <expected number of runs of the counted step since the start>
steps_ran() { local got; got="$(runs)"; if [ "$got" = "$2" ]; then ok "$1"; else bad "$1" "expected the steps to have run $2 times, they ran $got times"; fi; }
printf 'Passing|true\nCounted|printf x >> "%s"\n' "$ran" > scripts/verify.steps
mkdir -p src && echo 'a = 1' > src/a.txt && git add -A
commit "$(printf 'First\n\nBrief: none: first commit of the stamp test')" >/dev/null
bare="$work/stamp-remote.git"
git init -q --bare -b main "$bare"
git remote add origin "$bare"
stamp="$(git rev-parse --git-dir)/devcore-verified"

scripts/verify.sh >/dev/null 2>&1
steps_ran 'a manual run runs every step' 1
accepts 'a green run on clean committed content leaves a stamp' test -f "$stamp"
scripts/verify.sh >/dev/null 2>&1
steps_ran 'a manual run never reuses the stamp' 2

out="$(git push origin main 2>&1)"
steps_ran 'a push right after a green run skips the steps' 2
if printf '%s' "$out" | grep -qi 'skipped'; then ok 'the push says the steps were skipped'; else bad 'the push says the steps were skipped' "$out"; fi

echo 'b = 2' > src/b.txt && git add -A
commit "$(printf 'Add b\n\nBrief: none: second file for the stamp test')" >/dev/null
git push -q origin main >/dev/null 2>&1
steps_ran 'a push of a new commit runs the whole gate' 3

echo 'c = 3' > src/c.txt && git add -A
commit "$(printf 'Add c\n\nBrief: none: third file for the stamp test')" >/dev/null
echo 'edited' >> src/a.txt
scripts/verify.sh >/dev/null 2>&1
accepts 'a green run with an uncommitted change to a tracked file leaves no stamp' test ! -f "$stamp"
git checkout -q src/a.txt
echo 'untracked code' > src/untracked.txt
scripts/verify.sh >/dev/null 2>&1
accepts 'a green run with an untracked code file leaves no stamp' test ! -f "$stamp"
rm -f src/untracked.txt
echo junk > .DS_Store && echo doc > notes.docx
scripts/verify.sh >/dev/null 2>&1
accepts 'an untracked file that is not code does not stop the stamp' test -f "$stamp"
before="$(runs)"
git push -q origin main >/dev/null 2>&1
steps_ran 'and the push after it skips the steps' "$before"
rm -f .DS_Store notes.docx

echo 'd = 4' > src/d.txt && git add -A
commit "$(printf 'Add d\n\nBrief: none: fourth file for the stamp test')" >/dev/null
scripts/verify.sh >/dev/null 2>&1
sed -i.bak 's/^time=.*/time=1000/' "$stamp" && rm -f "$stamp.bak"
before="$(runs)"
git push -q origin main >/dev/null 2>&1
steps_ran 'a stamp older than the allowed hours is not trusted' "$((before + 1))"

printf 'Passing|true\nCounted|printf x >> "%s"\nFailing|false\n' "$ran" > scripts/verify.steps
git add -A && git commit -q --no-verify -m "$(printf 'A failing step\n\nBrief: none: a step that fails for the stamp test')"
scripts/verify.sh >/dev/null 2>&1
accepts 'a run that fails removes the stamp' test ! -f "$stamp"
git reset -q --hard HEAD~1

echo 'e = 5' > src/e.txt && git add -A
git commit -q --no-verify -m 'Sneaky commit without a brief'
VERIFY_RANGE='HEAD..HEAD' scripts/verify.sh >/dev/null 2>&1
accepts 'a green run exists for the content that holds the sneaky commit' test -f "$stamp"
before="$(runs)"
refuses 'a stamp does not excuse a pushed commit that has no brief' 'BRIEF:' git push origin main
steps_ran 'and the steps did not need to run for that refusal' "$before"

echo
printf '%s passed, %s failed\n' "$passed" "$failed"
[ "$failed" -eq 0 ]
