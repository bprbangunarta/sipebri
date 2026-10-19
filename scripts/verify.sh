#!/bin/sh
# Everything that must be green before anything is pushed. This is the only definition of the gate: the pre-push hook, the project's own
# "verify" command, and CI run this script and never list checks of their own. The checks are the lines of scripts/verify.steps, one per
# line as "label|command". It stops at the first failure and exits with a failure status.
#
# The commits that are checked for weakened checks and Brief lines: VERIFY_RANGE when set (one range per line; the pre-push hook fills
# it from the commits that are really being sent, CI from the push or the pull request), otherwise the commits not yet upstream, and on
# a branch without an upstream every commit that is on no remote, never only the last commit.
set -e
root="$(git rev-parse --show-toplevel)"
cd "$root"
# shellcheck source=/dev/null
. ./devcore.conf
# shellcheck source=/dev/null
. ./scripts/lib/msg.sh

if [ "$(git config core.hooksPath)" != ".githooks" ]; then
    dc_msg hook_missing_1 >&2
    echo >&2
    printf '      ' >&2
    dc_msg hook_missing_2 >&2
    echo >&2
    exit 1
fi

zero=0000000000000000000000000000000000000000
ranges="${VERIFY_RANGE:-}"

if [ -z "$ranges" ]; then
    if git rev-parse --abbrev-ref '@{upstream}' >/dev/null 2>&1; then
        ranges='@{upstream}..HEAD'
    else
        ranges='HEAD --not --remotes'
    fi
fi

# A repository without a commit yet has nothing to read.
if ! git rev-parse --verify --quiet HEAD >/dev/null; then
    ranges=''
fi

# check_range <rev-list arguments>: one range, read by the two checkers.
check_range() {
    spec="$1"

    case "$spec" in
        "$zero"*)
            # A new branch in CI: no known starting point, use the main branch when there is one.
            if git rev-parse --verify --quiet "origin/${MAIN_BRANCH:-main}" >/dev/null; then spec="origin/${MAIN_BRANCH:-main}..HEAD"; else spec='HEAD~1..HEAD'; fi
            ;;
    esac

    # A starting point this clone does not know (someone else's commit not fetched yet): check everything that is on no remote.
    # shellcheck disable=SC2086
    if ! git rev-list -n 1 $spec >/dev/null 2>&1; then
        spec='HEAD --not --remotes'
    fi

    # shellcheck disable=SC2086
    scripts/check-weakened.sh --range $spec || return 1
    # shellcheck disable=SC2086
    scripts/check-brief.sh --range $spec || return 1
}

printf '→ '
dc_msg verify_commits
echo
printf '%s\n' "$ranges" | while IFS= read -r range; do
    [ -z "$range" ] && continue
    check_range "$range" || exit 1
done

steps_file="scripts/verify.steps"

if [ ! -f "$steps_file" ] || ! grep -qvE '^[[:space:]]*(#|$)' "$steps_file"; then
    dc_msg verify_nosteps >&2
    echo >&2
    exit 1
fi

while IFS= read -r line; do
    case "$line" in
        '' | '#'*) continue ;;
    esac

    label="${line%%|*}"
    command="${line#*|}"

    echo "→ $label"

    if ! sh -c "$command"; then
        dc_msg verify_failed "$label" >&2
        echo >&2
        exit 1
    fi
done < "$steps_file"

dc_msg verify_green
echo
