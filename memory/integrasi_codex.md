# Integrasi API Codex (sistem data nasabah)

Aktif sejak 22/06/2026 — sebelumnya `CustomerDirectory` memakai data MOCK.

## Endpoint
- Token: `POST {CODEX_BASE_URL}/oauth/token`
  body JSON `{client_id, client_secret, grant_type: "client_credentials"}`,
  header `Accept: application/json`, `Content-Type: application/json`.
  Respons: `token_type`, `expires_in` (±1 tahun), `access_token`.
- Nasabah: `GET {CODEX_BASE_URL}/api/customers/{nomor_ktp}`
  header `Accept: application/json`, `Authorization: Bearer {token}`.
  Respons: `{ success: true, data: { ...kolom nasabah... } }`. NIK tidak terdaftar → HTTP 404.

## Kode di repo
- `config/services.php` → blok `codex` (base_url, client_id, client_secret, timeout,
  connect_timeout, token_skew, sample_niks). Kredensial hanya di `.env`:
  `CODEX_BASE_URL`, `CODEX_CLIENT_ID`, `CODEX_CLIENT_SECRET`, `CODEX_SAMPLE_NIKS`.
- `app/Services/CodexClient.php` — token di-cache (`cache key codex:access-token`) selama
  `expires_in - token_skew`; 401 → hapus cache & ulangi SEKALI; 404 → `null`;
  galat lain → `RuntimeException`. Retry hanya untuk kegagalan koneksi.
- `app/Support/CustomerDirectory.php` — memetakan kolom Codex ke bentuk SIPEBRI
  (`nik`, `cif_number`, `full_name`, `birth_place`, `gender`, `marital_status`, `address`,
  `region_code`, `region_label`, `phone`, `email`, `employer_name`, `income`, `dependents`,
  `companion`, plus `raw` = payload asli).
- Pemakai: `LoanApplicationController::lookup()` (modal No. KTP), `store()`, dan `checklist()`.
  Bila Codex tidak dapat dihubungi: lookup → HTTP 503 + pesan ramah, store → error validasi
  pada kolom `nik` (berkas TIDAK dibuat).

## Catatan penting
- SIPEBRI tetap **tidak menyimpan** identitas pemohon; hanya `nik`, `full_name`, `cif_number`.
- `penghasilan` dari Codex disimpan mentah sebagai `income` (contoh 84.000.000 — kemungkinan
  setahun; **belum** diasumsikan bulanan, dipakai nanti di modul analisa).
- Kode pemetaan (`jenis_kelamin`, `marital_status`, `pekerjaan`, `pendidikan`) masih berupa kode
  Codex; baru `jenis_kelamin` & `marital_status` yang diterjemahkan.
- Uji: `tests/Feature/LoanApplicationFlowTest.php` memakai `Http::fake` + `preventStrayRequests`,
  dengan flag `$codexDown` untuk mensimulasikan API mati.
  Jangan memanggil `Http::fake()` dua kali untuk pola yang sama di satu tes — stub pertama menang.
- Verifikasi nyata 22/06/2026: NIK `3213070701980004` → ZULFADLI RIZAL, CIF `01.1.038586`,
  berkas `00700002` terbentuk lewat UI.
