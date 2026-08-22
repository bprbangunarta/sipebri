# ROADMAP — SIPEBRI

Status per 22 Juni 2026. Riwayat pekerjaan: `CHANGELOG.md`. Ruang lingkup & arsitektur: `PRD.md`.

## P0 — perlu konfirmasi / uji user
- **Uji lokal seluruh lembar analisa** (bagian 1–8) lalu bandingkan dengan sistem lama.
- **Rumus evaluasi 5C**: saat ini memakai persentase skor (≥80 BAIK · ≥60 CUKUP BAIK · sisanya KURANG BAIK)
  karena `analisa5c.js` sistem lama tidak tersedia — perlu dicocokkan dengan hasil sistem lama.
- **Proses APHT & Biaya Fiducia** (Administrasi) dan **Max Plafond** (Memorandum): di sistem lama read-only
  tanpa rumus yang diketahui; sementara diisi manual / memakai taksasi agunan.
- **Uji banding pertanian PERPADIAN**: berkas contoh `00700007` (KBT · PERPADIAN · MUSIMAN · 45 jt / 12 bln).

## P1 — melengkapi alur
- **Ringkasan kelayakan**: kartu kemampuan angsuran & RC di atas lembar analisa.
- **Berita Acara Pemeriksaan (BA) agunan**: cetak/PDF dari data Analisa Agunan.

## P2 — modul & penyempurnaan
- **Persetujuan Komite Kredit**: keputusan per jenjang mengikuti jalur komite & parameter produk.
- **Simulasi angsuran**: kalkulator angsuran & RC di simulator komite.
- **Peta survei di berkas pengajuan**: tampilkan `SurveyMap.vue` pada detail berkas untuk komite.
- **Validasi keras jangka waktu**: peringatan kelipatan sistem cicilan dinaikkan menjadi penolakan simpan
  setelah simulasi dianggap final (keputusan user).
- **Cetak lembar analisa** (PDF) untuk rapat komite.
- **Halaman penuh notifikasi** dengan pencarian.

## Catatan teknis yang masih terbuka
- Kolom `analysis_businesses.take_portion` tidak lagi dipakai ("Ambil 70%" dihapus karena mati di sistem
  lama); kolomnya dibiarkan agar migrasi tidak berubah — hapus bila sudah pasti tidak dibutuhkan.
- Untuk usaha PERTANIAN pada produk bercicilan **BULANAN**, periode setoran = 1 bulan sehingga hasil bersih
  satu siklus panen dihitung sebagai pendapatan satu bulan (keputusan user). Perlu ditinjau bila ternyata
  ada produk pertanian non-musiman.
