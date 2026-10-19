#!/bin/sh
# Every check that must be green before anything reaches GitHub (AGENTS.md, section 3 step 4). This is the only definition of the gate:
# the pre-push hook, `composer verify`, `composer ci:check`, and CI run this script and list no checks of their own (`GateDefinitionTest`
# guards that). It stops at the first failure and exits with a failing status.
#
# Commit range checked (weakened checks and the Brief line in messages): `VERIFY_RANGE` when set (one range per line; the pre-push hook
# fills it from the commits actually being sent, CI from the push or pull request), otherwise what is not yet upstream, and on a branch
# without an upstream every commit that is not on any remote, never only the last commit.
set -e
cd "$(git rev-parse --show-toplevel)"

if [ "$(git config core.hooksPath)" != ".githooks" ]; then
    echo "HOOK: hook git belum dipasang di salinan ini (core.hooksPath bukan .githooks), jadi commit dan push tidak dijaga." >&2
    echo "      Jalankan \`composer setup\` (atau \`git config core.hooksPath .githooks\`), lalu ulangi." >&2
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

# check_range <rev-list arguments>: one range, read by both commit checkers.
check_range() {
    spec="$1"

    case "$spec" in
        "$zero"*)
            # New branch in CI: no known starting point, so use the main branch when present.
            if git rev-parse --verify --quiet origin/main >/dev/null; then spec='origin/main..HEAD'; else spec='HEAD~1..HEAD'; fi
            ;;
    esac

    # A starting point this clone does not know (someone else's commit not fetched yet): check everything not on a remote.
    # shellcheck disable=SC2086
    if ! git rev-list -n 1 $spec >/dev/null 2>&1; then
        spec='HEAD --not --remotes'
    fi

    # shellcheck disable=SC2086
    scripts/check-weakened.sh --range $spec || return 1
    # shellcheck disable=SC2086
    scripts/check-brief.sh --range $spec || return 1
}

echo "→ Pelemahan pemeriksaan dan baris Brief (commit yang akan di-push)"
printf '%s\n' "$ranges" | while IFS= read -r range; do
    [ -z "$range" ] && continue
    check_range "$range" || exit 1
done

echo "→ Kerentanan dependency"
scripts/check-dependencies.sh

echo "→ .env memuat semua variabel dari .env.example"
php artisan env:check

echo "→ Pint (gaya kode)"
vendor/bin/pint --test --format agent

echo "→ PHPStan (tipe)"
vendor/bin/phpstan analyse --memory-limit=1G --no-progress

# Pest renders Inertia pages through the Vite manifest, and the build also generates the Wayfinder files TypeScript needs, so it runs
# before the tests and the type check.
echo "→ Build aset (manifest Vite dan berkas Wayfinder)"
npm run build

echo "→ Pest (seluruh tes)"
php artisan test --compact

echo "→ Layar (format, lint, aturan rumah, tes)"
npm run check

echo "→ Kode salinan (jscpd)"
npm run duplicates

echo "→ TypeScript"
npm run types:check

echo "Semua pemeriksaan hijau."
