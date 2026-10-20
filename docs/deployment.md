# Deployment SIPEBRI ke server sendiri

Untuk pemilik atau operator yang memasang SIPEBRI di VPS (bukan Laravel Cloud) dan perlu tahu apa yang diisi di `.env`, perintah apa yang dijalankan saat pasang dan saat memperbarui, dan apa yang harus berjalan terus (penjadwal). Dokumen ini menggantikan komentar penjelasan yang dulu ada di `.env.example`; berkas itu kini hanya berisi variabel.

**Belum teruji di server sungguhan** (dicatat di `memory/PRD.md`): pasang dari nol, penjadwal (`schedule:run`), `audit:verify` pada data besar, dan foto ke S3 pada kondisi buruk. Yang tertulis di bawah adalah apa yang dilakukan kode, bukan hasil uji produksi.

## Kebutuhan

PHP 8.3 dengan ekstensi yang dipakai Laravel 13 dan `phpredis` hanya bila `REDIS_*` dipakai, MySQL, Node.js 22 untuk membangun aset (`npm run build`), dan Composer 2. Aplikasi harus dilayani lewat **HTTPS**: tombol "Tandai lokasi" memakai GPS ponsel, yang ditolak peramban pada `http://` biasa.

## Pasang dari nol

```bash
git clone https://github.com/bprbangunarta/sipebri.git && cd sipebri
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate   # lalu isi .env (bagian di bawah)
php artisan migrate --force
php artisan db:seed --force                        # aman diulang; mengisi izin, peran, dan data referensi
php artisan storage:link                           # hanya bila ATTACHMENTS_DISK=public
npm ci && npm run build
```

`db:seed` bersifat idempoten: hanya menambah yang belum ada dan tidak menimpa yang diubah Super Admin di layar. `UserSeeder` tidak dijalankan di produksi; pengguna datang dari Codex saat masuk.

## Memperbarui

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan optimize:clear
```

Migrasi lama tidak pernah diubah; skema berubah lewat migrasi baru, jadi memperbarui selalu `migrate --force`. Mode pemeliharaan (halaman 503 bertema, jalan walau aplikasi tidak bisa boot): `php artisan down --render="errors::503" --retry=60`, dijalankan setelah `npm run build` dan tidak saat `public/hot` ada; `php artisan up` mengakhirinya.

## Yang harus berjalan terus

Penjadwal Laravel harus dipanggil tiap menit oleh cron server:

```cron
* * * * * cd /path/ke/sipebri && php artisan schedule:run >> /dev/null 2>&1
```

Ia menjalankan `audit:verify` tiap hari pukul 02:00 (memeriksa rantai hash audit log). Tanpa cron ia tidak jalan. Sistem **tidak pernah menghapus audit sendiri**: retensi dijalankan pemilik dengan tangan (lihat "Retensi audit log" di bawah). Tidak ada pekerja antrean yang wajib: kode OTP email dikirim sinkron saat orang menunggu.

## Isi `.env`

| Variabel                                                                                                                                                      | Arti                                                                                                                    | Untuk produksi                                                                                                         |
| ------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------- |
| `APP_ENV`, `APP_DEBUG`, `APP_URL`                                                                                                                             | lingkungan, mode galat, alamat dasar (dipakai tautan foto dan email)                                                    | `production`, `false`, alamat `https://` yang sebenarnya                                                               |
| `APP_KEY`                                                                                                                                                     | kunci enkripsi (rahasia verifikasi dua langkah, kode pemulihan, dan NIK anggota komite dienkripsi dengannya)            | dibuat `php artisan key:generate`, disimpan aman; kehilangan kunci berarti data terenkripsi itu tidak terbaca          |
| `DB_*`                                                                                                                                                        | koneksi MySQL                                                                                                           | akun khusus aplikasi, bukan `root` tanpa kata sandi                                                                    |
| `SESSION_SECURE_COOKIE`                                                                                                                                       | cookie sesi hanya dikirim lewat HTTPS (tidak ada di `.env.example`; bawaannya mengikuti skema permintaan)               | `true`                                                                                                                 |
| `SESSION_*`, `SESSION_COOKIE`                                                                                                                                 | sesi di tabel `sessions`; nama kuncinya sendiri supaya proyek lain di host yang sama tidak saling menimpa               | `SESSION_LIFETIME` sesuai kebijakan; nama cookie tetap `sipebri_session`                                               |
| `CODEX_ENDPOINT`, `CODEX_TOKEN`                                                                                                                               | masuk lewat Codex (`/api/web-auth`)                                                                                     | wajib; tanpa `CODEX_ENDPOINT` tidak ada yang bisa masuk                                                                |
| `CODEX_ID`, `CODEX_SECRET`                                                                                                                                    | master nasabah lewat OAuth Codex                                                                                        | wajib; tanpanya pencarian nasabah gagal (503)                                                                          |
| `CODEX_VERIFY`                                                                                                                                                | memeriksa sertifikat TLS Codex                                                                                          | `true`; bila `false` di produksi, log menulis peringatan di tiap panggilan                                             |
| `CODEX_TIMEOUT`, `CODEX_CONNECT_TIMEOUT`, `CODEX_TOKEN_SKEW`                                                                                                  | detik batas permintaan (10), batas sambung (5), dan berapa detik sebelum token habis ia diperbarui (300)                | bawaan cukup                                                                                                           |
| `MAIL_MAILER`                                                                                                                                                 | `log` menulis email ke `storage/logs` (tidak ada yang terkirim); untuk kode OTP email sungguhan pakai `smtp`            | `smtp` dengan `MAIL_HOST`, `MAIL_PORT` (587, STARTTLS), `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` yang sah |
| `MAIL_TIMEOUT`                                                                                                                                                | detik menunggu server SMTP (10); kode dikirim saat orang menunggu                                                       | bawaan cukup                                                                                                           |
| `ATTACHMENTS_DISK`                                                                                                                                            | tempat foto survei: `public` (server ini) atau `s3` (ember AWS, foto dilayani lewat tautan bertanda tangan 30 menit)    | pilih satu; untuk `s3` isi `AWS_*`                                                                                     |
| `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT`                               | ember untuk `s3` (`AWS_ENDPOINT` dan gaya path untuk penyimpanan yang kompatibel S3)                                    | hanya bila `ATTACHMENTS_DISK=s3`                                                                                       |
| `REVERSE_GEOCODING`                                                                                                                                           | menampilkan alamat perkiraan di bawah posisi tersimpan lewat Nominatim OpenStreetMap; **posisi dikirim ke layanan itu** | `false` bila koordinat tidak boleh keluar, atau isi `GEOCODER_ENDPOINT` dengan Nominatim sendiri                       |
| `GEOCODER_ENDPOINT`, `GEOCODER_USER_AGENT`, `GEOCODER_TIMEOUT`                                                                                                | alamat layanan, identitas pemanggil (wajib untuk Nominatim publik), dan batas detik (3)                                 | isi `GEOCODER_USER_AGENT` dengan kontak yang bisa dihubungi                                                            |
| `MFA_ENABLED`                                                                                                                                                 | verifikasi dua langkah (aplikasi authenticator atau email), dipilih tiap pengguna; `false` mematikan seluruh fitur      | `true`                                                                                                                 |
| `AUDIT_RETENTION_YEARS`                                                                                                                                       | tahun yang disimpan perintah manual `audit:prune` (5); tidak ada yang berjalan otomatis                                 | bawaan 5 sesuai kebijakan pemilik                                                                                      |
| `APP_MAINTENANCE_DRIVER`, `APP_MAINTENANCE_STORE`                                                                                                             | tempat status pemeliharaan; `file` cukup untuk satu server, `database` bila lebih dari satu                             | `file` untuk satu server                                                                                               |
| `CACHE_STORE`, `QUEUE_CONNECTION`, `BROADCAST_CONNECTION`, `FILESYSTEM_DISK`, `LOG_*`, `BCRYPT_ROUNDS`, `REDIS_*`, `MEMCACHED_HOST`, `PHP_CLI_SERVER_WORKERS` | bawaan kerangka Laravel; SIPEBRI memakai `database` untuk cache, sesi, dan antrean                                      | tinggalkan bawaan kecuali ada alasan                                                                                   |

`php artisan env:check` membandingkan `.env` dengan `.env.example` dan melaporkan variabel yang kurang; dengan `--add` ia menambahkannya, dan kecuali `.env` berisi `APP_ENV=local` atau `testing` (termasuk `staging` atau tanpa `APP_ENV`) ia tidak menyalin nilai contoh yang tidak aman untuk server (mis. `APP_DEBUG=true`) melainkan menyebut namanya supaya Anda menulis nilai sebenarnya. Ia tidak memeriksa nilai yang sudah ada. Gerbang `scripts/verify.sh` menjalankannya di mesin pengembang.

## Cadangan dan pemulihan

Yang perlu dicadangkan: database MySQL (termasuk `audit_logs`, yang tidak boleh diubah atau dihapus oleh siapa pun) dan foto survei (folder `storage/app/public` atau ember S3). `php artisan audit:verify` sesudah pemulihan membuktikan rantai audit utuh. Jadwal dan tempat cadangan adalah keputusan pemilik, belum ditetapkan.

## Belum diputuskan

- **Retensi audit log (diputuskan pemilik 2026-10-20).** Kebijakan retensi 5 tahun diatur di luar sistem dan dijalankan pemilik sendiri, tidak otomatis. Cara yang dipakai: `php artisan audit:prune --dry-run` untuk melihat, lalu `php artisan audit:prune` (meminta konfirmasi; `--force` bila tanpa terminal). Perintah itu menghapus yang lebih tua dari `AUDIT_RETENTION_YEARS` dan menulis jangkar supaya `audit:verify` tetap hijau. Menghapus baris lewat SQL tanpa jangkar membuat verifikasi harian melaporkan rantai putus.
- **Proxy tepercaya.** Di balik proxy atau penyeimbang beban, alamat IP yang dicatat dan yang membatasi percobaan masuk adalah IP proxy sampai proxy yang dipercaya dikonfigurasi.
- **Cadangan, pemantauan, dan rotasi kunci** belum dirancang.
