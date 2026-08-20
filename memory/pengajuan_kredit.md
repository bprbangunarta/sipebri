# Pengajuan Kredit (tahap 1 dari 9) — rancangan database

Dibuat 21/06/2026. Menu: **Simulasi → Pengajuan Kredit** (`/loan-simulation`),
izin `loan-simulation.view` / `loan-simulation.manage`.

## Kode pengajuan
`application_code` — 8 digit, unik, **wajib**, dibuat sistem berurutan mulai **00700001**
(sistem lama berhenti di `00360623`; blok 007xxxxx dipakai agar tidak bentrok — sebelumnya
008xxxxx, dipindah 22/06/2026 karena angka 8 terlalu mirip 0).
Lihat `LoanApplication::nextCode()` (`CODE_START = 700000`, memperhitungkan data terarsip).

## Kolom wajib
`application_code` (otomatis), `nik` (angka 8–20 digit), `full_name`. Sisanya opsional supaya
berkas bisa dibuka cepat lalu dilengkapi pada tahap berikutnya.

## Tabel `loan_applications`
- **Identitas berkas**: `application_code`, `application_date`, `status`, `office_id`,
  `product_id`, `purpose`, `economic_sector`, `source`.
- **Pemohon**: HANYA `nik`, `full_name`, `cif_number`. Identitas lengkap **tidak disimpan** —
  diambil dari API sistem pengelola nasabah lewat nomor KTP (lihat bagian di bawah).
- **Permohonan**: `requested_amount`, `requested_tenor`, `tenor_principal` (JK Pokok),
  `tenor_interest` (JW Bunga), `usage_type` (KONSUMTIF/PRODUKTIF/INVESTASI), `method_id`,
  `installment_id`, `interest_rate`, `provision_rate`, `admin_rate`, `institution_id`
  (resort/instansi — opsional, hanya untuk pengelompokan), `purpose`, `note`, `collateral_note`.
- **Penugasan**: `supervisor_id` (Kasi Analis), `surveyor_id`.
  Pilihan Kasi Analis/Surveyor diambil dari pengguna SIPEBRI sesuai peranan.
  Kolom `confirmed_at` / `confirmed_by` sudah **dihapus** (22/06/2026) — pengajuan cukup
  ditandai perubahan status DRAFT → DIAJUKAN, dan jejak pelakunya ada di `updated_by`.
- **Kolom audit** (konvensi standar): `created_by` (wajib), `updated_by`, `deleted_by` —
  bertipe string berisi **nama lengkap pengguna**, diisi otomatis oleh trait
  `App\Models\Concerns\TracksAuthor` (fallback `SISTEM`), plus `softDeletes`.
- **Analisa (rangka)**: `analyst_id`, `analyzed_at`, `analysis_note`, `rc_ratio`,
  `repayment_capacity`, `recommended_amount`, `recommended_tenor`.
  Metode analisa berbeda per produk → modul analisa dibuat terpisah menyusul.
- **Persetujuan**: `committee_path_id`, `decision`, `decided_at`, `decided_by`, `decision_note`,
  `approved_amount`, `approved_tenor`, `approved_rate`.
- **Realisasi**: `credit_account` (unik, dari respons CBS), `disbursed_at`.
- Jejak: `created_by`, `timestamps`, `softDeletes`.

## Tabel pendukung
- `loan_application_collaterals` — berkas ↔ agunan (`collateral_simulations`), unik per pasangan.
  Setelah posting kredit ke CBS, `credit_account` pada agunan terkait ikut diperbarui.
- `loan_approvals` — jejak keputusan berjenjang: `committee_tier_id`, `level`, `role`, `user_id`,
  `decision`, `note`, `decided_at` (mengikuti jalur komite produk di `committee_paths`/`committee_tiers`).

## Status berkas
`DIAJUKAN → ANALISA → KOMITE → DISETUJUI / DITOLAK / DIBATALKAN → REALISASI`
(konstanta `LoanApplicationController::STATUSES`).

## Belum dikerjakan
1. Modul analisa kredit per produk (RC, kelayakan) + pengisian `analyst_id`/`analyzed_at` otomatis.
2. UI relasi berkas ↔ agunan dan alur keputusan komite (tabel sudah siap).
3. Posting ke CBS: kirim data kredit → simpan `credit_account` ke berkas & agunan terkait.
4. Master nasabah/CIF (saat ini data pemohon direkam per berkas).

## Data pemohon: dari sistem lain, bukan milik SIPEBRI (keputusan user 21/06/2026)
- Bank sudah punya sistem pengelola nasabah & calon nasabah. SIPEBRI **hit API dengan nomor KTP**;
  bila KTP tidak ditemukan, pengajuan **tidak bisa dilanjutkan** (nasabah harus didaftarkan dulu).
- Identitas (termasuk data **pendamping**) hanya ditampilkan read-only pada panel "Info Nasabah".
  Tanggal lahir tidak dipakai/disimpan.
- Implementasi sementara **MOCK**: `App\Support\CustomerDirectory` dengan 3 KTP contoh
  (`3213011203950001`, `3213012509880007`, `3213015207920003`). Ganti isi `find()` dengan panggilan
  HTTP saat endpoint siap; endpoint UI-nya `GET /loan-simulation/lookup?nik=`.
- Kategori pengajuan (BARU/RSC/dll) di sistem lama **tidak dipakai** — jalur komite sudah ditentukan
  oleh produk + Komite Kredit.

## Alur UI
1. `/loan-simulation/create` — form ringkas: No. KTP + tombol **Cek KTP** (panel identitas muncul),
   plafon, jangka waktu. Tombol "Buka Berkas" nonaktif sampai KTP ditemukan.
2. `/loan-simulation/{id}` — berkas dengan tab: **Data Pengajuan** → **Data Jaminan**
   (lekatkan agunan + total taksasi) → **Data Surveyor** (kantor, Kasi Analis, Surveyor) →
   **Konfirmasi** (checklist 4 baris; setelah dikonfirmasi status jadi `ANALISA`, tidak bisa ulang).
