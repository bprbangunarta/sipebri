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

## Keputusan desain yang DISETUJUI user (2026-06-19)
- Pengaturan komite kredit **punya menu tersendiri** ("Komite Kredit", href `/committees`, ada di grup Referensi
  — menu dibuat user), BUKAN di dalam detail produk. Alasan: master produk adalah cermin CBS,
  aturan komite banyak baris, perlu tampilan lintas produk, ada aturan lintas produk (RELOAN), perlu snapshot versi.
- Struktur induk–anak disetujui: Jalur (produk + kondisi/kategori + mekanisme) → Jenjang (peranan pemutus,
  batas plafon, keputusan diizinkan). Fitur "Salin Jenjang Dari" jalur lain.
- Rencana lanjutan yang juga disetujui (BELUM dibuat): modul **Parameter Produk** terpisah
  (plafon min/maks, jangka waktu, bunga/provisi/admin default, metode & pola cicilan default, ambang RC)
  supaya master produk CBS tetap murni.
- User akan mereview hasilnya bersama bagian terkait di perusahaan → siap menerima koreksi aturan.

## Sudah dibangun (2026-06-19)
Modul Komite Kredit: migrasi `committee_paths`/`committee_tiers`, model, CommitteeController,
StorePathRequest/StoreTierRequest, halaman `Committees.vue` + `CommitteeDetail.vue`,
izin `committees.view/manage`, `CommitteeSeeder` (17 jalur / 111 jenjang sesuai dokumen kebijakan),
serta ProductSeeder/InstallmentSeeder/MethodSeeder (data CBS yang diisi user).

## Masih menunggu jawaban user (tanyakan bertahap, maks 2-3 sekali)
A.2 satu pemutus per level?; A.4 pengaruh kantor; A.5 delegasi;
B.6 batas Rp1.000; B.9 wewenang ikut turun bila plafon diturunkan?; B.10 boleh naikkan plafon?; B.11 asal max_plafond;
C.12 beda Dibatalkan vs Ditolak; C.13 jalur hierarki tanpa opsi tolak di level bawah (kebijakan atau bug?);
C.14 boleh kembalikan ke analisa/survey; C.15 boleh lompat level; C.16 nilai final versi siapa; C.17 setuju bersyarat;
D.18-D.22 RC & rumus (ambang RC, EFEKTIF tanpa pokok, pembulatan);
E.24 master kondisi/kategori; E.25 definisi RELOAN; E.27 atribut Parameter Produk; E.28 cetak berita acara komite.
