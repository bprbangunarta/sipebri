# PRD — AdminKit (Starter Kit Panel Admin)

## Problem statement (asli)
"buatkan starterkit admin panel dengan teknologi Backend Laravel 12, PHP 8.2+ / Frontend Vue 3, Inertia.js, TailwindCSS 3, Vite 6. saya melampirkan sistem flowdesk yang saya bangun menggunakan emergent, yang saya inginkan kita akan membuat sebuah admin panel dengan mengikuti design sistem yang ada pada project itu."

Klarifikasi user:
- Mulai dengan **halaman statis** dulu sampai UI disetujui (hindari revisi besar).
- Database: **SQLite**.
- Design system: **ikuti persis FlowDesk**, konsep **compact UI** diutamakan.
- **Dark mode**: ya.
- Lampiran zip FlowDesk = referensi UI (bukan untuk dijalankan); CSS ikut dipelajari, bukan hanya docs.
- Iterasi lanjutan: **hanya menu Dashboard** dulu; sidebar mengikuti FlowDesk (screenshot dilampirkan).

## Arsitektur
- Laravel 12 (PHP 8.3.33) di `/app/adminkit`, SQLite (`database/database.sqlite`).
- Catatan (Jun 2026): pod restart menghapus PHP lagi → pulihkan dengan `bash /app/memory/restore_php.sh` (repo Debian bawaan hanya punya php8.2; composer.lock butuh 8.3 → repo sury wajib).
- Jun 2026: UI halaman Parameter Produk dirapikan — badge status parameter, sub-judul "Plafon & Tenor", daftar metode/cicilan jadi kotak scroll tinggi tetap (168px) sehingga kolom kiri-kanan sejajar, semua baris kontrol 32px.
- Jun 2026: Field **Kantor** pada form pengguna kini dropdown (Combobox) dari referensi Data Kantor. Tetap disimpan sebagai teks nama kantor (keputusan user); validasi `Rule::in` nama kantor + nilai lama pengguna (mis. "Kantor Pusat") agar data legacy tak tertolak; impor Excel menerima kode/alias/nama kantor dan dipetakan ke nama resmi; template impor memakai nama kantor nyata.
- Jun 2026: Filter **Kantor** ditambahkan di tabel Kelola Pengguna (`users-filter-office`), opsinya referensi Data Kantor + nilai kantor lain yang masih terpakai; filter ini juga terbawa ke Ekspor Excel.
- Jun 2026 (20/08): **UserSeeder** kini berisi 22 akun (superadmin `SA@4dm1n` + 21 pegawai uji kata sandi `password`) lengkap dengan peranan, kantor, alias, kode MSO/kolektor; akun terarsip dipulihkan, peranan disinkronkan, kata sandi tak pernah ditimpa saat seed ulang.
- Jun 2026 (20/08): Form Agunan Kredit **dirombak mengikuti referensi user** (hasil inspect element): kartu **Informasi Agunan** (Jenis Agunan, Jenis Pengikatan, No. Dokumen, Nama Pemilik, Alamat Agunan, Lokasi Agunan, Keterangan Agunan) + kartu **Kondisi & Asuransi** (Kondisi, Tgl Kondisi, Diasuransikan, Tgl Asuransi, Nilai Jaminan, Nilai Pasar, Nilai Taksasi, Tgl Taksasi, Nilai Apraisal, Tgl Apraisal) dengan jarak antar-baris seragam. Lokasi Agunan kini satu pilihan dati2 (513 opsi, kode dikirim ke CBS). Field yang dihapus dari form tetap ada di tabel/payload (nullable + default: insured `T`, ppap `1`, nilai `0`).
- Jun 2026 (20/08): Agunan Kredit: judul kartu "Agunan Kredit", aksi baris **Ubah · Payload · Hapus**, modal **Payload CBS** (footer: kiri Batal, kanan Salin + **Posting** — tombol Posting masih placeholder toast, nanti memvalidasi kolom wajib sebelum kirim ke CBS). Kartu **Kondisi & Asuransi disembunyikan di halaman Tambah** (CS/kepala kantor hanya mengisi Informasi Agunan); saat **Ubah** (tahap analisa) kolom Kondisi, Tgl Kondisi, Diasuransikan, Tgl Asuransi, Tgl Taksasi **wajib**. No. Dokumen kini wajib.
- Jun 2026 (20/08): **Aturan placeholder** ditetapkan: kolom tidak wajib → `(Opsional)` (termasuk selectbox & date picker); kolom wajib → tanpa placeholder, selectbox `-- Pilih --`. Diterapkan pada form Agunan; baris nilai jadi 4 kolom (Nilai Jaminan · Nilai Pasar · **Nilai NJOP** · **Adjusment**) — NJOP & Adjusment opsional, default tersimpan `0`. Kolom wajib di form Agunan: Jenis Agunan, Nama Pemilik, Alamat Agunan, Lokasi Agunan.
- Jun 2026 (20/08): `composer.json` `platform-check: false` → aplikasi jalan di PHP 8.2 maupun 8.3 (mengakhiri kerusakan berulang saat pod reset menghapus PHP 8.3).
- Jun 2026 (20/08): **Aturan format angka Indonesia** ditetapkan & diterapkan: komponen `NumberInput.vue` (tampil `1.000`, simpan `1000`) dan `DecimalInput.vue` (tampil `12,75`, simpan `12.75`) + helper `persen()` di `constants/committee.js`. Dipakai di ProductDetail (plafon, tenor, bunga/provisi/admin/RC), CommitteeDetail, CommitteeSimulator, form Contoh Agunan, dan persentase Dashboard. Aturan wajib di `/app/memory/ui_rules.md` + README.
- Jun 2026 (20/08): **Modul Contoh Agunan Kredit** (`/collateral-simulation`, CRUD + dialog **Payload CBS**) mengikuti form core banking: identitas, jenis/pengikatan/dokumen, pemilik & lokasi (pemilihan bertahap Kabupaten→Kecamatan→Kelurahan yang mengisi kode dati2), nilai agunan (diisi Staff Analis setelah survey), kondisi & asuransi. **TIDAK ADA PERHITUNGAN** — `CollateralSimulation::toCbsPayload()` hanya memetakan data ke kontrak API CBS. Referensi baru: `regions` (82.449 baris, menu **Data Wilayah** `/regions`), `ownership_statuses` (jenis 05: 11 pilihan), kondisi agunan `9 : TIDAK ADA MASALAH`. Peringkat & Pemeringkat Surat Berharga sengaja **disabled** (belum dipakai). 2 contoh agunan dari CBS diseed. Total 41 izin, 24 menu, 27 tes lolos.
- Jun 2026 (20/08): Alias produk kode 15 diubah `KPM` → **KPMI** (ProductSeeder + CommitteeSeeder ikut diperbarui). Grup menu **Simulasi** dibuat: `/collateral-simulation` (Agunan Kredit) & `/analysis-simulation` (Analisa Kredit) — masih **halaman placeholder** ("Segera hadir"), controller `SimulationController`, izin `collateral-simulation.view` & `analysis-simulation.view` (total 38 izin), 23 menu.
- Jun 2026 (20/08): Dashboard = **ringkasan pemberian kredit** DATA STATIS dengan **filter kantor** (Semua Kantor + 7 kantor). Kartu Aktivitas Terakhir, Berkas per Tahap, Penyimpanan, Galeri Komponen, dan metrik NPL/kolektibilitas DIHAPUS. Penyaluran per Kantor & Komposisi Produk kini menampilkan **jumlah berkas + plafon**. Responsif diverifikasi pada 390/768/1024/1440 (tanpa overflow horizontal): KPI `grid-cols-2 md:grid-cols-3 xl:grid-cols-5`, grafik `overflow-x-auto min-w-[460px] md:min-w-0`, sel tabel `whitespace-nowrap`. Checklist responsif disimpan di `/app/memory/ui_rules.md`. Ikon menu Jenis Pengikatan di MenuSeeder = `lock`.
- Jun 2026 (20/08): Referensi grup **Agunan**: `/collateral-types` (19 jenis agunan), `/collateral-bindings` (7 jenis pengikatan — sebelumnya `/binding-types`), `/collateral-conditions` (6 kondisi agunan), `/collateral-methods` (4 metode hitung). Semua memakai pola `ReferenceController` + `pages/Reference.vue`; izin `collateral-*.view/manage` (total 36 izin), MenuSeeder membuat grup `Agunan`, breadcrumb ditambahkan di `resources/js/config/navigation.js`.
- Jun 2026 (20/08): `SettingSeeder` kini memanggil `Branding::forget()` — sebelumnya cache branding "selamanya" membuat identitas kembali ke default AdminKit setelah `migrate:fresh --seed`.
- Jun 2026 (20/08): Test suite jadi 26 tes, termasuk `tests/Feature/SeederTest.php` (jumlah data referensi, satu peranan per pengguna, kata sandi bawaan, kantor mengacu referensi, idempotensi seed, pemulihan akun terarsip, login memakai kata sandi bawaan).
- Jun 2026: Form pengguna diringkas — kartu "Keamanan" dihapus. Halaman Tambah menampilkan field **Kata Sandi** (menggantikan Terakhir Login), halaman Ubah menampilkan **Terakhir Login** saja (tanpa field kata sandi). Kata sandi kini diatur dari aksi baris **Reset Sandi** di daftar pengguna (`PUT /users/{user}/password`, permission `users.manage`, remember_token diacak ulang) — modalnya menampilkan info **Terakhir Login** (bukan kolom tabel). Aksi **Kirim Email** memakai dialog konfirmasi sebelum benar-benar mengirim.
- Inertia.js 2 + Vue 3.5 + Tailwind 3.4 + Vite 6.4, primitive UI via `reka-ui`, ikon `lucide-vue-next`.
- Referensi FlowDesk disimpan di `/app/reference/flowdesk-frontend` (docs + src + index.css).
- Preview: supervisor `frontend` menjalankan `php artisan serve --port 3000` (script di `/app/frontend/package.json`). Aset di-build (`yarn build`) — jalankan ulang setelah mengubah JS/CSS.
- `trustProxies(at: '*')` agar URL aset memakai https di balik ingress.

## Dokumen terkait
- `CHANGELOG.md` — riwayat pekerjaan per tanggal (terus bertambah).
- `ROADMAP.md` — daftar pekerjaan berikutnya (P0/P1/P2).
- `alur_kredit.md`, `pengajuan_kredit.md`, `agunan_cbs_form.md`, `integrasi_codex.md`,
  `skema_migrasi.md`, `sinkronisasi_staging.md`, `ui_rules.md`, `test_credentials.md`.

## Modul inti SIPEBRI (ringkas)
Alur kredit: **Pengajuan → Penjadwalan → Survei → Analisa → Persetujuan Komite**.
- **Pengajuan**: data nasabah (lewat API Codex), produk & parameter, agunan, peringatan kelipatan jangka waktu.
- **Penjadwalan**: penugasan surveyor, batas 3× jadwal ulang, pembatalan.
- **Survei**: catatan, foto (S3) dan titik koordinat (Leaflet).
- **Analisa** (8 bagian): 1 Analisa Usaha (Perdagangan/Pertanian/Jasa/Lainnya) · 2 Analisa Keuangan ·
  3 Analisa Kepemilikan · 4 Analisa Agunan · 5 Analisa 5C · 6 Analisa Kualitatif · 7 Memorandum ·
  8 Administrasi. Bagian 1–3 sudah jadi; bagian 4–8 masih placeholder.
- **Komite**: jalur & jenjang pemutus per produk. **Modul keputusan selesai 22/06/2026**: berkas status
  `KOMITE` diputus berjenjang (TERUSKAN → naik komite; DISETUJUI/DITOLAK/DIBATALKAN = keputusan akhir),
  dialog "Persetujuan Komite" (Max Plafon, Metode RPS, biaya, suku bunga, RC, usulan plafon, jangka, catatan),
  kartu Catatan Komite per jenjang, izin `approval-simulation.manage` untuk Kasi/Kabag Analis & Direksi.
