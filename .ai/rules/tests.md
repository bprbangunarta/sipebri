---
paths:
    - 'tests/**'
    - 'resources/js/**/*.test.ts'
    - 'resources/js/**/*.test.tsx'
---

# Test

## Database

Test PHP memakai SQLite memori (`phpunit.xml`), bukan MySQL; perilaku yang bergantung pada MySQL dicek manual dan disebut di laporan. Test tidak pernah memanggil Codex, Nominatim, atau S3 asli (`Http::fake`, `Storage::fake`). Test yang dilewati dilaporkan sebagai dilewati, tidak pernah sebagai lulus.

## Struktur

Ikuti skill `testing-best-practices`. Tulis feature test dulu; unit test hanya untuk logika bebas framework. Nama test (Inggris) menyebut hasil dan syaratnya. `beforeEach()` hanya berisi konfigurasi, tanpa kondisi. Helper bersama: `superAdmin()`, `userWith([...])`, `fakeCustomers()` di `tests/Pest.php`. Tiap fitur punya `tests/Feature/*Test.php`.

Test layar memakai Vitest + Testing Library (jsdom), berkas `*.test.ts(x)` di samping kodenya. Wajib dites: logika murni (`lib/`), komponen isian yang punya perilaku (format, mask, kursor), dan perilaku aksesibilitas yang mudah rusak (label terhubung ke isian, `aria-describedby`). Uji lewat perilaku yang terlihat pengguna (`userEvent.type`, `getByLabelText`, `toHaveValue`), bukan detail implementasi.

## Deterministik dan mandiri

Test lulus dalam urutan apa pun dan pada hari apa pun: tanpa jam nyata (bekukan), tanpa jaringan nyata (palsukan panggilan keluar), tanpa data produksi, tanpa sisa dari test lain. Perbaikan bug datang dengan test yang gagal sebelum perbaikan. Test yang tidak dijalankan tidak dilaporkan lulus, dan risiko yang hanya dibaca, tidak pernah dijalankan, tidak dilaporkan sudah dites.

## Jejak audit

Setiap fitur yang menyentuh data, autentikasi, hak akses, ekspor, atau baca data sensitif butuh test yang membuktikan jejak auditnya tercatat (lihat `tests/Feature/AuditLogTest.php`).

## Menjalankan

Jalankan test tersempit yang relevan saat bekerja, lalu seluruh suite sendiri sebelum commit: `php artisan test --compact` dan `npm run test`. Ini menggantikan bawaan Boost yang meminta pemilik menjalankan seluruh suite. Test merender halaman Inertia lewat manifest Vite, jadi `npm run build` dijalankan setelah menambah halaman React.

## Dokumen cocok dengan kode

`tests/Feature/DocsMatchCodeTest.php` gagal bila `AGENTS.md`, `CLAUDE.md`, `memory/PRD.md`, atau `.ai/rules/*` menyebut berkas, kelas, perintah `php artisan`, atau variabel lingkungan yang tidak ada di kode. Perbaiki dokumennya (atau pulihkan kodenya); tambahkan entri ke daftar izin test hanya dengan alasan di komentar. Test itu memeriksa bahwa nama ada, bukan bahwa yang dikatakan dokumen benar: perbandingan itu adalah `AGENTS.md` langkah 4a.

## Pemeriksaan silang yang dijaga suite

Masing-masing membandingkan dua sisi yang harus sepakat, sehingga perubahan di satu sisi saja gagal. Jangan dilemahkan; perbaiki sisi yang salah.

| Test                      | Sisi yang dibandingkan                                                                                                                                            |
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `DocsMatchCodeTest`       | dokumen dan kode (nama yang disebut dokumen harus ada)                                                                                                            |
| `ArchitectureTest`        | kode dan aturan proyek (audit, `DataTable`, `env()`, penjajaran baris judul + aksi, geolokasi)                                                                    |
| `NavigationLinksTest`     | menu (`Navigation::definition()`) dan route yang terdaftar                                                                                                        |
| `SkillsRegistryTest`      | folder skill di `.ai/skills`, barisnya di `AGENTS.md` bagian 5, dan berkas vendored yang persis sama byte demi byte                                               |
| `CommentHygieneTest`      | komentar dan teks terhadap `antislop-code` dan larangan tanda pisah panjang                                                                                       |
| `DesignQualityTest`       | batas ukuran dan sarang, nama kelas generik, pelapisan, panggilan debug; pengecualian tercantum butuh alasan                                                      |
| `BriefGateTest`           | `scripts/check-brief.sh` dijalankan sungguhan di repositori sementara: commit mana yang lolos dan mana yang ditolak                                               |
| `WeakenedCheckGateTest`   | `scripts/check-weakened.sh` dijalankan sungguhan: tiap cara melonggarkan pemeriksaan ditolak, tiap pengecualian sah lolos                                         |
| `DependencyAuditGateTest` | `scripts/check-dependencies.sh` dijalankan dengan `composer` dan `npm` palsu: bersih, ada temuan, tak terjangkau                                                  |
| `BriefsTest`              | `docs/briefs/TEMPLATE.md` dan setiap brief aktif: nama, status, judul, butir tercentang, tanpa brief selesai yang tertinggal                                      |
| `VerifyGateTest`          | `scripts/verify.sh` dan hook `pre-push` dijalankan dengan alat palsu: rentang commit yang diperiksa, dan penolakan bila hook tidak terpasang                      |
| `GateDefinitionTest`      | `composer.json`, hook, dan workflow CI: hanya memanggil `scripts/verify.sh` dan tidak mendaftar pemeriksaan sendiri                                               |
| `EnvExampleTest`          | `.env.example` dan konfigurasi (`env()` di `config/*.php`)                                                                                                        |
| `EnvCheckCommandTest`     | `php artisan env:check` dijalankan pada berkas `.env` sementara: yang kurang, `--add`, nilai rahasia tidak tercetak                                               |
| `ScriptsHostileInputTest` | `scripts/check-standards.mjs` di folder dan berkas bernama aneh: pelanggaran tetap dilaporkan dengan berkas dan baris, galat berupa kalimat, bukan jejak tumpukan |
| `ui-guideline.test.ts`    | token warna di `resources/css/app.css` dan komponen `components/ui` dengan contohnya di `/ui-guideline`                                                           |
| `app.contrast.test.ts`    | token warna dan rasio kontras WCAG                                                                                                                                |
