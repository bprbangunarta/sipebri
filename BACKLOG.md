# Backlog

Legenda: ✅ selesai · 🔜 berikutnya · ⏸ ditunda (belum diminta) · ❌ dibatalkan user
Terakhir diperbarui: 2026-09-30.

## Perapihan

- ✅ Hapus semua menu/route/controller/request/resource/halaman/test HRIS (Employees, Gender, Religion, Position). Dashboard tetap. Model, migrasi, factory, seeder karyawan dipertahankan untuk analitik. Sisa HRIS (model, migrasi, factory, seeder, fallback nasabah) kemudian juga dihapus (2026-10-01).
- ✅ Sidebar disesuaikan ke label Indonesia (Pengajuan, Jaminan, Jadwalan, Survey, Analisa; Referensi; Hak Akses). Hanya label/struktur menu, fungsi halaman tidak diubah (judul halaman masih Inggris).
- ✅ Menu **Data Perizinan** ada (controller + halaman index kosong). 🔜 Isi halaman (kelola izin Spatie) belum dibuat.
- ✅ Menu **Data Wilayah** (di bawah Data Resort) dan label **Jadwal** sudah disesuaikan.
- ✅ Dashboard diganti ringkasan kredit (berkas per status/bulan/produk/kantor, survei mendatang).
- ✅ Sisa HRIS dibersihkan; `CustomerDirectory` hanya Codex.
- Role lama `HR Admin` / `Viewer` masih ada di database dev (tanpa izin, tidak dipakai); hapus lewat halaman Roles bila perlu.

## Modul kredit (porting dari SIPEBRI)

| #   | Tahap                                                   | Status | Catatan                                              |
| --- | ------------------------------------------------------- | ------ | ---------------------------------------------------- |
| 0   | Peran, izin, sidebar per izin, notifikasi, Users, Roles | ✅     | `spatie/laravel-permission`                          |
| 1   | Data referensi kredit + parameter produk                | ✅     | 16 tabel via registry; wilayah 82k baris             |
| 2   | Komite kredit (jalur, jenjang, Check authority)         | ✅     | Seeder aturan bawaan                                 |
| 3   | Agunan + Pengajuan kredit (Codex/fallback karyawan)     | ✅     |                                                      |
| 4   | Penjadwalan survei + Survei (foto berkoordinat)         | ✅     |                                                      |
| 5a  | Analisa: halaman index berkas siap analisa              | ✅     | Hanya daftar, tanpa aksi buka                        |
| 5b  | Analisa: lembar 8 bagian                                | ⏸      | Lihat detail di bawah                                |
| 6   | Persetujuan komite (keputusan per jenjang)              | ⏸      | Bergantung 5b; tabel `loan_approvals`                |
| 7   | Realisasi/pencairan + posting core banking              | ⏸      | Belum dirancang di sumber                            |
| 8   | Dashboard kredit (ringkasan berkas per status)          | ✅     | Selesai; akan bertambah seiring tahap analisa/komite |

### Detail 5b – Lembar analisa (dari `AnalysisController`, `AnalysisBusinessController`, model `Analysis*` di sumber)

1. Analisa Usaha: 4 tipe usaha (perdagangan/pertanian/jasa/lainnya), kode otomatis AUPG/AUP/AUJ/AUL, rumus di backend dan dicerminkan di frontend
2. Analisa Keuangan: 7 biaya rumah tangga + kewajiban; pendapatan usaha = jumlah kontribusi per bulan
3. Analisa Kepemilikan: 8 harta + harta lain
4. Analisa Agunan: berita acara pemeriksaan per agunan
5. Analisa 5C: 28 aspek berskor, evaluasi ≥80 BAIK / ≥60 CUKUP BAIK
6. Analisa Kualitatif: karakter, usaha, SWOT
7. Memorandum: kebutuhan dana, usulan plafon, biaya, pengikatan
8. Administrasi: 16 pos biaya
   Tombol "Ajukan ke Komite" (syarat: ≥1 usaha, biaya RT, 5C dinilai, usulan plafon) → status `committee`, notifikasi ke Kepala Seksi Analis & pemegang `committees.view`.
   Catatan porting: kolom sumber berbahasa Indonesia; putuskan penamaan Inggris konsisten sebelum membuat migrasi.

## Autentikasi Codex

- ✅ Kantor pengguna = relasi ke tabel `offices` (`users.office_id`), disinkronkan dari Codex saat login (hanya kode, alias, nama; kolom Codex lain tidak dipakai sistem ini dan tidak disimpan). Kolom teks `office`/`office_code` di users sudah dihapus.
- ✅ Login memakai Codex (meniru simontok); peran dari nama peran Codex (`RoleName`, `RoleSeeder`); migrasi mengganti nama peran & tier komite lama (Kasi Analis → Kepala Seksi Analis, dst.).
- ✅ Data Pengguna menampilkan pengguna tidak aktif juga (soft delete dari Codex) dengan filter status Active/Inactive, kolom username, kantor, dan status; baris tidak aktif tanpa aksi.
- 🔜 Sisa di halaman **Data Pengguna** (tambah/ubah password/peran) masih versi lama (buat user + password lokal, ubah peran). Sekarang akun berasal dari Codex dan peran di-reset tiap login, jadi halaman ini perlu diubah jadi daftar baca-saja (tanpa tambah/ubah password/peran).
- 🔜 Sisa pesan validasi kustom & atribut (`->attributes()`) di controller masih Inggris; rapikan saat terjemahan UI (pakai pesan laravel-lang sebisanya).
- 🔜 Terjemahan UI ke bahasa Indonesia per layar (kode tetap Inggris; lihat `.ai/rules/general.md`).
- Catatan dev: akun lokal lama (admin@example.com, ao@, dst.) tidak bisa login lagi; barisnya masih ada di database dan akan ditimpa bila id Codex sama.

## Keputusan yang masih terbuka

- Status `disbursed`/`REALISASI`: bentuk integrasi core banking belum jelas.
- Apakah role bank (Kepala Seksi Analis, dll.) perlu dipetakan ke `positions`/karyawan HRIS? Saat ini terpisah (user ↔ role saja).
- Fallback nasabah dari tabel `employees` hanya untuk uji lokal; produksi memakai Codex.

## Utang teknis / catatan

- ✅ Audit log lengkap sudah ada (lihat CLAUDE.md). Keputusan komite/realisasi nanti wajib memakai `Auditable`/`Audit::record()`.
- Retensi/arsip audit log (OJK: simpan bertahun-tahun) belum diatur; jangan hapus baris. Pertimbangkan partisi/arsip bila tabel besar.
- `RoleSeeder` tidak menimpa izin role yang sudah diatur manual (disengaja); izin wajib lewat `REQUIRED`.
- Dashboard HRIS belum tahu peran/izin (tampil sama untuk semua yang punya `dashboard.view`).
- Ekspor/impor Excel (ada di SIPEBRI) belum dibawa.

## Saran peningkatan (belum dikerjakan, keputusan ada di user)

- **Layanan email (OTP).** Kuota pengirim diurus user; ini hanya catatan saran:
  - Kuota SMTP Gmail/Workspace terbatas (sekitar 500–2.000 penerima/hari dan bisa berubah), dan login ikut gagal bila akun diblokir atau password aplikasinya dicabut. Untuk produksi pertimbangkan layanan email transaksional (SES, Postmark, Mailgun) atau relay resmi.
  - Pasang SPF, DKIM, dan DMARC untuk `bprbangunarta.co.id` supaya email OTP tidak masuk spam atau ditolak.
  - Pengiriman kini sinkron dengan timeout 10 detik dan batas 6 kode/jam/orang. Bila volume tumbuh, pindahkan ke antrean (perlu worker yang selalu hidup) dan tambahkan mailer cadangan (`failover`) yang tidak membuka celah keamanan.
  - Ganti password SMTP yang pernah terlihat di komentar `.env`.
- **Audit akses baca.** Saat ini hanya halaman detail data sensitif yang dicatat (pengajuan, jaminan, survei, cek nasabah). Halaman daftar (list) sengaja belum dicatat karena volumenya besar. Bila OJK/auditor meminta jejak baca untuk daftar juga, tambahkan pencatatan ringkas (siapa, halaman, filter, jumlah baris), sebaiknya dengan sampling atau ringkasan per sesi agar tabel tidak membengkak, dan perhatikan retensi 5 tahun.

## Dibatalkan

- ❌ HRIS Round 2 (absensi, cuti, payroll, audit log, impor/ekspor karyawan, peran HR/Manager/Employee): dibersihkan atas permintaan user. Cadangan DB pra-modul-kredit ada di scratchpad sesi (`database.before-credit.sqlite`).
