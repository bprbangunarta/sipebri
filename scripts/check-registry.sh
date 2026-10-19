#!/usr/bin/env bash
# Keeps rules and skills honest, so a rule or skill that an agent is never told about cannot exist, and a vendored skill cannot be edited
# by hand. Custom rules and custom skills of a project are treated exactly like the standard ones:
#   - every file in .ai/rules/ (except index.md) is listed in .ai/rules/index.md, and every file that index.md names exists;
#   - every folder in .ai/skills/ has a SKILL.md that declares "name: <folder>", and is named in the agent guide;
#   - a skill folder that carries a MANIFEST.sha256 still matches it.
set -uo pipefail

cd "$(git rev-parse --show-toplevel)"
# shellcheck source=/dev/null
. ./devcore.conf
# shellcheck source=/dev/null
. ./scripts/lib/msg.sh

failed=0
fail() { printf '%s\n' "$1" >&2; failed=1; }

index='.ai/rules/index.md'

if [ -f "$index" ]; then
    for rule in .ai/rules/*.md; do
        [ -f "$rule" ] || continue
        [ "$rule" = "$index" ] && continue
        grep -qF "$rule" "$index" || fail "$(dc_msg reg_unlisted "$rule")"
    done

    while IFS= read -r named; do
        [ -f "$named" ] || fail "$(dc_msg reg_missing "$named")"
    done < <(grep -oE '\.ai/rules/[A-Za-z0-9._/-]+\.md' "$index" | sort -u | grep -v "^${index}$")
fi

if [ -d .ai/skills ]; then
    for dir in .ai/skills/*/; do
        [ -d "$dir" ] || continue
        skill="$(basename "$dir")"

        if [ ! -f "${dir}SKILL.md" ]; then
            fail "$(dc_msg reg_skill_file ".ai/skills/${skill}")"
            continue
        fi

        grep -qE "^name: ${skill}\$" "${dir}SKILL.md" || fail "$(dc_msg reg_skill_name ".ai/skills/${skill}" "$skill")"
        grep -qF "\`${skill}\`" "$GUIDE" 2>/dev/null || fail "$(dc_msg reg_skill_guide "$skill" "$GUIDE")"

        if [ -f "${dir}MANIFEST.sha256" ]; then
            if command -v sha256sum >/dev/null 2>&1; then sum_cmd='sha256sum -c --quiet'; else sum_cmd='shasum -a 256 -c --quiet'; fi
            (cd .ai/skills && $sum_cmd "${skill}/MANIFEST.sha256" >/dev/null 2>&1) || fail "$(dc_msg reg_manifest ".ai/skills/${skill}")"
        fi
    done
fi

[ "$failed" -eq 0 ] && { dc_msg reg_ok; echo; }
exit "$failed"
