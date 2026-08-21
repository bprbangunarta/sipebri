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
- **Batas 3 kali** (`LoanSchedule::MAX_SCHEDULES`). Pembatalan oleh Staff Analis
  (`POST /scheduling-simulation/{id}/cancel`, izin `survey-simulation.manage`) wajib **alasan**:
  status kembali **DIAJUKAN**; bila jadwal sudah 3 kali → kembali ke **DRAFT** (petugas pengaju bisa
  mengubah/menghapus, atau kasi analis membatalkan berkas).
- Notifikasi: staff analis dapat penugasan; kasi analis dapat permintaan penjadwalan ulang.

## 3. Survei (BELUM — tahap berikutnya)
Menu `/survey-simulation` masih placeholder. Rencana: menampilkan berkas PENJADWALAN milik staff
analis yang bersangkutan **dan tanggal survei = hari berjalan**. Aksi hanya **Ubah** dan **Batal**.
Wajib **minimal 1 foto lokasi** (kamera diutamakan, boleh galeri bila kamera gagal) dan sistem
mengambil **koordinat (lat/long)** saat pengambilan foto → dasar peta lokasi survei.
Simpan hasil → status **SURVEY**. Foto disimpan di object storage (S3), bukan base64.
Catatan: geolokasi browser hanya jalan di **HTTPS**.

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
