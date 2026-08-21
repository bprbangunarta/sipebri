# Rencana sinkronisasi data staging (MySQL VPS) → seeder repo

Disepakati 22/06/2026. **Menunggu lampiran user**: `mysqldump` lengkap (struktur + data) dari
database staging di VPS. Belum ada file yang dilampirkan sampai catatan ini dibuat.

## Keputusan user
- Format: `mysqldump` lengkap (struktur + data).
- Acuan bila bentrok: **staging (VPS) selalu menang**, preview disamakan.
- Struktur tabel **tidak berubah** di staging (user hanya mengisi data) → kemungkinan besar tidak
  perlu migrasi baru; tetap wajib dibandingkan untuk memastikan.
- Tabel prioritas: `product_parameters`, `committee_paths` + `committee_tiers`,
  `permissions`/`roles`/`role_has_permissions` (termasuk **urutan** perizinan), `menus`,
  `offices`, `institutions`, `products`.
- **Data pengguna TIDAK disalin** — akun uji yang sekarang tetap dipakai
  (lihat `/app/memory/test_credentials.md`).

## Langkah eksekusi begitu dump masuk
1. Simpan dump ke `/tmp`, jalankan MariaDB lokal (lihat cara di `/app/memory/env_notes.md`),
   impor ke database sementara mis. `stg_import`.
2. Bandingkan per tabel: struktur (`Schema::getColumns`) + isi data vs database preview.
   Laporkan selisihnya ke user sebelum menulis kode.
3. Tulis ulang seeder terkait supaya `migrate:fresh --seed` menghasilkan data identik staging:
   `ProductSeeder`, **`ProductParameterSeeder` (baru — belum ada)**, `CommitteeSeeder` (+ jenjang
   sesuai staging, jangan lagi memakai konstanta default kalau berbeda), `PermissionSeeder`,
   `RoleSeeder`, `MenuSeeder`, `OfficeSeeder`, `InstitutionSeeder`.
   Seeder harus tetap **idempoten** (`updateOrCreate`).
4. Perbarui hitungan pada `tests/Feature/SeederTest.php` sesuai jumlah baru.
5. Uji: `php artisan migrate:fresh --seed` di **SQLite dan MySQL** + `php artisan test`.
6. Perbarui `/app/memory/PRD.md` dan `/app/memory/test_credentials.md` bila ada perubahan akun.

## Rambu-rambu
- Jangan memakai SQL khas SQLite di migrasi/seeder (`typeof`, `PRAGMA`, `||`) dan jangan menulis
  nilai negatif ke kolom unsigned — lihat `/app/memory/env_notes.md`.
- `regions` (82.449 baris) tidak perlu diambil dari dump; sumbernya tetap `RegionSeeder`.
- Tabel transaksi grup Simulasi (`loan_applications`, `collateral_simulations`, dll) dibiarkan KOSONG.
