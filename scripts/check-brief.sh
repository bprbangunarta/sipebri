#!/usr/bin/env bash
# Brief gate (AGENTS.md, section 3), called by the commit-msg hook with one argument: the commit message file. It binds every agent
# because a git hook does not care who runs the commit. Two rules:
#   1. A commit that touches code needs a brief with "Status: approved" in docs/briefs/, or a "Brief: none: <reason>" line in its message
#      (for small fixes, text changes, or changes already agreed in chat).
#   2. Deleting a brief (closing it) is allowed only when its last version has no "- [ ]" item left.
set -euo pipefail

cd "$(git rev-parse --show-toplevel)"

code_paths='^(app|bootstrap|config|database|lang|public|resources|routes|scripts|tests|\.githooks|\.github)/|^(composer\.json|composer\.lock|package\.json|package-lock\.json|phpunit\.xml|phpstan\.neon|pint\.json|vite\.config\.ts|tsconfig\.json)$'

# After the fact: for commits that already exist (verify.sh and CI), in case the commit-msg hook was skipped. Every code commit (not a
# merge) must name its brief in the message: "Brief: NNNN-title" or "Brief: none: <reason>". The brief's content can only be judged at
# commit time.
if [ "${1:-}" = '--range' ]; then
    shift
    [ "$#" -gt 0 ] || { echo 'pemakaian: check-brief.sh --range <argumen rev-list> | <berkas pesan commit>' >&2; exit 2; }
    missing=0

    for commit in $(git rev-list --reverse "$@"); do
        [ "$(git rev-list --parents -n 1 "$commit" | wc -w)" -gt 2 ] && continue

        # Command output is stored first and read through a here-string: `grep -q` stops at the first match, which kills the upstream
        # command with SIGPIPE, and under `pipefail` that reads as "no match".
        files="$(git diff-tree --root --no-commit-id --name-only -r -z "$commit" | tr '\0' '\n')"
        message="$(git log -1 --format=%B "$commit")"

        if grep -qE "$code_paths" <<< "$files"; then
            if ! grep -qE '^Brief: (none: .{10,}|[0-9]{4}-[a-z0-9-]+)' <<< "$message"; then
                echo "BRIEF: commit $(git log -1 --format='%h %s' "$commit") menyentuh kode tetapi pesannya tidak punya baris 'Brief: NNNN-judul' atau 'Brief: none: <alasan>'." >&2
                missing=1
            fi
        fi
    done

    [ "$missing" = 0 ] || echo "       Lihat AGENTS.md bagian 3. Hook commit-msg tampaknya dilewati; jangan memakai --no-verify." >&2
    exit "$missing"
fi

msg_file="${1:?berkas pesan commit}"

# Merges and reverts restore a state that was already approved. They are recognised by git's own state (MERGE_HEAD, REVERT_HEAD), not by
# the first word of the message: anyone can write a message, including one meant to slip past this gate.
for state in MERGE_HEAD REVERT_HEAD; do
    [ -f "$(git rev-parse --git-path "$state")" ] && exit 0
done

brief_path='^docs/briefs/[0-9]{4}-[a-z0-9-]+\.md$'

fail() {
    echo "BRIEF: $1" >&2
    echo "       Lihat AGENTS.md bagian 3 dan docs/briefs/TEMPLATE.md. Jangan dilewati dengan --no-verify." >&2
    exit 1
}

closing_approved=0

if git rev-parse --verify --quiet HEAD >/dev/null; then
    # File names are read NUL-separated: otherwise git quotes names that contain quotes or non-ASCII letters, those no longer match the
    # path pattern below, and the file slips through uncounted.
    while IFS= read -r -d '' deleted; do
        [[ "$deleted" =~ $brief_path ]] || continue
        last="$(git show "HEAD:${deleted}")"

        if grep -qE '^[[:space:]]*- \[ \]' <<< "$last"; then
            fail "${deleted} ditutup, tetapi masih ada butir '- [ ]' yang belum selesai di dalamnya."
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
        fail "commit ini menyentuh kode, tetapi tidak ada brief yang disetujui di docs/briefs/ dan tidak ada baris 'Brief: none: <alasan, minimal 10 huruf>' di pesannya."
    fi
fi
