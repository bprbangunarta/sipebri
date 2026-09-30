---
paths:
    - 'resources/js/**'
---

# Js

## Compact enterprise UI conventions (wajib)

Semua UI baru WAJIB mengikuti pola Compact UI yang sudah ada; jangan membuat gaya baru per halaman.

- Reuse komponen di resources/js/components/ui (Button, Input, Field, Combobox, DatePicker, Modal/ConfirmDialog, Dropdown, Tabs, Tip, Card/EmptyState/ErrorState/Skeleton/PageHeader) sebelum membuat yang baru. Jangan pakai kontrol native/berukuran lain.
- Ukuran: kontrol h-8 (tombol kecil h-7, icon size-7), font text-sm (0.8125rem) untuk isi & text-xs untuk label/hint; ikon size-3.5/4 (lucide-react); radius rounded-md; card rounded-lg border border-line.
- Warna hanya lewat token tema (primary, primary-soft, surface, canvas, line, ink, muted, danger) di app.css; jangan hardcode warna. Chart memakai oklch primary/palet yang sama.
- Layout: sidebar w-60 (drawer mobile w-64), header h-12, konten p-3 sm:p-5, PageHeader (judul text-base font-semibold + deskripsi text-xs text-muted + tombol aksi di kanan), gap antar seksi 3 (gap-3).
- Tabel: text-sm, header text-xs text-muted bg-canvas, sel px-3 py-1.5, divide-y divide-line, kolom sekunder disembunyikan dengan hidden md/lg:table-cell, overflow-x-auto; kolom sortable memakai ikon panah; aksi baris lewat dropdown 'MoreHorizontal' + Tip.
- Form: grid 2-4 kolom dalam Card per seksi (judul seksi text-sm font-semibold), Field dengan label text-xs, error text-xs text-danger di bawah input, tanda * merah untuk wajib.
- Master data: modal kecil (max-w-sm) untuk create/edit, ConfirmDialog sebelum hapus.
- Setiap daftar harus punya loading (Skeleton / opacity), empty state, error state, dan toast (sonner) untuk feedback sukses/gagal.
- Kalender memakai kelas compact-calendar (sel 28px); jangan tampilkan kalender berukuran default.
- Perubahan pola yang sudah ada harus memperbaiki komponen bersama dan diterapkan konsisten ke semua halaman, bukan hanya satu halaman.
- Toolbar daftar: selalu pakai `FilterBar` + `SearchInput` (`components/ui/filter-bar.tsx`). Kolom pencarian sendirian di pojok kiri, semua filter dan tombol Reset dikelompokkan di pojok kanan (di layar kecil filter turun ke bawah pencarian). Jangan menaruh filter di sebelah pencarian.
- Ikon pada tombol (aturan tetap):
    - Tombol aksi level halaman/toolbar/header kartu (Add, New, Back, Reset, Save, Submit, Log in/out, Export) = **ikon + label**, ikon di kiri label (lucide, `size-3.5` otomatis dari Button).
    - Tombol **footer dialog dan form** (Cancel, Back, Create, Save, Delete, Confirm) = **hanya teks**. Footer selalu memakai `DialogFooter` (`components/ui/dialog.tsx`): tombol menutup/batal **paling kiri**, tombol konfirmasi **paling kanan**. Form satu halaman memakai pola sama (`justify-between`).
    - Aksi baris tabel = tombol ikon saja (`size="icon"`) dengan `aria-label` dan `Tip`, atau item dropdown berikon.
    - Aksi kecil sebaris dalam form (Look up, Attach, Check, Rename) dan tautan di keadaan kosong ("Clear filters") boleh teks saja.

## Tabel dan overflow horizontal

- Pembungkus tabel yang bisa digulir wajib `relative overflow-x-auto`. Tanpa `relative`, elemen `position:absolute` di dalamnya (mis. `sr-only` pada header kolom aksi) keluar dari area gulir dan melebarkan seluruh halaman di ponsel, sehingga layout bisa digeser ke samping.
- Setelah mengubah halaman daftar, periksa di lebar 375px bahwa `document.documentElement.scrollWidth` sama dengan lebar layar.

## DataTable: satu-satunya tabel

- Semua tabel memakai `components/ui/data-table.tsx` (`DataTable` + tipe `Column`); jangan menulis `<table>` di halaman (`ArchitectureTest` akan gagal). Komponen ini mengurus kartu, toolbar (`FilterBar`), header yang bisa diurutkan (`sort` pada kolom), skeleton, kondisi kosong, galat + coba lagi, klik baris (`onRowClick`), paginasi, dan pembungkus gulir `relative overflow-x-auto`.
- Definisikan kolom sebagai `Column<Row>[]`: `hideBelow` ('sm'|'md'|'lg') untuk kolom yang disembunyikan di layar kecil, `align: 'right'` untuk angka, `srOnly` + `narrow` untuk kolom aksi (klik di kolom aksi tidak memicu klik baris). Aksi baris: ikon saja dengan `Tip`.
- Tabel di dalam kartu lain atau dialog: `bare` (tanpa kartu), dan `dense` untuk teks kecil. `className` mengatur kartu (mis. `max-w-3xl`).
- Halaman daftar server-side tetap memakai `useListQuery` untuk filter/urutan/halaman; hasilnya dioper ke `DataTable`.

## Pilihan (Combobox) dengan teks panjang

- Jangan memotong identitas di dalam daftar pilihan. Daftar boleh lebih lebar dari kolomnya dan membungkus baris; kolom yang menampung pilihan panjang (jenis agunan, pengikatan, wilayah) dibuat selebar baris form, dan dialognya `wide`.
- Untuk pilihan yang mirip satu sama lain (mis. agunan), isi `description` pada opsi: label = identitas singkat, `description` = pembeda (nomor dokumen, jenis, nilai, alamat lengkap). `description` ikut dicari. Teks terpilih di kolom yang terpotong tetap punya tooltip (`title`).
- Combobox di dalam dialog: kunci gulir dialog memblokir roda/sentuh pada konten yang di-portal, jadi `Combobox` menahan `wheel`/`touchmove` di kontennya; navigasi keyboard wajib menggulir opsi aktif ke area terlihat (`scrollIntoView`). Periksa keduanya bila membuat dropdown baru di dalam `Modal`.

## DatePicker

- Kalender tanpa nilai terbuka di bulan berjalan (atau bulan terdekat dalam rentang bila hari ini di luar rentang), bukan di bulan terakhir rentang. Rentang lebar tidak boleh membuat kalender terbuka jauh di masa depan: memilih tanggal dari sana memberi tahun yang salah tanpa disadari.

## Lokasi dan peta

- Koordinat survei tidak pernah diambil dari perangkat yang mengunggah foto (survei diisi di kantor; foto datang lewat WhatsApp). Sumbernya: tombol "Tandai lokasi" di lapangan, tempel koordinat/tautan, klik peta, atau GPS foto bila ada. Jangan menambah `navigator.geolocation` di alur unggah.
- Peta selalu lewat `components/location-map.tsx` (Leaflet dimuat malasan); tampilan baca-saja lewat `components/locations-panel.tsx`.
