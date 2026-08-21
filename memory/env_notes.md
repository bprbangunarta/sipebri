# Catatan Lingkungan — AdminKit

## PHP hilang setelah pod restart (terjadi 2026-06-15)
Gejala: supervisor `frontend` BACKOFF, log `php: not found`, preview 502.
Perbaikan (jalankan di background, ±2 menit):

```bash
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y -qq lsb-release ca-certificates apt-transport-https gnupg curl
curl -sSL https://packages.sury.org/php/apt.gpg -o /usr/share/keyrings/deb.sury.org-php.gpg
echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ bookworm main" > /etc/apt/sources.list.d/sury-php.list
apt-get update -qq
apt-get install -y -qq php8.3-cli php8.3-sqlite3 php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl
sudo supervisorctl restart frontend
```

Catatan:
- `vendor/` dan `database/database.sqlite` ada di `/app/adminkit` → persist, tidak perlu composer install ulang.
- Server preview = `php artisan serve --port 3000` lewat script `start` di `/app/frontend/package.json`.
- Composer TIDAK terpasang; instal hanya jika perlu menambah paket PHP.

## Kompatibilitas MySQL/MariaDB (diverifikasi 2026-06-22)
Preview memakai SQLite; deploy VPS memakai MySQL. `php artisan migrate:fresh --seed` sudah diuji
nyata di MariaDB 10.11 (database `stg_sipebri`) dan lulus penuh setelah 3 perbaikan:
1. `2026_08_21_090000_normalize_boolean_flags` — `typeof()` hanya ada di SQLite. Migrasi sekarang
   langsung `return` bila `DB::connection()->getDriverName() !== 'sqlite'`.
2. `2026_08_21_100000_renumber_loan_application_codes` — operator `||` (concat SQLite) berarti OR di
   MySQL. Diganti update per baris (chunk) memakai PHP `substr`, portabel di kedua driver.
3. `SchemaDraftSeeder` — kolom `schema_draft_columns.sort` bertipe **unsignedInteger**, sedangkan
   seeder dulu memakai sort negatif untuk menaikkan kolom utama (SQLite mengabaikan unsigned,
   MySQL error 1264). Sekarang urutan ditulis ulang dengan angka positif 0..n.

Aturan: **jangan pakai SQL mentah khas SQLite** (`typeof`, `PRAGMA`, `||`, `strftime`) di migrasi/seeder,
dan jangan menulis nilai negatif ke kolom unsigned.
Cara uji cepat di pod: `apt-get install -y mariadb-server php8.2-mysql`, jalankan
`mariadbd --user=mysql &`, buat DB + user, salin `.env` dengan `DB_CONNECTION=mysql`, jalankan
`php artisan migrate:fresh --seed`, lalu kembalikan `.env` ke SQLite.
