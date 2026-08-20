# Aturan WAJIB sebelum menyatakan UI selesai (SIPEBRI/AdminKit)

User berulang kali mengeluh soal responsivitas. JANGAN pernah menyelesaikan pekerjaan UI
tanpa langkah di bawah ini.

## Checklist responsif (wajib, setiap kali menyentuh file .vue)
Uji minimal 4 lebar: **390** (ponsel), **768** (tablet), **1024** (laptop, sidebar terbuka), **1440**.
Untuk tiap lebar, cek:
1. `document.documentElement.scrollWidth === viewport width` (tidak ada overflow horizontal).
2. Tidak ada label/angka yang terpotong ("PENGAJ…") atau membungkus jadi 2 baris di kartu KPI.
3. Tabel: bungkus otomatis oleh `Table.vue` (`overflow-auto`) → beri `whitespace-nowrap`
   pada `TableHead`/`TableCell` agar tabel menggeser, bukan sel yang membelit.
4. Grafik SVG (`MiniBarChart`): bungkus `overflow-x-auto` + `min-w-[460px] md:min-w-0`.
5. Header aksi: `flex-col sm:flex-row`, filter `flex-1 sm:w-[170px]`, tombol teks
   disembunyikan di ponsel (`hidden sm:inline`).

## Placeholder kolom form (WAJIB, berlaku untuk semua form sekarang & nanti)
- Kolom **TIDAK WAJIB** → placeholder **`(Opsional)`**, termasuk selectbox/combobox dan date picker.
- Kolom **WAJIB** → **tanpa placeholder**; khusus selectbox/combobox placeholder-nya **`-- Pilih --`**.
- Kolom angka opsional yang dikirim ke CBS (mis. NJOP, Adjusment): placeholder `(Opsional)`,
  namun bila dibiarkan kosong **nilai yang disimpan/dikirim = 0** (lihat `withDefaults()`).

## Format angka Indonesia (WAJIB, tanpa kecuali)
- **Semua angka yang TAMPIL** (tabel, kartu, grafik, ringkasan) memakai format Indonesia:
  pemisah ribuan titik → `1000` ditampilkan `1.000`; uang pakai `rupiah()` (`Rp 1.000`).
- **Semua kolom INPUT angka** memakai `components/ui/NumberInput.vue`:
  `1.000` hanyalah tampilan, **nilai yang disimpan/dikirim tetap `1000`** (integer murni,
  tanpa titik). Jangan pernah mengirim string berformat ke backend/API.
- Jangan memakai `<Input type="number">` untuk rupiah/plafon/tenor/persen bulat — pakai `NumberInput`.
- Desimal (suku bunga, provisi, biaya admin, ambang RC, persentase): pakai
  `components/ui/DecimalInput.vue` → **tampil `12,75`**, **nilai simpan `12.75`**.
  Untuk teks tampilan persen pakai `persen(value, decimals)` dari `constants/committee.js`
  (`12.75` → `12,75%`).


- KPI 5 kartu: `grid-cols-2 md:grid-cols-3 xl:grid-cols-5`, kartu terakhir `col-span-2 md:col-span-1`.
- Dua kartu berdampingan: `lg:grid-cols-2`; tiga kartu: `lg:grid-cols-3` (jangan 5 kolom di `lg`).
- Kartu jangan pakai tinggi grafik tetap besar; pakai konten yang tumbuh agar tidak ada ruang kosong.

## Preferensi user lain yang sudah tegas
- Semua kontrol tinggi 32px (`h-8`).
- Jangan ada ruang kosong berlebih di dalam kartu.
- Dashboard = proses pemberian kredit (origination). JANGAN masukkan metrik monitoring
  (NPL, kolektibilitas) kecuali diminta.
- Balas selalu dalam bahasa Indonesia.

## Kolom angka (WAJIB, jangan pernah dilanggar lagi)
User pernah sangat marah karena kolom **Nomor KTP menerima huruf**. Penyebab: memakai `<Input>`
biasa dengan filter di handler halaman. `Input.vue` mengikat `:value`, jadi bila nilai hasil filter
tidak berubah, DOM TIDAK ikut di-patch → huruf tetap terlihat di kolom.

Aturan:
- Uang/plafon/tenor → `NumberInput.vue` · Desimal/persen → `DecimalInput.vue`
- **Angka murni tanpa pemisah ribuan (Nomor KTP, NPWP, telepon, kode angka) → `DigitsInput.vue`**
  (`maxlength` opsional). JANGAN pakai `<Input>` + filter manual di halaman.
- Setiap kali membuat kolom angka baru: uji dengan MENGETIK HURUF, lalu pastikan kolom benar-benar
  kosong/menolak huruf pada tampilan (bukan hanya di state), lewat screenshot atau evaluasi
  `input.value` di browser.

## Setelah mengubah file .vue (WAJIB)
- Pastikan **semua komponen yang dipakai di template sudah di-import**. Pernah terjadi:
  `CardHeader` & `CardTitle` hilang dari import saat file disunting lewat skrip → judul kartu
  tampil sebagai teks melayang tanpa header bar (user marah).
- Setelah `yarn build`, ambil screenshot **dengan `capture_logs`/console listener** dan pastikan
  TIDAK ada peringatan `Failed to resolve component`.
- Pola kartu form baku: `Card > CardHeader(CardTitle) + CardContent(grid field) + CardFooter(Batal/Simpan)`
  — persis seperti `CollateralSimulationForm.vue`.

## Placeholder pada input UPPERCASE
Kolom yang isinya dipaksa huruf besar WAJIB pakai `class="uppercase placeholder:normal-case"`,
kalau tidak placeholder `(Opsional)` ikut jadi `(OPSIONAL)`.

## Select bergantung (dependent select)
Kolom yang pilihannya bergantung kolom lain (mis. Kategori bergantung Produk, seperti pada
Simulasi Kewenangan Komite) WAJIB `:disabled` sampai kolom induknya dipilih, dengan placeholder
penjelas (mis. "Pilih produk dahulu"), dan nilainya direset saat induk berubah.
