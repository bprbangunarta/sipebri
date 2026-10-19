#!/usr/bin/env bash
# Detects weakened checks (.ai/rules/php.md, "Produksi, bukan prototipe"). It binds every agent because it reads the diff.
# Two ways to call it:
#   check-weakened.sh --staged <message-file>   for the commit being made (commit-msg hook)
#   check-weakened.sh --range <rev-list args>   for the commits about to be pushed (scripts/verify.sh): one range "a..b", or several
#                                               arguments such as "HEAD --not --remotes" (every commit not yet on any remote)
# Rejected in the diff: skipped or focused tests, PHPStan/TypeScript/linter suppressions, a new PHPStan baseline, deleted test files, and
# test files that lose more cases than they gain. Allowed only with one of:
#   - a "weakening-allowed: <reason, at least 10 letters>" marker on the same line (for that line), or
#   - a "Weakened: <reason, at least 10 letters>" line in the commit message (for a commit that weakens on purpose, e.g. a feature removed
#     together with its tests).
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

empty_tree='4b825dc642cb6eb9a060e54bf8d69288fbee4904'
failed=0

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
    diff_out="$(git -c core.quotepath=false diff -U0 --no-color --no-ext-diff "$@" | awk '
        function scanned(f) {
            if (f == "scripts/check-weakened.sh" || f == "tests/Feature/WeakenedCheckGateTest.php") return 0
            return (f ~ /^(app|bootstrap|config|database|lang|resources|routes|scripts|tests|\.githooks)\// || f ~ /^(phpstan|vite\.config|tsconfig|pint)/)
        }
        function istest(f) { return (f ~ /^tests\// || f ~ /\.test\.(ts|tsx)$/) }
        /^\+\+\+ / { f = $0; sub(/^\+\+\+ "?(b\/)?/, "", f); sub(/"$/, "", f); if (f == "/dev/null") f = ""; next }
        /^--- / { next }
        /^@@/ { match($0, /\+[0-9]+/); ln = substr($0, RSTART + 1, RLENGTH - 1) + 0; next }
        /^\+/ {
            l = substr($0, 2); n = ln; ln++
            if (f == "" || !scanned(f)) next
            p = index(l, "weakening-allowed: ")
            allowed = (p > 0 && length(substr(l, p + 19)) >= 10)
            what = ""
            if (l ~ /@phpstan-ignore|@ts-ignore|@ts-expect-error|eslint-disable|oxlint-disable|@codeCoverageIgnore|phpcs:ignore|@psalm-suppress/) what = "penekanan pemeriksa"
            if (istest(f) && l ~ /->skip\(|markTestSkipped|(it|test|describe)\.(skip|only)\(|xit\(|xdescribe\(|\.only\(|->only\(/) what = "tes dilewati atau difokuskan"
            if (what != "") {
                if (allowed) printf "A\t%s:%d  %s (diizinkan: %s)\n", f, n, what, substr(l, p + 19)
                else printf "F\t%s:%d  %s\n", f, n, what
            }
            next
        }
    ')"
    # Lost test cases per file, counted separately because it needs the old file name.
    local file removed added
    while IFS= read -r -d '' file; do
        [ -z "$file" ] && continue
        if ! printf '%s\n' "$file" | grep -qE '^tests/|\.test\.(ts|tsx)$'; then continue; fi
        if [ "$file" = 'tests/Feature/WeakenedCheckGateTest.php' ]; then continue; fi
        removed="$(git diff -U0 --no-color --no-ext-diff "$@" -- ":(literal)${file}" | grep -cE '^-[[:space:]]*(it|test)\(|^-.*#\[Test\]' || true)"
        added="$(git diff -U0 --no-color --no-ext-diff "$@" -- ":(literal)${file}" | grep -cE '^\+[[:space:]]*(it|test)\(|^\+.*#\[Test\]' || true)"
        if [ "$removed" -gt "$added" ]; then
            findings="${findings}${file}  kehilangan kasus tes: ${removed} dihapus, ${added} ditambah"$'\n'
        fi
    done < <(git diff --name-only -z --diff-filter=M "$@")

    while IFS= read -r -d '' file; do
        [ -z "$file" ] && continue
        if printf '%s\n' "$file" | grep -qE '^tests/|\.test\.(ts|tsx)$'; then
            findings="${findings}${file}  berkas tes dihapus"$'\n'
        fi
    done < <(git diff --name-only -z --diff-filter=D "$@")

    while IFS= read -r -d '' file; do
        [ -z "$file" ] && continue
        findings="${findings}${file}  baseline PHPStan baru (perbaiki penyebabnya, jangan disembunyikan)"$'\n'
    done < <(git diff --name-only -z --diff-filter=A "$@" | tr '\0' '\n' | grep -E 'phpstan-baseline|baseline\.neon' | tr '\n' '\0' || true)

    while IFS=$'\t' read -r kind text; do
        [ -z "$kind" ] && continue
        if [ "$kind" = 'A' ]; then
            notices="${notices}${text}"$'\n'
        else
            findings="${findings}${text}"$'\n'
        fi
    done <<< "$diff_out"

    [ -n "$notices" ] && printf 'PELEMAHAN (diizinkan, tinjau alasannya):\n%s' "$notices" >&2

    if [ -n "$findings" ]; then
        if [ -n "$reason" ]; then
            printf 'PELEMAHAN (diizinkan oleh pesan commit "Weakened: %s"):\n%s' "$reason" "$findings" >&2
        else
            printf 'PELEMAHAN: perubahan ini membuat pemeriksaan lebih lemah:\n%s' "$findings" >&2
            echo '           Perbaiki penyebabnya. Bila memang disengaja, beri alasan: penanda "weakening-allowed: <alasan>" pada barisnya,' >&2
            echo '           atau baris "Weakened: <alasan>" di pesan commit. Jangan dilewati dengan --no-verify.' >&2
            failed=1
        fi
    fi
}

case "${1:-}" in
    --staged)
        # Merges and reverts are recognised by git's state, not by the first word of the message: anyone can write a message.
        for state in MERGE_HEAD REVERT_HEAD; do
            [ -f "$(git rev-parse --git-path "$state")" ] && exit 0
        done

        scan "$(cat "${2:?berkas pesan commit}")" --cached
        ;;
    --range)
        shift
        [ "$#" -gt 0 ] || { echo 'pemakaian: check-weakened.sh --range <argumen rev-list>' >&2; exit 2; }
        for commit in $(git rev-list --reverse "$@"); do
            # A merge commit (two or more parents) carries no change of its own.
            [ "$(git rev-list --parents -n 1 "$commit" | wc -w)" -gt 2 ] && continue
            parent="$(git rev-parse --quiet --verify "${commit}^" || echo "$empty_tree")"
            scan "$(git log -1 --format=%B "$commit")" "$parent" "$commit"
        done
        ;;
    *)
        echo 'pemakaian: check-weakened.sh --staged <berkas-pesan> | --range <rentang>' >&2
        exit 2
        ;;
esac

exit "$failed"
