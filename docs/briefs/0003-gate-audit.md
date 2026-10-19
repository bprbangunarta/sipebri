# Audit fitur 25: gerbang dan kerangka agent

Status: approved
Approved: 2026-10-20

## Goal

Gerbang yang menolak commit dan push yang buruk, dan aturan yang dibaca setiap agent, benar-benar menahan apa yang dijanjikannya, tidak hanya lulus pada contoh yang sudah dipilih. Sesudahnya pemilik tahu lubang mana yang ditemukan, mana yang ditutup, dan mana yang diterima, alih-alih menganggap gerbang kokoh karena `selftest.sh` hijau.

Bagian dari seri audit fitur (urutan pemilik: 25, 8, 6, 7, 2 sampai 5, 9 sampai 13). Fitur 1 dan 24 sudah selesai.

## Acceptance criteria

Yang diperiksa (seluruh permukaan fitur): `scripts/{verify.sh,verify.steps,check-brief.sh,check-weakened.sh,check-registry.sh,selftest.sh,lib/msg.sh}`, `devcore.conf`, `.githooks/{commit-msg,pre-push}`, `.github/workflows/ci.yml` dan `.github/dependabot.yml`, skrip `composer.json` dan `package.json` yang memanggil gerbang, `.jscpd.json`, `phpstan.neon`, `pint.json`, `app/Console/Commands/EnvCheck.php` dengan `.env.example`; kerangka agent: `AGENTS.md` (bagian standar, bagian SIPEBRI, blok Boost), `CLAUDE.md`, `.ai/rules/*` (11 berkas dan `index.md`), `.ai/skills/*` (6 skill, `MANIFEST.sha256`), `boost.json`, `.mcp.json`, `docs/briefs/TEMPLATE.md`, `memory/PRD.md`; dan test penjaganya: `BriefsTest`, `SkillsRegistryTest`, `DocsMatchCodeTest`, `CommentHygieneTest`, `DesignQualityTest`, `EnvExampleTest`, `EnvCheckCommandTest`, `ScriptsHostileInputTest`, `ArchitectureTest`.

- [x] Setiap skrip gerbang dibaca utuh lalu diserang di repositori sementara dengan git sungguhan: nama berkas berisi spasi, tanda kutip, huruf bukan ASCII, dan awalan `-`; commit penggabungan, rebase, `--amend`, cherry-pick, commit kosong, commit pertama, cabang tanpa upstream, HEAD terlepas, klon dangkal (CI `fetch-depth`), dan rentang push dengan sha nol. Tiap lubang yang ditemukan mendapat kasus di `scripts/selftest.sh` atau test PHP, yang gagal dulu.
- [x] Daftar cara melonggarkan pemeriksaan disusun dan masing-masing dicoba: menurunkan level PHPStan, membuang `--test` dari langkah Pint, mengubah `CODE_PATHS` atau `WEAKENED_SCAN` di `devcore.conf`, mengubah skrip gerbang atau hook sendiri, mengendurkan `.jscpd.json`, `vite.config.ts` (aturan lint), `phpstan.neon` (`ignoreErrors`), `pint.json`, `phpunit.xml`, skrip `composer.json`, dan `ci.yml`. Untuk tiap cara diputuskan: dideteksi (dengan test) atau diterima (dicatat di PRD dengan alasan).
- [x] Pemasangan hook dan `--no-verify`: klon baru tanpa `core.hooksPath`, perilaku `verify.sh` tanpa hook, dan jaring pengaman CI diperiksa; klon baru dari remote dijalankan dari nol (`git clone`, `composer setup`, `scripts/verify.sh`) dan hasilnya dilaporkan, termasuk lamanya.
- [x] Tiap langkah `verify.steps` dijalankan dari repositori bersih: urutan dan biaya (waktu), perilaku tanpa jaringan (`composer audit`, `npm audit`), dan perbedaan lingkungan CI (`LARAVEL_BYPASS_ENV_CHECK`, SQLite memori, tanpa `.env` asli).
- [x] `ci.yml` diperiksa: aksi dikunci dengan SHA, izin minimal, `fetch-depth: 0`, rumus `VERIFY_RANGE` untuk push pertama, push biasa, pull request, dan cabang baru (kasus sha nol), dan `dependabot.yml` mencakup composer, npm, dan aksi. Rumus dijalankan sungguhan terhadap contoh riwayat.
- [x] Registri dan skill: `MANIFEST.sha256` utuh, versi dan lisensi tercatat, setiap skill disebut di `AGENTS.md`, skill Boost di `boost.json` cocok dengan tabel bagian 5, dan `php artisan boost:update` dijalankan di salinan sementara untuk membuktikan bagian 1 `AGENTS.md` dan blok Boost tidak saling menimpa.
- [x] Setiap berkas aturan dan dokumen agent dibaca terhadap kodenya dua arah: klaim yang tidak ditepati kode, dan perilaku yang tidak disebut dokumen mana pun. Termasuk konflik antara aturan standar dan aturan proyek (`.env.example` ber-komentar, `general.md` yang masih berjudul "UI nanti Indonesia"), baris `index.md` terhadap jalur nyata (setiap jalur kode terpetakan ke minimal satu aturan), dan temuan awal di PRD.
- [x] Setiap test penjaga dimutasi dengan sengaja (mengubah apa yang dijaganya di salinan sementara) untuk membuktikan ia gagal; test yang tidak bisa gagal dicari dan dilaporkan atau dikuatkan.
- [x] `EnvCheck`: masukan buruk (kutip, `=` ganda, CRLF, baris panjang, berkas hilang atau tak terbaca), nilai rahasia tidak pernah tercetak, penulisan atomik, kode keluar; ditambah pemeriksaan bahwa `.env.example` dan konfigurasi saling cocok.
- [x] Sembilan pertanyaan `.ai/rules/quality.md` dijawab untuk skrip dan perintah fitur ini (masukan buruk, hasil tetap, dua commit atau push bersamaan, sisa berkas sementara, biaya waktu, kebocoran rahasia ke log, catatan jejak) dengan bukti.
- [x] Temuan diperbaiki di perubahan ini, masing-masing dengan test yang gagal sebelum perbaikan dan lulus sesudahnya; laporan memuat tiap temuan dengan tingkat keparahan, perbaikan, dan test. Temuan yang mengubah kebijakan (level PHPStan, `.env.example`, cakupan pelonggaran yang dideteksi) dijelaskan ke pemilik sebelum ditulis.
- [x] Temuan pada berkas milik devcore (skrip, hook, aturan standar, template) tidak diedit di sini: dikumpulkan sebagai daftar umpan balik untuk devcore dengan bukti dan usulan perbaikannya, kecuali pemilik memutuskan lain.
- [x] `AGENTS.md`, `CLAUDE.md`, `memory/PRD.md`, dan `.ai/rules/*` dibandingkan dua arah dengan kodenya; laporan menyebut selisih yang ditemukan dan diperbaiki (atau tidak ada).

## Out of scope

- Isi aturan UI, komponen, dan `scripts/check-standards.mjs` (fitur 24, sudah diaudit; di sini hanya sebagai satu langkah gerbang).
- Berkas dalam `/Users/Bananay/Claude/devcore` (sumber, tidak disunting; temuan menjadi daftar umpan balik).
- Halaman error dan status jaringan (fitur 8), menu dan navigasi (fitur 5), audit log aplikasi (fitur 6).
- Mengganti alat (Pint, PHPStan, Vitest, jscpd) atau menambah pemeriksaan baru di luar menutup lubang yang ditemukan.

## Assumptions and open questions

- Audit dikerjakan dengan menjalankan gerbang dan mencoba merusaknya di repositori sementara, bukan memercayai `selftest.sh`; hijau hanya titik awal.
- Bagian yang saling bebas (skrip dan hook, CI dan klon baru, dokumen dan aturan, mutasi test penjaga) dikerjakan paralel oleh beberapa agen pada berkas yang tidak tumpang tindih, supaya audit tidak lama; saya yang menggabungkan dan menjalankan gerbang akhir.
- Temuan diperbaiki langsung kecuali yang mengubah kebijakan; itu dijelaskan dulu.
- Tidak ada branch protection di GitHub (pilihan pemilik untuk codex); bila relevan, hanya dicatat sebagai risiko yang diterima.
- Dijawab pemilik (2026-10-20): temuan di berkas milik devcore hanya dilaporkan sebagai daftar umpan balik, tidak diedit lokal. Berkas milik devcore: `scripts/{verify.sh,check-brief.sh,check-weakened.sh,check-registry.sh,selftest.sh,lib/msg.sh}`, `.githooks/*`, `.ai/rules/{quality,testing,laravel,ui}.md`, `docs/briefs/TEMPLATE.md`, `.ai/skills/*`. Milik proyek: `devcore.conf`, `scripts/verify.steps`, `.github/workflows/ci.yml` (bagian penyiapan stack), `scripts/check-standards.mjs`, aturan proyek (`general`, `audit-log`, `database`, `php`, `tests`, `js`), bagian SIPEBRI di `AGENTS.md`, `CLAUDE.md`, `memory/PRD.md`, `EnvCheck`, dan semua test PHP/Vitest.

## Checklist

Every line is ticked `[x]`; a line that does not apply is ticked and ends with `N/A: reason`. Nothing is "done" while a `[ ]` remains. The rule behind each line is in the file it names; do not copy the rule here. A rule that the project adds (`AGENTS.md`, section 6) adds its own line here, under the group it belongs to.

Reading and scope

- [x] The PRD and the rule files matched to the paths touched are read (`AGENTS.md` section 1, `.ai/rules/index.md`)
- [x] The skills of section 5 that apply are named and used
- [x] The person who meets this change and the moment they are most likely to be confused are named, and handled (principle at the top of `AGENTS.md`)
- [x] Out of scope is respected; findings outside the task are reported, not fixed on the side

Design

- [x] The impact of a changed function, interface, field, or contract is searched (callers, screens, documents, tests) before the change
- [x] Who may do this is decided before the input is validated, and every input limit is explicit (`.ai/rules/quality.md`, questions 1 and 2)
- [x] Every outcome has one defined response, and a failure leaves nothing half done (`.ai/rules/quality.md`, questions 3 to 5)
- [x] Concurrency, repetition, and cost are handled (`.ai/rules/quality.md`, questions 4 and 6)
- [x] The code is readable, upgradeable, extensible, and scalable as `.ai/rules/quality.md`, "Design quality", asks: small units, one place to add a case, nothing kept in a process, nothing a person would not have written
- [x] Nothing leaks, and what must be on record is on record (`.ai/rules/quality.md`, questions 7 and 8)
- [x] Project rule: the change is on the audit record, a new model uses `Auditable`, and a new relation has a foreign key with a deliberate delete rule (`.ai/rules/audit-log.md`, `.ai/rules/general.md`, "Integritas relasi") N/A: audit ini tidak mengubah data bisnis, model, atau relasi
- [x] Project rule: a schema change is a new migration, never an edited one, and the database is not reset (`.ai/rules/database.md`) N/A: tidak ada perubahan skema atau migrasi
- [x] Project rule: code, comments, tests, and URLs are English; screen text is Indonesian (`.ai/rules/general.md`)

Screen (N/A without a screen)

- [x] States (loading, empty, error), words, and confirmation of irreversible actions follow the UI rules (`.ai/rules/ui.md`, and the project's `.ai/rules/js.md`: shared components, one value per column, server-side sort keys, inputs that enforce the server rule) N/A: tidak ada layar yang diubah
- [x] Checked in a browser at 1280 px and at 375 px, with the console clear N/A: tidak ada layar yang diubah (gerbang, dokumen, dan test)
- [x] The antislop Delivery Gate is answered as PASS/FAIL lines with evidence in the report N/A: tidak ada layar atau teks UI; dokumen baru ditulis lugas dan tanda pisah panjang dijaga CommentHygieneTest

Tests and documents

- [x] Each risk and each refusal has a test (`.ai/rules/quality.md`, question 9; `.ai/rules/testing.md`), and the cross-check tests are green
- [x] Documents and code are compared in both directions, stale text is fixed, and `memory/PRD.md` is updated (a decision that outlives this change is one line there)
- [x] The notes an agent keeps for itself (its own memory, if it has one) are updated with what this change decided or taught, without waiting to be asked

Gates and close

- [x] `scripts/verify.sh` is green and the build runs
- [x] Every acceptance criterion above is verified with evidence in the report
- [x] The owner is told what changed, what was not done, and why; this brief is deleted in the closing commit
