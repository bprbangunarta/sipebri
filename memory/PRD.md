# PRD / Memori proyek

> Satu-satunya berkas memori proyek ini; berkas pelacak lain dilarang (`AGENTS.md` bagian 1).
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
- **Gerbang mutu (devcore):** `scripts/verify.sh` menjalankan baris-baris `scripts/verify.steps` (registri aturan dan skill, selftest gerbang,
  audit dependency PHP dan JS, `env:check`, Pint, PHPStan, build, Pest, standar UI, `npm run check`, jscpd, `tsc`); pengaturannya di
  `devcore.conf`. Hook `commit-msg` menolak commit kode tanpa brief (`docs/briefs/TEMPLATE.md`) atau baris `Brief: none:`, dan commit yang
  melemahkan pemeriksaan (termasuk membuang langkah dari `verify.steps`). `pre-push`, `composer verify`, dan CI menjalankan gerbang yang sama.
  `scripts/selftest.sh` membuktikan gerbang menolak yang harus ditolak. Hook dipasang dengan `composer setup` atau
  `git config core.hooksPath .githooks`.
- **UI:** Compact UI dari react-starter/codex: komponen `resources/js/components/ui`, token di `resources/css/app.css`, pemeriksa
  `npm run standards` (`scripts/check-standards.mjs`), tes Vitest di samping kodenya, contoh semua komponen di `/ui-guideline` (Super Admin).
- **Dokumen agent:** `AGENTS.md` (panduan devcore + bagian proyek), `.ai/rules/*` (aturan standar dan aturan proyek, terdaftar di `index.md`),
  `.ai/skills/antislop*` (skill vendored), `CLAUDE.md` (peta arsitektur), berkas ini. Tidak ada yang lain.

## Keputusan

- **Kerangka agent = devcore (`/Users/Bananay/Claude/devcore`, user, 2026-10-19), menggantikan salinan codex yang dipasang lebih dulu hari itu.**
  Dipasang dengan layer laravel dan react: pesan gerbang Inggris, berkas standar (`AGENTS.md` bagian standar, `quality.md`, `testing.md`,
  `laravel.md`, `ui.md`, `TEMPLATE.md`) Inggris persis seperti sumbernya supaya mudah diperbarui, agent hanya commit dan **bertanya dulu sebelum
  push**. Aturan khas SIPEBRI tetap berbahasa Indonesia sebagai aturan proyek (`general.md`, `audit-log.md`, `database.md`, `php.md`, `tests.md`,
  `js.md`) dan terdaftar di `.ai/rules/index.md`. Lima test PHP gerbang (Brief, Weakened, Verify, GateDefinition, DependencyAudit) diganti
  `scripts/selftest.sh`; skrip audit dependency buatan sendiri diganti dua langkah audit di `verify.steps`. Aturan UI dan komponen dari react-starter tetap
  (`js.md`, `components/ui`, `/ui-guideline`). **Tidak diambil dari codex/react-starter** (alasan): trigger MySQL untuk audit (SIPEBRI memakai
  `Auditable` + SQLite untuk test), `lib/http.ts` (SIPEBRI memakai Inertia + Wayfinder), `layouts/menu.ts` (menu dari `Navigation`), SSO,
  karyawan, `ReferenceManager`/`RegionPicker`, kata "akun" dilarang, `IdleTimeout` dan foto profil (butuh kontrak server yang belum ada).
- **Penyimpangan dari standar devcore, dicatat:** (1) PHPStan level 7, bukan 8; level 8 memberi 47 temuan di kode lama (`AuditPrune`,
  `LoanApplicationController`, `SchedulingController`, dan lain-lain), ditunda sebagai brief terpisah; `GateCoverageTest` menjaga level agar tidak turun di bawah 7. (2) (selesai 2026-10-20) `.env.example` kini hanya variabel; penjelasannya di `docs/deployment.md`, dijaga `EnvExampleTest`. (3) Audit
  dependency tanpa jaringan kini menggagalkan gerbang (tidak lagi peringatan) mengikuti devcore. (4) `DesignQualityTest`, `check-standards.mjs`,
  `phpstan.neon`, `pint.json`, dan `.jscpd.json` tetap versi SIPEBRI (lebih lengkap atau memuat daftar utang).
- **Dependency karena adopsi (user, 2026-10-19):** ditambah devDependency `@testing-library/{react,user-event,jest-dom}`, `jsdom`, `jscpd`; `recharts` dibuang (grafik lewat `components/ui/chart.tsx`). Risiko diterima: `npm audit` masih melaporkan temuan hanya di alat pengembangan (`--omit=dev` bersih), perbaikannya butuh versi di luar rentang.
- **Tabel memecah kolom bertumpuk** (satu nilai satu kolom) di semua daftar, termasuk analisa dan persetujuan (2026-10-19). Daftar persetujuan kini diurutkan di server pada semua kolom: usulan plafon/tenor jatuh ke permohonan bila memorandum belum ada, posisi = langkah tertunda, "Giliran" memakai uji yang sama dengan tampilan "Menunggu saya". Kunci dari tabel lain diurutkan lewat subkueri berkunci unik; tanpa migrasi indeks baru karena kolom berkas yang diurutkan sudah berindeks dan indeks pada nilai hasil subkueri tidak dipakai. Audit log hanya urut waktu (tabel besar tanpa indeks lain). Label menu "Data Audit Log" menjadi "Audit Log" (aturan label maksimal dua kata).
- **Deploy ke VPS sendiri, bukan Laravel Cloud** (sama dengan codex): skill `deploying-to-cloud` tidak dipakai.
- **Login (audit fitur 1, user, 2026-10-19):** batas gagal 5 per orang+IP dan 60 per IP per menit (longgar karena kantor mungkin berbagi IP);
  `CODEX_VERIFY=false` di produksi hanya diperingatkan di log; username yang diketik pada percobaan gagal disimpan hanya bila berbentuk
  username/email (kata sandi pendek tanpa simbol yang diketik di situ tetap tak terbedakan); orang berkas-ada dengan id Codex berubah
  ditolak dan diselesaikan Super Admin (memindahkan semua rujukan id berisiko bagi rantai audit). IP yang dicatat adalah IP sambungan; di balik proxy yang
  belum dipercaya itu IP proxy (bukan perubahan fitur, diatur saat deploy bila perlu).
- **Audit fitur 24, UI bersama (brief 0002, user, 2026-10-19):** 15 temuan diperbaiki, masing-masing dengan test yang gagal dulu (komponen berperilaku
  kini punya test Vitest di sampingnya; 235 test layar): Combobox tanpa kotak cari tidak bisa dipakai dengan keyboard (dipakai di 27 tempat); keterangan
  `hint` tidak pernah terbuka di layar sentuh; baris tabel yang bisa diklik tidak terjangkau keyboard; tempelan kode OTP kehilangan digit ("Kode: 482915"
  menjadi kosong); nominal tempelan dengan desimal berlipat 100 ("1.500.000,50" menjadi 150 juta); `Tip` melempar galat di luar penyedia tooltip;
  `Field` tidak menyambungkan galat dan keterangan ke Combobox dan DatePicker; judul kolom bernode tampil sebagai kuncinya; memuat dan galat tidak
  diumumkan ke pembaca layar; DatePicker mogok pada tanggal rusak; `rupiah` dan `formatNumber` menampilkan "NaN"; seretan berkas mengabaikan `accept`
  dan seretan yang ditolak menghapus berkas yang sudah dipilih; pesan 403 paket izin tampil berbahasa Inggris. (Dugaan Tabs melebar di ponsel tidak terbukti
  di 375 px dan tidak diubah; `CommitteeTabs` adalah navigasi tautan buatan sendiri, bukan `Tabs` kit: untuk audit fitur 12.)
  **Keputusan pemilik:** tepi kontrol 1,26:1 menjadi token `control` (3,65:1), placeholder `muted` penuh (5,27:1), cincin fokus `primary` penuh (7,7:1); dijaga
  `app.contrast.test.ts`. **Diterima, tidak diubah:** tombol tutup dialog tanpa `Tip` (tooltip yang terbuka pada fokus pertama menelan Escape pertama);
  target sentuh 28 px (kepadatan, `ui.md`); `Combobox` dan `DatePicker` memakai `onChange` yang membawa nilai, sedangkan isian lain `onValueChange` (mengubahnya
  menyentuh puluhan layar); singkatan Agustus "Agt" dari date-fns (bukan "Agu"); jam tampil menurut zona perangkat, bukan WIB (perangkat di luar WIB melihat jam lain
  dari audit log); `ColorInput` dan `RadioGroup` belum dipakai layar mana pun (tetap ada dengan contoh dan test).
- **Audit fitur 25, gerbang dan kerangka agent (brief 0003, user, 2026-10-20):** temuan pada berkas milik devcore (skrip, hook, aturan standar) hanya dilaporkan
  sebagai umpan balik, tidak diedit lokal. Diperbaiki di sisi proyek dengan test yang gagal dulu: rentang pemeriksaan CI kosong pada cabang baru, force-push, dan
  `before` kosong (commit tanpa `Brief:` lolos; kini dikoreksi di `ci.yml`); `dependabot.yml` hanya mencakup aksi (kini juga composer dan npm); `env:check` (BOM, kunci
  berindentasi atau huruf kecil, tulis serentak yang menggandakan variabel, nilai rahasia tercetak di peringatan, symlink, berkas read-only, contoh kosong; kini semua
  `APP_ENV` selain `local` dan `testing` diperlakukan seperti server); `.env.example` hanya variabel dan `docs/deployment.md` baru; `.gitignore` menutup `.env.*`; blok Boost
  rusak bila bagian proyek `AGENTS.md` menyebut tag pembukanya (`RulesCoverageTest`); banyak klaim basi di `CLAUDE.md`, `general.md`, `php.md`, `audit-log.md`, `js.md`.
  Penjaga baru: `GateCoverageTest` (langkah `verify.steps` wajib, level PHPStan, `.jscpd.json`, `denyWarnings`, cakupan `CODE_PATHS`/`WEAKENED_SCAN`/`TEST_PATHS`, bit eksekusi
  hook, tanda to-do dan skip yang tidak dibaca skrip), `CiWorkflowTest`, `RulesCoverageTest`; test penjaga lama dimutasi (±250 mutasi) dan yang hijau-setelah-mutasi diperkuat
  (`DocsMatchCodeTest`, `CommentHygieneTest`, `SkillsRegistryTest`, `EnvExampleTest`, `DesignQualityTest`, `ArchitectureTest`, `BriefsTest`). `CODE_PATHS` kini mencakup `artisan`,
  `.jscpd.json`, `.env.example`, `.npmrc`, `pnpm-workspace.yaml`, `.gitattributes`, `.mcp.json`; `composer install` memasang hook; `composer setup` memakai `npm ci` dan `package.json` bernama
  `sipebri`; test kosong bawaan skeleton Laravel dan enum `BloodType` (sisa HRIS) dihapus. **Diterima:** edit skrip atau hook sendiri hanya terlihat di tinjauan; alasan 10 karakter apa pun
  lolos; klon baru tanpa hook hanya dijaga CI sampai `composer install`; audit dependency butuh jaringan dan memblokir push saat offline; urutan langkah gerbang tidak ketat
  dari yang tercepat (standar dan jscpd sesudah Tests); `boost:update` membuang bagian "Test Enforcement" dan menulis ulang `boost.json` (dikecualikan dari format). Waktu terukur: klon baru ke
  hijau ±170 dtk (Tests 54 dtk terbesar), `selftest.sh` 8 sampai 10 dtk. **Di luar cakupan:** PR atau penggabungan ke `main`; pekerjaan hanya di `v3.0.0-alpha` (pemilik, 2026-10-20).
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
- **Retensi audit log:** kodenya sudah ada (`AUDIT_RETENTION_YEARS`, bawaan 5; `audit:prune` terjadwal tanggal 1 pukul 03:00; `audit:verify` harian pukul 02:00), tetapi aturan proyek menyatakan baris audit tidak boleh dihapus; keputusan kebijakan retensi (OJK) belum ada dan belum diuji di server. Sampai putus, jangan menyalakan penjadwal produksi tanpa memutuskan nasib `audit:prune`.
- Role lama `HR Admin`/`Viewer` masih ada di database dev (tanpa izin).

## Langkah berikutnya

Dipilih user; agent tidak memulai fitur baru tanpa diminta.

1. **Audit silang dokumen ↔ kode per fitur** (user, 2026-10-19; nomor fitur = baris tabel fitur yang ditampilkan di chat, fitur 1 login, 24 UI bersama, dan 25 gerbang
   sudah selesai): satu brief "Audit fitur N" per fitur (bentuk: brief 0004 codex, commit 584013f), mengikuti `AGENTS.md` bagian 1 dan 3; temuan
   langsung diperbaiki kecuali yang mengubah kebijakan (dijelaskan ke pemilik dulu). **Urutan dari pemilik:** (24 dan 25 selesai), 8 halaman error + status jaringan, 6 audit log, 7 notifikasi, 2 MFA + profil, 3 peran dan izin, 4 Data Pengguna,
   5 navigasi (di sini juga: pesan 403 berbahasa Inggris dari paket izin sudah dibuang di `bootstrap/app.php`, uji ulang bersama halaman error), 9 data referensi, 10 parameter produk, 11 BMPK, 12 komite, 13 anggota komite; alur kredit (14 sampai 23) dibahas nanti.
   Temuan awal untuk fitur yang belum diaudit: (4) `UserSeeder` memuat nama dan email yang tampak asli, (6) `audit:prune` terjadwal bulanan
   padahal retensi belum diatur.
2. **Periksa layar internal di peramban** setelah adopsi UI 2026-10-19 (1280 px dan 375 px, keadaan memuat/kosong/galat): `/ui-guideline`, layar tamu, halaman error, serta daftar Analisa dan Persetujuan sudah; layar fitur lain diperiksa bersama audit fiturnya.
3. Realisasi/pencairan + posting core banking (menunggu rancangan).
   **Brief terpisah yang menunggu:** PHPStan level 8 (47 titik akses ke nilai yang bisa null di tujuh controller dan command); daftar umpan balik untuk devcore dari audit 25
   (rename menyembunyikan test yang dihapus, spoof header `++ `, `diff.mnemonicPrefix`, rentang CI kosong, pre-push dengan sha remote tak dikenal, merge buruk, selftest tidak hermetik).
4. Pesan validasi kustom dan atribut (`->attributes()`) yang masih Inggris di controller: rapikan, pakai laravel-lang sebisanya.
5. Ekspor/impor Excel (ada di SIPEBRI lama) belum dibawa.
6. Saran layanan email OTP (keputusan user): layanan transaksional atau relay resmi, SPF/DKIM/DMARC untuk `bprbangunarta.co.id`, antrean +
   mailer cadangan bila volume tumbuh, ganti password SMTP yang pernah terlihat di komentar `.env`.
7. Pengujian yang butuh perangkat/layanan nyata: GPS lewat HTTPS di iOS Safari dan Chrome Android; unggah foto ke S3 pada kondisi buruk;
   alur MFA penuh dengan authenticator sungguhan; Nominatim produksi (batas pemakaian); cron `schedule:run` di server; `audit:verify` pada jutaan
   baris; deploy dari awal (`migrate`, `db:seed --force`, `locations:geocode`, `ATTACHMENTS_DISK=s3`); kewenangan komite dengan nominal sungguhan.
