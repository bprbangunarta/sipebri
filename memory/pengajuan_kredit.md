# Pengajuan Kredit (tahap 1 dari 9) — rancangan database

Dibuat 21/06/2026. Menu: **Simulasi → Pengajuan Kredit** (`/loan-simulation`),
izin `loan-simulation.view` / `loan-simulation.manage`.

## Kode pengajuan
`application_code` — 8 digit, unik, **wajib**, dibuat sistem berurutan mulai **00800001**
(sistem lama berhenti di `00360623`, jadi blok 008xxxxx dipakai agar tidak bentrok).
Lihat `LoanApplication::nextCode()` (`CODE_START = 800000`, memperhitungkan data terarsip).

## Kolom wajib
`application_code` (otomatis), `nik` (angka 8–20 digit), `full_name`. Sisanya opsional supaya
berkas bisa dibuka cepat lalu dilengkapi pada tahap berikutnya.

## Tabel `loan_applications`
- **Identitas berkas**: `application_code`, `application_date`, `status`, `office_id`,
  `product_id`, `purpose`, `economic_sector`, `source`.
- **Pemohon** (master nasabah belum ada, jadi direkam di berkas): `cif_number`, `nik`,
  `full_name`, `birth_place`, `birth_date`, `gender`, `marital_status`, `mother_name`, `npwp`,
  `address`, `region_code`, `region_label`, `phone`, `email`, `occupation`, `employer_name`,
  `monthly_income`, `other_income`, `monthly_expense`, `spouse_name`, `spouse_nik`, `spouse_income`.
- **Permohonan**: `requested_amount`, `requested_tenor`, `method_id`, `installment_id`,
  `interest_rate`, `provision_rate`, `admin_rate`, `collateral_note`.
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
