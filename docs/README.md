# Dokumentasi Sistem Existing SIPEBRI

**SIPEBRI — Sistem Pemberian Kredit**

Paket ini **mengidentifikasi sistem existing (AS-IS)** per **16 September 2026**, versi **1.0**. Cakupannya hanya kondisi yang sudah ada: source, ekspor skema, dan keterangan pengguna. **Perubahan sistem dibahas terpisah** dan tidak dilanjutkan di sini.

## Kontrol dokumen

| Atribut | Nilai |
|---|---|
| ID | `SIPEBRI-ASIS-2026-09-16` |
| Versi aktif | **1.0** |
| Referensi source | Commit `b12bf32019fd0b23570065dcc7a9954bfbf004a1` |
| Metode | Inspeksi source/manifest/route/model/view/JS/migrasi/test; ekspor DDL MySQL dari pengguna; konfirmasi pengguna |
| Bukti database | Analisis skema MySQL existing pada `database/analisis-skema-mysql.md` |
| Status | **Identifikasi existing ditutup**; bukan audit runtime, bukan izin ubah aplikasi/DB |
| Perubahan pekerjaan ini | Dokumentasi saja |

## Dokumen aktif

1. [Analisis sistem existing](analisis-sistem-existing.md)
2. [Proses bisnis existing](proses-bisnis-existing.md)
3. [Temuan dan observasi existing](temuan-dan-risiko.md)
4. [Bukti, batas, dan keterlacakan](validasi-dan-traceability.md)
5. [Analisis skema MySQL](database/analisis-skema-mysql.md)


## Label kepastian

| Label | Arti |
|---|---|
| **[B]** | Keterangan pengguna, 16 September 2026 |
| **[S]** | Terlihat pada source atau ekspor DDL |
| **[V]** | Belum diverifikasi runtime/walkthrough/operasi |

## Ringkasan yang dikonfirmasi pengguna [B]

- Nama aplikasi: SIPEBRI, Sistem Pemberian Kredit.
- Alur utama: pendaftaran → pelengkapan data → otorisasi → penjadwalan/survei → analisa → komite → notifikasi/PK → realisasi.
- Ekspor skema dari database **produksi**; database proyek adalah **lokal hasil restore**.
- Tidak memakai trigger, stored procedure/function, atau event database.
- `on_current` adalah flag handoff **CBS lama** (setelah pengajuan masuk CBS lama dan rekening kredit dibuat). **Saat ini tidak digunakan bisnis.** Filter/kolom residual masih ada di source/DDL.
- Integrasi API dropping ke **CBS baru** tidak tersedia pada source yang diidentifikasi.

## Batas paket

- Tidak menjalankan aplikasi, migration, seeder, test, atau koneksi DB oleh asisten.
- Tidak mengubah aplikasi, database, atau dependensi.
- Tidak memuat data nasabah, secret, atau konfigurasi deployment.
- Restore lokal **bukan** database uji disposable.
- Header SQL memuat metadata server/schema internal—samarkan sebelum dibagikan.
- Penutupan identifikasi **bukan** berarti seluruh tahap RUP selesai atau sistem sudah diuji end-to-end.

## Riwayat

| Versi | Tanggal | Isi |
|---|---|---|
| **1.0** | **2026-09-16** | **Penutupan: paket aktif hanya identifikasi existing** |
