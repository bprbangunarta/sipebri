# SIPEBRI — Catatan Diskusi (belum ada kode)

Proyek AdminKit akan dipakai sebagai basis **SIPEBRI** (sistem pemberian kredit BPR Bangunarta).
STATUS: TAHAP DISKUSI. Jangan menulis kode untuk alur kredit sampai user menyatakan mulai.
Menu Referensi (Data Instansi, Data Produk, Sistem Cicilan, Sistem Bunga) dibuat untuk dipakai
NANTI — user melarang membuat asumsi relasi data lebih dulu (mis. `users.office` → institutions).

## Alur yang diharapkan (9 tahap)
pengajuan → penjadwalan (survey) → survey (bisa dijadwalkan ulang, tiap jadwal tercatat)
→ analisa (boleh ubah/tambah jaminan) → persetujuan (komite kredit per produk)
→ notifikasi (surat persetujuan / surat penolakan; alasan penolakan ada 2: internal & untuk nasabah)
→ akad (cetak PK) → pencairan (unggah dokumentasi akad) → posting kredit & jaminan ke core banking

## Komite kredit (dari 3 dokumen + persetujuan_komite.js sistem lama)
Dua mekanisme:
1. **Berbasis plafon** — 12 produk (KRU, KRM, PRK, KTO, KPS, KIH, KPJ, KRS, KPN, KIU, KTA, KPM)
   + KBT bila `kondisi_khusus = PERPADIAN`.
   Jalur umum: ≤10 jt diteruskan ke Kasi; >10–35 jt Kasi Analis; >35–100 jt Kabag Analis;
   >100–300 jt Direktur Bisnis; >300 jt Direksi (Komite III).
   KBT PERPADIAN: ≤35 jt Kasi; >35–100 jt Kabag; >100–300 jt Dir. Bisnis; >300 jt Direksi.
   Dalam limit → Disetujui/Dibatalkan/Ditolak. Di atas limit → Naik Komite.
2. **Berbasis hierarki (tanpa limit)** — KUP, KKO, KBT-PERLELEAN, dan `kategori = RELOAN` (override):
   Staff Analis → Naik Kasi → Kasi (Naik Komite I) → Kabag (Naik Komite II) →
   Direktur Bisnis (Naik Komite III) → Direksi memutus. Level bawah TIDAK punya opsi tolak/batal.

Field yang diubah komite di sistem lama: usulan_plafon (dibatasi max_plafond, hanya bisa turun),
suku_bunga, b_provisi, b_admin, metode_rps. RC = angsuran ÷ keuangan_perbulan × 100.
Rumus angsuran: FLAT (bunga+pokok), EFEKTIF/EFEKTIF MUSIMAN (bunga 30/365 saja),
EFEKTIF ANUITAS (anuitas), KBT PERPADIAN (bunga saja), KBT PERLELEAN (bunga+pokok).

## Kejanggalan sistem lama yang sudah saya catat
- Semua aturan hanya di klien (tanpa penegakan server) → celah wewenang.
- `hasil.produk_kode = "KBT"` di-hardcode pada handler keyup → RC bisa salah rumus.
- Copy-paste bug pada cabang Customer Service / Kepala Kantor Kas.
- Celah rentang angka (10.000.000 vs 10.000.001; 35.000.000 vs 35.000.001).
- Direksi hanya dapat opsi bila plafon > 300 jt → dropdown kosong bila plafon lebih kecil.
- Tidak ada ambang RC.

## Jawaban user (final, jangan ditanya ulang)
- A.1 "Direksi" adalah nama peranan di sistem LAMA. Di sistem baru peranannya **Direktur Utama**.
  Kewenangan berbasis **peranan**, bukan user tertentu — siapa pun pemegang peranan itu boleh memutus.

## Pertanyaan yang masih menunggu jawaban
A.2–A.5, B.6–B.11, C.12–C.17, D.18–D.22, E.23–E.28 (lihat riwayat percakapan).
User menilai pertanyaannya terlalu banyak sekaligus → tanyakan bertahap, sedikit-sedikit,
setelah user selesai mengisi data master di menu baru.
