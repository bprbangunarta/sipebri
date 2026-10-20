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
- **Keputusan pemilik dari audit referensi, pengaturan, dan audit log (briefs 0004 sampai 0014, 2026-10-20).** Perilakunya ada di `CLAUDE.md` dan dijaga test; di sini hanya yang diputuskan atau diterima:
    - Import Excel: hanya Data Resort (kunci `import` di `config/references.php` untuk tabel lain); kode yang sudah ada memperbarui nama; dependency `openspout/openspout` disetujui (lisensi MIT, v5.3.0 rilis 2026-02, tanpa temuan `composer audit`); tombol _Tambah_ tanpa nama tabel; semua kolom daftar referensi bisa diurutkan; mutu data hasil import jadi tanggung jawab yang mengimpor (tanpa penjaga otomatis).
    - Kantor: kode terkunci selagi dipakai (`lock_code`), Codex menang atas nama/alias (formulir memberi tahu), nama kantor tidak dibesarkan.
    - Wilayah, masalah data sumber yang dibiarkan: kode 3408, 6114, dan 6904 memuat lebih dari satu nama kabupaten (kemungkinan salah kode di `regions.csv.gz`; cek dari sumber resmi) dan 407 kelompok desa persis kembar (tidak berbahaya karena agunan merujuk lewat kode; pembersihan + indeks unik = brief sendiri bila diminta). Diukur pada 82.449 baris di MySQL: halaman 3000 121 ms, urut per kolom paling lama 65 ms.
    - Klasifikasi Agunan (`ownership_statuses`) belum dipakai kode mana pun (hanya bisa dikelola di layar); dicatat dulu, penggunaannya ditemukan nanti.
    - Semua waktu tampil WIB apa pun zona perangkat (waktu tanpa zona di audit log dianggap sudah WIB); banner verifikasi dua langkah bisa ditutup sampai keluar.
    - Alasan wajib (min. 5 karakter, tercatat di audit) untuk setiap perubahan BMPK dan parameter produk; draf dicek ulang terhadap BMPK dan parameter saat diajukan.
    - Audit log: membuka layar tercatat sekali per 10 menit per orang; tiap verifikasi mencatat entri terakhir yang diperiksa (`audit_verified_heads`) agar penghapusan entri terbaru terdeteksi; retensi diurus pemilik dengan tangan (catatan "Retensi audit log" di bawah). `audit_verified_heads.entry_id` tanpa FK dengan sengaja: `audit:prune` menghapus entri lama dan baris itu harus tetap ada sebagai bukti.
    - Risiko diterima, kinerja audit log diukur di MySQL pada 100.000 baris: daftar halaman 1 14 ms, halaman 3000 79 ms, filter paling lama 19 ms, cari (LIKE di enam kolom, tanpa indeks) 118 ms, ekspor seluruh tabel 1,6 dtk, verifikasi 0,92 dtk (kira-kira 9 dtk per sejuta baris: tombol di layar bisa melewati batas waktu web pada puluhan juta baris, perintah `audit:verify` tidak terbatas).
- **Penilaian agunan (brief 0016, keputusan user 2026-10-20):** penilaian agunan adalah catatan berriwayat per agunan (agunan dipakai ulang untuk top-up atau kredit baru dan nilainya, terutama kendaraan, turun tiap tahun, jadi analisa selalu diulang); form manajemen tanah/bangunan dan kendaraan roda 2/4 sudah tercakup penuh (bagian A sampai R; brief 0016 dan 0018; kendaraan memakai jenis agunan 10 sampai 12, kapal belum punya form sendiri); jenis agunan lain memakai form umum sampai formnya ada. Tanda tangan dan cetak ditunda; siapa mengisi bagian mana dan pada langkah proses mana dibahas nanti (izin sementara `collaterals.manage`). Foto milik penilaian, bukan milik survei (agunan yang sama dipakai berkali-kali); layar survei kelak menulis ke foto penilaian yang sama. Asumsi bawaan yang perlu dikonfirmasi manajemen: status skenario stres \"M\" bila LTV setelah turun nilai tidak lebih dari 100%; penyesuaian pembanding dalam persen per faktor dan nilai indikasi dihitung dari harga tanah per m²; depresiasi tiap baris biaya memakai persen barisnya sendiri bila diisi, selain itu total ketiga jenis (maks 100%). Hard stop kendaraan mengikuti kalimat form (BPKB/STNK asli, nama konsisten, rangka dan mesin cocok, tidak diubah, tanpa blokir/sita/sengketa, bisa diikat fidusia); pemeriksaan curian, dijaminkan ke pihak lain, dan pembiayaan tersembunyi dicatat tanpa memblokir. Satu kendaraan satu jaminan (nomor rangka dan mesin unik). **Belum:** sambungan ke Analisa Kredit (\"Analisa Agunan\") dan pemetaan ke nilai CBS; `Collateral::update` masih mencap penaksir dan tanggal taksasi dengan penyimpannya.
- **Dashboard:** ringkasan berkas kredit (KPI, per bulan/status/produk/kantor, berkas terbaru, survei mendatang).
- **Login dan nasabah lewat Codex** (`App\Auth\CodexAuthClient`, `App\Services\CodexClient`); tanpa login lokal. MFA opsional (TOTP/OTP email).
- **Audit log:** append-only, rantai hash HMAC, `audit:verify` harian, halaman Audit Logs (filter, detail, ekspor CSV, verifikasi).
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
  `phpstan.neon`, `pint.json`, dan `.jscpd.json` tetap versi SIPEBRI (lebih lengkap atau memuat daftar utang). (5) (brief 0017, user, 2026-10-21) **stempel gerbang:** `verify.sh`, hook `pre-push`, `selftest.sh`, dan `msg.sh` diubah di sini (berkas milik devcore, dengan persetujuan) agar push tidak mengulang gerbang yang baru hijau pada isi yang persis sama; perubahan ini masuk daftar umpan balik devcore supaya diambil ke hulu dan tidak hilang saat devcore diperbarui.
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
  **Keputusan pemilik:** tepi kontrol 1,26:1 menjadi token `control` (3,65:1), **diubah 2026-10-20: kolom isian, tombol outline, zona seret berkas, dan pemilih warna kembali ke `line` yang terang (sesuai referensi desain, kontras tepi ±1,3:1 diterima); `control` hanya untuk kotak centang, radio, sakelar mati;** placeholder `muted` penuh (5,27:1), cincin fokus `primary` penuh (7,7:1); dijaga
  `app.contrast.test.ts`. **Diterima, tidak diubah:** tombol tutup dialog tanpa `Tip` (tooltip yang terbuka pada fokus pertama menelan Escape pertama);
  target sentuh 28 px (kepadatan, `ui.md`); `Combobox` dan `DatePicker` memakai `onChange` yang membawa nilai, sedangkan isian lain `onValueChange` (mengubahnya
  menyentuh puluhan layar); singkatan Agustus "Agt" dari date-fns (bukan "Agu"); jam tampil menurut zona perangkat, bukan WIB (**diputuskan 2026-10-20: semua waktu WIB, lihat brief 0009**; sebelumnya perangkat di luar WIB melihat jam lain
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
- **Retensi audit log (diputuskan user 2026-10-20):** kebijakan retensi 5 tahun diatur di luar sistem dan dijalankan user sendiri, tidak otomatis. Sistem tidak pernah menghapus audit sendiri: `audit:prune` tidak terjadwal, hanya jalan bila dijalankan dengan tangan (meminta konfirmasi, `--force` tanpa terminal) dan menulis jangkar supaya `audit:verify` harian (02:00) tetap hijau; hapus lewat SQL tanpa jangkar membuat verifikasi melaporkan rantai putus.
- **Agunan tanpa nilai (temuan brief 0019):** layar core banking dummy memuat dokumen pendukung (kartu Jamsostek, SK pegawai) berjenis 14 dengan nilai 0, tetapi form umum tidak menerima agunan yang nilai pasarnya 0, jadi keduanya tidak bisa dinilai. Diputuskan pemilik nanti: apakah agunan seperti itu dinilai sama sekali, atau diterima tanpa nilai.
- **Klasifikasi Agunan menemukan pemakainya (catatan brief 0019):** di layar core banking, isian pilihan sebelum "Keterangan Agunan" (mis. MOBIL) dan "Status/Bukti Kepemilikan" (mis. SK PEGAWAI, LAINNYA) tampak sebagai klasifikasi per jenis agunan; tabel Klasifikasi Agunan (`ownership_statuses`) belum dipakai kode mana pun. Belum dikerjakan; menunggu pemetaan CBS.
- **Form manajemen (pemilik menambah delapan form 2026-10-21; pemahaman lengkap di `docs/management-forms.md`):** form 3 sampai 10 (keuangan sederhana, stabilitas CV, RC, residual/DSCR/stress, uji stres, SLIK, rekomendasi, persetujuan komite) belum dikerjakan dan belum dibahas bentuknya. Yang perlu diputuskan lebih dulu: matriks level form 10 (Rp 25 juta, 100 juta, 300 juta; Kasi Analis sampai Direktur Utama) berbeda dari jenjang `CommitteeSeeder` (10, 35, 100, 300 juta); hubungan form dengan lembar Analisa Kredit yang ada; sumber ambang kebijakan yang dikosongkan form (batas RC, DSCR, residual, CV, bobot scoring); sumber data SLIK. Form 5, 6, dan 7 berisi badan yang sama.
- Role lama `HR Admin`/`Viewer` masih ada di database dev (tanpa izin).
- **Temuan retro-check brief 0015 yang dibiarkan (dicatat, belum diputuskan):** `DashboardController` membaca tabel dengan `DB::table` (`laravel.md`); orang yang masuk diambil dari `$request->user()` di banyak controller, bukan dari satu tempat; `beforeEach()` di `AuthTest`, `TwoFactorTest`, `CollateralTest`, `CommitteeDefaultLevelsTest`, dan `CommitteeTest` masih membuat data (`tests.md`); jaminan yang hanya dilekatkan ke pengajuan terhapus lunak bisa dihapus (hitungan lekat tak menyertakan pengajuan terhapus); koordinat desimal `(10,7)` tidak diperiksa jumlah desimalnya. Belum diperiksa: tiap baris skill `inertia-react-development`, `tailwindcss-development`, dan aturan Laravel di luar daftar yang dibaca (antrean, cache, event, mail).

## Langkah berikutnya

Dipilih user; agent tidak memulai fitur baru tanpa diminta.

1. **Audit silang dokumen ↔ kode per fitur: ditunda** (user, 2026-10-20; fitur 1, 24, dan 25 sudah selesai). Fokus sekarang penyesuaian dan fitur baru. Audit fitur 2 sampai 23
   tidak dijalankan sebagai deret sendiri; fitur yang belum diaudit diaudit **sekalian saat ada penyesuaian pada fitur itu**, dan baru dianggap selesai bila penyesuaiannya selesai
   (satu brief memuat penyesuaian dan auditnya, bentuk: brief 0004 codex, commit 584013f, `AGENTS.md` bagian 1 dan 3; temuan diperbaiki kecuali yang mengubah kebijakan, dijelaskan ke pemilik dulu).
   Fitur baru langsung dibuat mengikuti aturan kerja sekarang, jadi tidak perlu audit susulan. Urutan lama dari pemilik, bila audit berdiri sendiri dilanjutkan: 8 halaman error + status jaringan, 6 audit log, 7 notifikasi,
   2 MFA + profil, 3 peran dan izin, 4 Data Pengguna, 5 navigasi (pesan 403 berbahasa Inggris dari paket izin sudah dibuang di `bootstrap/app.php`, uji ulang bersama halaman error),
   9 data referensi, 10 parameter produk, 11 BMPK, 12 komite, 13 anggota komite; alur kredit (14 sampai 23) dibahas nanti.
2. **Periksa layar internal di peramban** setelah adopsi UI 2026-10-19 (1280 px dan 375 px, keadaan memuat/kosong/galat): `/ui-guideline`, layar tamu, halaman error, serta daftar Analisa dan Persetujuan sudah; layar fitur lain diperiksa bersama audit fiturnya.
3. **Alasan wajib untuk perubahan peran, perizinan, dan jenjang komite** (keputusan user 2026-10-20, dari retro-check brief 0015; `php.md` butir 8): tercatat di audit log seperti BMPK; butuh brief sendiri.
4. Realisasi/pencairan + posting core banking (menunggu rancangan).
   **Brief terpisah yang menunggu:** PHPStan level 8 (47 titik akses ke nilai yang bisa null di tujuh controller dan command); daftar umpan balik untuk devcore dari audit 25 dan stempel gerbang dari brief 0017
   (rename menyembunyikan test yang dihapus, spoof header `++ `, `diff.mnemonicPrefix`, rentang CI kosong, pre-push dengan sha remote tak dikenal, merge buruk, selftest tidak hermetik).
5. Pesan validasi kustom dan atribut (`->attributes()`) yang masih Inggris di controller: rapikan, pakai laravel-lang sebisanya.
6. Ekspor/impor Excel (ada di SIPEBRI lama) belum dibawa.
7. Saran layanan email OTP (keputusan user): layanan transaksional atau relay resmi, SPF/DKIM/DMARC untuk `bprbangunarta.co.id`, antrean +
   mailer cadangan bila volume tumbuh, ganti password SMTP yang pernah terlihat di komentar `.env`.
8. Pengujian yang butuh perangkat/layanan nyata: GPS lewat HTTPS di iOS Safari dan Chrome Android; unggah foto ke S3 pada kondisi buruk;
   alur MFA penuh dengan authenticator sungguhan; Nominatim produksi (batas pemakaian); cron `schedule:run` di server; `audit:verify` pada jutaan
   baris; deploy dari awal (`migrate`, `db:seed --force`, `locations:geocode`, `ATTACHMENTS_DISK=s3`); kewenangan komite dengan nominal sungguhan.
