# SIPEBRI: Panduan Agent (baca ini dulu)

Berkas ini punya dua bagian. Ketahui mana yang mana sebelum bertindak:

| Bagian | Di mana | Isinya | Pemilik |
| --- | --- | --- | --- |
| **1. Panduan proyek** | bagian ini (di atas blok Boost) | aturan pemilik: alur kerja, aturan bisnis dan proses, git, dokumen dan skill mana yang dipakai | pemilik, boleh diubah |
| **2. Pedoman Laravel Boost** | blok Boost yang dibuat otomatis di bawah (judul `# Bagian 2`) | praktik umum Laravel untuk **menulis kode** | dibuat `php artisan boost:update`, jangan diubah |

Bila bertentangan, Bagian 1 yang menang; setiap pengecualian dari bawaan Boost tercantum di bagian 6.

**Proyek:** SIPEBRI v3 adalah aplikasi kredit BPR Bangunarta (Laravel 13 + Inertia v3 + React 19 + Wayfinder, MySQL; versi di `composer.json`, `package.json`). Fungsional SIPEBRI lama ditulis ulang di sini; pengguna dan nasabah datang dari Codex. Setiap perubahan data bisnis harus bisa diaudit (standar OJK). Repositori `https://github.com/bprbangunarta/sipebri.git`, cabang kerja `v3.0.0-alpha`. Peta arsitektur dan cara menjalankan ada di `CLAUDE.md`.

**Prinsip: orang lebih dulu.** Pada akhirnya SIPEBRI dipakai orang: AO yang membuka berkas, surveyor di lapangan dengan sinyal lemah, analis yang mengisi lembar analisa, anggota komite yang memutus, auditor yang membaca log, Super Admin yang mengatur referensi. Setiap perubahan dinilai pertama dari apa yang dilihat, dipahami, dan bisa dilakukan orang itu berikutnya, baru kemudian dari apakah ia berjalan. Aturan konkretnya ada di tempat berlakunya dan tidak diulang di sini: apa yang dikatakan pesan atau galat (`.ai/rules/php.md`, pertanyaan 3), bagaimana kegagalan meninggalkan orang tetap bisa mengulang (`.ai/rules/php.md`, pertanyaan 5), bagaimana layar berperilaku (`.ai/rules/js.md`, plus `antislop-human` untuk keyboard, kontras, dan keadaan), dan bagaimana dokumen dibaca (`antislop-copywriting`, dan pencocokan dengan kode di langkah 4a). Bila dua rancangan setara bagi mesin, ambil yang lebih ramah bagi orangnya.

**Bahasa.** Aturannya satu tempat: `.ai/rules/general.md` (kode, komentar, test, dan commit berbahasa Inggris; semua yang dibaca orang di produk berbahasa Indonesia; URL Inggris tetapi sepadan dengan judul layar). Dokumen agent (`AGENTS.md` bagian 1, `.ai/rules`, `memory/PRD.md`, `CLAUDE.md`) berbahasa Indonesia. Tanggal, waktu, dan angka untuk orang ditulis di satu tempat (`.ai/rules/js.md`, "Tanggal, waktu, dan angka").

## 1. Urutan membaca: bagaimana aturan sampai ke Anda

Baca dalam urutan ini, setiap tugas, setiap agent. Tiap langkah menyebut apa yang diambil; langkah tidak dilewati karena tugasnya tampak kecil, dan berkas yang hilang atau tak terbaca disebut di laporan, tidak dilewati diam-diam.

| Langkah | Baca | Yang diambil |
| --- | --- | --- |
| 1 | berkas ini | prinsip, alur kerja (bagian 3), dan letak semua hal lain (bagian 2) |
| 2 | `memory/PRD.md` | apa yang ada, apa yang diputuskan, apa yang terbuka, apa berikutnya, tanpa membaca seluruh kode |
| 3 | permintaan, lalu jalurnya | fitur atau perubahan perilaku, kontrak, skema, atau keamanan mendapat brief (bagian 3, langkah 0, `docs/briefs/TEMPLATE.md`); yang kecil menyebut `Brief: none: <alasan>` |
| 4 | `.ai/rules/index.md`, lalu setiap berkas aturan yang glob-nya cocok dengan jalur yang akan disentuh, lalu `grep -rin '<kata kunci>' .ai/rules` | konvensi dan aturan bisnis proyek, per jalur (Boost juga menegakkannya) |
| 5 | skill di bagian 5 yang berlaku, masing-masing dari `SKILL.md`-nya | panduan rinci: Laravel dan test untuk PHP, Inertia/Wayfinder/Tailwind untuk layar, antislop untuk layar, teks, dan komentar |
| 6 | `CLAUDE.md` (tabel "Arsitektur singkat") | bagian kode mana yang mengerjakan fitur yang akan disentuh |
| 7 | kodenya: berkas sejenis dulu, lalu yang akan diubah | pola yang diikuti; cari implementasi yang sudah ada sebelum menulis yang baru |

Langkah 3 sampai 5 berakhir di brief: berkas aturan dan skill yang dibaca disebut di sana sebelum suntingan pertama, dan daftar periksanya merujuk balik ke sana. **Bukti membaca** adalah brief (atau, untuk `Brief: none`, daftar periksa yang sama dijawab di laporan): satu butir dicentang hanya setelah berkas yang dirujuknya dibaca.

## 2. Dokumen mana menjawab pertanyaan apa

| Pertanyaan | Dokumen |
| --- | --- |
| Bagaimana menulis kode Laravel yang baik (praktik umum)? | blok Boost di bawah + skill (bagian 5) |
| Bagaimana kode ditulis di proyek **ini** (standar mutu, konvensi)? | `.ai/rules/general.md`, `.ai/rules/php.md`, `.ai/rules/database.md`, `.ai/rules/tests.md` |
| Apa aturan bisnis dan proses (audit log, integritas data)? | `.ai/rules/audit-log.md`, `.ai/rules/general.md` |
| Bagaimana bekerja di sini (alur kerja, git, otonomi, pengecualian)? | berkas ini, Bagian 1 |
| Bagaimana memulai fitur atau perubahan perilaku, dan apa yang harus benar sebelum disebut selesai? | `docs/briefs/TEMPLATE.md` |
| Bagaimana UI dibangun (komponen, token, layar, pemeriksaan frontend)? | `.ai/rules/js.md` |
| Bagian kode mana mengerjakan apa, cara menjalankan, status alur berkas? | `CLAUDE.md` |
| Apa yang ada sekarang, apa yang diputuskan, apa yang terbuka, apa berikutnya? | `memory/PRD.md` |

Tiap fakta tinggal di tepat satu dokumen; tautkan, jangan menyalin. Jangan membuat berkas pelacak atau memori lain (`BACKLOG.md`, `memory.md`, `state.md`, `notes.md`, ...); satu-satunya pengecualian sementara adalah brief di `docs/briefs/`, yang dihapus saat pekerjaannya selesai. Gagasan dan tindak lanjut masuk ke "Pertanyaan terbuka" atau "Langkah berikutnya" di `memory/PRD.md`, atau ke issue GitHub.

## 3. Alur kerja per tugas

0. **Brief dulu (fitur dan setiap perubahan perilaku, kontrak, skema, atau keamanan).** Salin `docs/briefs/TEMPLATE.md` ke `docs/briefs/NNNN-judul-singkat.md` (nomor bebas berikutnya), isi tujuan, kriteria penerimaan, di luar cakupan, dan asumsi, lalu usulkan ke pemilik. Revisi sampai pemilik berkata "setuju"; baru kemudian isi `Status: approved` dan `Approved: <tanggal>` dan mulai bekerja. Daftar periksa brief adalah satu-satunya daftar aturan yang harus terpenuhi: tidak ada yang disebut selesai selama masih ada butir `[ ]`; butir yang tidak berlaku dicentang dengan `N/A: <alasan>`. Bila semua tercentang, hapus brief di commit penutup (riwayat tetap di git; keputusan yang bertahan menjadi satu baris di `memory/PRD.md`). Perbaikan kecil, perubahan teks, atau perubahan dokumen saja tidak butuh berkas brief: pesan commit memuat `Brief: none: <alasan>` dan laporan menjawab daftar periksa yang sama. Saat diam, `docs/briefs/` hanya berisi `TEMPLATE.md`; berkas lain di sana adalah pekerjaan yang belum selesai. `.githooks/commit-msg` (`scripts/check-brief.sh`) menolak commit kode tanpa brief yang disetujui atau `Brief: none:`, dan menolak menutup brief yang masih punya butir `[ ]`; `BriefGateTest` dan `BriefsTest` menjaga keduanya. Hook yang sama menjalankan `scripts/check-weakened.sh` (dan `scripts/verify.sh` menjalankannya lagi atas commit yang akan di-push), yang menolak commit yang melonggarkan pemeriksaan kecuali memuat `Weakened: <alasan>` (`WeakenedCheckGateTest`). Aturan ini mengikat setiap agent sama.
1. **Pahami.** Perjelas permintaan yang ambigu atau berdampak besar lebih dulu. Rujukan luar (tangkapan layar, proyek lain, kode yang ditempel) berlaku hanya untuk aspek yang disebut pemilik: sebutkan apa yang diadopsi, sisanya tetap mengikuti aturan proyek ini, dan tanyakan bila rujukan bertentangan dengan aturan tertulis.
2. **Muat konteks dan skill.** Jalankan urutan membaca bagian 1 dan sebut skill bagian 5 yang dipakai (atau "tidak ada" dan alasannya) sebelum suntingan pertama.
3. **Kerjakan** mengikuti berkas aturan dan skill yang cocok. Setelah dua atau tiga percobaan gagal dengan pendekatan yang sama, berhenti: sebut dugaan penyebabnya dan ganti pendekatan atau tanyakan, jangan mengulang atau mencari jalan memutari pemeriksaan.
4. **Verifikasi.** Jawab sembilan pertanyaan "Standar produksi" di `.ai/rules/php.md` di laporan. Lalu jalankan yang diminta "Sebelum selesai" di tiap berkas aturan yang cocok (test tersempit dulu, lalu semuanya). Selesai berarti terverifikasi, bukan "tampak benar": `scripts/verify.sh` (pelemahan dan baris Brief, audit dependency, `.env` vs `.env.example`, Pint, PHPStan, semua test, `npm run check`, `npm run duplicates`, TypeScript, build) adalah seluruh gerbang, berjalan pada setiap `git push` lewat `.githooks/pre-push`, dan `composer verify` menjalankannya manual (`composer setup` memasang hook dengan `git config core.hooksPath .githooks`). Skrip itu satu-satunya definisi gerbang: CI juga menjalankannya, sehingga push dengan `--no-verify` tertangkap sesudahnya. Untuk pekerjaan UI, teks, dan komentar, Delivery Gate antislop (bagian 5) bagian dari langkah ini. Sebut dalam satu baris orang yang akan menemui perubahan ini dan saat ia paling mungkin bingung atau tertahan, dan apa yang dilakukan untuk itu (prinsip di atas).
4a. **Cocokkan dokumen dan kode (setiap test, audit, dan perubahan, tanpa diminta).** Tugas belum selesai sebelum dokumen yang menggambarkan apa yang disentuh atau diperiksa dibandingkan dengan kodenya, dua arah: baca klaim dokumen, baca kode yang mengerjakannya, dan perbaiki yang salah di commit yang sama (klaim yang tidak ditepati kode, atau perilaku yang tidak disebut dokumen mana pun). Cakupannya `AGENTS.md`, `CLAUDE.md`, `memory/PRD.md`, dan `.ai/rules/*`. `tests/Feature/DocsMatchCodeTest.php` mengerjakan separuh mekanisnya pada setiap run (setiap berkas, kelas, perintah, dan variabel yang disebut dokumen ada); membandingkan apa yang dikatakan dengan apa yang dilakukan adalah tugas Anda.
5. **Catat.** Perbarui `memory/PRD.md` bila yang ada berubah (Kondisi sekarang), keputusan dibuat, atau pertanyaan dibuka/ditutup; perbarui tabel arsitektur `CLAUDE.md` bila peta kode berubah. Tulis ulang di tempat; kepala berkas PRD memuat aturannya.
6. **Commit dan push** (bagian 4).
7. **Laporan:** baris pertama `Brief: NNNN-judul-singkat (disetujui <tanggal>)` atau `Brief: none: <alasan>`, lalu baris `Dokumen ↔ kode:` (dokumen mana dibandingkan dengan kode mana, apa yang basi dan diperbaiki, atau tidak ada), lalu apa yang berubah, apa yang diverifikasi, apa yang dilewati dan mengapa, dan langkah berikutnya. Jangan pernah meninggalkan tiruan atau TODO diam-diam.

## 4. Git dan otonomi

- Bekerja hanya di `v3.0.0-alpha`. Bila tugas selesai dan terverifikasi, commit dan push ke `origin v3.0.0-alpha` tanpa menunggu persetujuan: commit kecil yang logis, pesannya menjelaskan alasannya. Jangan commit atau push ke `main`, jangan force-push, jangan commit `.env`, kredensial, atau rahasia yang dibuat. Commit yang menyentuh kode butuh brief yang disetujui atau baris `Brief: none: <alasan>` (langkah 0); `composer setup` memasang hook, dan `git commit --no-verify` tidak pernah dipakai.
- Ini proyek lokal yang baru: jangan meminta izin untuk tindakan lokal rutin (test, migrasi di database dev, memperbaiki temuan sendiri). Minta izin hanya untuk tindakan merusak atau tak bisa dibatalkan di luar repositori (termasuk mereset database: `CLAUDE.md`), dan untuk keputusan pemilik: kebijakan penamaan, perilaku produk, status berkas baru, dan dependency yang tidak jelas dibutuhkan tugas (sebut dependency yang ditambahkan di laporan dan di `memory/PRD.md`).
- Jangan pernah mengetik kredensial Codex milik pemilik; login uji memakai akun yang diberikan pemilik untuk itu.

## 5. Skill dan alat: kapan memakai yang mana

Bagian ini satu-satunya tempat yang menyebut kapan sebuah skill dipakai; berkas aturan atau deskripsi skill tidak mengulangnya. Skill datang dari dua sumber dan keduanya diterbitkan ke `.claude/skills/` (dibuat ulang, tidak di-commit): milik Laravel Boost (`boost.json`) dan skill antislop vendored di `.ai/skills/` (di-commit). Setelah clone baru jalankan `php artisan boost:update` untuk membuat ulang folder skill dan blok Boost. Agent yang tidak punya folder skill sendiri (semua kecuali Claude Code, yang terdaftar di `boost.json`) membaca skill langsung dari `.ai/skills/<nama>/SKILL.md`, atau untuk skill Boost dari foldernya di `vendor/laravel/boost/.ai/`, sebelum suntingan pertama jenis pekerjaan itu. `tests/Feature/SkillsRegistryTest.php` gagal bila folder skill di `.ai/skills/` tidak disebut di bagian ini.

| Skill | Status di sini | Pakai bila | Jangan pakai bila |
| --- | --- | --- | --- |
| `laravel-best-practices` | **Aktif, setiap tugas PHP** | menulis, mengubah, atau meninjau PHP: controller, model, migrasi, request, policy, job, command, service, query, config. Petakan tiap urusan ke berkas aturannya di indeks skill dan baca sebelum menyunting. | tugas tidak menyentuh PHP (dokumen saja) |
| `testing-best-practices` | **Aktif** | menulis, memperbaiki, atau meninjau test apa pun | tidak ada test yang ditambah atau diubah |
| `inertia-react-development` | **Aktif untuk layar** | halaman, form, navigasi, prop tertunda, `useForm`, `router` di `resources/js` | pekerjaan backend saja |
| `wayfinder-development` | **Aktif untuk layar** | layar memanggil route atau aksi controller (`@/actions`, `@/routes`) | tidak ada pemanggilan route dari layar |
| `tailwindcss-development` | **Aktif untuk UI** | menulis atau memperbaiki kelas Tailwind di `resources/js` | pekerjaan backend saja |
| `infer-conventions` | Manual saja (`/infer-conventions`) | pemilik meminta, untuk mencatat konvensi nyata ke `.ai/rules` (butuh 3 contoh konsisten atau lebih) | tanpa permintaan |

<!-- antislop:start -->
### antislop: penyaring untuk UI, teks, orang, tata letak ponsel, dan komentar kode

Divendor tangan dari <https://github.com/miqdadbadjuber/anti-slop> (MIT; rilis di `.ai/skills/antislop/VERSION`, lisensi di sebelahnya) ke `.ai/skills/`, sama persis dengan yang dipakai codex. Pemasang, server kontras MCP, dan pintu plugin-nya tidak dipakai. Jangan menyunting folder ini dengan tangan (`SkillsRegistryTest` memeriksa setiap berkas terhadap `MANIFEST.sha256`, dan formatter melewatinya): perbarui dengan menggantinya dengan rilis yang lebih baru, lalu catat versi dan checksum barunya. Intinya (`antislop`, aturan R-01 sampai R-38, tiga tingkat, Delivery Gate) dimuat bersama skill tugasnya.

| Skill | Pakai bila | Jangan pakai bila |
| --- | --- | --- |
| `antislop` (inti) | bersama skill mana pun di bawah, sebelum suntingan pertama jenis pekerjaan itu | tugas tidak menyentuh urusan di bawah (logika backend, test, migrasi, dokumen tentang kode) |
| `antislop-ui` | membangun atau menyunting layar atau komponen: warna, tata letak, komponen, dekorasi, gerak, dan layar aplikasi (dashboard, tabel, keadaan kosong, status) | pekerjaan backend saja |
| `antislop-copywriting` | menulis atau menyunting teks yang dibaca orang: label dan pesan UI, email, halaman error, dan prosa dokumen | komentar kode (pakai `antislop-code`) |
| `antislop-human` | pekerjaan UI yang harus tetap bisa dipakai semua orang: kontras (jalankan `.ai/skills/antislop-human/contrast-check.py`, jangan mengklaim lolos tanpa menjalankannya), keyboard, fokus, keadaan | tidak ada UI yang disentuh |
| `antislop-layoutmobile` | tata letak yang harus mengalir ulang antara lebar ponsel dan desktop | tidak ada perubahan tata letak |
| `antislop-code` | menulis atau menyunting komentar kode (PHP, TypeScript) | tidak ada komentar yang ditulis atau disentuh |

- **Mode:** pemilik memilih DURING (aturan diterapkan sambil menulis) untuk proyek ini, sama dengan codex. Umumkan `antislop active: during (session override).` sekali per sesi sebelum suntingan pertama dan jangan menanyakan mode; AFTER (audit dengan temuan bernomor di `anti-slop/audit-NNN-YYYY-MM-DD.md`) hanya bila pemilik meminta audit pekerjaan yang sudah ada.
- **Arah dan pilihan tetap pemilik** (tanpa `DESIGN.md`, set ikon, tema, kepadatan) ditulis sekali di `.ai/rules/js.md`, bagian "antislop: arah dan pilihan pemilik". Aturan yang bertabrakan dengannya disebut ke pemilik, tidak diikuti atau dilanggar diam-diam (R-37).
- **Gerbang di laporan:** pekerjaan UI, teks, dan komentar diakhiri Delivery Gate antislop berupa baris PASS/FAIL dengan bukti, setelah baris `Dokumen ↔ kode:`. Yang sudah ditegakkan mesin (`CommentHygieneTest`, `npm run check`, `ArchitectureTest`) dikutip sebagai bukti, tidak dijalankan ulang manual.
<!-- antislop:end -->

Alat MCP Boost, bila tersambung, lebih diutamakan daripada tebakan shell: `search-docs` sebelum perubahan yang bergantung pada API atau perilaku versi Laravel/Inertia/Pest; `database-schema` sebelum menulis migrasi atau query; `database-query` untuk pemeriksaan database baca-saja; `last-error` dan `read-log-entries` untuk mendiagnosis kegagalan; `browser-logs` untuk galat di peramban.

## 6. Pengecualian dari bawaan Boost

- Boost meminta pemilik menjalankan seluruh test: di sini Anda menjalankannya sendiri (`.ai/rules/tests.md`).
- Boost meminta membuat berkas dokumentasi hanya bila diminta: memperbarui `AGENTS.md`, `CLAUDE.md`, `.ai/rules/*`, dan `memory/PRD.md` adalah bagian dari pekerjaan dan sudah diminta pemilik. Membuat berkas dokumentasi lain tetap butuh permintaan.
- Boost melarang folder dasar baru tanpa persetujuan: `.ai/`, `.githooks/`, `memory/`, `docs/briefs/`, dan `scripts/` sudah disetujui; yang lain belum.
- Boost menyebut Laravel Cloud untuk deploy: abaikan. Pemilik men-deploy ke VPS sendiri; skill dan CLI Laravel Cloud tidak dipakai.
- Tetap berlaku dari Boost: tanpa perubahan dependency, dan tanpa membuat model di tinker, tanpa persetujuan.

## 7. Yang tidak bisa ditawar (walau tidak membaca yang lain)

Satu baris masing-masing; aturan dan alasannya ada di berkas yang disebut, bukan di sini.

- Setiap perubahan data bisnis, kejadian autentikasi, ekspor, dan baca data sensitif meninggalkan jejak audit: `.ai/rules/audit-log.md`.
- Jangan pernah menyunting migrasi yang sudah dijalankan di mana pun, dan jangan mereset database: `.ai/rules/database.md`.
- Jangan menimpa berkas yang belum dibaca utuh; pakai suntingan terarah.
- Jangan menjalankan `vp check --fix` pada `resources/js`: `.ai/rules/js.md`.
- Setiap perubahan memenuhi standar produksi dan diperiksa test: `.ai/rules/php.md`.
- Apa yang ditegakkan server tentang karakter yang diketik, ditegakkan juga oleh isian: `.ai/rules/js.md`.
- Dokumen dan kode dicocokkan di setiap test, audit, dan perubahan (langkah 4a); dokumen basi adalah cacat yang diperbaiki di commit yang sama.
- Tanpa rahasia di kode, tiruan diam-diam, gerbang yang dilewati, atau galat yang ditelan (`.ai/rules/php.md`); jangan pernah `git push --no-verify`, dan jangan merangkai push setelah pemeriksaan yang kegagalannya tidak menghentikan rangkaian.

# Bagian 2: Pedoman Laravel Boost (dibuat otomatis; praktik menulis kode; jangan diubah)

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record a rule with `record-rule` only when the user explicitly asks for one. Instructions for the work at hand are not rules, no matter how emphatic: "remove this typo", "use X here" are work to do, not rules to record. Never record a rule on your own initiative, as a byproduct of a change, or to summarize what you just did. When the user does ask, pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Use `record-rule` rather than your native memory or notes tool, because native memory is personal and session-scoped, while only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

</laravel-boost-guidelines>
