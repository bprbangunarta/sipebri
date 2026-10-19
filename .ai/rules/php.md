---
paths:
    - 'app/**'
    - 'routes/**'
    - 'config/**'
    - 'bootstrap/**'
    - 'lang/**'
    - '.env.example'
    - 'database/seeders/**'
    - 'database/factories/**'
    - 'composer.json'
    - 'phpunit.xml'
    - 'phpstan.neon'
    - 'pint.json'
    - '.jscpd.json'
---

# Kode aplikasi: tambahan khas SIPEBRI

Tambahan proyek di atas standar `.ai/rules/quality.md` (sembilan pertanyaan, mutu desain, dependency) dan `.ai/rules/laravel.md`; yang standar tetap berlaku dan tidak diulang di sini. Panduan Laravel umum: blok Boost di `AGENTS.md` dan skill `laravel-best-practices`.

## Pengecualian dari aturan standar

- **Tidak ada pengecualian untuk `.env.example`.** Berkas itu hanya berisi variabel (variabel opsional ditulis `# NAME=value`); penjelasan tiap variabel ada di `docs/deployment.md`, dan `EnvExampleTest` gagal bila ada baris komentar penjelasan (keputusan pemilik 2026-10-20).
- **Tidak ada pengecualian `catch (Throwable)` milik proyek.** Aturan standar (`quality.md`, "Errors and validation") sudah mengizinkan satu bentuk: menguraikan masukan yang bermusuhan di mana satu-satunya jawaban adalah penolakan polos, dengan komentar yang mengatakannya (kini `App\Support\PhotoExif`: metadata foto yang rusak berarti foto tanpa posisi). Penyimpanan alamat perkiraan (`ReverseGeocoder`) menangkap galat tetapi memanggil `report()`, jadi tercatat dan tidak melanggar apa pun.

## Sembilan pertanyaan: bentuk di SIPEBRI

Jawabannya masuk laporan, seperti di `quality.md`; nomornya sama dengan nomor di sana (pertanyaan 2, 4, dan 9 tidak punya tambahan khas SIPEBRI). Yang khas di sini:

- **1, siapa:** middleware `permission:<modul>.<aksi>` atau `role:Super Admin` di route, atau policy, sebelum validasi dan sebelum record dicari. Pemohon yang anggota komite tidak memutus berkasnya sendiri.
- **3, hasil:** halaman error Inertia untuk 403/404/419, galat validasi per kolom, toast untuk hasil aksi. Pesan untuk orang berupa kalimat Indonesia (pesan standar dari `laravel-lang`, jangan ditulis ulang). Teks eksepsi, nama kelas, SQL, dan jalur tidak pernah sampai ke jawaban, log untuk orang, atau audit log.
- **5, sisa kegagalan:** berkas lampiran (foto survei) ditulis ke disk lampiran (`config('filesystems.attachments')`) dan dibersihkan bila penyimpanan gagal.
- **6, biaya:** setiap panggilan keluar (Codex, Nominatim, S3, SMTP) punya batas waktu sambung dan total, dan kegagalannya menurunkan satu fitur saja; apa pun yang mengirim email atau menulis penyimpanan dibatasi per sasaran dan per waktu.
- **7, bocor:** NIK tersamar (4 digit terakhir) di layar dan di jejak cek nasabah, NIK pengguna tidak pernah masuk audit log; kata sandi, token, dan kode tidak masuk audit log (`Audit::REDACTED`).
- **8, tercatat:** lewat `Auditable` dan `Audit::record()` (`.ai/rules/audit-log.md`); perubahan sensitif membawa alasan; penolakan akses juga tercatat; mengulang sesuatu yang tidak mengubah apa pun (seeder, sinkron Codex tanpa perubahan) tidak menulis apa pun.

## Mutu desain: contoh di SIPEBRI

- Batas ukuran dan sarang dijaga `DesignQualityTest` (berkas PHP maks 400 baris, berkas TypeScript maks 500, method maks 80 baris dan empat tingkat sarang). Kode yang sudah melanggar sebelum 2026-10-19 tercantum di test itu dengan alasan; entri baru butuh persetujuan pemilik, dan entri yang tidak lagi cocok membuat test gagal.
- Aturan dan query tinggal di `App\Support` dengan nama menurut kerjanya (`ApprovalFlow`, `RepaymentCapacity`, `LoanListOrder`), bukan di controller. Enum untuk himpunan tertutup (`LoanStatus`, `RoleName`).
- Pustaka luar dipakai lewat satu kelas milik proyek (`CodexClient`, `ReverseGeocoder`).
- Kasus baru ditambahkan di satu tempat: registri `config/references.php`, `App\Support\Modules`, `Navigation::definition()`, kasus enum, atau baris config.
- Komentar berbahasa Inggris, bilang mengapa, bukan apa (`antislop-code`, dijaga `CommentHygieneTest`). `npx jscpd` (`.jscpd.json`) menolak blok salinan 20 baris atau lebih di `app` dan `resources/js`.

## Dependency

Gerbang menjalankan `composer audit --locked` dan `npm audit --omit=dev` (`scripts/verify.steps`). Temuan yang tidak bisa diperbaiki ditulis sebagai risiko yang diterima di `memory/PRD.md`.

## Sebelum selesai

`vendor/bin/pint --dirty --format agent` dan `vendor/bin/phpstan analyse --memory-limit=1G`, lalu baca ulang diff terhadap berkas aturan yang dipetakan skill `laravel-best-practices`, lalu `scripts/verify.sh`.
