# Form Agunan di Core Banking (CBS) + alur kerja SIPEBRI

**PRINSIP UTAMA (jangan dilanggar):**
SIPEBRI **TIDAK MENGHITUNG APA PUN** pada data agunan. Semua nilai hanya **direkam apa adanya**
lalu dikirim ke CBS via API. Seluruh perhitungan (PPKA/PPAP, nilai yang dapat diperhitungkan,
penyusutan, dsb) adalah urusan CBS. Jangan menambahkan rumus, auto-kalkulasi, atau validasi
turunan angka pada modul agunan kecuali user meminta.

## Alur proses yang disepakati user (20/08/2026)
1. **Tahap pendaftaran/pengajuan**: agunan diinput **tanpa** nilai apa pun
   (tanpa taksasi, nilai jaminan/HT, nilai wajar, NJOP, apraisal, tanggal penilaian, nama penilai).
   Yang diisi hanya identitas agunan: jenis agunan, pengikatan, bukti kepemilikan/no. dokumen,
   keterangan, pemilik, alamat, lokasi, kondisi, asuransi.
2. **Tahap analisa**: setelah **survey lapangan**, **Staff Analis** yang mengisi seluruh nilai agunan.
3. Pada tahap analisa, Staff Analis punya **akses CRUD penuh** atas semua agunan yang terhubung
   dengan pengajuan tersebut (tambah, ubah, hapus) karena merekalah yang paling tahu kondisi lapangan.

Implikasi desain: field nilai agunan harus terpisah (opsional saat pendaftaran, wajib/terbuka saat
analisa), dan hak akses agunan mengikuti tahap berkas + peranan.

## Kontrak API CBS — POST agunan (contoh dari user, 20/08/2026)

```json
{
  "no_rek": "string",
  "kepemilikan": "string",
  "keterangan": "string",
  "pemilik_nama": "string",
  "pemilik_alamat": "string",
  "lokasi": "stri",
  "asuransi": "T",
  "startdate": "2026-08-20",
  "peringkat_sb": "",
  "pemeringkat_sb": "",
  "jenis": "string",
  "jenis_pengikat": "06",
  "nilai": { "njop": 0, "jaminan": 0, "adjust": 0, "wajar": 0, "taksasi": 0, "independen": 0 },
  "penaksir": {
    "taksasi": { "penaksir": "string", "tanggal": "2026-08-20" },
    "independen": { "penaksir": "string", "tanggal": "2026-08-20" }
  },
  "ppap": 0
}
```

Pemetaan field form → payload:
- `no_rek` = nomor rekening/berkas kredit tempat agunan dilekatkan
- `kepemilikan` = Status/Bukti Kepemilikan (atau nomor dokumen seperti No. SHM)
- `keterangan` = Keterangan Agunan
- `pemilik_nama`, `pemilik_alamat` = Nama Pemilik & Alamat Agunan
- `lokasi` = kode wilayah dati2 (tampak **4 karakter**, mis. `0121`)
- `asuransi` = `"T"` (tidak) / `"Y"` (ya) — bukan boolean
- `startdate` = Tgl Mulai asuransi (format `YYYY-MM-DD`)
- `peringkat_sb`, `pemeringkat_sb` = peringkat & lembaga pemeringkat surat berharga (string, boleh kosong)
- `jenis` = kode Jenis Agunan, `jenis_pengikat` = kode Jenis Pengikatan (string berkode, mis. `"06"`)
- `nilai.*` = semua angka nilai (njop, jaminan/HT, adjust, wajar, taksasi, independen) — **integer rupiah**
- `penaksir.taksasi|independen` = nama penilai + tanggal penilaian
- `ppap` = kode Metode Hitung PPAP (integer: 0 DEFAULT, 1 NETTO, 2 GROSS, 3 FULL)

Catatan penting:
- Payload **tidak memuat** Kondisi Agunan, Tgl Kondisi, Paripasu, maupun Agunan ID — kemungkinan
  dikirim lewat endpoint/parameter lain atau dikelola CBS. Perlu konfirmasi sebelum dipakai.
- Semua nilai berangka default `0` → saat tahap pendaftaran (belum survey) cukup kirim `0`/kosong,
  lalu diperbarui setelah analis mengisi hasil taksasi.
- Tetap: SIPEBRI hanya meneruskan data, tidak menghitung.

## Susunan field (urut sesuai form CBS)

| # | Field | Tipe | Tahap | Catatan |
|---|---|---|---|---|
| 1 | Agunan ID (cari u/ paripasu) | teks | daftar | contoh `0141990`, `01.3.001419`; ada tombol pencari (`...`) & **Periksa** |
| 1b | Paripasu (%) + tombol Relasi | angka | daftar | default 0; menghubungkan agunan ke berkas lain |
| 2 | Nomor Berkas | teks + checkbox **Auto** | daftar | dikosongkan bila nomor dibuat otomatis |
| 3 | Jenis Agunan | kode + nama | daftar | referensi `collateral_types` (`14 : LAINNYA : SK/IJAZAH`, `05 : TANAH/BNGN-SERTIFIKAT DGN HT`) |
| 4 | Peringkat Surat Berharga + Pemeringkat | kode + pilihan | daftar | hanya untuk agunan surat berharga; referensi belum ada |
| 5 | Jenis Pengikatan | kode + nama | daftar | referensi `binding_types` (`01 : APHT : HAK TANGGUNGAN`); boleh kosong |
| 6 | Status/Bukti Kepemilikan **atau** No. dokumen (mis. No. SHM) | pilihan + teks | daftar | label berubah menurut jenis agunan |
| 7 | Keterangan Agunan (+ tombol Detail) | teks panjang | daftar | mis. "SERTIFIKA TANAH DAN BANGUNAN NO 1774 LUAS 72 M2" |
| 8 | Nama Pemilik + checkbox "Nama dan alamat sesuai CIF" | teks | daftar | bila dicentang, ambil dari data nasabah |
| 9 | Alamat Agunan | teks | daftar | |
| 10 | Lokasi Agunan | kode + nama | daftar | referensi wilayah dati2 (`0121 : SUBANG, KAB.`) — **belum ada di SIPEBRI** |
| 11 | Nilai Jaminan / **Nilai Hak Tanggungan** + Adjusment | rupiah | **analisa** | label ikut pengikatan; Adjusment "u/ perhitungan ppka" (CBS yang hitung) |
| 12 | Nilai Wajar/Pasar + NJOP | rupiah | **analisa** | |
| 13 | Nilai Taksasi (Internal) + Tgl Penilaian + Nama Penilai | rupiah + tanggal + teks | **analisa** | diisi analis setelah survey |
| 14 | Nilai Apraisal Independen + Tgl Penilaian + Nama Penilai | rupiah + tanggal + teks | **analisa** | boleh kosong |
| 15 | Kondisi Agunan + Tgl Kondisi | pilihan + tanggal | daftar | CBS memakai `9 : TIDAK ADA MASALAH` sebagai kondisi normal |
| 16 | Diasuransikan (YA/TDK) + Metode Hitung PPAP + Tgl Mulai | pilihan + pilihan + tanggal | daftar | Metode Hitung = referensi `collateral_methods` (`1 : NETTO`) |
| — | Register / Update / DelDate | jejak audit | — | timestamp + user; DelDate `2099-12-31` = belum dihapus |

## Temuan yang perlu ditindaklanjuti
1. Referensi **Kondisi Agunan** perlu tambahan `9 : TIDAK ADA MASALAH` (nilai default CBS; kita baru punya 1–6).
2. Referensi baru yang belum ada: **Lokasi/Wilayah (dati2)**, **Status/Bukti Kepemilikan**,
   **Peringkat Surat Berharga & Lembaga Pemeringkat**.
3. Field bersyarat menurut jenis agunan (No. SHM, peringkat surat berharga) — tetap dikirim apa adanya ke CBS.
4. Nilai uang disimpan bulat, apa adanya, tanpa turunan hitungan.

## Nama kolom `collateral_simulations` setelah tinjauan skema (21/06/2026)

Kolom yang **dihapus** karena tidak dipakai form/payload: `paripasu`, `file_number`,
`auto_number`, `ownership`, `owner_same_as_cif`, `region_id`.
Akibatnya key payload `no_rek` dan `kepemilikan` **selalu string kosong** — bila CBS benar-benar
membutuhkannya, kolom tersebut harus dikembalikan.

Kolom yang **diganti nama** (mengikuti rancangan user di modul Skema Migrasi):

| Lama | Baru |
|---|---|
| `insured` | `insurance_code` (`Y`/`T`, default `T`) |
| `insurance_start_date` | `insurance_date` |
| `value_guarantee` | `guarantee_value` |
| `value_fair` | `fair_value` |
| `value_njop` | `njop_value` |
| `value_adjustment` | `adjustment_value` |
| `value_appraisal` | `appraisal_value` |
| `value_independent` | `independent_value` |
| `independent_appraiser_name` | `independent_name` |
| `independent_appraised_at` | `independent_at` |

Perubahan lain: `collateral_id` kini **unique** dan diisi otomatis `AGN-000001` bila dikosongkan;
`ppap_code` default `1`. Migration: `2026_08_21_000000_drop_unused_columns_...` dan
`2026_08_21_020000_rename_collateral_simulation_columns.php`. Struktur payload CBS **tidak berubah**.

Belum ada di form (catatan hasil uji iterasi 39):
- Input **Agunan ID** tidak ditampilkan di form, jadi aturan unique hanya berlaku dari sisi API.
- Input **Penaksir** (`appraiser_name`) & **Penaksir Independen** (`independent_name`) belum ada di
  form, sehingga `penaksir.*.penaksir` pada payload selalu kosong kecuali diisi lewat seeder/API.

## Keputusan user 21/06/2026 (identitas & penaksir)
- `collateral_id` **dibiarkan kosong** saat input. Nilainya diisi/diperbarui **setelah posting ke CBS**
  dari respons API. Karena itu tidak ada input Agunan ID di form dan tidak ada penomoran otomatis
  dari SIPEBRI (auto `AGN-xxxxxx` sudah dihapus).
- Kolom baru **`credit_account`** (string 30, nullable, unique) = nomor rekening kredit.
  Alur: posting data kredit ke CBS → terima nomor rekening dari respons → update `credit_account`
  pada semua agunan yang dipakai di pengajuan tersebut. Di rancangan skema diletakkan paling atas,
  di atas `collateral_id`.
- `appraiser_name` & `appraised_at` **diisi sistem** saat penyimpanan pada tahap analisa
  (nama petugas yang login + tanggal hari ini). Input keduanya dihapus dari form; form hanya
  menampilkan keterangan "diisi sistem" + jejak terakhir.
- Kolom independen (`independent_value`, `independent_name`, `independent_at`) **tetap disimpan**
  tetapi TIDAK dipakai — praktik di lapangan tidak memakai penilai pihak ketiga. Inputnya
  dihilangkan dari form; payload CBS tetap mengirim `nilai.independen` = 0 dan `penaksir.independen` kosong.
