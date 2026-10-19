#!/usr/bin/env bash
# Dependency vulnerability check (.ai/rules/php.md, "Dependency dan cakupan"), called by scripts/verify.sh so every push is guarded.
# A vulnerability finding stops the script. When the advisory database cannot be reached (offline), the script warns and continues: a push
# without network must stay possible, and the warning is printed where it is read. Findings that cannot be fixed are written as accepted
# risks in memory/PRD.md; the check itself is never switched off.
set -uo pipefail

cd "$(git rev-parse --show-toplevel)"

failed=0

# audit <name> <finding pattern> <command...>
audit() {
    local name="$1" pattern="$2"
    shift 2

    local output status
    output="$("$@" 2>&1)"
    status=$?

    if [ "$status" -eq 0 ]; then
        echo "  ${name}: tidak ada temuan"
    elif printf '%s\n' "$output" | grep -qiE "$pattern"; then
        printf '%s\n' "$output" >&2
        echo "DEPENDENCY: ${name} menemukan kerentanan pada dependency (keluaran di atas). Perbarui paketnya; bila tidak bisa, tulis sebagai risiko yang diterima di memory/PRD.md." >&2
        failed=1
    else
        echo "  PERINGATAN: ${name} tidak bisa memeriksa (jaringan atau daftar kerentanan tidak terjangkau); pemeriksaan ini TIDAK dilakukan. Jalankan ulang saat daring." >&2
    fi
}

audit 'composer audit' 'security vulnerability advisor|^Package:' composer audit --locked --no-interaction
audit 'npm audit' 'severity vulnerabilit|npm audit fix' npm audit --omit=dev

exit "$failed"
