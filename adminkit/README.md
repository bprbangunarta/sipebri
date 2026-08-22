# SIPEBRI — Sistem Pemberian Kredit (di atas AdminKit Starter Kit)

Starter kit panel admin **compact UI** yang kini dipakai sebagai fondasi **SIPEBRI** (sistem pemberian kredit PT BPR Bangunarta): Laravel 12 + Vue 3 + Inertia.js + TailwindCSS 3 di atas SQLite, lengkap dengan autentikasi, hak akses berbasis peranan, audit trail, pengaturan penampilan, object storage (S3), data referensi (produk/cicilan/bunga mengikuti core banking), dan **aturan komite kredit**.

> Design system mengikuti **FlowDesk** (compact, monokrom) dengan komponen porting **shadcn/ui** dan dukungan **dark mode**.
>
> **Status pengembangan:** fondasi + master data + aturan komite kredit **selesai**. Alur berkas kredit (pengajuan → penjadwalan → survey → analisa → persetujuan komite → notifikasi → akad → pencairan → posting core banking) **belum dibangun** — rancangannya dicatat di `/app/memory/sipebri_discussion.md`.

---

## Daftar Isi

- [Tumpukan Teknologi](#tumpukan-teknologi)
- [Fitur](#fitur)
- [Persyaratan](#persyaratan)
- [Instalasi](#instalasi)
- [Perintah Harian](#perintah-harian)
- [Struktur Proyek](#struktur-proyek)
- [Skema Basis Data](#skema-basis-data)
- [Alur Proses Kredit](#alur-proses-kredit)
- [Integrasi API Codex (Data Nasabah)](#integrasi-api-codex-data-nasabah)
- [Modul Komite Kredit](#modul-komite-kredit)
- [Modul Data Referensi](#modul-data-referensi)
- [Ekspor & Impor Excel](#ekspor--impor-excel)
- [Standar Validasi (WAJIB)](#standar-validasi-wajib)
- [Hak Akses & Peranan](#hak-akses--peranan)
- [Audit Trail](#audit-trail)
- [Notifikasi](#notifikasi)
- [Aksi Massal (Bulk Action)](#aksi-massal-bulk-action)
- [Pengaturan Penampilan (Branding)](#pengaturan-penampilan-branding)
- [Object Storage (S3)](#object-storage-s3)
- [Design System & Konvensi UI](#design-system--konvensi-ui)
- [Tabel Server-side](#tabel-server-side)
- [Rute](#rute)
- [Telescope (Debug)](#telescope-debug)
- [Bahasa & Pesan Validasi](#bahasa--pesan-validasi)
- [Pengujian](#pengujian)
- [Deployment](#deployment)
- [Pemecahan Masalah](#pemecahan-masalah)

---

## Tumpukan Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Backend | Laravel 12, PHP 8.2+ |
| Frontend | Vue 3 (Composition API), Inertia.js 2, Vite 6 |
| Styling | TailwindCSS 3, komponen porting shadcn/ui, `lucide-vue-next` |
| Basis Data | SQLite (mudah diganti MySQL/PostgreSQL) |
| Hak Akses | `spatie/laravel-permission` |
| Penyimpanan | Disk `public` (lokal) atau S3 via `league/flysystem-aws-s3-v3`, dipilih oleh `FILESYSTEM_DISK` |
| Kualitas Kode | Laravel Pint, Prettier |

---

## Fitur

**Autentikasi & Akun**
- Masuk memakai **email, nama pengguna, atau nomor HP** dengan satu kolom kredensial.
- Pembatasan percobaan masuk (rate limit 5 percobaan) + pencatatan percobaan gagal.
- Halaman profil: ubah data diri, unggah/hapus foto profil, ganti kata sandi.

**Manajemen Pengguna**
- Tabel server-side: pencarian, sortir, paginasi, filter **Peranan** (dinamis) dan **Status** (Aktif / Terarsip / Semua).
- **Tambah & ubah lewat halaman tersendiri** (`/users/create`, `/users/{user}/edit`) — 3 kartu: identitas, penempatan & kode, keamanan.
- Kolom pegawai: `role` (cermin peranan Spatie), `office`, `alias`, `mso_code`, `collector_code` (unik, otomatis HURUF BESAR).
- **Arsip (SoftDelete)** menggantikan status aktif: Arsipkan / Pulihkan / Hapus Permanen — per baris maupun massal; pengguna terarsip **tidak dapat masuk**, dan akun sendiri selalu dilewati.
- **Impor Excel** (`.xlsx` sesuai template yang dapat diunduh; baris judul diabaikan, baris tidak valid dilewati, kata sandi kosong diisi acak) dan **Ekspor Excel** mengikuti filter aktif.
- `username`, `email`, `phone` opsional namun **unik**; nomor HP hanya menerima angka (boleh `+`).

**Data Referensi & Komite Kredit** (fondasi SIPEBRI)
- Master mengikuti core banking: **Data Kantor**, **Data Instansi**, **Data Produk**, **Sistem Cicilan**, **Sistem Bunga** (lihat [Modul Data Referensi](#modul-data-referensi)).
- **Komite Kredit**: jalur kewenangan per produk/kondisi (mekanisme plafon atau hierarki) beserta jenjang pemutusnya, plus ekspor seluruh aturan dalam satu Excel (lihat [Modul Komite Kredit](#modul-komite-kredit)).

**Perizinan**
- Halaman `/permissions` untuk mengelola permission Spatie: tabel server-side (pencarian, sortir, filter **Entitas** dinamis, paginasi), tambah/ubah/hapus, dan hapus massal.
- Nama izin wajib berformat `entitas.aksi` huruf kecil (mis. `projects.view`, `projects.delete_any`).
- **Izin inti** bawaan modul (`Modules::permissions()`) terkunci: ikon kunci, tanpa menu aksi, dan ditolak 403 dari server bila dipaksa diubah/dihapus.
- **Generator izin standar**: masukkan entitas lalu pilih aksi (`view`, `view_any`, `create`, `update`, `delete`, `delete_any`) — izin yang sudah ada dilewati.

**Peranan**
- CRUD peranan, peranan `Super Admin` terkunci dari perubahan/penghapusan.
- **Impor peranan dari Excel** (nama peranan pada kolom pertama, baris judul diabaikan, duplikat dilewati).
- **Salin Hak Akses**: pada dialog *Tambah Peranan* ada pilihan **Salin Hak Akses Dari** (opsional) — seluruh izin peranan sumber langsung disalin ke peranan baru; jumlah izin ditampilkan di daftar pilihan dan pada teks bantuan.
- **Matriks hak akses** di halaman detail peranan: izin dikelompokkan per entitas, toggle "pilih semua" per entitas dan global, pencarian izin, penghitung izin terpilih (Super Admin bersifat read-only).

**Audit Trail**
- Mencatat siapa mengubah apa, **diff nilai sebelum → sesudah**, konteks permintaan, dan kegagalan sistem.
- Halaman detail khusus pengembang termasuk **Payload Mentah (JSON)**.
- Hapus jejak audit berdasarkan rentang tanggal.

**Ekspor & Impor**
- Ekspor Excel **mengikuti filter aktif** di Pengguna (`/users/export`), Perizinan (`/permissions/export`), dan Audit Trail (`/audit-trail/export`) — `.xlsx` via `App\Support\Excel` (PhpSpreadsheet).
- Impor Excel untuk Pengguna (`POST /users/import`) dan Peranan (`POST /roles/import`), lengkap dengan template contoh (`/users/import/template`, `/roles/import/template`).

**Notifikasi**
- Notifikasi **per pengguna** (tabel `notifications`, satu baris = satu penerima) — bukan siaran ke semua orang.
- Bertarget izin: hanya pengguna aktif yang memiliki izin modul terkait yang menerimanya, dan **pelaku aksi tidak menerima notifikasi atas aksinya sendiri**.
- Lonceng di header menampilkan jumlah belum dibaca, tombol **Tandai** (tandai semua dibaca), dan klik item menandai dibaca lalu membuka halaman terkait.

**Aksi Massal**
- Checkbox pada tabel Pengguna, Peranan, dan modul data referensi (pilih baris / pilih semua baris pada halaman aktif).
- Pengguna: **Pulihkan**, **Arsipkan**, **Hapus Permanen** (akun sendiri otomatis dilewati; hapus permanen hanya untuk yang sudah terarsip).
- Peranan: **Hapus** (Super Admin dan peranan yang masih dipakai otomatis dilewati).
- Data referensi: **Hapus** (permanen).

**Pengaturan**
- **Penampilan**: identitas aplikasi (nama, tagline, inisial brand), logo terang/gelap, favicon, SEO & metadata (termasuk Open Graph), kontak & footer.

**Notifikasi**
- Lonceng di header memuat notifikasi **milik pengguna aktif** saja (share Inertia `notifications`).
- **Tandai Semua** (`POST /notifications/read-all`) menandai seluruh notifikasi belum dibaca sekaligus; klik satu item menandainya dibaca lalu membuka `url` bila ada.
- **Penyaring Semua / Belum Dibaca** di dropdown (server menyediakan `items` dan `unread_items`, masing-masing 10 terbaru); teks kosong menyesuaikan konteks ("Semua notifikasi sudah dibaca." vs "Belum ada notifikasi.").

**Dashboard**
- Widget **data nyata** dari basis data: KPI (pengguna aktif/nonaktif, peranan, izin + jumlah entitas, aktivitas 7 hari + yang perlu ditinjau, notifikasi belum dibaca), Aktivitas Terakhir, Tren 7 Hari (pengguna baru vs aktivitas), Aktivitas per Modul, Sebaran Peranan, dan Penyimpanan nyata (berkas unggahan, ukuran basis data, disk server).
- **Galeri Komponen** (`components/composite/ComponentGallery.vue`): showcase interaktif seluruh komponen — tombol & status muat, toast 4 varian, lencana + StateChip, avatar, tooltip, isian formulir (Input, error state, PasswordInput, PhoneInput, Textarea), Combobox, DatePicker, Checkbox, Switch, Alert, Progress, Skeleton, Dialog, ConfirmDeleteDialog, DropdownMenu, Tabel + RowActions, dan EmptyState (6 varian dapat dipilih).

**UI**
- Dark mode, sidebar dapat di-collapse (mode ikon), breadcrumb otomatis, toast, dialog, combobox dengan pencarian, date picker.
- **Tinggi kontrol seragam**: seluruh Input, Combobox, DatePicker, FileInput, dan Button (`sm`/`default`/`icon`) memakai token `--ctl-h` (2rem/32px) sehingga sejajar di dialog maupun toolbar tabel. Jangan meng-override tinggi kontrol secara lokal.
- **Label formulir — ATURAN BAKU**: semua label memakai komponen `components/ui/Label.vue` yang sudah membawa gaya wajib **12px (`--label-size`), UPPERCASE, letter-spacing `--label-tracking` (0.06em), `font-weight` `--label-weight` (500), warna `text-foreground`**. Berlaku di seluruh halaman & dialog (Pengguna, Peranan, Perizinan, Audit Trail, Penampilan, Menu Sidebar, Object Storage, Profil, Login, Galeri Komponen). Jangan menulis `<label>` mentah dan jangan meng-override ukuran/huruf/spasi label per halaman — ubah tokennya di `resources/css/app.css` bila perlu penyesuaian global. Pengecualian tunggal: teks pendamping checkbox berupa kalimat (mis. "Ingat saya" di Login) boleh memakai `normal-case tracking-normal font-normal`.
- **Menu sidebar dari basis data**: modul **Menu Sidebar** (`/menus`) menyusun menu dengan drag & drop hingga **3 tingkat** (grup → menu → submenu); atur label, alamat, ikon (registry lucide), izin, area (Member/Administrator), dan status aktif. Item tanpa alamat otomatis menjadi grup yang dapat dibuka-tutup; grup yang seluruh anaknya tidak berizin otomatis disembunyikan.
- Sidebar dua area: **Member Area** (Dashboard) dan **Administrator** (Perizinan → Peranan → Pengguna → Penampilan → Audit Trail). Profil diakses lewat dropdown akun di footer sidebar.
- **Halaman error bertema design system** (`pages/Error.vue`) untuk 401/403/404/419/429/500 — dua kolom: narasi + tindakan di kiri, panel kode status (angka mono besar, arsir diagonal, animasi masuk bertahap) dan **Pintasan Cepat** ke modul utama di kanan; header brand + toggle tema, footer `HTTP <status>`.
- **Halaman pemeliharaan** (`resources/views/errors/503.blade.php`) tampil saat `php artisan down` — mandiri tanpa Vite/DB, senada tema (kisi latar, panel 503, bilah progres bergerak, daftar "Yang Sedang Kami Lakukan"), mendukung mode gelap otomatis, auto-refresh sesuai `--retry`.
- **Deteksi koneksi**: banner offline di bawah header + toast saat koneksi terputus/pulih (`composables/useNetworkStatus.js`, `components/layout/OfflineBanner.vue`).
- Responsif: kolom tabel sekunder otomatis disembunyikan pada layar kecil.

---

## Persyaratan

- PHP **8.2+** dengan ekstensi: `sqlite3`, `mbstring`, `xml`, `curl`, `zip`, `bcmath`, `gd`, `intl`
- Composer 2
- Node.js 18+ dan **Yarn**

---

## Instalasi

```bash
git clone <url-repo> adminkit && cd adminkit

composer install
yarn install

cp env.example .env           # berkas contoh bernama env.example (tanpa titik) agar ikut ter-push ke GitHub
php artisan key:generate

touch database/database.sqlite
php artisan migrate --seed    # membuat peranan, izin, akun Super Admin, dan setelan
php artisan storage:link      # agar berkas unggahan lokal dapat diakses

yarn build                    # atau: yarn dev (mode pengembangan)
php artisan serve
```

Variabel `.env` yang relevan:

```env
APP_LOCALE=id                 # tanggal, waktu relatif, dan pesan validasi (Laravel Lang)
APP_TIMEZONE=Asia/Jakarta
DB_CONNECTION=sqlite
DB_DATABASE=/abs/path/database/database.sqlite

SESSION_SAME_SITE=none        # WAJIB bila aplikasi dimuat di dalam iframe (mis. panel preview)
SESSION_SECURE_COOKIE=true    # pasangan dari SameSite=none — hanya untuk HTTPS

FILESYSTEM_DISK=local         # local atau s3 (lihat Object Storage)
TELESCOPE_ENABLED=true
TELESCOPE_ALLOWED_EMAILS=email@anda.com
```

### Seeder (data awal)

`DatabaseSeeder` hanya memanggil **seeder terpisah per domain** supaya mudah diubah dan bisa dijalankan sendiri-sendiri:

| Seeder | Isi |
|---|---|
| `PermissionSeeder` | izin (`view`/`manage` per entitas) diturunkan dari `App\Support\Modules::MAP` |
| `RoleSeeder` | `Super Admin` (selalu sinkron dengan SELURUH izin) + `Guest` + 43 peranan struktur organisasi. `Kasi Analis` (penjadwalan) dan `Staff Analis` (survei + analisa) sudah diberi izin bawaan; peranan yang **sudah** punya izin tidak ditimpa |
| `UserSeeder` | 22 akun: pemilik sistem `IT Support` / `superadmin` / `sa@bprbangunarta.co.id` (peranan Super Admin, kata sandi `SA@4dm1n`) + 21 akun pegawai uji lengkap dengan peranan, kantor, alias, kode MSO, dan kode kolektor (kata sandi awal `password`) |
| `SettingSeeder` | Identitas merek SIPEBRI, SEO/OG, kontak, zona waktu, dan urutan 15 entitas pada matriks izin |
| `MenuSeeder` | 29 menu: `Dashboard` + grup `Referensi` (Data Kantor, Data Instansi, Data Produk, Data Wilayah, Sistem Cicilan, Sistem Bunga, Komite Kredit) + grup `Agunan` (Jenis Agunan, Jenis Pengikatan, Kondisi Agunan, Metode Hitung) + grup `Simulasi` (Agunan, Pengajuan, Penjadwalan, Survei, Analisa, Persetujuan) + 8 menu Administrator |
| `InstallmentSeeder` | 8 pola cicilan + **kelipatan jangka waktu** (HARIAN/MINGGUAN/BULANAN 1, TRIWULANAN 3, SEMESTERAN 6, TAHUNAN 12, MUSIMAN 6, NON ANGSURAN 0) |
| `OfficeSeeder`, `InstitutionSeeder`, `ProductSeeder`, `MethodSeeder`, `CollateralTypeSeeder`, `BindingTypeSeeder`, `CollateralConditionSeeder`, `CollateralMethodSeeder` | Data referensi mengikuti core banking: 7 kantor, 10 instansi, 17 produk kredit, 8 pola cicilan, 10 metode bunga, 19 jenis agunan, 7 jenis pengikatan, 7 kondisi agunan, 4 metode hitung |
| `RegionSeeder` | 82.449 baris wilayah (kode dati2 → kabupaten → kecamatan → kelurahan + kode pos) dari `database/data/regions.csv.gz`; dilewati bila tabel sudah terisi |
| `OwnershipStatusSeeder` | Status/bukti kepemilikan per jenis agunan (baru jenis `05`: 11 pilihan) |
| `ProductParameterSeeder` | parameter SK Direksi untuk **17 produk**: plafon & tenor min/maks, suku bunga, provisi, admin, ambang RC, sistem bunga & cicilan yang diizinkan, wajib agunan |
| `SchemaDraftSeeder` | 4 rancangan Skema Migrasi yang mencerminkan tabel nyata |
| `CommitteeSeeder` | 19 jalur komite kredit + 76 jenjang pemutus sesuai dokumen kebijakan |
| `LoanApplicationSeeder` | 5 berkas contoh untuk menguji alur kredit: `00700003` & `00700004` berstatus `SURVEY` (siap dipakai menguji Analisa; `00700004` lengkap dengan jadwal, hasil survei, foto, dan agunan) serta 3 berkas `DRAFT` (`00700005` KTA, `00700006` KPS, `00700007` KBT PERPADIAN) beserta 3 agunan. Relasi memakai kunci alami (alias produk, kode kantor/cicilan, username) dan **idempoten** — kode berkas yang sudah ada tidak ditimpa |

```bash
php artisan db:seed                          # semua seeder (idempoten)
php artisan db:seed --class=MenuSeeder       # hanya satu domain
php artisan migrate:fresh --seed --force     # instalasi bersih
```

- **Idempoten**: `updateOrCreate`/`findOrCreate`, tidak menghapus data lain.
- **Kata sandi hanya disetel saat akun dibuat** (`firstOrNew`), jadi seeding ulang tidak menimpa kata sandi yang sedang dipakai. Ganti kata sandi bawaan setelah instalasi.
- Akun **terarsip** (soft delete) akan **dipulihkan** oleh `UserSeeder`, dan peranan selalu disinkronkan ulang (satu peranan per pengguna).
- Kantor pengguna disimpan sebagai **nama kantor** dan divalidasi terhadap `OfficeSeeder`; satu-satunya pengecualian yang disengaja adalah `superadmin` yang berkantor di **Kantor Pusat** (belum menjadi data kantor operasional).
- **Izin peranan tambahan tidak ditimpa** bila peranan sudah punya izin — aman diubah dari modul Peranan.
- **Data simulasi TIDAK diseed**: `loan_applications`, `loan_schedules`, `loan_surveys`,
  `loan_survey_photos`, dan `collateral_simulations` sengaja dibiarkan **kosong** pada instalasi baru.
- Ingin cetakan baru? Salin data yang sudah Anda atur di aplikasi ke konstanta di seeder terkait
  (`ROLES`, `USERS`, `SETTINGS`, `MENUS`, `PRODUCTS`, `PARAMETERS`).

### Menjalankan di lokal (SQLite atau MySQL)

```bash
cp .env.example .env && php artisan key:generate
# SQLite (bawaan)
touch database/database.sqlite            # DB_CONNECTION=sqlite
# MySQL/MariaDB
#   DB_CONNECTION=mysql, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD

composer install && yarn install
php artisan migrate:fresh --seed --force
yarn build                                # atau `yarn dev` untuk HMR
php artisan serve                         # http://127.0.0.1:8000
```

Yang perlu diperhatikan saat uji di lokal:

1. Isi kredensial **API Codex** di `.env` (`CODEX_BASE_URL`, `CODEX_CLIENT_ID`, `CODEX_CLIENT_SECRET`),
   jika tidak, modal No. KTP akan menjawab "sistem data nasabah tidak dapat dihubungi".
2. Unggah foto survei butuh **koordinat browser** → jalankan di `http://localhost` (diizinkan) atau HTTPS.
   Alamat IP LAN tanpa HTTPS akan ditolak browser.
3. `php artisan migrate:fresh --seed` sudah diuji di **SQLite dan MariaDB 10.11**. Jangan menambahkan
   SQL khas SQLite (`typeof`, `PRAGMA`, `||`) pada migrasi/seeder — lihat `/app/memory/env_notes.md`.
4. Sebelum commit perubahan UI: `yarn ui:check && yarn build && php artisan test`.

---

## Perintah Harian

```bash
yarn dev                 # Vite dev server (HMR)
yarn build               # kompilasi aset produksi — WAJIB setelah mengubah .vue/.css bila tidak memakai yarn dev
yarn ui:check            # WAJIB sebelum menyatakan pekerjaan UI selesai (penjaga konsistensi UI)
php artisan migrate      # migrasi
php artisan db:seed      # seeding ulang (idempoten, atau --class=NamaSeeder)
php artisan cache:clear  # WAJIB setelah mengubah tabel settings langsung dari DB (branding di-cache)
./vendor/bin/pint        # format kode PHP
```

---

## Struktur Proyek

```text
app/
├── Enums/RoleName.php                  # enum nama peranan
├── Http/
│   ├── Controllers/                    # Auth, Dashboard, User, Permission, Role, Notification, Profile, Appearance, ActivityLog, Menu, ObjectStorage, Committee, Reference (+Institution/Product/Installment/Method)
│   ├── Middleware/HandleInertiaRequests.php   # share auth, branding, flash
│   └── Requests/                       # SATU Form Request per form (lihat Standar Validasi)
├── Models/                             # User, Role & Permission (Spatie), ActivityLog, Notification, Setting, Menu, Office, Institution, Product, Installment, Method, CommitteePath, CommitteeTier
├── Providers/
│   ├── AppServiceProvider.php          # branding untuk blade root + locale Carbon
│   └── TelescopeServiceProvider.php    # gate & middleware Telescope
└── Support/
    ├── Branding.php                    # pembacaan setelan branding (+cache)
    ├── Committee.php                   # resolver jalur & pemutus komite (simulasi + nanti alur kredit)
    ├── Excel.php                       # unduhan & pembacaan berkas .xlsx (PhpSpreadsheet)
    ├── FileStorage.php                 # satu pintu unggahan (local/s3, prefix disk)
    ├── Modules.php                     # daftar modul & izin inti
    ├── Notify.php                      # notifikasi bertarget izin
    ├── Rules.php                       # SATU sumber aturan validasi per tipe kolom
    └── TableQuery.php                  # helper query tabel server-side

resources/
├── css/app.css                         # token FlowDesk, dark mode, densitas tabel
├── js/
│   ├── components/
│   │   ├── composite/                  # DataTableCard, RowActions, StateChip, BrandMark, AssetUploader, dll
│   │   ├── layout/                     # AppLayout, AppSidebar, AuthLayout
│   │   └── ui/                         # porting shadcn/ui (Button, Card, Table, Dialog, Combobox, DatePicker, ...)
│   ├── composables/                    # useServerTable, useLiveValidation, useTheme, useFlashToast, useToast, useMenuLabel, useNetworkStatus
│   ├── config/navigation.js            # area, menu, breadcrumb (ROUTE_TRAILS)
│   ├── constants/labels.js             # label aksi (Title Case)
│   ├── constants/committee.js          # mekanisme & keputusan jenjang komite, format Rupiah
│   ├── lib/menuIcons.js                # nama ikon Lucide → komponen (dinamis, seluruh koleksi)
│   ├── lib/validators.js               # cermin Rules.php untuk validasi cepat UI
│   └── pages/                          # Dashboard, Users, UserForm, Permissions, Roles, RoleDetail, AuditTrail, AuditDetail, Appearance, Menus, ObjectStorage, Reference, Committees, CommitteeDetail, Profile, Error, auth/Login
└── views/app.blade.php                 # root blade (judul, favicon, meta SEO/OG)

lang/id/, lang/id.json                  # terjemahan Laravel Lang (pesan validasi bawaan)
routes/web.php
database/{migrations,seeders,factories}
```

---

## Catatan (Jun 2026): PHP di pod suka reset ke 8.2
`composer.json` sudah disetel `"config": { "platform-check": false }` dan autoload di-dump ulang,
jadi aplikasi tetap jalan di **PHP 8.2 maupun 8.3** tanpa error `platform_check.php`.
Bila `php: not found` (pod baru), pasang ulang: `apt-get install -y php8.2-cli php8.2-sqlite3
php8.2-curl php8.2-xml php8.2-mbstring php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl`
lalu `sudo supervisorctl restart frontend`.

## Aturan placeholder kolom form (wajib)
- Kolom **tidak wajib** → placeholder `(Opsional)` (termasuk selectbox & date picker).
- Kolom **wajib** → tanpa placeholder; selectbox memakai `-- Pilih --`.
- Filter toolbar memakai `Semua …`, kolom pencarian `Cari…`, kata sandi `Minimal 8 karakter`.
- Angka opsional yang dikirim ke CBS tetap tersimpan `0` bila dikosongkan.

## Tombol aksi form (wajib) — `FormActions`
Pasangan tombol **Batal / aksi utama** HANYA lewat `resources/js/components/composite/FormActions.vue`
(Batal pojok kiri + ikon X, aksi utama pojok kanan + ikon + status memuat). Berlaku untuk
`CardFooter` maupun slot `#footer` Dialog; footer satu tombol memakai `:cancel="false"`.
Dilarang menulis pasangan tombol ini manual per halaman.

## `yarn ui:check` (penjaga konsistensi UI)
`scripts/ui-check.mjs` menolak: placeholder di luar daftar putih, `<Input type="date|number">`
(harus `DatePicker` / `NumberInput` / `DecimalInput` / `DigitsInput`), footer tanpa `FormActions`,
footer yang menimpa perataan, ukuran tombol tidak baku, serta komponen/ikon yang dipakai template
tetapi lupa di-import. Jalankan bersama `yarn build` + uji lebar **390/768/1024/1440**.

## Aturan format angka & responsif (wajib)
- Detail lengkap: `/app/memory/ui_rules.md`.
- Ringkas: setiap angka yang tampil memakai format Indonesia (`1.000`, `12,75%`); input bilangan
  bulat memakai `resources/js/components/ui/NumberInput.vue` (`1.000` → simpan `1000`) dan input
  desimal memakai `DecimalInput.vue` (`12,75` → simpan `12.75`).

## Skema Basis Data

| Tabel | Isi penting |
| --- | --- |
| `users` | `name` (wajib), `username`/`email`/`phone` (opsional & unik), `role` (cermin peranan Spatie), `office`, `alias`/`mso_code`/`collector_code` (unik), `password`, `avatar`, `last_login_at`, `deleted_at` (SoftDelete = Terarsip) |
| `offices`, `institutions`, `products`, `installments`, `methods` | data referensi: `code` (unik), `alias` (unik, pada `offices` & `products`), `name` |
| `collateral_types`, `binding_types`, `collateral_conditions`, `collateral_methods` | referensi agunan (`code` unik + `name`) untuk rute `/collateral-types`, `/collateral-bindings`, `/collateral-conditions`, `/collateral-methods` |
| `regions` | referensi wilayah: `code` (dati2, dikirim ke CBS), `regency`, `district`, `village`, `postal_code` |
| `ownership_statuses` | status/bukti kepemilikan per jenis agunan: `collateral_type_code` + `code` + `name` |
| `collateral_simulations` | contoh data agunan mengikuti form CBS (identitas, dokumen, pemilik, lokasi, nilai, kondisi, asuransi). **Tanpa perhitungan** — `toCbsPayload()` memetakan ke kontrak API CBS |
| `product_parameters` | parameter SK Direksi per produk: plafon & tenor min/maks, `interest_rate`/`provision_rate`/`admin_rate`/`rc_threshold` (persen), metode & pola cicilan yang diizinkan (JSON) + nilai bawaan, `collateral_required`, `decree`, `note` |
| `committee_paths` | jalur komite: `product_id` (null = semua produk), `condition` (null = Normal), `mechanism` (`plafon`/`hierarki`), `is_active`, `note` |
| `committee_tiers` | jenjang: `committee_path_id`, `sort`, `label`, `role`, `min_amount`, `max_amount`, `can_escalate`, `can_approve`, `can_cancel`, `can_reject` |
| `roles`, `permissions`, `model_has_roles`, `role_has_permissions` | standar `spatie/laravel-permission`; nama izin memakai pola `entitas.aksi` |
| `activity_logs` | `actor_name`, `action`, `module`, `level`, `subject_type/id`, `changes` (JSON diff), `context` (JSON), `ip`, `method`, `url`, `status_code`, `user_agent` |
| `notifications` | satu baris per penerima: `user_id`, `title`, `body`, `module`, `level`, `url`, `actor_id`, `read_at` |
| `settings` | `key` (primary), `value` — branding, SEO, dan `permission_entity_order` (urutan kartu entitas matriks) |
| `telescope_entries`, `telescope_entries_tags`, `telescope_monitoring` | penyimpanan Laravel Telescope |

---

## Alur Proses Kredit

Status berkas (tidak boleh menambah status baru tanpa dibahas):
`DRAFT → DIAJUKAN → PENJADWALAN → SURVEY → ANALISA → KOMITE → DISETUJUI/DITOLAK → REALISASI`
(+ `DIBATALKAN`). Rincian & keputusan bisnis: `/app/memory/alur_kredit.md`.

| Tahap | Menu | Status masuk → keluar | Inti aturan |
| --- | --- | --- | --- |
| 1. Pengajuan | `/loan-simulation` | `DRAFT` → `DIAJUKAN` | Identitas pemohon **hanya** dari API Codex (No. KTP); plafon/tenor/sistem bunga/cicilan dibatasi **parameter produk**; agunan wajib mengikuti `collateral_required`. Setelah **Ajukan**, berkas **terkunci** (tidak bisa diubah/dihapus/lepas agunan). Daftar hanya menampilkan berkas milik pembuatnya. |
| 2. Penjadwalan | `/scheduling-simulation` | `DIAJUKAN` → `PENJADWALAN` | Kasi Analis menetapkan tanggal survei (≥ hari ini) + staff analis. Filter cakupan **Berkas saya / Semua Kasi Analis**. Batas 3 kali hanya **peringatan**. Semua jadwal/jadwal ulang/pembatalan tercatat di `loan_schedules` (**append-only**, tidak pernah dihapus). |
| 3. Survei | `/survey-simulation` | `PENJADWALAN` → `SURVEY` | Daftar **hanya jadwal hari ini** milik staff analis yang ditugaskan. Wajib **1–5 foto lokasi** dengan **koordinat** yang diambil sistem saat foto dipilih (disimpan ke object storage). Setelah disimpan, hasil **terkunci**. Tombol **Batal & Minta Jadwal Ulang** (alasan wajib) mengembalikan berkas ke `DIAJUKAN`. |
| 4. Analisa | `/analysis-simulation` | `SURVEY` → `KOMITE` (lewat tombol **Ajukan ke Komite**; wajib: ≥1 usaha, biaya rumah tangga, Analisa 5C dinilai, usulan plafon memorandum — lalu notifikasi ke Kasi Analis & pemegang izin `committees.view`) | Lembar analisa **8 bagian** (navigasi bernomor, sticky). Sudah jalan: **1 Analisa Usaha** (4 tipe usaha, tiap usaha punya lembar sendiri di `/analysis-simulation/{berkas}/businesses/{usaha}` — kode usaha otomatis AUPG/AUP/AUJ/AUL, seluruh angka dihitung sistem), **2 Analisa Keuangan** (biaya rumah tangga + kewajiban; pendapatan usaha = jumlah kontribusi per bulan tiap usaha), **3 Analisa Kepemilikan** (8 harta + harta lain). Bagian 4–8 (Agunan, 5C, Kualitatif, Memorandum, Administrasi) masih placeholder. |
| 5. Persetujuan | `/approval-simulation` | `KOMITE` → keputusan | Daftar berkas yang sudah diajukan Staff Analis (kode & tanggal, pemohon, produk + jalur komite, plafon diajukan, **usulan plafon analis** dari memorandum, pengaju & waktunya). Halaman `/approval-simulation/{berkas}` menampilkan ringkasan berkas + **jenjang pemutus** dari `committee_tiers` (level, pemutus, batas plafon, kewenangan setuju/tolak/teruskan); **form keputusan masih placeholder**. Izin `approval-simulation.view` diberikan ke Kasi Analis, Kabag Analis dan jajaran Direktur pada `RoleSeeder` |

Tabel terkait: `loan_applications` (+ kolom audit `created_by`/`updated_by`/`deleted_by` berisi **nama
pengguna**), `loan_application_collaterals`, `loan_schedules`, `loan_surveys`, `loan_survey_photos`.
Notifikasi lonceng dikirim ke pihak yang punya tugas (kasi analis saat ada pengajuan baru / permintaan
jadwal ulang, staff analis saat mendapat penugasan survei).

> **Wajib HTTPS**: pengambilan koordinat oleh browser (`navigator.geolocation`) hanya berjalan di
> HTTPS atau `localhost`. Di staging/produksi tanpa HTTPS, foto survei tidak bisa diunggah.

## Integrasi API Codex (Data Nasabah)

SIPEBRI **tidak menyimpan** identitas pemohon; hanya `nik`, `full_name`, dan `cif_number`.

- `app/Services/CodexClient.php` — OAuth2 `client_credentials`, token di-cache sampai mendekati
  kedaluwarsa (`codex:access-token`), 401 → ambil token baru sekali, 404 → nasabah belum terdaftar.
- `app/Support/CustomerDirectory.php` — memetakan kolom Codex ke bentuk SIPEBRI (payload asli tetap
  dibawa pada kunci `raw`).
- Konfigurasi `.env`: `CODEX_BASE_URL`, `CODEX_CLIENT_ID`, `CODEX_CLIENT_SECRET`, `CODEX_SAMPLE_NIKS`
  (opsional: `CODEX_TIMEOUT`, `CODEX_CONNECT_TIMEOUT`, `CODEX_TOKEN_SKEW`).
- Bila Codex tak dapat dihubungi: lookup → HTTP 503 + pesan ramah, simpan berkas → galat validasi
  pada kolom `nik` (berkas tidak dibuat). Uji memakai `Http::fake` — lihat `LoanApplicationFlowTest`.

## Modul Komite Kredit

Pengaturan **kewenangan persetujuan kredit** (dipakai SIPEBRI). Rute `/committees`, izin `committees.view/manage`, struktur induk–anak:

1. **Jalur Komite** (`committee_paths`) — satu baris per kombinasi **produk + kondisi/kategori**:
   - `product_id` kosong = **berlaku lintas produk** (mis. kategori `RELOAN`), `condition` kosong = **Normal**.
   - `mechanism`: `plafon` (kewenangan mengikuti batas plafon) atau `hierarki` (wajib naik berjenjang tanpa batas plafon).
   - Kombinasi produk + kondisi dijaga unik (termasuk kondisi kosong), kondisi selalu disimpan HURUF BESAR.
2. **Jenjang** (`committee_tiers`) — urutan, nama jenjang (mis. `Komite I`), **peranan pemutus** (peranan Spatie, bukan user tertentu), `min_amount`/`max_amount`, dan keputusan yang diizinkan: `can_escalate` (Naik Komite), `can_approve`, `can_cancel`, `can_reject`.
   Urutan diubah lewat tombol naik/turun; rute jenjang memakai `scopeBindings()` sehingga jenjang milik jalur lain tidak bisa disentuh.

Kemudahan: saat membuat jalur baru tersedia **Salin Jenjang Dari** jalur lain (satu jalur dibuat sekali, sisanya disalin). Tombol **Simulasi** di header membuka dialog untuk mencoba aturan: masukkan **produk + kondisi + plafon**, sistem menampilkan jalur yang cocok, rantai keputusan (mana yang *Naik Komite*, mana **Pemutus**, mana *Tidak diperlukan*), jumlah pengguna pemegang tiap peranan, serta **peringatan** bila tidak ada pemutus, peranan pemutus belum ada penghuninya, atau ada **celah/tumpang tindih** rentang plafon. Pilihan kondisi **mengikuti produk terpilih** (kondisi milik produk itu + kondisi lintas produk seperti RELOAN), dan resolver **tidak menurunkan** kombinasi yang tidak terdaftar ke jalur Normal — mis. `KRU + PERLELEAN` ditolak dengan pesan "bukan peruntukan produk ini". Logikanya ada di `App\Support\Committee::resolve()` (`GET /committees/simulate`) sehingga siap dipakai ulang oleh alur pengajuan kredit — simulasi tidak menyimpan apa pun dan tidak butuh akun uji per peranan. Jenjang berisi **hanya level pemutus** — hak *mengajukan/meneruskan* berkas ke komite bukan jenjang komite, melainkan izin pada modul pengajuan kredit. Data bawaan `CommitteeSeeder` mengikuti dokumen kebijakan: 14 produk umum (termasuk KPP dan KRISPI) + KBT `PERPADIAN` memakai jalur plafon (Kasi Analis ≤35 jt → Komite I/Kabag Analis ≤100 jt → Komite II/Direktur Bisnis ≤300 jt → Komite III/Direktur Utama >300 jt), sedangkan KUP, KKO, KBT `PERLELEAN`, dan `RELOAN` memakai hierarki (Kasi Analis → Komite I → Komite II → Komite III, hanya Komite III yang memutus).

---

## Modul Analisa Kredit

Lembar analisa satu berkas dibuka di `/analysis-simulation/{berkas}` (khusus **Staff Analis** yang
ditugaskan) dan terdiri dari 8 bagian bernomor. Semua kolom hasil hitung **tidak pernah diinput** —
rumusnya ada di backend (`AnalysisBusiness::metrics()`, `AnalysisSheet::metrics()`) dan dicerminkan di
`resources/js/constants/analysisMath.js` supaya angka berubah langsung saat analis mengetik.

| Bagian | Status | Isi |
| --- | --- | --- |
| 1. Analisa Usaha | ✅ | Sub-tab per tipe usaha (Perdagangan/Pertanian/Jasa/Lainnya) + daftar usaha; tiap usaha punya **satu lembar penuh** (tanpa tab) di `/analysis-simulation/{berkas}/businesses/{usaha}` dengan kartu form di kiri dan **Ringkasan Perhitungan** sticky + tombol **Simpan Semua** di kanan |
| 2. Analisa Keuangan | ✅ | 7 pos biaya rumah tangga + baris kewajiban; Pendapatan Usaha = jumlah kontribusi **per bulan** semua usaha; Keuangan Perbulan = pendapatan − biaya rumah tangga − kewajiban |
| 3. Analisa Kepemilikan | ✅ | 8 harta (rumah, mobil, motor, komputer, mesin cuci, televisi, kursi tamu, lemari panjang) + daftar harta lain |
| 4. Analisa Agunan | ✅ | Satu kartu per agunan berkas (identitas & taksasi CBS read-only) + berita acara pemeriksaan: jenis pemeriksaan (Kendaraan/Tanah/Lainnya), data kendaraan atau luas tanah, lokasi, nilai pasar, nilai taksasi, catatan. Data yang dikirim ke CBS tetap `collateral_simulations` |
| 5. Analisa 5C | ✅ | 28 aspek berskor (Character 7 · Capacity 8 · Capital 1 · Collateral 9 · Condition 3); evaluasi dihitung sistem: persentase skor terhadap skala maksimum, ≥80% BAIK · ≥60% CUKUP BAIK · sisanya KURANG BAIK |
| 6. Analisa Kualitatif | ✅ | Karakter (SLIK, pihak berwajib, hubungan tetangga, pengalaman TKI, waktu di rumah, info masyarakat, 3 kewajiban pihak lain), Usaha, SWOT, catatan tambahan |
| 7. Memorandum | ✅ | Kebutuhan dana 5 pos + keterangan (jumlah otomatis), usulan plafon, jangka waktu, biaya admin/bunga/provisi/penalti, syarat sebelum realisasi & tambahan, pengikatan; panel read-only: plafon diajukan, taksasi agunan, keuangan per bulan |
| 8. Administrasi | ✅ | 16 pos biaya (Biaya Kredit · Asuransi Jiwa · Agunan & Pengikatan) + total biaya otomatis |

**Kode usaha otomatis**: `AUPG` (perdagangan), `AUP` (pertanian), `AUJ` (jasa), `AUL` (lainnya) + 5 digit.

**Rumus tiap tipe usaha** (diverifikasi dengan angka contoh sistem lama, dikunci oleh
`tests/Feature/AnalysisBusinessTest.php`):

- **Perdagangan** — `%` per barang & margin total = **laba ÷ harga beli** (dibulatkan 2 desimal);
  Omset Harian = Belanja Harian × (1 + margin); Laba Bersih Harian = Omset − Pokok Penjualan;
  Laba/Biaya Bulanan = harian × 30; Hasil Bersih = Laba Bulanan − Biaya Bulanan + Proyeksi Penambahan.
- **Pertanian** — Pendapatan Panen = kwintal × harga; Pengeluaran = 12 pos biaya (**termasuk Pinjaman Bank
  Lain**); periode setoran diambil dari **kelipatan sistem cicilan berkas** (MUSIMAN 6, BULANAN 1,
  NON ANGSURAN = sepanjang jangka waktu); Setoran Pokok = plafon ÷ (jangka waktu ÷ periode);
  Pendapatan Per Bulan = ⌊(Hasil Bersih − Setoran Pokok) ÷ periode⌋ **+ Penambahan Hasil Usaha**.
- **Jasa** — Hasil Bersih = Pendapatan Usaha − (Pajak Kendaraan + Pengeluaran Lainnya).
- **Lainnya** — Bahan Baku = jumlah × harga; Hasil Bersih = Pendapatan Usaha − Biaya Operasional −
  Biaya Bahan Baku + Proyeksi Penambahan.

Bagian 2–8 memakai satu lembar penuh tanpa tab dengan tombol **Simpan Semua** di bilah bawah yang menempel.

Tabel: `analysis_businesses` (+ `monthly_income` = kontribusi per bulan), `analysis_business_items`
(satu tabel, kolom `group`: `GOODS`/`MATERIAL`/`INCOME`/`EXPENSE`), `analysis_sheets`,
`analysis_sheet_items` (`OBLIGATION`/`ASSET`), `analysis_collaterals`, `analysis_five_c`,
`analysis_qualitative`, `analysis_memorandums`, `analysis_administrations`.

## Modul Data Referensi

Lima modul data master sederhana (`kode` + `nama`, sebagian dengan `alias`) berbagi **satu** basis kode:

| Modul | Rute | Kolom | Izin |
|---|---|---|---|
| Data Kantor | `/offices` | code (unik), alias (unik), name | `offices.view/manage` |
| Data Instansi | `/institutions` | code (unik), name | `institutions.view/manage` |
| Data Produk | `/products` | code (unik), alias (unik), name + **Parameter Produk** di `/products/{id}` | `products.view/manage` |
| Sistem Cicilan | `/installments` | code (unik), name, **period_months** (Kelipatan Jangka Waktu, bulan; 0 = non angsuran) | `installments.view/manage` |
| Sistem Bunga | `/methods` | code (unik), name | `methods.view/manage` |

- Backend: `ReferenceController` (abstrak) menyediakan index/store/update/destroy/bulkDestroy + aturan validasi; turunannya hanya mendefinisikan `model()`, `slug()`, `label()`, dan `fields()`. Validasi lewat `Reference\StoreReferenceRequest` (mengambil aturan dari controller, `trim` semua nilai, `UPPERCASE` untuk kolom bertanda `uppercase`, `unique` hanya untuk kolom bertanda `unique`). Tipe kolom yang didukung: teks (bawaan), `boolean` (Switch aktif/nonaktif) dan `number` (bilangan bulat `min:0`/`max` sesuai definisi, input rata kanan, `hint` opsional di bawah kolom).
- Frontend: satu halaman generik `pages/Reference.vue` (DataTableCard server-side + dialog tambah/ubah dinamis dari `fields`). Kolom bertanda `hide_below` disembunyikan di layar kecil dan nilainya tetap tampil sebagai baris ringkas di bawah kolom pertama (responsif tanpa penyesuaian tambahan).
- **Penghapusan permanen** (tanpa arsip), tersedia per baris dan massal; semua aksi tercatat di Audit Trail.
### Parameter Produk (SK Direksi)

Setiap produk punya SK Direksi tersendiri. Parameternya diatur di **detail produk** (`/products/{id}`, aksi baris **Atur Parameter**) namun disimpan pada tabel `product_parameters` (1 baris per produk, `updateOrCreate`) supaya master produk tetap cermin core banking:

- **Batas**: plafon minimal/maksimal, tenor minimal/maksimal (bulan).
- **Bunga & biaya (persen)**: suku bunga, provisi, biaya admin — semuanya persen dari plafon.
- **Kelayakan**: ambang **RC maksimal (%)** per produk.
- **Metode bunga & pola cicilan**: daftar yang **diizinkan** (kosong = semua) + nilai **bawaan**; nilai bawaan wajib termasuk daftar yang diizinkan (divalidasi server).
- **Ketentuan lain**: wajib agunan, nomor SK Direksi, catatan.

Semua nilai bersifat **acuan** — petugas tetap dapat mengubahnya saat transaksi. Pengguna tanpa izin `products.manage` melihat formulir dalam keadaan nonaktif dan tanpa tombol Simpan (PUT juga ditolak 403).

- Menambah modul referensi baru: buat migrasi + model, satu controller turunan (≈20 baris), satu entri di `Modules::MAP`, satu entri pada `$references` di `routes/web.php`, lalu tambahkan menunya di Menu Navigasi.

---

## Ekspor & Impor Excel

**Ekspor** memakai `App\Support\Excel::download()` (PhpSpreadsheet, berkas `.xlsx` dengan header tebal, baris pertama dibekukan, auto-filter, lebar kolom otomatis) dan **selalu menghormati filter aktif**:

| Halaman | Rute | Kolom |
| --- | --- | --- |
| Pengguna | `GET /users/export?search=&status=&role=` | Nama Lengkap, Nama Pengguna, Alamat Email, Nomor HP, Peranan, Kantor, Alias, Kode MSO, Kode Kolektor, Status, Terakhir Login |
| Komite Kredit | `GET /committees/export` | Kode Produk, Nama Produk, Kondisi/Kategori, Mekanisme, Status Jalur, Urutan, Nama Jenjang, Peranan Pemutus, Plafon Minimal, Plafon Maksimal, Keputusan Diizinkan, Catatan — **satu baris per jenjang**, seluruh jalur dalam satu berkas untuk keperluan review |
| Perizinan | `GET /permissions/export?search=&entity=&sort=&dir=` | Nama Izin, Entitas, Aksi, Guard, Jumlah Peranan |
| Audit Trail | `GET /audit-trail/export?search=&date_from=&date_to=&sort=&dir=` | Waktu, Pelaku, Aksi, Modul, Level, Alamat IP, Metode, Kode Status, URL |

Di frontend, URL dibangun dari state `useServerTable` lalu dipakai pada `<Button as="a" :href="exportUrl">`.

**Impor** memvalidasi tiap baris memakai `App\Support\Rules` — baris tidak valid dilewati dan jumlahnya dilaporkan:

| Target | Rute | Format berkas |
| --- | --- | --- |
| Pengguna | `POST /users/import` | `.xlsx`/`.xls` sesuai template `GET /users/import/template` (baris judul diabaikan; kata sandi kosong → acak 12 karakter) |
| Peranan | `POST /roles/import` | `.xlsx`/`.xls` sesuai template `GET /roles/import/template` (nama peranan pada kolom pertama, duplikat dilewati) |

Tombol **Template** pada dialog impor mengunduh berkas contoh yang sudah berisi baris data teladan.

---

## Standar Validasi (WAJIB)

Aturan ini berlaku untuk **setiap** form baru — jangan berimprovisasi.

**1. Backend — satu Form Request per form.** Simpan di `app/Http/Requests/<Domain>/<Aksi><Entitas>Request.php`. Controller hanya menerima Form Request; **tidak boleh** `$request->validate()` inline.

**2. Aturan per tipe kolom dipusatkan di `app/Support/Rules.php`.**

| Helper | Aturan |
| --- | --- |
| `Rules::personName()` | wajib/opsional, 3–100 karakter, huruf/spasi/titik/apostrof/tanda hubung |
| `Rules::username($ignoreId)` | `alpha_dash`, huruf kecil, 3–50, unik |
| `Rules::email($ignoreId)` | `email:rfc`, maks 150, unik |
| `Rules::phone($ignoreId)` | `^\+?[0-9]{9,15}$`, unik |
| `Rules::password()` | minimal 8 karakter (`Password::min(8)`) |
| `Rules::text($max)` / `url()` / `slug()` / `path()` / `date()` | teks, URL http/https, slug teknis, path folder, `Y-m-d` |

Contoh:

```php
class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        $id = $this->route('user')?->id;

        return [
            'name' => Rules::personName(),
            'username' => Rules::username($id),
            'email' => Rules::email($id),
            'phone' => Rules::phone($id),
            'role' => ['required', 'string', Rule::exists('roles', 'name')],
            'password' => Rules::password($id === null),
        ];
    }

    public function attributes(): array { return ['name' => 'nama', /* ... */]; }

    public function messages(): array { return Rules::messages(); }
}
```

**3. Frontend — validasi cepat wajib.** `resources/js/lib/validators.js` adalah **cermin** aturan backend; gunakan bersama `useLiveValidation`:

```js
const form = useForm({ name: '', phone: '' });

const check = useLiveValidation(form, {
    name: all(required('nama'), min(3, 'Nama'), personName('Nama')),
    phone: phone('Nomor HP'),
});

const submit = () => check.submit(() => form.post('/users'));
```

- Pesan error ditulis ke `form.errors` sehingga tampilannya identik dengan error server.
- Validasi dijalankan saat `@blur` per kolom dan sebelum submit.
- Kolom bertipe khusus memakai komponen bertipe: **`PhoneInput`** (menolak non-digit, boleh satu `+`, maks 15 digit), `PasswordInput`, `DatePicker`, `Combobox`.
- Selalu pasang `maxlength` sesuai batas backend dan `novalidate` pada `<form>`.

**4. Nilai kosong tetap kosong.** Untuk setelan (`settings`), `Setting::putMany()` menyimpan `''` bila pengguna mengosongkan kolom; `Branding` hanya memakai nilai default untuk kunci yang **belum pernah** diatur. Jangan mengembalikan nilai lama/ default saat pengguna sengaja mengosongkan kolom.

---

## Hak Akses & Peranan

- Daftar modul dan izin ada di `app/Support/Modules.php` (`<modul>.view`, `<modul>.manage`).
- Rute dilindungi middleware `permission:` — contoh: `Route::middleware('permission:users.manage')`.
- Menu sidebar otomatis menyembunyikan item yang izinnya tidak dimiliki (`resources/js/config/navigation.js` + izin dari share Inertia).
- Peranan `Super Admin` (lihat `App\Enums\RoleName`) terkunci: tidak dapat diubah atau dihapus.

### Matriks hak akses per peranan

- Halaman detail peranan (`/roles/{id}`) menampilkan seluruh izin yang dikelompokkan per **entitas** (bagian sebelum titik) dengan checkbox per aksi, toggle "pilih semua" per entitas, toggle global, pencarian izin, dan penghitung izin terpilih.
- Disimpan lewat `PUT /roles/{role}/permissions` → `syncPermissions()`; peranan `Super Admin` bersifat **read-only** (kontrol disabled dan server menolak 403).
- Perubahan dicatat di audit trail sebagai diff daftar izin lama → baru dan memicu notifikasi bertarget `roles.view`.
- Saat membuat peranan, field opsional `copy_from` (id peranan sumber, divalidasi `Rule::exists`) menyalin seluruh izin peranan tersebut; peranan sumber dan jumlah izin dicatat pada konteks audit trail.
- **Kartu entitas dapat digeser** (drag & drop HTML5, ikon `GripVertical`) untuk menyusun urutan tampilnya. Urutan bersifat **global** (berlaku untuk semua peranan), tersimpan otomatis ke `settings.permission_entity_order` lewat `PUT /roles/entity-order` (`SaveEntityOrderRequest`, izin `roles.manage`), dan dipakai `RoleController::matrix()`; entitas baru menyusul di belakang. Drag dinonaktifkan selama kolom pencarian izin terisi.

### Generator izin standar

Halaman Perizinan menyediakan generator: masukkan entitas (mis. `projects`) lalu pilih aksi `view`, `view_any`, `create`, `update`, `delete`, `delete_any` (`GeneratePermissionRequest::ABILITIES`). Izin dibuat dengan pola `entitas.aksi`; yang sudah ada dilewati.

Menambah izin ad-hoc dapat dilakukan lewat halaman **Perizinan** tanpa menyentuh kode; izin yang dipakai kode aplikasi tetap didaftarkan di `Modules::MAP` agar terkunci dan ikut di-seed.

Menambah modul baru:
1. Tambahkan entri pada `Modules::MAP` (label + izin).
2. `php artisan db:seed` untuk membuat izin baru.
3. Tambahkan rute + middleware `permission:`.
4. Tambahkan menu di `navigation.js` beserta `ROUTE_TRAILS` untuk breadcrumb.

---

## Audit Trail

Rute `/audit-trail` (daftar) dan `/audit-trail/{id}` (detail untuk pengembang).

Mencatat satu jejak:

```php
ActivityLog::record(
    action: "Memperbarui pengguna {$user->name}",
    module: 'Pengguna',
    level: 'info',              // info | success | warning | danger
    subject: $user,
    changes: ActivityLog::diffOf($user, $before),
    context: ['catatan' => 'opsional'],
    statusCode: null,
);
```

Aturan penting:

- **`$before` wajib diambil SEBELUM `save()`**: `$before = $model->getOriginal();` — setelah `save()` Eloquent sudah menyinkronkan nilai aslinya (pernah menjadi bug: `old == new`).
- `ActivityLog::snapshotOf($model)` untuk data baru, `snapshotOf($model, deleted: true)` untuk penghapusan.
- `Setting::putMany()` mengembalikan diff setelan, langsung dipakai sebagai `changes`.
- Kolom rahasia pada `ActivityLog::MASKED` (`password`, `s3_secret`, `s3_key`, dll) otomatis disamarkan `••••••`.
- Konteks permintaan (IP, metode, URL, user agent) diisi otomatis.

Pencatatan otomatis:

| Peristiwa | Level | Status |
| --- | --- | --- |
| Percobaan masuk gagal | `danger` | 422 |
| Akun terkunci (rate limit) | `warning` | 429 |
| Akses ditolak | `danger` | 403 |
| Kegagalan sistem tak tertangani | `danger` | 500 |

Implementasinya di `bootstrap/app.php` memakai **`$exceptions->render()`** (bukan `report()`, karena Laravel mengabaikan turunan `HttpException` saat melapor). Kesalahan validasi (422) dan 404 **sengaja tidak dicatat** agar log tidak bising.

---

## Notifikasi

Kirim notifikasi bertarget dari controller:

```php
Notify::toPermission(
    permission: 'users.view',      // hanya pemegang izin ini yang menerima
    title: 'Pengguna baru terdaftar',
    module: 'Pengguna',
    body: "{$user->name} · peranan {$role}",
    url: '/users',                 // tujuan saat notifikasi diklik
    level: 'success',              // info | success | warning | danger
);

Notify::toUser($user, 'Kata sandi Anda diubah', 'Keamanan');
```

Aturan:
- `Notify::toPermission()` hanya mengirim ke pengguna **aktif** yang lulus `$user->can($permission)` dan **tidak** ke pelaku aksi.
- Daftar & jumlah belum dibaca dibagikan lewat `HandleInertiaRequests` (`notifications.items`, `notifications.unread`) dan **selalu diambil dari `Auth::user()`** sehingga tidak mungkin bocor ke pengguna lain.
- Menandai dibaca melalui `POST /notifications/{id}/read` (dibatasi kepemilikan, selain pemilik → 403) dan `POST /notifications/read-all`.

## Aksi Massal (Bulk Action)

`DataTableCard` menerima prop `selectable` + `selected` dan memancarkan `update:selected`; gunakan slot `#bulk-actions` untuk menaruh tombol:

```vue
<DataTableCard selectable :selected="selected" @update:selected="selected = $event">
    <template #bulk-actions>
        <Button size="sm" variant="destructive" @click="bulkConfirm = true">Hapus</Button>
    </template>
</DataTableCard>
```

Backend memakai Form Request (`BulkUserRequest`, `BulkRoleRequest`, `Reference\BulkReferenceRequest`) dengan validasi `ids.*` `exists`, mencatat audit trail beserta jumlah baris yang dilewati, dan mengirim notifikasi bertarget.

## Pengaturan Penampilan (Branding)

- Nilai disimpan pada tabel `settings` dan dibaca lewat `App\Support\Branding` (`raw()` untuk form, `values()` untuk tampilan) — hasilnya **di-cache**, jadi jalankan `php artisan cache:clear` bila mengubah langsung dari DB.
- Kunci bertipe aset: `logo_light`, `logo_dark`, `favicon`, `og_image` (lihat `Branding::ASSETS`).
- Urutan tampil merek (`BrandMark.vue`): **logo → inisial brand → ikon**. Bila berkas logo gagal dimuat (mis. driver penyimpanan berganti), komponen otomatis jatuh ke inisial/ikon sehingga tidak pernah tampil "gambar rusak".
- `resources/views/app.blade.php` memakai nilai ini untuk `<title>`, favicon, meta description/keywords, canonical, `noindex`, dan Open Graph.

---

## Object Storage (S3)

Tidak ada halaman pengaturan penyimpanan — **semua kredensial diambil dari `.env`**:

```
FILESYSTEM_DISK=s3          # local (default) atau s3
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_ENDPOINT=               # untuk S3-compatible (mis. https://nos.wjv-1.neo.id)
AWS_URL=                    # URL publik bila berbeda dari endpoint/bucket
AWS_PATH=                   # prefix folder opsional, mis. adminkit
AWS_USE_PATH_STYLE_ENDPOINT=false
```

- Semua unggahan melewati `App\Support\FileStorage` (`store`, `delete`, `url`).
- Nilai path yang disimpan **berawalan disk**, mis. `local:branding/x.png` atau `s3:branding/x.png`, sehingga berkas lama tetap dapat diakses walau `FILESYSTEM_DISK` diganti.
- Setelah mengubah `.env`, jalankan `php artisan config:clear`.

---

## Design System & Konvensi UI

- Token warna, densitas, dan tipografi ada di `resources/css/app.css` (`--ctl-h`, `--field-gap`, `--item-gap`, `--label-size`, `--label-tracking`, `--label-weight`, `.form-dense`, `.tbl-density`).
- **Badge** memakai referensi asli shadcn/ui (`default`, `secondary`, `destructive`, `outline`) dengan padding compact — tanpa palet warna kustom.
- **Label dan judul memakai Title Case** (mis. `Nama Pengguna`, `Kata Sandi`, `Tambah Pengguna`), bukan Sentence case.
- **Tombol aksi utama di header/toolbar memakai kata kerja pendek** — `Tambah`, `Ekspor`, `Impor`, `Simpan` (tanpa mengulang nama entitas), sedangkan **judul dialognya lengkap** (`Tambah Menu`, `Tambah Pengguna`, `Ubah Peranan`).
- **Footer kartu/halaman dengan tepat dua tombol memakai `justify-between`** — tombol sekunder (`Batal`) menempel di pojok kiri, aksi utama (`Simpan`) di pojok kanan. Bila hanya satu tombol, pakai `justify-end`. (Footer dialog tetap rata kanan mengikuti komponen `Dialog`.)
- Jangan menambahkan `CardDescription` di bawah judul kartu.
- Header dialog (`ui/Dialog.vue`): judul dan tombol tutup sejajar vertikal (`items-center`, judul `leading-6`, tombol tutup kotak `size-7`). Judul dialog memakai Title Case dan diakhiri `?` untuk konfirmasi.
- Ikon memakai `lucide-vue-next`; jangan memakai emoji.
- **Ikon menu bebas dari seluruh koleksi Lucide** (~1.600 ikon). Kolom `menus.icon` menerima nama Lucide apa pun — kebab-case (`house-wifi`) maupun PascalCase (`HouseWifi`), alias lama seperti `Users2` tetap dikenali. `resources/js/lib/menuIcons.js` memetakan nama → komponen lewat `import.meta.glob` ke berkas ikon Lucide, sehingga tiap ikon dimuat sebagai chunk terpisah saat dipakai (bundel utama tidak memuat semua ikon). Form Menu Navigasi memakai **input teks bebas** dengan pratinjau ikon di sebelahnya; nama yang tidak dikenali memunculkan galat dan jatuh ke ikon `Folder`.
- **Setiap elemen interaktif dan informasi penting wajib punya `data-testid`** dengan format kebab-case, mis. `user-form-save`, `users-filter-role`.
- Sidebar mendukung mode ikon (`collapsible="icon"`): elemen non-ikon disembunyikan dengan `group-data-[collapsible=icon]:hidden`, dan ikon utama memakai `shrink-0`.

---

## Tabel Server-side

Backend memakai `App\Support\TableQuery` (search, sort, direction, filter, paginasi):

```php
$search = TableQuery::search($request);
$sort = TableQuery::sort($request, self::SORTABLE, 'name');
$dir = TableQuery::direction($request);
```

Frontend memakai `useServerTable` + `DataTableCard`:

```js
const { query, loading, reload, onSearch, onSort, onPage, onPerPage, onFilter, sortState } = useServerTable({
    url: '/users',
    only: ['users', 'filters'],
    initial: { search: '', sort: 'name', dir: 'asc', status: 'all', role: 'all', page: 1, per_page: 10 },
});
```

Properti kolom `DataTableCard`: `{ key, label, align?, width?, sortable?, sortKey?, hideBelow? }`.
`hideBelow: 'sm' | 'md' | 'lg' | 'xl'` menyembunyikan kolom sekunder pada layar kecil (dipakai di Pengguna & Audit Trail agar tampilan mobile tetap rapi).
Nilai `'all'` dipakai sebagai sentinel filter "semua" karena `reka-ui` melarang nilai kosong pada item.

---

## Rute

| Metode | URI | Keterangan |
| --- | --- | --- |
| GET/POST | `/login`, `/logout` | Autentikasi |
| GET | `/` | Dashboard |
| GET | `/profile` | Profil pengguna |
| PUT | `/profile`, `/profile/password` | Perbarui profil & kata sandi |
| POST/DELETE | `/profile/avatar` | Unggah/hapus foto profil |
| GET | `/users` | Daftar pengguna (search, sort, filter peranan & status Aktif/Terarsip/Semua) |
| GET | `/users/create`, `/users/{user}/edit` | Halaman tambah & ubah pengguna |
| POST/PUT/DELETE | `/users`, `/users/{user}` | Simpan, perbarui, arsipkan (soft delete) pengguna |
| POST | `/users/{user}/restore` | Pulihkan pengguna terarsip |
| DELETE | `/users/{user}/force` | Hapus permanen pengguna terarsip |
| GET | `/permissions` | Daftar izin (Perizinan) |
| POST/PUT/DELETE | `/permissions`, `/permissions/{permission}` | CRUD izin |
| POST | `/permissions/bulk-destroy` | Hapus massal izin |
| POST | `/permissions/generate` | Generator izin standar per entitas |
| GET | `/permissions/export`, `/users/export`, `/audit-trail/export` | Unduh Excel (.xlsx) sesuai filter aktif |
| POST | `/users/import` | Impor pengguna dari Excel |
| GET | `/users/import/template` | Unduh template impor pengguna |
| PUT | `/roles/{role}/permissions` | Simpan matriks hak akses peranan |
| PUT | `/roles/entity-order` | Simpan urutan kartu entitas pada matriks |
| GET | `/roles`, `/roles/{role}` | Daftar & detail peranan |
| POST/PUT/DELETE | `/roles`, `/roles/{role}` | CRUD peranan |
| POST | `/roles/import` | Impor peranan dari Excel |
| GET | `/roles/import/template` | Unduh template impor peranan |
| POST | `/users/bulk` | Aksi massal pengguna (`archive`/`restore`/`force-delete`) |
| POST | `/roles/bulk-destroy` | Hapus massal peranan |
| POST | `/notifications/read-all`, `/notifications/{notification}/read` | Tandai notifikasi dibaca |
| GET | `/audit-trail`, `/audit-trail/{log}` | Audit trail & detail |
| DELETE | `/audit-trail` | Hapus jejak audit pada rentang tanggal |
| GET | `/appearance` | Pengaturan penampilan |
| PUT | `/appearance/{identity\|seo\|contact}` | Simpan per bagian |
| POST/DELETE | `/appearance/asset/{key}` | Unggah/hapus aset merek |
| GET | `/offices`, `/institutions`, `/products`, `/installments`, `/methods` | Data referensi (CRUD via dialog, hapus permanen) |
| GET | `/products/{id}` | Detail produk: **Parameter Produk (SK Direksi)** |
| PUT | `/products/{id}/parameters` | Simpan parameter produk (izin `products.manage`) |
| POST/PUT/DELETE | `/{slug}`, `/{slug}/{id}`, `/{slug}/bulk` | Simpan, perbarui, hapus (per baris & massal) data referensi |
| GET | `/committees`, `/committees/{path}` | Jalur komite kredit & pengelolaan jenjangnya |
| GET | `/committees/export` | Unduh seluruh jalur + jenjang dalam satu Excel (untuk review) |
| GET | `/committees/simulate?product_id=&condition=&amount=` | JSON simulasi kewenangan: jalur yang cocok, rantai keputusan, pemutus, peringatan |
| POST/PUT/DELETE | `/committees`, `/committees/{path}` | CRUD jalur komite |
| POST/PUT/DELETE | `/committees/{path}/tiers`, `/committees/{path}/tiers/{tier}` | CRUD jenjang (route ter-scope ke jalurnya) |
| PUT | `/committees/{path}/tiers/{tier}/move/{up\|down}` | Geser urutan jenjang |
| GET | `/telescope` | Laravel Telescope (login + email diizinkan) |
| * | selain di atas | `Route::fallback()` → halaman error 404 bertema |

---

## Telescope (Debug)

`laravel/telescope` terpasang di `/telescope` dengan **dua lapis** perlindungan:

1. Middleware `['web', 'auth', Authorize::class]` (`config/telescope.php`) → tamu dialihkan ke `/login`.
2. Gate `viewTelescope` hanya meloloskan email pada `TELESCOPE_ALLOWED_EMAILS` (dipisah koma di `.env`); pengguna lain menerima 403 (halaman error bertema).

```
TELESCOPE_ENABLED=true
TELESCOPE_ALLOWED_EMAILS=studio@jkv.co.id
```

Catatan penting: `App\Providers\TelescopeServiceProvider::boot()` mendaftarkan ulang grup middleware `telescope` **tanpa** `Laravel\Sentinel\Http\Middleware\SentinelMiddleware`. Sentinel memblokir `/telescope` dengan 401 ketika `APP_ENV=local` diakses lewat reverse proxy publik (kasus pod preview), padahal otorisasi sudah ditegakkan oleh sesi login + gate email. `authorization()` juga di-override agar gate berlaku di **semua** environment (bawaan Telescope melewati pemeriksaan saat `local`).

---

## Bahasa & Pesan Validasi

- `laravel-lang/common` terpasang dan berkas bahasa Indonesia ada di `lang/id/` + `lang/id.json` (`php artisan lang:add id`, perbarui dengan `php artisan lang:update`).
- `APP_LOCALE=id` → seluruh pesan validasi bawaan Laravel otomatis berbahasa Indonesia (`required`, `email`, `unique`, `in`, `max`, dll).
- **Pesan kustom tetap menang**: `App\Support\Rules::messages()` dan `messages()` pada setiap Form Request (mis. halaman Login, telepon, username) tidak berubah — Laravel Lang hanya mengisi aturan yang belum punya pesan kustom.

---

## Pengujian

```bash
php artisan test                             # seluruh suite (27 tes)
php artisan test --filter=SeederTest         # data awal: jumlah referensi, peranan, kata sandi bawaan, idempotensi, semua menu bawaan hidup
php artisan test --filter=CommitteeRulesTest # aturan komite: seeder, keunikan jalur, scope jenjang, ekspor, simulasi
php artisan test --filter=ExcelIoTest        # ekspor/impor .xlsx & penolakan berkas CSV
php artisan test --filter=ErrorPageTest      # 404/403 memakai halaman error Inertia
./vendor/bin/pint --test                     # pemeriksaan gaya kode
```

Catatan pengujian manual/otomatis:
- Kata sandi akun uji harus dikembalikan setelah pengujian.
- Hindari menyimpan form Penampilan dengan nilai contoh — nilainya persisten dan akan mengubah branding aplikasi.
- Rate limit masuk adalah 5 percobaan gagal per kredensial+IP.

---

## Deployment

1. `composer install --no-dev --optimize-autoloader`
2. `yarn install && yarn build`
3. `php artisan migrate --force`
4. `php artisan storage:link`
5. `php artisan config:cache route:cache view:cache` (jalankan `php artisan optimize:clear` saat men-debug)
6. Pastikan `storage/` dan `database/` dapat ditulis oleh web server.

---

## Pemecahan Masalah

| Gejala | Penyebab & Solusi |
| --- | --- |
| Perubahan `.vue`/`.css` tidak muncul | Aset belum dikompilasi — jalankan `yarn build` (atau `yarn dev`) |
| Branding tidak berubah setelah edit DB | Cache branding — `php artisan cache:clear` |
| Logo tampil sebagai ikon | Berkas tidak dapat dimuat (driver penyimpanan berganti / berkas terhapus) — unggah ulang; ini perilaku fallback yang disengaja |
| Gambar aset 404 setelah pindah driver | Nilai lama tanpa awalan disk; unggah ulang aset agar tersimpan sebagai `local:`/`s3:` |
| `php: not found` | Instal PHP 8.2+ beserta ekstensi pada bagian Persyaratan |
| Izin baru tidak langsung berlaku | Cache Spatie — controller sudah memanggil `forgetCachedPermissions()`; bila mengubah lewat tinker jalankan `app(PermissionRegistrar::class)->forgetCachedPermissions()` |
| `php artisan down` masih tampil bawaan Laravel | Pastikan `resources/views/errors/503.blade.php` ada dan hook `respond()` di `bootstrap/app.php` TIDAK menangani 503 |
| Impor ditolak "harus berformat Excel" | Simpan berkas sebagai `.xlsx` (Excel/LibreOffice/Google Sheets → Unduh sebagai Excel); `.csv` tidak lagi didukung |
| Waktu relatif berbahasa Inggris | Setel `APP_LOCALE=id` (locale Carbon mengikuti nilai ini) |
| Login selalu 419 saat dibuka di iframe | Cookie sesi diblokir lintas situs — setel `SESSION_SAME_SITE=none` + `SESSION_SECURE_COOKIE=true`, lalu `php artisan config:clear` |
| `/telescope` mengembalikan 401 | `laravel/sentinel` memblokir akses proxy publik saat `APP_ENV=local` — pastikan `TelescopeServiceProvider::boot()` mendaftarkan ulang grup middleware `telescope` (lihat bagian Telescope) |
| Username numerik tidak bisa masuk | Sudah diperbaiki: kredensial dicari serentak pada `email`/`username`/`phone`, bukan ditebak dari formatnya |
| Pesan validasi masih berbahasa Inggris | Jalankan `php artisan lang:add id` lalu `php artisan optimize:clear` |

---

## Lisensi

MIT.
