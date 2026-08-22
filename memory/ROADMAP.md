# ROADMAP — SIPEBRI

Status per 22 Juni 2026. Riwayat pekerjaan: `CHANGELOG.md`. Ruang lingkup & arsitektur: `PRD.md`.

## P0 — menunggu spesifikasi user
- **Analisa Agunan (bagian 4)**: form agunan Kendaraan / Tanah / Lainnya. Menunggu form sistem lama.
- **Uji banding pertanian PERPADIAN**: berkas contoh `00700007` (KBT · PERPADIAN · MUSIMAN · 45 jt / 12 bln)
  dipakai membandingkan hasil dengan sistem lama.

## P1 — analisa lanjutan
- **Analisa 5C (bagian 5)**: Character, Capacity, Capital, Collateral, Condition.
- **Analisa Kualitatif (bagian 6)**: Karakter, Usaha, SWOT, Lainnya.
- **Memorandum (bagian 7)**: Kebutuhan & Usulan.
- **Administrasi (bagian 8)**.
- **Ringkasan kelayakan**: kartu kemampuan angsuran & RC di atas lembar analisa.
- **Ajukan ke komite**: perubahan status berkas dari ANALISA → KOMITE beserta notifikasi.

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
