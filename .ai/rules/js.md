---
paths:
    - 'resources/js/**'
    - 'resources/css/**'
    - 'resources/views/**'
    - 'scripts/**'
---

# Antarmuka (React 19 + Inertia v3 + Tailwind v4)

Aturan UI dan komponen diambil dari react-starter (`docs/STANDARDS.md`, 2026-10-19) dan disesuaikan dengan Inertia. Aturan bertanda **[mesin]** diperiksa `npm run standards` (`scripts/check-standards.mjs`) atau `ArchitectureTest`; sisanya diperiksa saat tinjauan. `npm run check` menjalankan `vp check` (format + lint oxlint bertipe), `npm run standards`, dan Vitest.

## Mengubah UI

Ubah komponen dengan memperbaiki komponen bersama di `components/ui`, tidak pernah dengan gaya satu kali di halaman; perubahan pola diterapkan konsisten ke semua halaman. **Jangan menjalankan `vp check --fix` pada `resources/js`**: autofix oxlint pernah mengubah perilaku `lib/mask.ts` di codex dan merusak test-nya; pakai `vp check` dan `vp fmt`, perbaiki temuan lint dengan tangan. Acuan hidup semua komponen adalah halaman **`/ui-guideline`** (Super Admin, menu Pengaturan → UI Guideline): tampilan baru harus terlihat sama.

## antislop: arah dan pilihan pemilik

Skill antislop (`AGENTS.md` bagian 5) menyaring "slop" dan butuh arah. Bagian ini arahnya; tidak ada `DESIGN.md`. Sama dengan codex (disetujui pemilik 2026-10-17), diterapkan ke sipebri 2026-10-19; perubahan baris mana pun dibuat di sini.

- **Design Read (R-37):** alat internal staf BPR, padat dan ringkas, tenang: ENERGY 1 / RHYTHM 1 / MOTION 1. Ritme seragam disengaja (tabel, form, dan kartu berulang). Warna dan ukuran hanya dari `resources/css/app.css`, dan `/ui-guideline` menampilkannya.
- **Ikon (R-04):** `lucide-react` satu-satunya set ikon; tiap ikon dipilih menurut artinya di layar itu; entri submenu memakai titik polos (`dot`).
- **Tema (R-21):** terang saja, tanpa sakelar (alat kantor siang hari; token dibuat untuk satu tema).
- **Kepadatan (R-03):** kontrol `h-8` dan sidebar `w-60`, di bawah target sentuh 44 px yang diminta `antislop-layoutmobile`. Penyimpangan yang disengaja demi kepadatan; surveyor memakai ponsel, jadi layar survei diperiksa untuk luapan dan bisa dijangkau jempol, tetapi ukuran kontrol tetap.
- **Teks (R-02, R-16):** bahasa Indonesia, tanpa tanda pisah panjang (em dash) di teks layar, tanpa kata promosi, tanpa angka atau klaim karangan.
- **Pengganti yang jujur (R-23, R-38):** halaman yang belum dibangun mengatakannya di halaman dan tidak punya data, tanggal, atau daftar fitur karangan.

## Bahasa dan nama

Aturan bahasa di `.ai/rules/general.md`. Untuk layar: satu fitur satu nama dari label menu sampai berkas: URL adalah bentuk Inggris dari label (`/audit-logs` untuk "Audit Log"), halaman di `resources/js/pages/<url>/`, controller dan route bernama sama. Judul layar sama dengan label menu. Pesan validasi backend sudah Indonesia (`laravel-lang`): tampilkan, jangan ditulis ulang.

**Label menu** (`App\Support\Navigation::definition()`) satu kata, paling banyak dua, maksimal 15 karakter, tanpa garis miring **[mesin]**; nama panjang ada di judul halaman. Urutan mengikuti urutan pekerjaan (data yang dibutuhkan data lain lebih dulu). Halaman sejenis dikelompokkan sebagai submenu.

**Submenu mengikuti codex** (keputusan pemilik 2026-10-19; acuannya `layouts/app-layout.tsx` di codex): entri induk adalah tombol satu baris (ikon, label, `ChevronDown` di kanan yang berputar saat terbuka) dengan `aria-expanded` dan `aria-controls`; submenu **tertutup sampai dibuka, dan terbuka sendiri selama salah satu halamannya tampil** sampai orang menutupnya; anak-anaknya menjorok di bawah induk dengan garis kiri (`ml-[1.125rem] border-l border-line`) dan memakai **titik polos** (ikon `dot`), bukan ikon; baris semua entri sama (`h-8`, `rounded-md`, aktif = `bg-primary-soft text-primary`). Judul kelompok di dalam submenu (`group`) dan judul seksi memakai `text-2xs` kapital `text-muted/80`. Implementasinya satu: `SubMenu` dan `NavLink` di `layouts/app-layout.tsx`.

## Tanggal, waktu, dan angka untuk orang

Tanggal, waktu, dan angka diubah menjadi teks di satu tempat: `lib/format.ts` (`formatDate` "04 Okt 2026", `formatDateTime` "04 Okt 2026 08:45:03", `formatRelative` "3 menit yang lalu", `formatNumber` "1.500.000", `rupiah` "Rp 1.500.000"); `CurrencyInput` untuk mengetik uang. Layar tidak memanggil `format` dengan locale, `Intl`, atau `toLocale...String` sendiri **[mesin]**. Nilai untuk mesin (route, payload) tetap ISO dan angka polos; nilai kosong tampil sebagai tanda strip ("–").

## Bicara dengan server

Semua akses server lewat Inertia: `router`, `useForm`/`<Form>`, `useHttp` untuk permintaan JSON tanpa navigasi, dengan URL dari Wayfinder (`@/actions`, `@/routes`), bukan string tulisan tangan. `fetch` langsung dilarang **[mesin]** (kecuali ping koneksi di `hooks/use-network-status.ts`). Galat validasi tampil per kolom; jangan tampilkan galat teknis mentah. Jangan menyimpan rahasia (kata sandi, kode, token) di `localStorage`/`sessionStorage` **[mesin]**, URL, atau log.

## TypeScript dan React

- `strict`; tanpa `any`, `as any`, `@ts-ignore`, `@ts-nocheck` **[mesin]**; pakai `unknown` lalu persempit. Tipe prop halaman didefinisikan dan dipakai ulang (`types/`).
- Ekspor bawaan hanya untuk halaman, layout, dan `app.tsx` **[mesin]**; lainnya ekspor bernama. Tanpa `console.log`/`debugger` **[mesin]**.
- Komponen fungsi dan hooks saja; hooks di tingkat atas. Satu komponen satu berkas yang bertanggung jawab pada satu hal; jangan mendefinisikan komponen di dalam komponen lain.
- State seminimal mungkin: yang bisa dihitung dihitung saat render. `useEffect` hanya untuk sinkron dengan dunia luar (event, timer, pengamat), dibersihkan di fungsi balikan; jangan memuat data di efek (pakai prop Inertia, prop tertunda, atau `useHttp`).
- `key` pada daftar memakai id yang stabil, bukan indeks **[mesin]**. Promise di handler di-`void` atau ditangani. Isian terkendali memasangkan `value` dengan `onChange`. `dangerouslySetInnerHTML` dilarang **[mesin]**.
- `React.memo`/`useMemo`/`useCallback` hanya bila ada masalah kinerja terukur (React Compiler aktif).

## Aksesibilitas

Tombol ikon wajib `aria-label` plus `Tip`; isian dibungkus `Field` (label, hint, dan galat tersambung lewat `id`, `aria-describedby`, `aria-invalid`); dialog memakai `Modal`/`ConfirmDialog`; semua yang bisa diklik adalah `button` atau `Link`, bukan `div` ber-`onClick`. Aksesibilitas umum (kontras, keyboard, fokus, keadaan) adalah `antislop-human`.

## Compact UI

- **Pakai ulang** `components/ui`: Button, Input/InputGroup, Field, Combobox, DatePicker, Modal/ConfirmDialog/DialogBody/DialogFooter, Dropdown, Tabs, Tip, Card/CardHeader/CardBody/CardFooter, Alert, Badge, EmptyState, ErrorState, Skeleton, PageHeader, DataTable, BarChart/ColumnChart/DonutChart, Checkbox/Switch/RadioGroup, Textarea, CurrencyInput, MaskedInput, FilteredInput, ColorInput, FileInput, PasswordInput, CodeInput. Jangan pakai kontrol native (`<textarea>`, `<select>`, `type="checkbox|radio"`) di halaman **[mesin]**.
- **Ukuran:** kontrol `h-8` (tombol kecil `h-7`, tombol ikon `size-7`, tombol di samping isian `size="fieldIcon"`), font `text-sm` (0.8125rem) untuk isi, `text-xs` untuk label/hint, `text-2xs` untuk badge dan header tabel; ikon `size-3.5` di tombol, `size-4` di menu; radius `rounded-md`, kartu `rounded-lg border border-line bg-surface`.
- **Layout:** sidebar `w-60` (laci ponsel `w-64`), header `h-12`, konten `p-3 sm:p-5`, jarak antar seksi `gap-3`.
- **Baris teks + aksi** (header halaman/blok/dialog, baris pengaturan): `flex flex-wrap items-center justify-between gap-2`, teks `min-w-0`, aksi di kelompok `flex items-center`. `items-start justify-between` hanya untuk opsi `Combobox` **[mesin]**.
- **PageHeader:** judul `text-base font-semibold`, keterangan `text-xs text-muted` yang pendek dan tidak mengulang data yang sudah tampil di isi halaman (mis. nama pemohon di halaman survey). Susunannya sudah disepakati, jangan diubah tanpa diminta. Tombol di `actions` sama lebar: di ponsel dua kolom penuh seperti filter (tombol terakhir yang ganjil memenuhi dua kolom), dari `sm` ke atas di kanan selebar label terpanjang; isi `actions` dengan tombolnya langsung (fragment), **tanpa `div` pembungkus** **[mesin]**.
- **Tombol** (ikon atau tidak ditentukan oleh tempatnya, bukan selera):

    | Tempat                                                                | Bentuk                                               | Contoh                                      |
    | --------------------------------------------------------------------- | ---------------------------------------------------- | ------------------------------------------- |
    | header halaman, toolbar, header kartu, baris pengaturan di body kartu | **ikon + label** (ikon kiri)                         | Tambah, Ekspor CSV, Atur ulang, Simpan draf |
    | footer dialog dan footer kartu (`DialogFooter`, `CardFooter`)         | **teks saja** **[mesin]**                            | Batal, Simpan, Hapus, Konfirmasi            |
    | tombol kirim kartu auth tanpa header (login, verifikasi)              | teks saja, selebar kartu                             | Masuk, Verifikasi                           |
    | aksi baris tabel, tombol tutup, panah paginasi                        | **ikon saja** (`size="icon"`) + `aria-label` + `Tip` | Ubah, Hapus, Tutup                          |
    | tombol di samping isian (salin, buat acak, cari)                      | ikon saja, `size="fieldIcon"` di dalam `InputGroup`  | Salin                                       |
    | aksi kecil sebaris dalam form, tautan di keadaan kosong/galat         | teks saja                                            | Coba lagi, Hapus filter                     |

    Urutan footer: batal paling kiri, konfirmasi paling kanan. Satu tempat tidak bercampur: bila satu toolbar memakai ikon, semua tombolnya berikon. Semua varian `Button` berbagi kotak border 1px yang sama, jadi dua tombol bersebelahan sama besar.

- **Dialog** punya tiga bagian: header (judul; subjudul satu baris hanya untuk dialog form), **body** (`DialogBody`: isi form atau pesan konfirmasi), dan footer (`DialogFooter`, teks saja). Pesan `ConfirmDialog` ada di body. Dialog yang memakai `Modal` wajib memakai `DialogBody` **[mesin]**. Dialog berisi isian memakai `persistent` (klik di luar tidak menutup); dialog baca-saja boleh menutup lewat klik di luar. Dialog lebih tinggi dari layar menggulir utuh; jangan membuat gulir bersarang di `DialogBody`. Lebar: bawaan `max-w-sm`, `wide` (`max-w-2xl`) untuk form, `wide="xl"` (`max-w-4xl`) untuk form dengan beberapa seksi berdampingan. Tambah/ubah data master: modal kecil; `ConfirmDialog` sebelum menghapus atau aksi yang tak bisa dibatalkan, dengan pesan yang mengatakan apa yang akan terjadi.
- **Kartu** disusun seperti dialog: `CardHeader` (judul `text-sm font-semibold`, keterangan opsional, aksi/badge di kanan, garis bawah), `CardBody` (isi, `p-3`), dan `CardFooter` (garis atas) **hanya bila kartu punya tombol** kirim/batal. Jangan menaruh tombol Simpan di body atau membuat judul kartu dengan `<h2>` buatan sendiri.
- **Form:** grid 2–4 kolom dalam `Card` per seksi, `Field` dengan label `text-xs`, tanda `*` merah untuk wajib.
- **Tanpa keterangan di bawah isian:** di bawah isian hanya galat validasi. Keterangan isian adalah `hint` `Field`: tooltip pada ikon info di samping label, dibaca pembaca layar sebagai deskripsi isian. Hint pendek (format, batas, akibat pilihan); yang lebih panjang masuk ke deskripsi kartu atau halaman. `Textarea` tidak punya hint sendiri (penghitung huruf satu-satunya di bawahnya); baris sakelar dan centang memegang baris keterangannya sendiri.
- **Daftar:** selalu punya keadaan memuat (Skeleton/opacity), kosong, dan galat; setiap aksi memberi toast (sonner) sukses/gagal.
- **Toolbar daftar:** `FilterBar` + `SearchInput`; pencarian sendirian di kiri, semua filter dan tombol Reset di kanan (di layar kecil filter turun ke bawah pencarian).
- **Grafik:** `DonutChart` untuk sedikit bagian dari satu keseluruhan, `ColumnChart` untuk rentang berurutan (bulan), `BarChart` untuk nama panjang (produk, kantor). Angka selalu tertulis di samping tiap bagian; warna `chart-1` sampai `chart-5` tidak bermakna status. Tanpa pustaka grafik lain.
- **Lapisan mengambang** (dialog, laci, popover, dropdown, tooltip) hanya lewat `components/ui` (Radix); `fixed inset-0` buatan sendiri dilarang **[mesin]** (laci menu ponsel juga dialog Radix). Dialog punya nama dan peran; `Esc` dan tombol tutup menutupnya; fokus kembali ke tombol pembuka (`useRestoreFocus`); animasi hanya pudar (`overlay-fade`) dan mati bila pengguna meminta gerak berkurang.

## Jenis isian: layar menegakkan apa yang ditegakkan server

Aturan server tentang _apa yang boleh diketik_ (hanya angka, nama tanpa angka, panjang, rentang) ditegakkan juga oleh isian, sehingga kolom yang tidak bisa memuat suatu karakter tidak pernah menampilkannya; server tetap yang berwenang dan memeriksa lagi. Bila satu aturan berubah di satu sisi, ubah sisi lainnya di commit yang sama.

- **Uang dan angka besar:** `CurrencyInput` (format `1.000.000` otomatis, awalan `Rp`; `prefix={null}` untuk angka biasa). Nilainya **number**; ke server dikirim angka itu, tidak pernah teks berformat.
- **Format tetap dan angka polos** (NIK, telepon, NPWP, tenor, persen bulat): `MaskedInput` dengan `mask` dari `lib/mask.ts` (`9` angka, `a` huruf, `*` huruf/angka; `'9'.repeat(n)` untuk angka polos). Nilainya data tanpa tanda baca. Tambah mask baru di `MASKS`.
- **Huruf dan karakter terbatas** (nama, kode): `FilteredInput` dengan himpunan karakter yang sama dengan aturan server.
- **Desimal** (bunga, koordinat): `FilteredInput` dengan himpunan angka dan pemisah yang diterima server, atau `CurrencyInput` bila bilangan bulat.
- **Pilihan:** `Combobox` (banyak opsi) atau `RadioGroup` (sampai sekitar 5 opsi yang perlu terlihat); **teks panjang:** `Textarea` dengan `maxLength`; **tanggal:** `DatePicker`; **warna:** `ColorInput`; **kode OTP:** `CodeInput`; **kata sandi:** `PasswordInput`; **berkas:** `FileInput` (batasi `accept` dan `maxBytes`; ukuran dan tipe divalidasi ulang di server).
- **Centang vs sakelar:** `Checkbox` untuk pilihan yang menunggu tombol Simpan; `Switch` untuk pengaturan yang berlaku seketika.
- `maxLength` pada setiap isian teks bebas. `type="number"` dan `type="tel"` dilarang **[mesin]** (menerima `e`, tanda, dan huruf).

## Tabel

- Semua tabel memakai `components/ui/data-table.tsx` (`DataTable` + `Column`); `<table>` di halaman dilarang **[mesin]**. Komponen ini mengurus kartu, toolbar (`FilterBar`), urutan (`sort`), skeleton, kosong, galat + coba lagi, klik baris (`onRowClick`), paginasi, dan pembungkus gulir `relative overflow-x-auto` (tanpa `relative`, elemen `absolute` seperti `sr-only` melebarkan halaman di ponsel).
- **Kolom tidak pernah disembunyikan di layar sempit** (tidak ada `hideBelow`): sel dan header tidak membungkus teks, header kapital dengan sedikit jarak huruf dan `text-2xs`, dan tabel lebar menggulir ke samping di dalam kartunya. Satu nilai satu kolom (kolom tidak bertumpuk).
- Kolom: `align: 'right'` untuk angka, `srOnly` + `narrow` untuk kolom aksi (klik di sana tidak memicu klik baris). Dalam kartu/dialog: `bare`, dan `dense` untuk teks kecil.
- **Urutan:** tabel yang bisa diurutkan memberi **semua** kolom berisi data tombol header (`sort` di kolom dan properti `sort` di `DataTable`); hanya kolom aksi (`srOnly`) yang tidak. `DataTable` memperingatkan di konsol (mode dev) bila aturan ini dilanggar. Daftar yang dipaginasi server diurutkan di server dengan kunci yang sama di daftar putih controller (`SORTABLE`; kunci tak dikenal jatuh ke urutan bawaan), ditambah bersama kolomnya, punya indeks, dan test meminta tiap kunci dua arah. Klik pertama mengurutkan naik, berikutnya membalik; "Atur ulang" mengembalikan urutan bawaan. Tabel yang memang berurutan tetap (jenjang komite, riwayat jadwal, langkah persetujuan) tidak diurutkan sama sekali.
- Halaman daftar server-side memakai `useListQuery` untuk filter/urutan/halaman; hasilnya dioper ke `DataTable`.

## Token dan CSS

`resources/css/app.css` satu-satunya sumber warna dan ukuran (Tailwind v4 `@theme`). Tanpa warna tertulis langsung **[mesin]**, tanpa ukuran teks sembarang (`text-[11px]`) **[mesin]**, tanpa palet Tailwind untuk status **[mesin]**: pakai `success`, `warning`, `info`, `danger` dengan `-soft`, `-line`, `-ink`; pesan sebaris memakai `Alert`, bukan `div` buatan sendiri. `html { font-size: 15px }`, jadi `1rem = 15px`.

| Token                                                        | Dipakai untuk                                                             |
| ------------------------------------------------------------ | ------------------------------------------------------------------------- |
| `primary` / `-hover` / `-soft`                               | tombol utama, tautan, menu aktif, fokus / disorot / latar lembut          |
| `surface`, `canvas`, `line`                                  | kartu, sidebar, dialog / latar halaman, header tabel, hover / semua garis |
| `ink`, `muted`                                               | teks utama / label, hint, header tabel                                    |
| `{success,warning,info,danger}` + `-soft` / `-line` / `-ink` | status: badge, `Alert`, toast, indikator jaringan                         |
| `chart-1` … `chart-5`                                        | seri grafik (tanpa arti status), tiap warna ≥ 3:1 terhadap `surface`      |
| `text-2xs` / `text-xs` / `text-sm`                           | badge, header tabel, penghitung / label dan hint / isi, sel, tombol       |

Lapisan: header `z-30`, laci `z-40`, dialog dan penanda jaringan `z-50`, tooltip `z-60`, popover `z-70`. Bayangan: `shadow-sm` jempol sakelar, `shadow-md` penanda mengambang, `shadow-lg` dropdown/popover, `shadow-xl` dialog/laci. Kontras token dijaga `app.contrast.test.ts`; token warna baru masuk daftar token `/ui-guideline` dan komponen baru di `components/ui` punya contoh di sana (`ui-guideline.test.ts` menegakkan keduanya). Kalender memakai kelas `compact-calendar` (sel 28px).

## Combobox dengan teks panjang

- Jangan memotong identitas di dalam daftar pilihan. Daftar boleh lebih lebar dari kolomnya dan membungkus baris; kolom yang menampung pilihan panjang (jenis agunan, pengikatan, wilayah) dibuat selebar baris form, dan dialognya `wide`.
- Untuk pilihan yang mirip satu sama lain (mis. agunan), isi `description` pada opsi: label = identitas singkat, `description` = pembeda (nomor dokumen, jenis, nilai, alamat lengkap). `description` ikut dicari. Teks terpilih yang terpotong tetap punya tooltip (`title`).
- Combobox di dalam dialog: kunci gulir dialog memblokir roda/sentuh pada konten yang di-portal, jadi `Combobox` menahan `wheel`/`touchmove` di kontennya; navigasi keyboard menggulir opsi aktif ke area terlihat. Periksa keduanya bila membuat dropdown baru di dalam `Modal`.

## DatePicker

Kalender tanpa nilai terbuka di bulan berjalan (atau bulan terdekat dalam rentang bila hari ini di luar rentang), bukan di bulan terakhir rentang: memilih tanggal dari sana memberi tahun yang salah tanpa disadari.

## Lokasi dan peta

- Koordinat survei tidak pernah diambil dari perangkat yang mengunggah foto (survei diisi di kantor; foto datang lewat WhatsApp). Sumbernya: tombol "Tandai lokasi" di lapangan, tempel koordinat/tautan, klik peta, atau GPS foto bila ada. Jangan menambah `navigator.geolocation` di alur unggah (`ArchitectureTest`).
- Peta selalu lewat `components/location-map.tsx` (Leaflet dimuat malasan); tampilan baca-saja lewat `components/locations-panel.tsx`. Warna penanda peta diambil dari token CSS lewat `var(--color-…)`, bukan ditulis langsung.

## Periksa layarnya, bukan hanya kodenya

Periksa setiap layar yang diubah di **desktop 1280px** (bukan lebar bawaan panel peramban), tablet 768px, dan ponsel **375px** (juga 320px dan 390–430px bila tata letaknya berubah di sana), dengan isi panjang yang realistis, dalam keadaan memuat, kosong, dan galat. Pada setiap lebar: `document.documentElement.scrollWidth` sama dengan lebar layar (tanpa gulir horizontal halaman; hanya tabel yang menggulir di kartunya), tidak ada kontrol tanpa nama, satu `h1`, dialog dan laci muat di layar. Di panel peramban bawaan: `resize_window` dengan `width: 1280, height: 800`, lalu ukur, dan kembalikan ke `preset: desktop` setelah selesai.

## Sebelum selesai

`npm run check` (format, lint, aturan rumah, test Vitest), `npm run types:check`, `npm run duplicates`, dan `npm run build` (test Pest merender halaman lewat manifest Vite).
