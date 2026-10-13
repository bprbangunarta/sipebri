# Scoring Analisa Kredit KPMI — LPK Amaterasu (LPK01)

Penjelasan rumus pada file
[konsep-scoring-analisa-kredit-pmi-amaterasu.xlsx](konsep-scoring-analisa-kredit-pmi-amaterasu.xlsx).
Analisa ini khusus produk KPMI resort LPK01 dan tidak berhubungan dengan analisa kredit existing.
Dokumen ini hanya membahas **rumus dan keputusan**. Siapa yang mengisi dan apa buktinya belum dibahas.

## Ringkasan

- Form diisi dengan **angka dan pilihan Ya/Tidak**, lalu menghasilkan satu keputusan otomatis.
- Keputusan ditentukan oleh **7 syarat wajib (critical gate)**. Jika satu saja tidak terpenuhi, hasilnya
  **TUNDA**, berapa pun skornya.
- Jika semua syarat wajib terpenuhi, skor 0–100 menentukan: **Layak**, **Dipertimbangkan Komite**, atau
  **Tidak Direkomendasikan**.
- Kemampuan bayar **keluarga** adalah inti penilaian (bobot 35 dari 100). Skor kelayakan siswa dari Lendana
  (97,4) hanya ditampilkan dan tidak ikut dihitung.
- **Rumus sudah dibuktikan sama dengan Excel** (lihat bagian Pembuktian). Satu hal harus diperhatikan saat
  dibuat menjadi program: skor dibulatkan dulu sebelum dibandingkan dengan 80 dan 70.

## 1. Cara kerja dalam tiga langkah

1. **Isi data**: 4 angka keuangan keluarga per bulan, 7 pilihan Ya/Tidak, dan 14 nilai penilaian (0–5).
2. **Hitung skor**: setiap kriteria diberi nilai 0–5, dikalikan bobotnya, lalu dijumlahkan.
3. **Putuskan**: periksa 7 syarat wajib dulu, baru lihat skor.

## 2. Data yang diisi

**Angka keuangan keluarga (rupiah per bulan)**

Analis mengisi tiga angka pertama. Angsuran kredit ini bukan isian.

| Isian | Catatan |
|---|---|
| Penghasilan bersih | Rata-rata 6–12 bulan. Jangan memasukkan proyeksi gaji Jepang |
| Biaya hidup | Termasuk tanggungan dan kebutuhan dasar |
| Angsuran kredit berjalan | Dicocokkan dengan SLIK dan deklarasi utang |
| Angsuran kredit ini | **Tidak diisi.** Dihitung otomatis dari plafon, bunga, dan jangka waktu pengajuan (lihat bagian 3) |

**Pilihan Ya/Tidak** — Ya bernilai 5, Tidak bernilai 0.

| Pertanyaan | Syarat wajib? |
|---|---|
| Pihak pembayar primer sudah ditetapkan | Ya |
| Bukti penghasilan sudah diverifikasi | Ya |
| Pengeluaran dan seluruh utang sudah diverifikasi | Ya |
| Debitur Bersama bersedia tanggung renteng | Ya |
| Orang tua/siswa memahami program dan dana talang | Ya |
| Tahapan pencairan dan refund sudah disepakati | Tidak |
| Pencairan bertahap sudah disetujui | Tidak |

**Penilaian 0–5** (0 = data belum ada): 14 kriteria seperti identitas dan alamat, SLIK, interview, motivasi,
disiplin, kesehatan, rekam jejak LPK, agunan/asuransi. Daftar lengkap ada di bagian 5.

## 3. Dua perhitungan keuangan

**a. DSR** — porsi penghasilan yang habis untuk angsuran.

```
DSR = (angsuran berjalan + angsuran kredit ini) / penghasilan
```

| DSR | Nilai |
|---|---|
| sampai 35% | 5 |
| di atas 35% sampai 40% | 4 |
| di atas 40% sampai 45% | 3 |
| di atas 45% sampai 50% | 2 |
| di atas 50% | 1 |

**Angsuran kredit ini** dihitung dari data pengajuan, bukan diketik analis. Metodenya efektif anuitas:

```
angsuran = plafon × r / (1 − (1 + r) ^ −n)
r = bunga per tahun / 12,   n = jangka waktu (bulan)
```

Contoh pengajuan 00361336: plafon Rp30.000.000, bunga 32%, 18 bulan → **Rp2.120.255 per bulan**.

**b. Stress test** — apakah keluarga masih mampu membayar jika keadaan memburuk
(penghasilan turun 30%, biaya hidup naik 10%).

```
sisa uang = penghasilan × 70%  −  biaya hidup × 110%  −  seluruh angsuran
```

| Kondisi sisa uang | Nilai |
|---|---|
| Nol atau negatif | 1 |
| Positif, kurang dari 10% penghasilan stress | 3 |
| 10% sampai kurang dari 20% | 4 |
| 20% atau lebih | 5 |

Batas bersifat inklusif: DSR tepat 35% tetap bernilai 5, sisa uang tepat 20% bernilai 5.
Isian penghasilan atau biaya hidup yang kosong membuat nilainya 0.

## 4. Aturan keputusan

**Langkah 1 — syarat wajib.** Ada 7 kriteria wajib: pembayar primer, bukti penghasilan, utang terverifikasi,
DSR, stress test, pemahaman program, dan Debitur Bersama. Masing-masing harus bernilai **minimal 3**.

**Langkah 2 — keputusan.**

| Kondisi | Keputusan |
|---|---|
| Ada syarat wajib yang belum terpenuhi | **TUNDA** |
| Semua lolos, skor 80 ke atas | **LAYAK DIREKOMENDASIKAN** |
| Semua lolos, skor 70 sampai 79,9 | **DIPERTIMBANGKAN KOMITE** |
| Semua lolos, skor di bawah 70 | **TIDAK DIREKOMENDASIKAN** |

Di halaman analisa, jumlah syarat wajib ditampilkan sebagai **terpenuhi** dari 7 (misalnya 6 / 7), disertai
daftar syarat yang belum terpenuhi.

Akibat praktisnya:

- Jawaban **Tidak** pada salah satu dari 5 pertanyaan wajib langsung membuat TUNDA.
- **DSR di atas 45%** membuat TUNDA.
- **Sisa uang stress test nol atau negatif** membuat TUNDA.

## 5. Semua kriteria dan bobotnya

Skor tiap kriteria = bobot × nilai ÷ 5. Skor total = jumlah semuanya, maksimum 100.
"Wajib" artinya termasuk 7 syarat wajib.

| Kelompok (total bobot) | Kriteria | Bobot | Sumber nilai | Wajib |
|---|---|---|---|---|
| **Character & SLIK (15)** | Konsistensi identitas dan alamat | 3 | Diisi | |
| | Riwayat pembayaran/SLIK siswa | 5 | Diisi | |
| | Keterbukaan informasi dan pemahaman awal | 4 | Diisi | |
| | Dukungan keluarga yang dikonfirmasi | 3 | Diisi | |
| **Kesiapan siswa (20)** | Hasil interview | 4 | Diisi | |
| | Motivasi dan komitmen | 4 | Diisi | |
| | Kemampuan kognitif dan belajar | 4 | Diisi | |
| | Disiplin, ketekunan, daya tahan | 5 | Diisi | |
| | Kesehatan untuk bekerja | 3 | Diisi | |
| **Kemampuan bayar keluarga (35)** | Pembayar primer ditetapkan | 5 | Ya/Tidak | ✔ |
| | Penghasilan terverifikasi | 8 | Ya/Tidak | ✔ |
| | Pengeluaran dan utang terverifikasi | 6 | Ya/Tidak | ✔ |
| | DSR setelah kredit | 8 | Dihitung (bagian 3a) | ✔ |
| | Sisa uang saat stress test | 8 | Dihitung (bagian 3b) | ✔ |
| **Program & LPK (15)** | Rekam jejak LPK | 5 | Diisi | |
| | Pemahaman program dan dana talang | 4 | Ya/Tidak | ✔ |
| | Milestone pencairan dan refund | 3 | Ya/Tidak | |
| | Kejelasan penempatan/refinancing | 3 | Diisi | |
| **Struktur & mitigasi (15)** | Debitur Bersama/tanggung renteng | 5 | Ya/Tidak | ✔ |
| | Agunan, asuransi, dana darurat | 3 | Diisi | |
| | Pencairan bertahap | 3 | Ya/Tidak | |
| | Monitoring kemajuan siswa | 2 | Diisi | |
| | Kelengkapan dokumen kredit | 2 | Diisi | |

## 6. Contoh: data Paridudin pada file

Contoh pada file Excel: penghasilan Rp9.000.000, biaya hidup Rp1.000.000, angsuran kosong, semua Ya/Tidak diisi "Ya".

| Perhitungan | Hasil |
|---|---|
| DSR: 0 ÷ 9.000.000 | 0% → nilai 5 |
| Sisa uang stress: 6.300.000 − 1.100.000 | Rp5.200.000 (82,5%) → nilai 5 |
| Skor Character & SLIK | 9,0 |
| Skor Kesiapan siswa | 13,2 |
| Skor Kemampuan bayar keluarga | 35,0 |
| Skor Program & LPK | 13,2 |
| Skor Struktur & mitigasi | 9,6 |
| **Skor total** | **80,0** |
| Syarat wajib terpenuhi | 7 dari 7 |
| **Keputusan** | **LAYAK DIREKOMENDASIKAN** |

Hasil ini muncul karena semua isian wajib sudah "Ya" dan angsuran pada file Excel dibiarkan kosong (dihitung 0). Teks kesimpulan di sheet
Ringkasan menyebut keputusan masih tertahan, karena data nyata Paridudin memang belum terbukti. Sheet RC Keluarga
pada file masih berisi data contoh.

**Dengan angsuran otomatis** (penghasilan dan biaya hidup sama, angsuran kredit ini Rp2.120.255):
DSR 23,56% (nilai 5), sisa uang saat stress test Rp3.079.745 (nilai 5). Skornya tetap **80,0** dan keputusannya
tetap **LAYAK**, karena kedua nilai itu masih 5. Jika angsuran naik menjadi Rp6.000.000 (DSR 66,67%, stress test
negatif), hasilnya **TUNDA** dengan skor 67,2.

Untuk melihat perilaku lain, ini beberapa skenario dengan isian lain sama seperti contoh:

| Skenario | Skor | Syarat wajib terpenuhi | Keputusan |
|---|---|---|---|
| Semua syarat belum terpenuhi, angka keuangan kosong | 30,0 | 0 dari 7 | TUNDA |
| DSR 40% | 78,4 | 7 dari 7 | Dipertimbangkan Komite |
| DSR 45% | 76,8 | 7 dari 7 | Dipertimbangkan Komite |
| DSR 50% | 73,6 | 6 dari 7 | TUNDA (skor 73,6 tidak menolong) |
| Sisa uang stress tepat 0 | 67,2 | 5 dari 7 | TUNDA |

## 7. Pembuktian bahwa rumus sama dengan Excel

**Hasilnya cocok.** Dua cara pengecekan:

1. **Dibandingkan dengan hasil hitung Excel yang tersimpan di file.** Untuk data contoh, skor total 80,0,
   syarat wajib terpenuhi 7 dari 7, dan keputusan LAYAK sama persis.
2. **Rumus asli di file dijalankan ulang pada 70 skenario** (10 skenario batas dan 60 skenario acak),
   lalu dibandingkan dengan aturan di dokumen ini. **68 cocok. Dua sisanya** adalah skenario yang skornya
   tepat 80,0, dan bedanya hanya soal pembulatan, dijelaskan di bawah.

### Hal yang harus diperhatikan: pembulatan skor

Komputer menyimpan skor contoh sebagai 79,99999999999999, bukan 80. Excel menganggapnya sama dengan 80
sehingga hasilnya LAYAK. Pembanding yang lebih ketat membacanya sebagai di bawah 80 dan menghasilkan
"Dipertimbangkan Komite".

**Saat dibuat menjadi program, bulatkan skor total (misalnya 2 desimal) sebelum dibandingkan dengan 80 dan 70.**
Dengan begitu skor yang tepat 80,0 tetap diputuskan LAYAK, sama seperti Excel.

### Catatan untuk angsuran otomatis

Pembuktian di atas memakai versi Excel, di mana angsuran adalah angka yang diketik. Aplikasi hanya mengganti
**sumber angka itu** (dihitung dari plafon, bunga, tenor), sedangkan aturan DSR, stress test, skor, dan keputusan
tidak berubah. Rumus angsuran anuitas di atas memakai pembulatan ke rupiah. Angsuran pada jadwal angsuran (RPS)
BPR bisa berbeda beberapa rupiah karena aturan pembulatannya.

### Batas pembuktian

- LibreOffice tidak terpasang di mesin ini, jadi Excel tidak bisa dijalankan langsung.
- Hasil hitung Excel yang tersimpan di file hanya untuk satu kombinasi isian (data contoh). Skenario lain
  diuji lewat mesin pembaca rumus pihak ketiga.

## 8. Perilaku yang mengikuti Excel apa adanya

Hal berikut sengaja tidak diubah pada tahap ini:

1. **Angsuran kosong dihitung 0 (khusus file Excel).** DSR menjadi 0% dan mendapat nilai 5. Pada aplikasi
   hal ini tidak terjadi, karena angsuran kredit ini selalu terisi otomatis dari pengajuan.
2. **Ya/Tidak dianggap benar tanpa pemeriksaan bukti.** Semua "Ya" memberi nilai 5.
3. **Stress test hanya satu skenario**: penghasilan turun 30% dan biaya hidup naik 10%.
4. **Skor Lendana tidak mempengaruhi keputusan.**
5. **Batas DSR 35/40/45/50%** adalah angka awal dalam file. Batas final mengikuti kebijakan internal BPR.

## 9. Penyimpanan hasil analisa

Hasil satu analisa disimpan di dua tabel. Satu pengajuan hanya punya satu analisa.

**Tabel analisa** (`data_analisas_kpmi_amaterasu`) — satu baris per pengajuan:

| Kelompok | Isi |
|---|---|
| Kunci | Kode pengajuan (unik) |
| Isian analis | Skor Lendana, penghasilan, biaya hidup, angsuran berjalan, catatan |
| Angsuran | Angsuran kredit ini, dicatat sesuai nilai saat analisa dibuat |
| Hasil hitung | DSR, sisa penghasilan, sisa penghasilan saat stress test, nilai DSR, nilai stress test |
| Keputusan | Skor total, jumlah syarat wajib terpenuhi dan jumlah syarat wajib, rekomendasi |
| Jejak | Pembuat, pengubah, waktu |

**Tabel detail** (`data_analisas_kpmi_detail_amaterasu`) — satu baris per kriteria, **23 baris** per analisa:
7 pilihan Ya/Tidak, 14 penilaian 0–5, serta DSR dan stress test.

| Kolom | Isi |
|---|---|
| Kriteria | Nama kriteria, misalnya `pembayar_primer` atau `dsr` |
| Jawaban | Ya atau Tidak, hanya untuk 7 pilihan Ya/Tidak |
| Nilai | 0–5 |
| Bobot | Bobot saat analisa dibuat |
| Skor | Bobot × nilai ÷ 5 |
| Wajib | Termasuk 7 syarat wajib atau tidak |

Bobot dan angsuran dicatat per analisa, sehingga jika bobot atau data pengajuan berubah di kemudian hari,
analisa yang sudah ada tidak ikut berubah. Skor dan rekomendasi dihitung ulang di server saat disimpan,
tidak dipercaya dari kiriman halaman.

## 10. Menyimpan dan membuka kembali analisa

**Syarat agar analisa bisa disimpan.** Semua isian harus lengkap, kecuali Skor Lendana dan catatan:

| Isian | Aturan |
|---|---|
| Penghasilan bersih | Wajib, minimal Rp1 |
| Biaya hidup | Wajib, boleh 0 |
| Angsuran kredit berjalan | Wajib, isi 0 jika tidak ada |
| 7 pilihan Ya/Tidak | Wajib dijawab |
| 14 penilaian | Wajib diberi nilai 0–5 |
| Skor Lendana | Opsional, 0–100 |
| Catatan analis | Opsional |

Berbeda dengan Excel yang membiarkan sel kosong dihitung 0, analisa yang belum lengkap **tidak bisa disimpan**.
Halaman tetap menghitung hasil sementara saat mengisi, tetapi yang tersimpan hanya analisa yang lengkap.
Jika ada yang kurang, halaman menampilkan daftar isian yang belum diisi dan isian lain tidak hilang.

**Perhitungan saat menyimpan dilakukan ulang di server**, dengan aturan yang sama dengan halaman. Angka hasil
yang tampil di halaman tidak dipercaya begitu saja. Angsuran kredit ini juga dihitung ulang dari data pengajuan.

**Hasil uji** (aturan server, sama dengan angka di dokumen):

| Skenario | Skor | Syarat wajib terpenuhi | Rekomendasi |
|---|---|---|---|
| Contoh Excel, angsuran otomatis Rp2.120.255 | 80,0 | 7 dari 7 | LAYAK |
| DSR tepat 35% | 80,0 | 7 dari 7 | LAYAK |
| DSR tepat 40% | 78,4 | 7 dari 7 | Dipertimbangkan Komite |
| DSR tepat 45% | 76,8 | 7 dari 7 | Dipertimbangkan Komite |
| DSR tepat 50% | 73,6 | 6 dari 7 | TUNDA |
| Sisa uang stress tepat 0 | 67,2 | 5 dari 7 | TUNDA |
| Pemahaman program dijawab Tidak | 76,0 | 6 dari 7 | TUNDA |

**Menyimpan ulang.** Satu pengajuan hanya punya satu analisa. Menyimpan lagi memperbarui analisa yang sama
(23 baris detail diganti), dan pengubahnya dicatat. Saat halaman dibuka lagi, semua isian terisi dari analisa
yang tersimpan, disertai waktu dan nama penyimpan terakhir.

## 11. Cetak analisa

Halaman analisa selalu menampilkan ikon cetak di pojok kanan atas kotak Hasil Analisa. Sebelum analisa tersimpan
ikon redup dan tidak bisa diklik (keterangan: simpan analisa dulu). Setelah tersimpan, ikon aktif dan membuka
halaman cetak di tab baru.
Yang dicetak adalah analisa yang **sudah tersimpan**, bukan isian yang masih berubah di layar. Jika analisa belum
disimpan, halaman kembali ke form dengan pesan agar menyimpan dulu.

Halaman cetak berformat A4 (sekitar dua halaman) dan dicetak lewat tombol "Cetak / Simpan PDF" di browser.
Isinya berurutan:

| Bagian | Isi |
|---|---|
| Judul | Analisa Kredit KPMI, produk, dan nama LPK |
| 1. Data pengajuan dan debitur | Kode pengajuan, debitur, plafon, bunga, jangka waktu, angsuran per bulan, pendamping, skor Lendana |
| 2. Kemampuan bayar keluarga | Penghasilan, biaya hidup, angsuran, DSR, sisa penghasilan, dan hitungan stress test |
| 3. Penilaian dan skor | 23 kriteria per kelompok, dengan keterangan, bobot, nilai, skor, dan status syarat wajib |
| 4. Hasil analisa | Skor, syarat wajib terpenuhi, rekomendasi, dan daftar syarat yang belum terpenuhi |
| 5. Catatan analis | Catatan yang diisi analis |
| Penutup | Nama pembuat analisa, tanggal, dan waktu cetak |

Semua angka diambil dari analisa yang tersimpan (bobot dan angsuran saat analisa dibuat), sehingga hasil cetak
tidak berubah walau data pengajuan berubah di kemudian hari. Kelompok dan urutan kriteria mengikuti file Excel konsep.

## 12. Catatan data uji

Analisa yang tersimpan untuk pengajuan **00361336 (ALIF NURAULIA)** adalah **data contoh** dari file Excel konsep,
dibuat untuk melihat tampilan halaman dan cetakan. Catatannya bertuliskan "DATA CONTOH" dan penyimpannya `CONTOH`.
Hasilnya (skor 80,0, LAYAK DIREKOMENDASIKAN) bukan hasil analisa sebenarnya. Menyimpan ulang dari halaman analisa
menimpa data contoh ini.
