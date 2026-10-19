#!/usr/bin/env bash
# The brief gate (see the workflow section of the agent guide). Called by the commit-msg hook with one argument, the commit message file.
# It binds every agent, because a git hook does not care who presses commit. Two rules:
#   1. A commit that touches code needs a brief with Status: approved in docs/briefs/, or a "Brief: none: <reason>" line in its message
#      (for a small fix, a text change, or a change that was already discussed).
#   2. A brief may only be deleted (closed) when its last version has no "- [ ]" item left.
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"
# shellcheck source=/dev/null
. ./devcore.conf
# shellcheck source=/dev/null
. ./scripts/lib/msg.sh

code_paths="$CODE_PATHS"

# After the fact: for commits that already exist (verify.sh and CI), when the commit-msg hook was skipped. Every code commit (not a merge)
# must name its brief in the message: "Brief: NNNN-title" or "Brief: none: <reason>". The content of the brief itself can only be judged
# when the commit is made.
if [ "${1:-}" = '--range' ]; then
    shift
    [ "$#" -gt 0 ] || { dc_msg range_usage >&2; echo >&2; exit 2; }
    missing=0

    for commit in $(git rev-list --reverse "$@"); do
        [ "$(git rev-list --parents -n 1 "$commit" | wc -w)" -gt 2 ] && continue

        # The output is captured first and read with a here-string: a `grep -q` that stops at the first match would kill the command
        # upstream of a pipe with SIGPIPE, and with `pipefail` that reads as "no match".
        files="$(git diff-tree --root --no-commit-id --name-only -r -z "$commit" | tr '\0' '\n')"
        message="$(git log -1 --format=%B "$commit")"

        if grep -qE "$code_paths" <<< "$files"; then
            if ! grep -qE '^Brief: (none: .{10,}|[0-9]{4}-[a-z0-9-]+)' <<< "$message"; then
                dc_msg brief_range "$(git log -1 --format='%h %s' "$commit")" >&2
                echo >&2
                missing=1
            fi
        fi
    done

    if [ "$missing" != 0 ]; then
        printf '       ' >&2
        dc_msg brief_range_hint "$GUIDE" >&2
        echo >&2
    fi
    exit "$missing"
fi

msg_file="${1:?commit message file}"

# A merge and a revert restore a state that was approved before. They are recognised by the state of git (MERGE_HEAD, REVERT_HEAD),
# never by the first word of the message: anyone can write a message, also to get past this gate.
for state in MERGE_HEAD REVERT_HEAD; do
    [ -f "$(git rev-parse --git-path "$state")" ] && exit 0
done

brief_path='^docs/briefs/[0-9]{4}-[a-z0-9-]+\.md$'

fail() {
    printf '%s\n' "$1" >&2
    printf '       ' >&2
    dc_msg brief_hint "$GUIDE" >&2
    echo >&2
    exit 1
}

closing_approved=0

if git rev-parse --verify --quiet HEAD >/dev/null; then
    # File names are read with a NUL separator: without it git puts a name with a quote or a non-ASCII letter inside quotes, which does
    # not match the path pattern below, so the file would pass uncounted.
    while IFS= read -r -d '' deleted; do
        [[ "$deleted" =~ $brief_path ]] || continue
        last="$(git show "HEAD:${deleted}")"

        if grep -qE '^[[:space:]]*- \[ \]' <<< "$last"; then
            fail "$(dc_msg brief_open "$deleted")"
        fi

        if grep -qE '^Status: approved$' <<< "$last"; then
            closing_approved=1
        fi
    done < <(git diff --cached --name-only -z --diff-filter=D)
fi

staged_code="$(git diff --cached --name-only -z --diff-filter=ACMRD | tr '\0' '\n' | grep -E "$code_paths" || true)"

if [ -n "$staged_code" ]; then
    has_brief="$closing_approved"

    for brief in docs/briefs/[0-9][0-9][0-9][0-9]-*.md; do
        if [ -f "$brief" ] && grep -qE '^Status: approved$' "$brief" && grep -qE '^Approved: [0-9]{4}-[0-9]{2}-[0-9]{2}$' "$brief"; then
            has_brief=1
        fi
    done

    if [ "$has_brief" = 0 ] && ! grep -qE '^Brief: none: .{10,}' "$msg_file"; then
        fail "$(dc_msg brief_none)"
    fi
fi
