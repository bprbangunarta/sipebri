# Messages of the devcore gates, in the language set by DEVCORE_LANG in devcore.conf (en or id). Plain POSIX shell, sourced by the
# scripts. The first word of a refusal (BRIEF, WEAKENED, HOOK, VERIFY) is the same in both languages, so tools and tests can rely on it.
#
#   dc_msg <key> [printf arguments]    prints the message without a newline
dc_msg() {
    key="$1"
    shift

    case "${DEVCORE_LANG:-en}:${key}" in
        en:range_usage) printf 'usage: check-brief.sh --range <rev-list arguments> | <commit message file>' ;;
        id:range_usage) printf 'pemakaian: check-brief.sh --range <argumen rev-list> | <berkas pesan commit>' ;;

        en:brief_range) printf "BRIEF: commit %s touches code but its message has no 'Brief: NNNN-title' or 'Brief: none: <reason>' line." "$@" ;;
        id:brief_range) printf "BRIEF: commit %s menyentuh kode tetapi pesannya tidak punya baris 'Brief: NNNN-judul' atau 'Brief: none: <alasan>'." "$@" ;;

        en:brief_range_hint) printf 'See %s, the workflow section. The commit-msg hook seems to have been skipped; never use --no-verify.' "$@" ;;
        id:brief_range_hint) printf 'Lihat %s, bagian alur kerja. Hook commit-msg tampaknya dilewati; jangan memakai --no-verify.' "$@" ;;

        en:brief_hint) printf 'See %s and docs/briefs/TEMPLATE.md. Never skip it with --no-verify.' "$@" ;;
        id:brief_hint) printf 'Lihat %s dan docs/briefs/TEMPLATE.md. Jangan dilewati dengan --no-verify.' "$@" ;;

        en:brief_open) printf "BRIEF: %s is being closed, but it still has an unticked '- [ ]' item." "$@" ;;
        id:brief_open) printf "BRIEF: %s ditutup, tetapi masih ada butir '- [ ]' yang belum selesai di dalamnya." "$@" ;;

        en:brief_none) printf "BRIEF: this commit touches code, but there is no approved brief in docs/briefs/ and no 'Brief: none: <reason, at least 10 letters>' line in its message." ;;
        id:brief_none) printf "BRIEF: commit ini menyentuh kode, tetapi tidak ada brief yang disetujui di docs/briefs/ dan tidak ada baris 'Brief: none: <alasan, minimal 10 huruf>' di pesannya." ;;

        en:weak_usage) printf 'usage: check-weakened.sh --staged <message file> | --range <range>' ;;
        id:weak_usage) printf 'pemakaian: check-weakened.sh --staged <berkas pesan> | --range <rentang>' ;;

        en:weak_suppress) printf 'check suppression' ;;
        id:weak_suppress) printf 'penekanan pemeriksa' ;;
        en:weak_skipped) printf 'test skipped or focused' ;;
        id:weak_skipped) printf 'tes dilewati atau difokuskan' ;;
        en:weak_allowed_why) printf '(allowed: %s)' "$@" ;;
        id:weak_allowed_why) printf '(diizinkan: %s)' "$@" ;;
        en:weak_lost_cases) printf 'lost test cases: %s removed, %s added' "$@" ;;
        id:weak_lost_cases) printf 'kehilangan kasus tes: %s dihapus, %s ditambah' "$@" ;;
        en:weak_deleted_test) printf 'test file deleted' ;;
        id:weak_deleted_test) printf 'berkas tes dihapus' ;;
        en:weak_step_removed) printf 'a step was removed from the gate' ;;
        id:weak_step_removed) printf 'sebuah langkah dihapus dari gerbang' ;;
        en:weak_baseline) printf 'new static-analysis baseline (fix the cause, do not hide it)' ;;
        id:weak_baseline) printf 'baseline analisis statis baru (perbaiki penyebabnya, jangan disembunyikan)' ;;

        en:weak_notice) printf 'WEAKENED (allowed, review the reason):' ;;
        id:weak_notice) printf 'WEAKENED (diizinkan, tinjau alasannya):' ;;
        en:weak_by_message) printf 'WEAKENED (allowed by the commit message "Weakened: %s"):' "$@" ;;
        id:weak_by_message) printf 'WEAKENED (diizinkan oleh pesan commit "Weakened: %s"):' "$@" ;;
        en:weak_refused) printf 'WEAKENED: this change makes the checks weaker:' ;;
        id:weak_refused) printf 'WEAKENED: perubahan ini membuat pemeriksaan lebih lemah:' ;;
        en:weak_hint_1) printf 'Fix the cause. If it is on purpose, give a reason: a "weakening-allowed: <reason>" marker on the line,' ;;
        id:weak_hint_1) printf 'Perbaiki penyebabnya. Bila memang disengaja, beri alasan: penanda "weakening-allowed: <alasan>" pada barisnya,' ;;
        en:weak_hint_2) printf 'or a "Weakened: <reason>" line in the commit message (at least 10 letters). Never skip it with --no-verify.' ;;
        id:weak_hint_2) printf 'atau baris "Weakened: <alasan>" di pesan commit (minimal 10 huruf). Jangan dilewati dengan --no-verify.' ;;

        en:hook_missing_1) printf 'HOOK: the git hooks are not installed in this clone (core.hooksPath is not .githooks), so commits and pushes are not guarded.' ;;
        id:hook_missing_1) printf 'HOOK: hook git belum dipasang di salinan ini (core.hooksPath bukan .githooks), jadi commit dan push tidak dijaga.' ;;
        en:hook_missing_2) printf 'Run: git config core.hooksPath .githooks   (then try again)' ;;
        id:hook_missing_2) printf 'Jalankan: git config core.hooksPath .githooks   (lalu ulangi)' ;;

        en:verify_commits) printf 'Weakened checks and Brief lines (commits being pushed)' ;;
        id:verify_commits) printf 'Pelemahan pemeriksaan dan baris Brief (commit yang akan di-push)' ;;
        en:verify_nosteps) printf 'VERIFY: scripts/verify.steps does not exist or has no steps, so the gate would check nothing. Add one "label|command" line per check.' ;;
        id:verify_nosteps) printf 'VERIFY: scripts/verify.steps tidak ada atau kosong, jadi gerbang tidak memeriksa apa pun. Tambahkan satu baris "label|perintah" per pemeriksaan.' ;;
        en:verify_failed) printf 'VERIFY: the step "%s" failed, so nothing is pushed.' "$@" ;;
        id:verify_failed) printf 'VERIFY: langkah "%s" gagal, jadi tidak ada yang di-push.' "$@" ;;
        en:verify_reused) printf 'Steps skipped: the gate was green on exactly this content %s minutes ago. The commit checks above ran as always.' "$@" ;;
        id:verify_reused) printf 'Langkah dilewati: gerbang sudah hijau pada isi yang persis sama %s menit lalu. Pemeriksaan commit di atas tetap berjalan.' "$@" ;;
        en:verify_green) printf 'All checks are green.' ;;
        id:verify_green) printf 'Semua pemeriksaan hijau.' ;;

        en:reg_unlisted) printf 'REGISTRY: the rule file %s is not listed in .ai/rules/index.md, so no agent is told to read it. Add a row with the paths it covers.' "$@" ;;
        id:reg_unlisted) printf 'REGISTRY: berkas aturan %s tidak terdaftar di .ai/rules/index.md, jadi tidak ada agent yang diperintahkan membacanya. Tambahkan baris dengan jalur yang dicakupnya.' "$@" ;;
        en:reg_missing) printf 'REGISTRY: .ai/rules/index.md lists %s, but that file does not exist.' "$@" ;;
        id:reg_missing) printf 'REGISTRY: .ai/rules/index.md menyebut %s, tetapi berkasnya tidak ada.' "$@" ;;
        en:reg_skill_file) printf 'REGISTRY: the skill folder %s has no SKILL.md.' "$@" ;;
        id:reg_skill_file) printf 'REGISTRY: folder skill %s tidak punya SKILL.md.' "$@" ;;
        en:reg_skill_name) printf 'REGISTRY: %s/SKILL.md must declare "name: %s" in its header.' "$@" ;;
        id:reg_skill_name) printf 'REGISTRY: %s/SKILL.md harus menulis "name: %s" di kepalanya.' "$@" ;;
        en:reg_skill_guide) printf 'REGISTRY: the skill %s is not named in %s, so no agent is told when to use it. Add a row to the skills section.' "$@" ;;
        id:reg_skill_guide) printf 'REGISTRY: skill %s tidak disebut di %s, jadi tidak ada agent yang diberi tahu kapan memakainya. Tambahkan baris di bagian skill.' "$@" ;;
        en:reg_manifest) printf 'REGISTRY: %s does not match its MANIFEST.sha256. A vendored skill is replaced by a newer release, never edited by hand.' "$@" ;;
        id:reg_manifest) printf 'REGISTRY: %s tidak cocok dengan MANIFEST.sha256. Skill yang diambil dari luar diganti dengan rilis yang lebih baru, tidak diedit tangan.' "$@" ;;
        en:reg_ok) printf 'Rules and skills are registered.' ;;
        id:reg_ok) printf 'Aturan dan skill terdaftar.' ;;

        *) printf '%s' "$key" ;;
    esac
}
