#!/usr/bin/env bash
# Detector of weakened checks. It binds every agent because what it reads is the diff. Two ways to call it:
#   check-weakened.sh --staged <message file>   for the commit being made (the commit-msg hook)
#   check-weakened.sh --range <rev-list args>   for commits about to be pushed (scripts/verify.sh): one range "a..b", or several
#                                               arguments such as "HEAD --not --remotes" (every commit that is on no remote yet)
# What it refuses in a diff: a skipped or focused test, a suppression of the static analysis, type checker, or linter, a new analysis
# baseline, a deleted test file, and a test file that loses more cases than it gains. It passes only with one of:
#   - a "weakening-allowed: <reason, at least 10 letters>" marker on the same line (for one line), or
#   - a "Weakened: <reason, at least 10 letters>" line in the commit message (for a commit that weakens on purpose, for example a
#     feature removed together with its tests).
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"
# shellcheck source=/dev/null
. ./devcore.conf
# shellcheck source=/dev/null
. ./scripts/lib/msg.sh

empty_tree='4b825dc642cb6eb9a060e54bf8d69288fbee4904'
failed=0
label_suppress="$(dc_msg weak_suppress)"
label_skipped="$(dc_msg weak_skipped)"
label_step="$(dc_msg weak_step_removed)"
label_allowed_why="$(dc_msg weak_allowed_why '%s')"

# scan <commit message> <git diff arguments...>
scan() {
    local message="$1"
    shift

    local reason
    reason="$(printf '%s\n' "$message" | sed -n 's/^Weakened: \(.*\)$/\1/p' | head -n 1)"
    if [ "${#reason}" -lt 10 ]; then
        reason=''
    fi

    local findings=''
    local notices=''

    local diff_out
    diff_out="$(git -c core.quotepath=false diff -U0 --no-color --no-ext-diff "$@" | awk -v scan_re="$WEAKENED_SCAN" -v test_re="$TEST_PATHS" -v sup="$label_suppress" -v skp="$label_skipped" -v stp="$label_step" -v allowed_fmt="$label_allowed_why" '
        function scanned(f) {
            if (f ~ /^scripts\/check-weakened\.sh$/ || f ~ /WeakenedCheckGate/) return 0
            return (f ~ scan_re)
        }
        function istest(f) { return (f ~ test_re) }
        /^\+\+\+ / { f = $0; sub(/^\+\+\+ "?(b\/)?/, "", f); sub(/"$/, "", f); if (f == "/dev/null") f = ""; next }
        /^--- / { next }
        /^-/ {
            # A step taken out of the gate is the quietest way to get green.
            if (f == "scripts/verify.steps" && substr($0, 2) !~ /^[[:space:]]*(#|$)/) printf "F\t%s  %s\n", f, stp
            next
        }
        /^@@/ { match($0, /\+[0-9]+/); ln = substr($0, RSTART + 1, RLENGTH - 1) + 0; next }
        /^\+/ {
            l = substr($0, 2); n = ln; ln++
            if (f == "" || !scanned(f)) next
            p = index(l, "weakening-allowed: ")
            allowed = (p > 0 && length(substr(l, p + 19)) >= 10)
            what = ""
            if (l ~ /@phpstan-ignore|@ts-ignore|@ts-expect-error|eslint-disable|oxlint-disable|@codeCoverageIgnore|phpcs:ignore|@psalm-suppress|# *noqa|# *type: *ignore|nolint/) what = sup
            if (istest(f) && l ~ /->skip\(|markTestSkipped|(it|test|describe)\.(skip|only)\(|xit\(|xdescribe\(|\.only\(|->only\(|@pytest\.mark\.skip|t\.Skip\(|#\[Ignore\]/) what = skp
            if (what != "") {
                if (allowed) { why = substr(l, p + 19); k = index(allowed_fmt, "%s"); printf "A\t%s:%d  %s %s%s%s\n", f, n, what, substr(allowed_fmt, 1, k - 1), why, substr(allowed_fmt, k + 2) }
                else printf "F\t%s:%d  %s\n", f, n, what
            }
            next
        }
    ')"
    # Count the test cases that went missing per file (needs the old file name, so it is done separately).
    local file removed added
    while IFS= read -r -d '' file; do
        [ -z "$file" ] && continue
        if ! printf '%s\n' "$file" | grep -qE "$TEST_PATHS"; then continue; fi
        case "$file" in *WeakenedCheckGate*) continue ;; esac
        removed="$(git diff -U0 --no-color --no-ext-diff "$@" -- ":(literal)${file}" | grep -cE '^-[[:space:]]*(it|test)\(|^-.*#\[Test\]|^-[[:space:]]*def test_|^-[[:space:]]*func Test' || true)"
        added="$(git diff -U0 --no-color --no-ext-diff "$@" -- ":(literal)${file}" | grep -cE '^\+[[:space:]]*(it|test)\(|^\+.*#\[Test\]|^\+[[:space:]]*def test_|^\+[[:space:]]*func Test' || true)"
        if [ "$removed" -gt "$added" ]; then
            findings="${findings}${file}  $(dc_msg weak_lost_cases "$removed" "$added")"$'\n'
        fi
    done < <(git diff --name-only -z --diff-filter=M "$@")

    while IFS= read -r -d '' file; do
        [ -z "$file" ] && continue
        if printf '%s\n' "$file" | grep -qE "$TEST_PATHS"; then
            findings="${findings}${file}  $(dc_msg weak_deleted_test)"$'\n'
        fi
        [ "$file" = 'scripts/verify.steps' ] && findings="${findings}${file}  ${label_step}"$'\n'
    done < <(git diff --name-only -z --diff-filter=D "$@")

    while IFS= read -r -d '' file; do
        [ -z "$file" ] && continue
        findings="${findings}${file}  $(dc_msg weak_baseline)"$'\n'
    done < <(git diff --name-only -z --diff-filter=A "$@" | tr '\0' '\n' | grep -E 'phpstan-baseline|baseline\.neon' | tr '\n' '\0' || true)

    while IFS=$'\t' read -r kind text; do
        [ -z "$kind" ] && continue
        if [ "$kind" = 'A' ]; then
            notices="${notices}${text}"$'\n'
        else
            findings="${findings}${text}"$'\n'
        fi
    done <<< "$diff_out"

    [ -n "$notices" ] && printf '%s\n%s' "$(dc_msg weak_notice)" "$notices" >&2

    if [ -n "$findings" ]; then
        if [ -n "$reason" ]; then
            printf '%s\n%s' "$(dc_msg weak_by_message "$reason")" "$findings" >&2
        else
            printf '%s\n%s' "$(dc_msg weak_refused)" "$findings" >&2
            printf '           %s\n' "$(dc_msg weak_hint_1)" >&2
            printf '           %s\n' "$(dc_msg weak_hint_2)" >&2
            failed=1
        fi
    fi
}

case "${1:-}" in
    --staged)
        # A merge and a revert are recognised by the state of git, never by the first word of the message: anyone can write a message.
        for state in MERGE_HEAD REVERT_HEAD; do
            [ -f "$(git rev-parse --git-path "$state")" ] && exit 0
        done

        scan "$(cat "${2:?commit message file}")" --cached
        ;;
    --range)
        shift
        [ "$#" -gt 0 ] || { dc_msg weak_usage >&2; echo >&2; exit 2; }
        for commit in $(git rev-list --reverse "$@"); do
            # A merge commit (two parents or more) carries no change of its own.
            [ "$(git rev-list --parents -n 1 "$commit" | wc -w)" -gt 2 ] && continue
            parent="$(git rev-parse --quiet --verify "${commit}^" || echo "$empty_tree")"
            scan "$(git log -1 --format=%B "$commit")" "$parent" "$commit"
        done
        ;;
    *)
        dc_msg weak_usage >&2
        echo >&2
        exit 2
        ;;
esac

exit "$failed"
