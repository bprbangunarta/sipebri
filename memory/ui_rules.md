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
