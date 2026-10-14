# Scoring Analisa Kredit KPMI — LPK Amaterasu (LPK01)

Penjelasan rumus pada file
[konsep-scoring-analisa-kredit-pmi-amaterasu.xlsx](konsep-scoring-analisa-kredit-pmi-amaterasu.xlsx).
Analisa ini khusus produk KPMI resort LPK01 dan tidak berhubungan dengan analisa kredit existing.
Dokumen ini membahas **rumus dan keputusan**, serta fitur pendukung halaman analisa.

**Status konsep.** Parameter analisa kredit (kriteria, bobot, aturan hitung, syarat wajib, dan aturan keputusan)
sudah **diverifikasi manajemen** dan menjadi acuan tetap. Penambahan berikutnya hanya berupa **informasi
tambahan** (misalnya dokumen pendukung) yang **tidak mengubah** parameter, rumus, skor, syarat wajib, maupun
rekomendasi.

## Ringkasan

- Form diisi dengan **angka dan pilihan Ya/Tidak**, lalu menghasilkan satu keputusan otomatis.
- Keputusan ditentukan oleh **7 syarat wajib (critical gate)**. Jika satu saja tidak terpenuhi, hasilnya
  **TUNDA**, berapa pun skornya.
- Jika semua syarat wajib terpenuhi, skor 0–100 menentukan: **Layak**, **Dipertimbangkan Komite**, atau
  **Tidak Direkomendasikan**.
- Kemampuan bayar **keluarga** adalah inti penilaian (bobot 35 dari 100). Skor kelayakan siswa dari Lendana
  (97,4) hanya ditampilkan dan tidak ikut dihitung.
- **Dokumen pendukung** (tujuh dokumen dari LPK) hanya **informasi tambahan**. Dokumen itu bukan parameter
  analisa kredit dan tidak memengaruhi skor, syarat wajib, maupun rekomendasi.
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

## 12. Kirim ke Komite

Setelah analisa disimpan, tombol hijau **KIRIM KE KOMITE** di tab **5. Pengajuan Komite** (lihat bagian 16) mengirim
pengajuan ke Komite.
Untuk KPMI **tidak ada syarat analisa standar** (collateral, sandi lapbul, administrasi) seperti produk lain;
satu-satunya syarat adalah analisa KPMI sudah tersimpan. Persetujuan selanjutnya mengikuti proses Komite yang sudah ada.

| Kondisi | Perilaku |
|---|---|
| Analisa belum disimpan | Tombol redup dan tidak bisa diklik |
| Analisa tersimpan | Tombol aktif, ada dialog konfirmasi (SweetAlert) sebelum dikirim |
| Rekomendasi TUNDA | Tetap bisa dikirim, rekomendasi tampil di konfirmasi dan ikut ke Komite lewat analisa tersimpan |
| Sudah dikirim | Tombol berubah menjadi "Sudah dikirim ke Komite" dan tidak bisa diklik |

Saat dikirim, sistem melakukan hal yang sama dengan konfirmasi analisa produk lain:

1. Tahap pengajuan menjadi **Persetujuan Komite** dan statusnya menjadi **Sudah Otorisasi**.
2. Waktu analisa kredit pada data tracking diisi dengan waktu saat itu.
3. Halaman berpindah ke daftar Komite dengan pesan "Konfirmasi berhasil".

Yang dikirim adalah analisa yang **sudah tersimpan**, bukan isian yang belum disimpan.

**Dialog konfirmasi.** Klik tombol tidak langsung mengirim. Dialog "Kirim ke Komite?" tampil dengan isi:

- rekomendasi dan skor analisa yang tersimpan,
- peringatan agar memastikan analisa sudah sesuai dan benar, karena setelah dikirim pengajuan masuk ke tahap
  persetujuan Komite,
- penjelasan bahwa yang dikirim adalah analisa yang sudah disimpan.

Jika ada isian yang diubah tetapi belum disimpan, dialog menambah peringatan merah bahwa perubahan itu tidak ikut
terkirim dan sebaiknya SIMPAN dulu. Pengiriman hanya berjalan setelah menekan **Ya, kirim**; tombol **Batal**
menutup dialog tanpa mengirim.

## 13. Akses halaman analisa

Halaman analisa, simpan, cetak, dan kirim ke Komite semuanya memakai **kode pengajuan terenkripsi** dari URL
(tautan di daftar analisa), sehingga setiap pengajuan membuka analisanya sendiri.

| Kondisi | Hasil |
|---|---|
| Kode terenkripsi valid, resort LPK01 | Halaman analisa pengajuan itu |
| Kode rusak atau tidak bisa didekripsi | Ditolak (403) |
| Pengajuan tidak ada, atau bukan resort LPK01 | Tidak ditemukan (404) |

Modul ini khusus resort LPK01 (LPK Amaterasu), sehingga pengajuan resort lain tidak bisa dibuka lewatnya.

## 14. Tampilan pilihan saat diisi

Warna tombol berubah **langsung saat diklik**, sebelum disimpan:

| Bagian | Tombol dipilih | Warna |
|---|---|---|
| Syarat dan kesepakatan | Ya | Hijau |
| Syarat dan kesepakatan | Tidak | Merah |
| Penilaian | Nilai 0 sampai 5 | Biru |

Hanya satu tombol per baris yang berwarna; memilih yang lain mengembalikan tombol sebelumnya menjadi polos. Garis di
sisi kiri baris berubah dari oranye (belum diisi) menjadi hijau (sudah diisi), dan panel Hasil Analisa ikut dihitung
ulang saat itu juga.

## 15. Dokumen pendukung

> **Informasi tambahan, bukan parameter analisa kredit.** Parameter analisa sudah diverifikasi manajemen (lihat
> Status konsep di awal). Dokumen pendukung hanya melengkapi berkas pengajuan agar dapat dilihat bersama analisa.

Tahap **2. Analisa Dokumen** pada halaman analisa (lihat bagian 16) berisi kotak **Dokumen pendukung** untuk
mengunggah tujuh dokumen yang diserahkan LPK:

| No | Dokumen |
|---|---|
| 1 | KTP |
| 2 | Surat Rekomendasi Amaterasu |
| 3 | Hasil MCU |
| 4 | Dokumen parameter scoring Lendana |
| 5 | Hasil SLIK (CLIK) |
| 6 | Hasil tes psikologi |
| 7 | Scoring interview |

**Cara memakai.** Tabel punya tiga kolom: Dokumen, File, dan Aksi.

- **Pilih file**: file langsung diunggah dan disimpan saat dipilih, tanpa menunggu tombol SIMPAN. Selama proses
  tampil "Mengunggah...", lalu tampil nama file dan ukurannya.
- **Ganti**: memilih file lain untuk dokumen yang sama **menggantikan** file lama.
- **Lihat**: membuka dialog pratinjau dari file yang tersimpan di server. PDF ditampilkan dengan penampil PDF bawaan
  browser (bisa digulir, diperbesar, dan dicetak), gambar ditampilkan apa adanya.
- **Hapus** (tombol silang): ada dialog konfirmasi, dan file di server ikut **dihapus**.
- Dokumen yang sudah diunggah tetap tampil saat halaman dibuka kembali. Penanda di pojok kanan atas kotak
  menghitung dokumen terunggah (misalnya 2 / 7) dan menjadi hijau saat lengkap.
- Nama file yang terlalu panjang dipotong dengan "…" (nama lengkap tampil saat kursor diarahkan).

**Aturan file.** Format PDF, JPG, JPEG, dan PNG, maksimal 5 MB per file. Format dicek dari **isi file**, bukan hanya
dari nama. Jika tidak sesuai, muncul pesan merah di barisnya dan file tidak disimpan.

**Penyimpanan.**

- File disimpan di storage lokal server (tidak bisa diakses langsung dari browser; hanya lewat tombol Lihat setelah
  login) pada folder per pengajuan: `kpmi/amaterasu/{kode pengajuan}/`.
- Nama file berawalan kode pengajuan: `{kode pengajuan}_{jenis}.{ekstensi}`, misalnya `00361336_ktp.pdf` dan
  `00361336_hasil_mcu.pdf`.
- Satu pengajuan hanya punya **satu file per jenis dokumen**. Jika diunggah lagi dengan ekstensi berbeda (misalnya
  PDF diganti PNG), file lama dihapus sehingga tidak menumpuk. Folder pengajuan dihapus saat sudah kosong.
- Tabel `data_analisas_kpmi_dokumen` mencatat dokumen, satu baris per dokumen, terhubung ke **kode pengajuan**
  (bukan ke baris analisa, sehingga dokumen tetap ada walau analisa disimpan ulang):

| Kolom | Isi |
|---|---|
| Kode pengajuan | Pengajuan pemilik dokumen |
| Jenis | Salah satu dari tujuh dokumen, misalnya `ktp` atau `hasil_mcu` |
| Nama asli | Nama file saat diunggah, ditampilkan di halaman |
| Lokasi | Letak file di storage server |
| Tipe dan ukuran | Jenis file dan ukuran dalam byte |
| Pengunggah | Pengguna yang mengunggah, beserta waktunya |

**Keamanan.** Hanya pengajuan resort LPK01 yang bisa diakses. Jenis dokumen di luar tujuh yang terdaftar ditolak.
Dokumen dikirim dengan pengaturan cache pribadi (tidak disimpan cache bersama) dan tanpa penyimpanan di browser.

**Tidak memengaruhi analisa.** Dokumen, termasuk jumlah dokumen yang terunggah, tidak memengaruhi skor, syarat wajib,
rekomendasi, tombol Kirim ke Komite, maupun hasil cetak. Dokumen boleh belum lengkap saat analisa disimpan dan dikirim
ke Komite.

## 16. Tahapan pada halaman analisa

Analisa KPMI terdiri dari lima tab. Halaman analisa menampilkannya di bawah ringkasan data pengajuan dan debitur,
yang tetap tampil di atas pada semua tahap.

| Tab | Isi | Status |
|---|---|---|
| 1. Analisa Kredit | Penilaian kredit KPMI: kemampuan bayar keluarga, syarat, penilaian, hasil analisa, dan simpan | Sudah tersambung |
| 2. Analisa Dokumen | Unggah dan pratinjau tujuh dokumen pendukung | Sudah tersambung |
| 3. Memorandum | Kebutuhan dana, usulan kredit, dan sandi lapbul | Sudah tersambung (lihat bagian 17) |
| 4. Biaya Administrasi | Biaya kredit | Sudah tersambung (lihat bagian 17) |
| 5. Pengajuan Komite | Resume status tahap 1 sampai 4 dan tombol Kirim ke Komite | Sudah tersambung (lihat bagian 18) |

Tahap 3 dan 4 dibutuhkan karena persetujuan Komite sampai pencairan memakai mekanisme lama, yang memerlukan
kolom-kolom tertentu agar hasil cetak sesuai kebutuhan. Keduanya mengadopsi bagian yang sudah ada pada alur lama.

Cara kerja tab:

- Tab 1 menampilkan tanda centang hijau bila analisa sudah tersimpan. Tab 2 menampilkan jumlah dokumen terunggah
  (misalnya 2/7), hijau bila lengkap. Tab 5 menampilkan tanda centang hijau bila pengajuan sudah dikirim ke Komite.
- Alamat halaman mengikuti tab yang dibuka (misalnya `#tab-dokumen`), sehingga tab tertentu bisa dibuka langsung
  dari tautan dan tetap sama saat halaman dimuat ulang.
- Langkah pengisian di dalam tab 1 diberi huruf **A, B, C** (kemampuan bayar keluarga, syarat dan kesepakatan,
  penilaian) agar tidak tertukar dengan nomor tab.
- Judul tab hanya berupa nomor dan nama (tanpa ikon hiasan). Pada layar sempit, deretan tab bisa digeser ke samping
  agar tetap satu baris.

Tombol **Simpan** ada di panel Hasil Analisa pada tab 1. Tombol **Kirim ke Komite** ada di tab 5, bukan lagi di tab 1.

## 17. Tahap 3 dan tahap 4 (tersambung)

Kedua tahap sudah tersambung ke penyimpanan dan menyimpan ke **tabel alur lama**, karena persetujuan Komite sampai
pencairan serta hasil cetak masih membacanya. Daftar kolom diatur di controller; pilihan sandi diambil dari database.

### Tab 3. Memorandum (tiga sub-tab, masing-masing punya tombol Simpan sendiri)

**Kebutuhan Dana** (tabel `a_kebutuhan_dana`, satu baris per pengajuan). Lima baris, masing-masing berisi nominal dan
keterangan: Modal Kerja, Investasi, Konsumtif, Pelunasan Kredit, dan Take Over. **Jumlah Kebutuhan Dana** menjumlahkan
kelima nominal; di layar dihitung langsung, dan saat disimpan dihitung ulang di server. Keterangan disimpan dengan huruf
kapital. Kode analisa berawalan `AUT`.

**Usulan Kredit** (tabel `a_memorandum`, satu baris per pengajuan, kode berawalan `AMO`).

| Kelompok | Kolom |
|---|---|
| Plafon dan jangka waktu | Kebutuhan Dana (dari sub-tab sebelumnya), Max Plafond, Usulan Plafond, Jangka Waktu (bulan), Repayment Capacity |
| Bunga dan biaya | S.Bunga, Biaya Admin, B.Provisi, B.Penalti (semua dalam %), Pengikatan Asuransi |
| Syarat | Sebelum Realisasi, Syarat Tambahan, Syarat Lainnya |

- Pengikatan Asuransi: 1 Tanpa Pengikatan, 2 APHT, 3 Fiducia, 4 APHT dan Fiducia (sama dengan sistem lama).
- Aturan isian: usulan plafond minimal Rp500.000, jangka waktu 1 sampai 99 bulan, persen 0 sampai 99,99.
- **Menyimpan usulan mengubah data pengajuan**, seperti pada alur lama: plafon, jangka waktu, bunga, dan persen biaya
  admin, provisi, dan penalti diganti dengan isian usulan. Plafon awal dicatat sekali pada `temp_plafon`.
- **Max Plafond dan Repayment Capacity** (baca-saja) dihitung dengan rumus metode efektif anuitas yang sama seperti
  sistem lama, tetapi "keuangan per bulan" diganti **kemampuan angsuran** dari analisa kredit (tab 1):

  ```
  kemampuan angsuran = penghasilan − biaya hidup − angsuran kredit berjalan
  faktor anuitas     = (1 − (1 + r) ^ −n) / r         r = bunga per tahun / 12,  n = jangka waktu (bulan)
  Max Plafond        = kemampuan angsuran × faktor anuitas
  Repayment Capacity = angsuran usulan / kemampuan angsuran × 100%,  angsuran usulan = usulan plafond / faktor anuitas
  ```

  Keduanya berubah langsung saat usulan plafond, jangka waktu, atau bunga diubah di layar; Max Plafond yang disimpan
  dihitung ulang di server dan masuk ke kolom max plafond memorandum. Jika bunga 0%, faktor anuitas sama dengan n.
  Contoh pengajuan dengan kemampuan angsuran Rp4.630.000, tenor 18 bulan, bunga 32%: Max Plafond Rp65.510.997; usulan
  plafond Rp50.000.000 menghasilkan Repayment Capacity 76,32%.
- Jika analisa kredit belum disimpan, atau kemampuan angsurannya nol atau negatif, kedua kolom kosong dan halaman
  menjelaskan alasannya. Repayment Capacity hanya ditampilkan; tabel memorandum lama tidak punya kolom untuknya.
- **Taksasi Agunan tidak ada** pada KPMI karena tidak memakai agunan.

**Sandi Lapbul** (kolom sandi pada baris `a_memorandum` yang sama). Sembilan pilihan: Sifat, Penggunaan Debitur,
Golongan Penjamin, Sektor Ekonomi, Sumber Dana Pelunasan, Jenis Usaha, Sektor Ekonomi (SLIK), Golongan Debitur, dan
Golongan Debitur (SLIK). Pilihan berbentuk "sandi - keterangan" dari sembilan tabel sandi pada sistem lama; pilihan yang
panjang (misalnya 476 sektor SLIK) bisa dicari bila template menyediakan select2. Kode yang dikirim dicek harus ada pada
tabel sandi.

> **Urutan simpan.** Sandi Lapbul hanya bisa disimpan setelah **Usulan Kredit** tersimpan, karena sandi disimpan pada
> baris memorandum yang dibuat oleh usulan. Jika belum, muncul pesan "Lengkapi usulan kredit terlebih dahulu".

**Dampak pada analisa kredit (tab 1).** Karena usulan mengubah plafon, tenor, dan bunga pengajuan, angsuran otomatis pada
tab 1 ikut berubah. Jika analisa sudah disimpan dengan angsuran yang berbeda, tab 1 menampilkan peringatan kuning
("Plafon, bunga, atau tenor pengajuan berubah sejak analisa disimpan"), dan resume tab 5 menandai tahap 1 sebagai
**Perlu disimpan ulang** sampai analisa disimpan lagi.

Setelah menyimpan, halaman kembali ke sub-tab yang sama (alamat `#mm-kebutuhan`, `#mm-usulan`, atau `#mm-sandi`).

### Tab 4. Biaya Administrasi (satu sub-tab: Biaya Kredit)

Enam belas kolom rupiah: Administrasi, Provisi, Pertanggungan Asr. Jiwa, Asr. Kendaraan Bermotor, Materai,
Asr. Jiwa Menurun 1, Transaksi Kredit, Proses SHM, Asr. Jiwa Menurun 2, Asr. Jiwa Menurun 3, Polis dan Materai,
Pajak STNK, Asr. Jiwa Tetap 1, Asr. Jiwa Tetap 2, Proses APHT (baca-saja), dan Biaya Fiducia (baca-saja).

**Nilai awal.** Sebelum pernah disimpan, **Administrasi** dan **Provisi** terisi dari plafon dikalikan persen pengajuan
(biaya admin dan provisi), dengan pola yang sama seperti sistem lama (plafon 40 juta: 2,5% menjadi 1.000.000 dan 1%
menjadi 400.000). Setelah disimpan, halaman menampilkan nilai yang tersimpan. Semua kolom kecuali dua yang baca-saja
bisa diubah.

**Penyimpanan.** Tombol **SIMPAN** menyimpan ke tabel administrasi pada **alur lama** (`a_administrasi`), karena
persetujuan Komite sampai pencairan masih membacanya. Aturannya mengikuti alur lama:

- Satu baris per pengajuan. Simpan pertama membuat baris baru dengan kode analisa `ADM` + lima angka; simpan
  berikutnya memperbarui baris yang sama.
- Kolom isian dikirim dengan pemisah ribuan (misalnya `1.750.000`); yang diambil angkanya saja, kosong dianggap 0,
  dan batas nilai adalah batas kolom integer database.
- **Proses APHT dan Biaya Fiducia selalu 0** karena KPMI tidak memakai agunan; nilai kiriman halaman untuk dua kolom
  itu diabaikan. Kolom `lainnya` pada tabel lama diisi 0 pada simpan pertama dan tidak diubah setelahnya.
- Persen **biaya admin** dan **provisi** pada data pengajuan disinkronkan dari nominal dibagi plafon (dua desimal),
  seperti perubahan biaya pada alur lama.
- Pengguna yang menyimpan dicatat dengan **kode pengguna 3 huruf** (`code_user`, misalnya `HAR`), bukan nama pengguna,
  karena kolom `input_user` pada tabel lama hanya 3 karakter. Nama pengguna lengkap akan terpotong diam-diam (database
  tidak dalam mode ketat), sehingga tidak dipakai di tabel ini. Tabel analisa KPMI buatan sendiri tetap memakai nama
  pengguna lengkap.
- Biaya fiducia pada tabel memorandum lama belum ikut disimpan, karena tahap 3 belum tersambung.

## 18. Tab Pengajuan Komite

Tab ini berisi **resume** keempat tahap sebelum pengajuan dikirim ke Komite, dan tombol **KIRIM KE KOMITE**.

| Tahap | Ringkasan yang tampil | Status |
|---|---|---|
| 1. Analisa Kredit | Skor, syarat wajib terpenuhi, rekomendasi, serta waktu dan penyimpan terakhir | Tersimpan (hijau), Belum disimpan, atau Perlu disimpan ulang (kuning) |
| 2. Analisa Dokumen | Jumlah dokumen terunggah, diperbarui langsung saat unggah atau hapus | Lengkap atau Belum lengkap; hanya informasi tambahan |
| 3. Memorandum | Kelengkapan tiga sub-tab (kebutuhan dana, usulan kredit, sandi lapbul) dan ringkasan usulan | Lengkap (hijau) atau Belum lengkap (kuning) |
| 4. Biaya Administrasi | Total biaya, administrasi, provisi, serta waktu dan penyimpan terakhir | Tersimpan (hijau) atau Belum disimpan (kuning) |

Setiap baris punya tautan **Buka tahap** untuk berpindah ke tab terkait.

Tombol Kirim ke Komite berperilaku sama seperti sebelumnya (bagian 12): redup bila analisa kredit belum disimpan,
berubah menjadi "Sudah dikirim ke Komite" bila sudah terkirim, dan memakai dialog konfirmasi yang menyebut rekomendasi
serta memperingatkan bila ada isian tab 1 yang belum disimpan. Tombol itu hanya bergantung pada tahap 1; tahap 2, 3,
dan 4 belum memengaruhi pengiriman. Aturan keterkaitan dengan tahap 3 dan 4 masih perlu ditentukan.

## 19. Susunan file tampilan

Halaman analisa dipecah menjadi beberapa partial agar mudah dicari dan diperbaiki. Semua ada di
`resources/views/kpmi/amaterasu/`. File `show.blade.php` hanya berisi kerangka halaman (judul tab dan pemanggilan
partial), sehingga perbaikan satu bagian cukup di satu file.

| File | Isi |
|---|---|
| `show.blade.php` | Kerangka: pesan, judul kelima tab, dan pemanggilan semua partial |
| `_style.blade.php` | Seluruh gaya halaman |
| `_data_pengajuan.blade.php` | Ringkasan debitur, pendamping, dan pengajuan di atas semua tab |
| `_kolom.blade.php` | Satu kolom isian (rupiah, persen, angka, teks, pilihan) yang dipakai tab 3 dan 4 |
| `tab/analisa.blade.php` | Tab 1: petunjuk dan form, memanggil lima file di bawah |
| `tab/analisa_a.blade.php` | Langkah A, kemampuan bayar keluarga |
| `tab/analisa_b.blade.php` | Langkah B, syarat dan kesepakatan |
| `tab/analisa_c.blade.php` | Langkah C, penilaian 0 sampai 5 |
| `tab/analisa_catatan.blade.php` | Catatan analis |
| `tab/analisa_hasil.blade.php` | Panel hasil analisa di kanan, tombol kembali dan simpan |
| `tab/dokumen.blade.php` | Tab 2: unggah dokumen pendukung |
| `tab/memorandum.blade.php` | Tab 3: tiga sub-tab memorandum |
| `tab/administrasi.blade.php` | Tab 4: biaya kredit |
| `tab/komite.blade.php` | Tab 5: resume dan tombol kirim ke Komite |
| `cetak.blade.php` | Halaman cetak (terpisah dari halaman analisa) |

Daftar kolom, bobot, dan aturan hitung tidak ada di file tampilan; semuanya dikirim dari
`AmaterasuController`. Perilaku di browser (hitung, tab, unggah, dialog) ada di
`public/assets/js/myscript/kpmi_amaterasu.js`.
