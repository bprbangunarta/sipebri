# Bukti, Batas Pemeriksaan, dan Traceability Existing SIPEBRI

Identifikasi **EXISTING 1.0**, 16 September 2026. Dokumen ini mencatat **bukti dan batas** paket existing. Skenario uji, gate RUP, dan rencana penerimaan perubahan **tidak dibahas di sini**.

## 1. Bukti yang tersedia

| Bukti | Status |
|---|---|
| Nama SIPEBRI dan alur utama kredit | [B] dikonfirmasi pengguna |
| Source lokal | Commit `b12bf32019fd0b23570065dcc7a9954bfbf004a1` |
| Inventaris route/controller/model/view/JS | [S] inspeksi statis |
| Manifest/lockfile, migrasi, suite test scaffold | [S] dibaca; runtime/test tidak dijalankan |
| Ekspor MySQL | [B] dari produksi; 104 tabel/26 view; lihat [analisis skema](database/analisis-skema-mysql.md) |
| DB proyek | [B] lokal hasil restore (bukan disposable test DB) |
| Automation DB | [B] tanpa trigger/procedure/function/event |
| `on_current` | [B] flag CBS lama; **tidak digunakan bisnis saat ini**; [S] filter residual masih ada |
| API dropping CBS baru | Tidak tersedia pada source yang diidentifikasi |
| Hasil test/runtime/walkthrough UAT | Tidak dilakukan |

## 2. Inventaris suite test existing [S]

24 deklarasi test (scaffold auth/profil/contoh). Bukan coverage workflow kredit. Suite **tidak dijalankan** dalam paket ini.

## 3. Keterlacakan ringkas

| Area | Dokumen | ID terkait |
|---|---|---|
| Modul/arsitektur/data/integrasi | [analisis sistem](analisis-sistem-existing.md) | INT-01–INT-06 |
| Aktor, UC, BR, status | [proses bisnis](proses-bisnis-existing.md) | UC-01–15, BR-01–10 |
| Temuan source/DDL | [temuan](temuan-dan-risiko.md) | R-01–R-26 |
| Skema fisik | [analisis skema](database/analisis-skema-mysql.md) | — |

## 4. Decision log existing

| ID | Keputusan / keterangan | Dasar |
|---|---|---|
| D-01 | Nama aplikasi SIPEBRI | [B] |
| D-02 | Urutan alur kredit utama benar | [B] |
| D-03 | Dokumentasikan existing dulu | [B] |
| D-04 | Pendekatan RUP disebut pengguna sebelumnya | [B]; rincian rencana **di luar** paket aktif ini |
| D-05 | Arsitektur/framework target | Tidak diputuskan dalam paket existing |
| D-06 | Ekspor `schema-mysql.sql` diterima | [B]; dianalisis statis |
| D-07 | Ekspor dari produksi; DB lokal restore; tanpa automation DB | [B] |
| D-08 | `on_current` = handoff CBS lama; **tidak dipakai bisnis kini**; API CBS baru tidak ada di source | [B] |
| D-09 | **Penutupan paket existing 1.0** — hanya identifikasi AS-IS; perubahan dibahas terpisah | [B] arahan pengguna |

## 5. Batas

- Tidak ada eksekusi aplikasi/DB/test oleh asisten.
- Tidak ada perubahan aplikasi/database/dependensi.
- Restore lokal jangan dipakai sebagai target reset test.
- Paket ini **bukan** penutupan seluruh fase RUP dan **bukan** izin implementasi.
