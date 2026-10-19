# PRD / Memori proyek

> SATU-SATUNYA berkas memori proyek ini. Jangan membuat `memory.md`, `state.md`, `backlog.md`, `notes.md`,
> atau berkas lain di `memory/` (BACKLOG.md lama sudah dilebur ke sini pada 2026-10-19).
>
> **Tujuan:** agent atau sesi baru tahu apa yang ada, mengapa, dan apa berikutnya tanpa membaca seluruh kode. Baca pertama kali, setiap sesi.
>
> **Aturan (tetap kecil, sasaran di bawah ±150 baris):**
>
> - Bagian tetap saja: Visi, Kondisi sekarang, Keputusan, Pertanyaan terbuka, Langkah berikutnya.
> - **Kondisi sekarang adalah potret, bukan log.** Tulis yang ada _sekarang_, di mana, dan cara memeriksanya. Bila kemampuan bertambah,
>   berubah, atau hilang, sunting barisnya di tempat.
> - **Ganti, jangan menambah di bawah.** Keputusan yang digantikan disunting di tempat dengan satu baris alasan. Langkah yang selesai dan
>   pertanyaan yang tertutup dihapus; riwayat git adalah arsipnya.
> - Tanpa changelog dan tanpa cerita per tugas. Perbarui bila yang ada berubah, keputusan dibuat atau dibatalkan, atau pertanyaan dibuka/ditutup.

## Visi

SIPEBRI v3 adalah aplikasi kredit BPR Bangunarta (Laravel 13 + Inertia v3 + React 19) yang menulis ulang fungsional SIPEBRI lama
(`/Users/Bananay/Projects/sipebri-v2.0.1/adminkit`, hanya dibaca). Alur berkas: `draft → submitted → scheduling → survey → analysis →
committee → approved | rejected → disbursed` (+ `cancelled`). Setiap perubahan data bisnis tercatat di audit log (standar OJK).
Identitas pengguna dan nasabah datang dari Codex.

## Kondisi sekarang

- **Modul kredit:** tahap 0 (peran, izin, notifikasi, pengguna, peranan), 1 (data referensi kredit + parameter produk + BMPK), 2 (komite:
  jalur, jenjang default/perorangan, aturan khusus, anggota, pengecualian), 3 (agunan + pengajuan), 4 (penjadwalan + survei + lokasi),
  5 (lembar analisa 8 bagian, ajukan ke komite), 6 (persetujuan komite per jenjang, RC, koreksi setelah disetujui) sudah ada. Peta kode: tabel
  "Arsitektur singkat" di `CLAUDE.md`. Periksa: `php artisan test --compact`.
- **Dashboard:** ringkasan berkas kredit (KPI, per bulan/status/produk/kantor, berkas terbaru, survei mendatang).
- **Login dan nasabah lewat Codex** (`App\Auth\CodexAuthClient`, `App\Services\CodexClient`); tanpa login lokal. MFA opsional (TOTP/OTP email).
- **Audit log:** append-only, rantai hash HMAC, `audit:verify` harian, halaman Audit Log (filter, detail, ekspor CSV, verifikasi).
- **Bahasa:** seluruh UI sudah Indonesia; kode, test, dan commit Inggris (`.ai/rules/general.md`).
- **Gerbang mutu:** `scripts/verify.sh` satu-satunya definisi gerbang (pelemahan + baris Brief, audit dependency, `.env` vs contoh, Pint,
  PHPStan, Pest, `npm run check`, jscpd, TypeScript, build); dijalankan hook `pre-push`, `composer verify`, dan CI. Hook `commit-msg` menegakkan
  brief (`docs/briefs/TEMPLATE.md`) dan menolak pemeriksaan yang dilemahkan. Hook dipasang dengan `composer setup` atau
  `git config core.hooksPath .githooks`.
- **UI:** Compact UI dari react-starter/codex: komponen `resources/js/components/ui`, token di `resources/css/app.css`, pemeriksa
  `npm run standards` (`scripts/check-standards.mjs`), tes Vitest di samping kodenya, contoh semua komponen di `/ui-guideline` (Super Admin).
- **Dokumen agent:** `AGENTS.md` bagian 1 (alur kerja), `.ai/rules/*` (aturan per jalur), `.ai/skills/antislop*` (skill vendored),
  `CLAUDE.md` (peta arsitektur), berkas ini. Tidak ada yang lain.

## Keputusan

- **Kerangka kerja agent diambil dari codex, aturan UI dan komponen dari react-starter (user, 2026-10-19).** Diadopsi: alur brief + hook,
  `verify.sh`, `check-weakened.sh`, `check-dependencies.sh`, jscpd, standar produksi 9 pertanyaan, kualitas desain, test penjaga (dokumen ↔ kode,
  skill, komentar, desain, gerbang), skill antislop (mode DURING), memori tunggal ini; komponen isian/grafik/kartu/dialog, token status dan grafik,
  aturan tabel, Vitest, `/ui-guideline`. **Tidak diambil** (alasan): komentar dan nama test berbahasa Indonesia (aturan sipebri: kode Inggris),
  dokumen berbahasa Inggris (dokumen sipebri berbahasa Indonesia), trigger MySQL untuk audit (sipebri memakai trait `Auditable` + SQLite untuk test),
  `lib/http.ts`/`useResource`/`useForm` buatan sendiri dan kontrak `/api/*` (sipebri memakai Inertia + Wayfinder), `layouts/menu.ts` (menu dari
  `App\Support\Navigation`), SSO, karyawan, `dev:accounts`, `ReferenceManager`/`RegionPicker` (domain codex), kata "akun" dilarang (pilihan
  bahasa codex), `IdleTimeout` dan foto profil (butuh kontrak server yang belum ada di sipebri).
- **Dependency karena adopsi (user, 2026-10-19):** ditambah devDependency `@testing-library/{react,user-event,jest-dom}`, `jsdom`, `jscpd`; `recharts` dibuang (grafik lewat `components/ui/chart.tsx`). Risiko diterima: `npm audit` masih melaporkan temuan hanya di alat pengembangan (`--omit=dev` bersih), perbaikannya butuh versi di luar rentang.
- **Tabel memecah kolom bertumpuk** (satu nilai satu kolom) di semua daftar, termasuk analisa dan persetujuan (2026-10-19). Daftar persetujuan kini diurutkan di server pada semua kolom: usulan plafon/tenor jatuh ke permohonan bila memorandum belum ada, posisi = langkah tertunda, "Giliran" memakai uji yang sama dengan tampilan "Menunggu saya". Kunci dari tabel lain diurutkan lewat subkueri berkunci unik; tanpa migrasi indeks baru karena kolom berkas yang diurutkan sudah berindeks dan indeks pada nilai hasil subkueri tidak dipakai. Audit log hanya urut waktu (tabel besar tanpa indeks lain). Label menu "Data Audit Log" menjadi "Audit Log" (aturan label maksimal dua kata).
- **Deploy ke VPS sendiri, bukan Laravel Cloud** (sama dengan codex): skill `deploying-to-cloud` tidak dipakai.
- **Login (audit fitur 1, user, 2026-10-19):** batas gagal 5 per orang+IP dan 60 per IP per menit (longgar karena kantor mungkin berbagi IP);
  `CODEX_VERIFY=false` di produksi hanya diperingatkan di log; username yang diketik pada percobaan gagal disimpan hanya bila berbentuk
  username/email (kata sandi pendek tanpa simbol yang diketik di situ tetap tak terbedakan); orang berkas-ada dengan id Codex berubah
  ditolak dan diselesaikan Super Admin (memindahkan semua rujukan id berisiko bagi rantai audit). IP yang dicatat adalah IP sambungan; di balik proxy yang
  belum dipercaya itu IP proxy (bukan perubahan fitur, diatur saat deploy bila perlu).
- **Tidak boleh menambah status berkas baru tanpa dibahas.**
- **Antrean offline survei tidak dibuat** (user): tanpa sinyal aplikasi pun tidak bisa dibuka; indikator jaringan sudah memberi tahu.
- **Alamat dari koordinat tidak dibandingkan dengan alamat KTP nasabah:** survei bisa dilakukan di mana saja.
- **HRIS dibatalkan dan dibersihkan** (user, 2026-10-01): jangan dilanjutkan tanpa diminta.
- **`RoleSeeder` tidak menimpa izin role yang sudah diatur manual** (disengaja); izin wajib lewat `REQUIRED`.

## Pertanyaan terbuka

- Status `disbursed`/realisasi: bentuk integrasi core banking belum jelas.
- **Persetujuan ulang:** bila angka yang sudah diputus komite perlu berubah, bentuknya (pengajuan baru atau putaran kedua) belum dirancang.
- **Parameter produk yang perlu dikonfirmasi perusahaan:** KRU bunga 3,5 dengan tenor sampai 300 bulan; ambang RC kosong di semua produk
  (bawaan kode 70%); kolom SK Direksi (`decree`) kosong; KRS sistem cicilan bawaan kosong di staging (seeder = Bulanan).
- **Konflik kepentingan keluarga dekat** anggota komite belum ditangani (butuh data hubungan keluarga atau penandaan manual wajib).
- **BMPK penuh** (berdasarkan modal, paparan per peminjam/kelompok) belum dimodelkan; sekarang satu angka.
- **Audit akses baca halaman daftar** belum dicatat (volume besar); tambahkan ringkas bila auditor meminta.
- Retensi/arsip audit log (OJK, bertahun-tahun) belum diatur; jangan hapus baris.
- Role lama `HR Admin`/`Viewer` masih ada di database dev (tanpa izin).

## Langkah berikutnya

Dipilih user; agent tidak memulai fitur baru tanpa diminta.

1. **Audit silang dokumen ↔ kode per unit** (user, 2026-10-19), satu brief "Audit fitur N" per fitur (bentuk: brief 0004 codex, commit 584013f), urutan dari pemilik ("fitur N"), mengikuti
   `AGENTS.md` bagian 1 dan 3; temuan langsung diperbaiki kecuali yang mengubah kebijakan (dijelaskan ke pemilik dulu). Unit yang selesai dihapus
   dari daftar. Platform: (2) MFA + profil, (3) peran dan izin, (4) Data Pengguna (temuan awal:
   `UserSeeder` memuat nama dan email pribadi yang tampak asli, padahal seeder hanya boleh berisi data karangan), (5) navigasi dan `/`,
   (6) audit log (temuan awal: `audit:prune` terjadwal bulanan, padahal "Pertanyaan terbuka" menyebut retensi belum diatur), (7) notifikasi,
   (8) halaman error + status jaringan. Referensi: (9) data referensi generik, (10) parameter produk, (11) BMPK, (12) komite: jalur, jenjang,
   aturan khusus, (13) anggota komite, NIK, konflik, pengecualian. Alur: (14) dashboard, (15) nasabah Codex, (16) pengajuan, (17) agunan,
   (18) penjadwalan + batal, (19) survei + foto, (20) lokasi + alamat perkiraan, (21) lembar analisa + rumus, (22) persetujuan komite + RC,
   (23) koreksi setelah persetujuan. Kerangka: (24) UI bersama + `/ui-guideline`, (25) gerbang dan kerangka agent (temuan awal: `.ai/rules/general.md` masih
   berjudul "UI nanti Indonesia" dan melarang menerjemahkan, padahal seluruh UI sudah Indonesia).
2. **Periksa layar internal di peramban** setelah adopsi UI 2026-10-19 (1280 px dan 375 px, keadaan memuat/kosong/galat): belum dilakukan karena login lewat Codex butuh kredensial pemilik; layar tamu, halaman error, serta daftar Analisa dan Persetujuan (sesi lokal pada salinan SQLite) sudah.
3. Realisasi/pencairan + posting core banking (menunggu rancangan).
4. Pesan validasi kustom dan atribut (`->attributes()`) yang masih Inggris di controller: rapikan, pakai laravel-lang sebisanya.
5. Ekspor/impor Excel (ada di SIPEBRI lama) belum dibawa.
6. Saran layanan email OTP (keputusan user): layanan transaksional atau relay resmi, SPF/DKIM/DMARC untuk `bprbangunarta.co.id`, antrean +
   mailer cadangan bila volume tumbuh, ganti password SMTP yang pernah terlihat di komentar `.env`.
7. Pengujian yang butuh perangkat/layanan nyata: GPS lewat HTTPS di iOS Safari dan Chrome Android; unggah foto ke S3 pada kondisi buruk;
   alur MFA penuh dengan authenticator sungguhan; Nominatim produksi (batas pemakaian); cron `schedule:run` di server; `audit:verify` pada jutaan
   baris; deploy dari awal (`migrate`, `db:seed --force`, `locations:geocode`, `ATTACHMENTS_DISK=s3`); kewenangan komite dengan nominal sungguhan.
