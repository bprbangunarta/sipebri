---
paths:
    - 'app/**'
    - 'routes/**'
    - 'database/**'
    - 'bootstrap/app.php'
---

# Audit log (wajib, standar OJK)

Aturan bisnis proyek ini: setiap perubahan data bisnis, kejadian autentikasi, ekspor, dan baca data sensitif meninggalkan jejak audit. Berkas ini satu-satunya sumber aturan itu.

- Semua perubahan data bisnis dicatat lewat trait `App\Audit\Auditable` (model baru yang memuat data kredit/master/akses **harus** memakainya). Kejadian non-model (login, MFA, perubahan izin, akses ditolak, cek nasabah, lihat berkas) lewat `App\Audit\Audit::record()`.
- Tabel `audit_logs` append-only dengan rantai hash HMAC; jangan pernah update/delete barisnya. Verifikasi: `php artisan audit:verify` (terjadwal harian) atau tombol di halaman Audit Log.
- Rahasia tidak boleh masuk log (`Audit::REDACTED`); NIK pengguna tidak pernah masuk log (`User::auditExcept()` mengecualikan `nik` dan `nik_hash`; yang tercatat hanya `users.nik_changed` dengan sumbernya), dan NIK nasabah pada cek nasabah (`customers.lookup`) hanya 4 digit terakhir. Aksi baru yang mengubah data lewat query builder/pivot (tanpa event model) harus mencatat manual.
- Penolakan juga tercatat (akses ditolak), dan mengulang sesuatu yang tidak mengubah apa pun (seeder, sinkron Codex tanpa perubahan) tidak menulis apa pun.
- Username yang diketik pada percobaan masuk gagal disimpan hanya bila berbentuk username atau email, selain itu `[bukan username]` (orang kadang mengetik kata sandi di kolom itu).
- Penguncian login dicatat sekali per penguncian, tidak per permintaan yang ditolak sesudahnya (jejak tidak boleh tumbuh karena serangan).
- Membuka audit log juga tercatat (`audit_logs.viewed`, sekali per 10 menit per orang). Verifikasi rantai menuntut entri terakhir yang pernah diperiksa (`audit_verified_heads`) masih ada, sehingga penghapusan di ujung terdeteksi. Akses ke audit log hanya baca: tidak ada layar, tombol, atau panggilan yang mengubah atau menghapus entri. Layar menyediakan filter, detail sebelum/sesudah, ekspor CSV (tercatat), dan verifikasi rantai.
