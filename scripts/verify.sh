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

# A green run leaves a stamp inside .git: the tree it checked and the time. The pre-push hook (and only it, through VERIFY_REUSE_SHAS)
# skips the steps when every commit being sent has exactly that tree and the stamp is young enough; anything else, a manual run and CI
# included, runs every step.
stamp_file="$(git rev-parse --git-dir)/devcore-verified"
stamp_hours="${VERIFY_STAMP_HOURS:-24}"
case "$stamp_hours" in '' | *[!0-9]*) stamp_hours=24 ;; esac

# stamp_covers: the stamp names the tree of every commit being sent, and is not older than the allowed hours.
stamp_covers() {
    [ -n "${VERIFY_REUSE_SHAS:-}" ] && [ -f "$stamp_file" ] || return 1
    stamped_tree="$(sed -n 's/^tree=//p' "$stamp_file")"
    stamped_at="$(sed -n 's/^time=//p' "$stamp_file")"
    case "$stamped_at" in '' | *[!0-9]*) return 1 ;; esac
    [ -n "$stamped_tree" ] || return 1
    age=$(($(date +%s) - stamped_at))
    [ "$age" -ge 0 ] && [ "$age" -le $((stamp_hours * 3600)) ] || return 1

    for sha in $VERIFY_REUSE_SHAS; do
        [ "$(git rev-parse --verify --quiet "${sha}^{tree}")" = "$stamped_tree" ] || return 1
    done
}

# clean_tree: prints the tree of HEAD when what was checked is exactly the committed content: no tracked file differs from HEAD and no
# untracked file that is not ignored is code (an untracked note or .DS_Store changes nothing the gate checks).
clean_tree() {
    git rev-parse --verify --quiet HEAD >/dev/null || return 1
    git diff --quiet HEAD -- || return 1
    if git ls-files --others --exclude-standard | grep -Eq "${CODE_PATHS:-^$}"; then return 1; fi
    git rev-parse 'HEAD^{tree}'
}

# run_steps: every step of verify.steps, then the stamp when the content checked is exactly the committed one.
run_steps() {
    # Every run that gets this far starts without a stamp: a failing or interrupted run leaves none.
    rm -f "$stamp_file"
    tree_before="$(clean_tree || true)"

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

    tree_after="$(clean_tree || true)"
    if [ -n "$tree_before" ] && [ "$tree_before" = "$tree_after" ]; then
        printf 'tree=%s\ntime=%s\n' "$tree_after" "$(date +%s)" > "$stamp_file"
    fi

    dc_msg verify_green
    echo
}

if stamp_covers; then
    minutes=$((($(date +%s) - $(sed -n 's/^time=//p' "$stamp_file")) / 60))
    printf '→ '
    dc_msg verify_reused "$minutes"
    echo
else
    run_steps
fi
