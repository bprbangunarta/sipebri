# Alur Proses Pengajuan Kredit (SIPEBRI)

Sumber: penjelasan user 22/06/2026. Dikerjakan bertahap.

## Status berkas (jangan menambah status baru tanpa dibahas)
`DRAFT → DIAJUKAN → PENJADWALAN → SURVEY → ANALISA → KOMITE → DISETUJUI/DITOLAK → REALISASI`
(+ `DIBATALKAN`). Status = posisi/proses terakhir berkas.

## 1. Pengajuan (SELESAI)
Menu **Simulasi → Pengajuan** (`/loan-simulation`). Tombol **Ajukan** mengubah DRAFT → DIAJUKAN dan
**mengunci** berkas (tidak bisa diubah/dihapus/lepas agunan). Notifikasi ke semua pemegang izin
`scheduling-simulation.manage`.

## 2. Penjadwalan (SELESAI — 22/06/2026)
Menu **Penjadwalan** (`/scheduling-simulation`), izin `scheduling-simulation.view|manage`.
- Menampilkan berkas berstatus DIAJUKAN & PENJADWALAN. Filter cakupan: **Berkas saya** (bawaan,
  `supervisor_id` = pengguna) / **Semua Kasi Analis** (antisipasi kasi tidak masuk kerja).
- Kasi Analis mengisi **tanggal survei** (tidak boleh masa lalu) + **staff analis** + catatan →
  status jadi PENJADWALAN, `loan_applications.surveyor_id` & `survey_date` diisi.
- Histori di tabel **`loan_schedules`** — APPEND ONLY (tidak ada `updated_at`, tidak pernah dihapus):
  `sequence`, `action` (JADWAL / JADWAL ULANG / BATAL), `survey_date`, `surveyor_id`,
  `surveyor_name` (snapshot), `note`, `reason`, `created_by`, `created_at`.
- **Batas 3 kali hanya PERINGATAN** (`LoanSchedule::MAX_SCHEDULES`) — dikonfirmasi kasi analis
  22/06/2026: penjadwalan ulang boleh lebih dari 3 kali. Di daftar muncul "Jadwal n/3 · lewat batas"
  merah dan modal jadwal memberi peringatan; notifikasi ke kasi analis bertanda `warning`.
  Pembatalan oleh Staff Analis (`POST /scheduling-simulation/{id}/cancel`, izin
  `survey-simulation.manage`) wajib **alasan** → status selalu kembali **DIAJUKAN**.
- Notifikasi: staff analis dapat penugasan; kasi analis dapat permintaan penjadwalan ulang.

## 3. Survei (SELESAI — 22/06/2026)
Menu `/survey-simulation` (`SurveyController`), izin `survey-simulation.view|manage`.
- Daftar **hanya jadwal HARI INI** milik staff analis yang ditugaskan (persis spek). Jadwal terlewat
  hilang dari daftar — itu risiko yang disepakati: staff analis harus **Batal + minta jadwal ulang**.
- Lembar survei (`SurveyDetail.vue`) hanya bisa dibuka oleh `surveyor_id` yang bersangkutan (selain
  itu 404). Bagian read-only: Data Pengajuan ringkas + alamat/HP pemohon dari Codex + Data Agunan.
- Foto: **1–5** (`LoanSurveyPhoto::MAX_PHOTOS`), tombol **Ambil Foto** (`capture=environment`) dan
  **Dari Galeri**. Koordinat diambil `navigator.geolocation` tepat saat foto dipilih dan **wajib**
  (server memvalidasi `latitude`/`longitude`); izin lokasi ditolak → foto tidak diunggah.
  Setiap foto diunggah satu-satu (`POST .../photos`) agar payload kecil, disimpan lewat
  `FileStorage::store()` ke folder `survei/{kode berkas}` pada disk aktif (S3).
- **Simpan Hasil Survei** (min 1 foto) → baris `loan_surveys` (catatan, koordinat foto pertama,
  pelaku, waktu), foto ditautkan ke survei, status berkas menjadi **SURVEY** dan **terkunci**
  (tidak bisa tambah/hapus foto atau simpan ulang).
- **Batal & Minta Jadwal Ulang** memakai endpoint pembatalan penjadwalan (alasan wajib).
- Tabel: `loan_surveys`, `loan_survey_photos` (lat/long desimal 10,7 + `source` KAMERA/GALERI).
- CATATAN PENTING: geolokasi browser hanya jalan di **HTTPS** — pastikan staging/produksi HTTPS.

## 4. Analisa (BELUM)
Menu `/analysis-simulation`. Menampilkan berkas SURVEY milik staff analis. Form dibahas bertahap.
Rencana user: **v1 mengikuti sistem lama (sama untuk semua produk)**, v2 per produk.

## 5. Persetujuan Komite (BELUM)
Menu `/approval-simulation`. Berkas selesai analisa, disaring sesuai peranan & kewenangan komite.

## Catatan penting
- Setiap proses penting punya histori sendiri dan histori TIDAK boleh dihapus.
- Notifikasi lewat lonceng harus mengarah ke data/tindakan yang perlu dikerjakan.
- Izin baru: `scheduling-simulation.*`, `survey-simulation.*`, `approval-simulation.*`,
  `analysis-simulation.manage` (lihat `app/Support/Modules.php`).
- Peranan bawaan di `RoleSeeder` (hanya diisi bila peranan belum punya izin):
  Kasi Analis → penjadwalan; Staff Analis → survei + analisa.
