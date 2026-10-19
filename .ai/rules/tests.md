---
paths:
    - 'tests/**'
    - 'resources/js/**/*.test.ts'
    - 'resources/js/**/*.test.tsx'
---

# Test: tambahan khas SIPEBRI

Tambahan proyek di atas standar `.ai/rules/testing.md`; yang standar tetap berlaku. Skill `testing-best-practices` untuk Pest.

## Database dan luar

Test PHP memakai SQLite memori (`phpunit.xml`), bukan MySQL; perilaku yang bergantung pada MySQL dicek manual dan disebut di laporan. Test tidak pernah memanggil Codex, Nominatim, atau S3 asli (`Http::fake`, `Storage::fake`). Test yang dilewati dilaporkan sebagai dilewati.

## Struktur

Feature test dulu; unit test hanya untuk logika bebas framework. Nama test berbahasa Inggris dan menyebut hasil dan syaratnya. `beforeEach()` hanya berisi konfigurasi. Helper bersama di `tests/Pest.php`: `superAdmin()`, `userWith([...])`, `fakeCustomers()`. Tiap fitur punya `tests/Feature/*Test.php`.

Test layar memakai Vitest + Testing Library (jsdom), berkas `*.test.ts(x)` di samping kodenya. Wajib dites: logika murni (`lib/`), komponen isian yang punya perilaku (format, mask, kursor), dan perilaku aksesibilitas yang mudah rusak. Uji lewat perilaku yang terlihat pengguna (`userEvent.type`, `getByLabelText`), bukan detail implementasi.

## Jejak audit

Setiap fitur yang menyentuh data, autentikasi, hak akses, ekspor, atau baca data sensitif butuh test yang membuktikan jejak auditnya (lihat `tests/Feature/AuditLogTest.php`).

## Menjalankan

Test tersempit yang relevan dulu, lalu seluruh gerbang `scripts/verify.sh` sendiri sebelum commit (ini menggantikan bawaan Boost yang meminta pemilik menjalankannya). Test merender halaman Inertia lewat manifest Vite, jadi `npm run build` dijalankan setelah menambah halaman React (gerbang melakukannya sebelum test).

## Pemeriksaan silang yang dijaga suite

Masing-masing membandingkan dua sisi yang harus sepakat, sehingga perubahan di satu sisi saja gagal. Jangan dilemahkan; perbaiki sisi yang salah. Perilaku gerbang (brief, pelemahan, registri, hook, `verify.sh`) dibuktikan `scripts/selftest.sh`, yang berjalan sebagai langkah gerbang.

| Test                                    | Sisi yang dibandingkan                                                                                  |
| --------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| `scripts/selftest.sh`                   | gerbang dan aturannya: tiap pelanggaran ditolak, tiap pengecualian sah lolos, dengan git sungguhan      |
| `DocsMatchCodeTest`                     | dokumen dan kode (nama yang disebut dokumen harus ada)                                                  |
| `ArchitectureTest`                      | kode dan aturan proyek (audit, `DataTable`, `env()`, penjajaran baris judul + aksi, geolokasi)          |
| `NavigationLinksTest`                   | menu (`Navigation::definition()`) dan route yang terdaftar                                              |
| `SkillsRegistryTest`                    | skill di `.ai/skills`, barisnya di `AGENTS.md` bagian 5, dan berkas vendored yang persis sama           |
| `CommentHygieneTest`                    | komentar dan teks terhadap `antislop-code` dan larangan tanda pisah panjang                             |
| `DesignQualityTest`                     | batas ukuran dan sarang, nama kelas generik, pelapisan, panggilan debug; pengecualian butuh alasan      |
| `BriefsTest`                            | `docs/briefs/TEMPLATE.md` dan setiap brief aktif                                                        |
| `EnvExampleTest`, `EnvCheckCommandTest` | `.env.example` dan konfigurasi; `php artisan env:check` pada `.env` sementara                           |
| `ScriptsHostileInputTest`               | `scripts/check-standards.mjs` di folder dan berkas bernama aneh: kalimat, bukan jejak tumpukan          |
| `ui-guideline.test.ts`                  | token warna di `resources/css/app.css` dan komponen `components/ui` dengan contohnya di `/ui-guideline` |
| `app.contrast.test.ts`                  | token warna dan rasio kontras WCAG                                                                      |
