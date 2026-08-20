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

## Pola grid yang aman
- KPI 5 kartu: `grid-cols-2 md:grid-cols-3 xl:grid-cols-5`, kartu terakhir `col-span-2 md:col-span-1`.
- Dua kartu berdampingan: `lg:grid-cols-2`; tiga kartu: `lg:grid-cols-3` (jangan 5 kolom di `lg`).
- Kartu jangan pakai tinggi grafik tetap besar; pakai konten yang tumbuh agar tidak ada ruang kosong.

## Preferensi user lain yang sudah tegas
- Semua kontrol tinggi 32px (`h-8`).
- Jangan ada ruang kosong berlebih di dalam kartu.
- Dashboard = proses pemberian kredit (origination). JANGAN masukkan metrik monitoring
  (NPL, kolektibilitas) kecuali diminta.
- Balas selalu dalam bahasa Indonesia.
