# Audit fitur 24: UI bersama

Status: approved
Approved: 2026-10-19

## Tujuan

Semua layar berdiri di atas satu kit UI (komponen, token warna, tabel, isian, format tanggal dan angka), jadi satu cacat di sini muncul di puluhan layar. Fitur ini diperiksa lebih dulu dari 25 terhadap seluruh kerangka kerja seperti yang berlaku sekarang: standar produksi untuk kode layar, Delivery Gate antislop untuk setiap komponen, test, dan dokumen. Sesudahnya pemilik tahu apa yang ditemukan, diperbaiki, dan diterima, tidak hanya menganggap kit ini sudah rapi karena diambil dari react-starter.

Bagian dari seri audit fitur (satu brief per fitur, urutan dari pemilik: 24, 25, 8, 6, 7, 2 sampai 5, 9 sampai 13; alur kredit nanti). Fitur 1 sudah selesai.

## Kriteria penerimaan

Yang diperiksa (seluruh permukaan fitur): `resources/js/components/ui/*` (33 komponen dan 9 berkas test); token dan gaya dasar di `resources/css/app.css`; `resources/js/lib/{format,mask,utils}.ts` dan test-nya; `resources/js/hooks/use-list-query.ts`; halaman acuan `resources/js/pages/ui-guideline.tsx` dengan `UiGuidelineController`, rute `ui-guideline`, `UiGuidelineTest`, dan `ui-guideline.test.ts`; `scripts/check-standards.mjs` (`npm run standards`); `app.contrast.test.ts`; aturan mesin di `ArchitectureTest` dan `DesignQualityTest` yang menyangkut layar; dan bagian layar di `.ai/rules/js.md`, `.ai/rules/tests.md`, serta baris "Tabel/daftar" dan "UI bersama & acuan" di `CLAUDE.md`.

- [x] Setiap komponen di `components/ui` diperiksa satu per satu terhadap `.ai/rules/js.md` dan skill `antislop-ui`, `antislop-human`, dan `antislop-layoutmobile`: keadaan (memuat, kosong, galat, nonaktif, tidak valid), nama aksesibel, hubungan label, hint, dan galat (`Field`), keyboard dan fokus tampak, dan perilaku di lebar ponsel. Hasilnya baris PASS/FAIL per komponen dengan bukti, bukan klaim. (laporan: tabel PASS/FAIL per komponen di laporan penutup)
- [x] Kontras: setiap pasangan warna token yang dipakai komponen dijalankan lewat `contrast-check.py` (bukan hanya `app.contrast.test.ts`), termasuk keadaan hover, fokus, nonaktif, dan status; hasilnya dibandingkan dengan yang dijaga test, dan selisihnya dilaporkan.
- [x] Masukan aneh dicoba pada komponen isian (`CurrencyInput`, `MaskedInput`, `FilteredInput`, `CodeInput`, `PasswordInput`, `DatePicker`, `FileInput`, `Combobox`): tempel teks sembarang, angka sangat besar, negatif, desimal, spasi dan emoji, teks sangat panjang, dan penggantian nilai dari luar; kursor, format, dan nilai yang dikirim ke server diperiksa. Yang belum dites mendapat test (Vitest).
- [x] `useListQuery` dan `DataTable` (urutan, paginasi, galat dan coba lagi, klik baris, skeleton) diperiksa: penundaan pencarian, nilai bawaan yang dibuang dari URL, "Atur ulang", dan galat jaringan. Hook ini belum punya test sendiri; ditambah.
- [x] `lib/format.ts` diuji dengan nilai tak terbaca, kosong, nol, negatif, dan batas zona waktu (`Asia/Jakarta`, locale `id`); layar tidak memformat tanggal atau angka sendiri (pemeriksa mesin dikonfirmasi bekerja dengan sengaja melanggarnya di salinan sementara).
- [x] Setiap komponen dan token di kit punya contoh di `/ui-guideline` dan, bila berperilaku, test; daftar ekspor dibandingkan dengan isi halaman acuan dan dengan test penjaga, dan kekurangannya ditutup. Komponen yang tidak dipakai layar mana pun dicari (kode mati) dan dilaporkan.
- [x] `npm run standards` dibandingkan dua arah dengan `.ai/rules/js.md`: setiap klaim bertanda **[mesin]** punya pemeriksa yang gagal bila dilanggar (dibuktikan dengan pelanggaran sengaja di salinan sementara), dan setiap pemeriksa punya satu baris aturan di dokumen.
- [x] `/ui-guideline` diperiksa di peramban pada 1280 px dan 375 px: semua contoh interaktif dioperasikan dengan keyboard, konsol bersih, tanpa gulir horizontal halaman, dan halaman hanya terbuka untuk Super Admin (penolakan juga diuji). (di peramban dioperasikan dengan keyboard: Combobox dengan dan tanpa kotak cari, baris tabel yang membuka dialog, Escape; contoh lain lewat test keyboard Vitest; penolakan non-Super Admin dicek di peramban dan di test)
- [x] Mutu desain kode kit: ukuran berkas dan sarang, salinan kode (`npm run duplicates`), nama, dan keseragaman prop antar komponen sejenis diperiksa; pengecualian di `DesignQualityTest` (mis. `ui-guideline.tsx`) dicek masih punya alasan yang benar.
- [x] Sembilan pertanyaan `.ai/rules/php.md` dijawab untuk bagian server fitur ini (`UiGuidelineController` dan rutenya) dan, untuk kode layar, dijawab versi yang berlaku (masukan, keadaan galat, biaya render, kebocoran data di layar), dengan bukti.
- [x] Temuan diperbaiki di perubahan ini, masing-masing dengan test yang gagal sebelum perbaikan dan lulus sesudahnya; laporan memuat tiap temuan dengan tingkat keparahan, perbaikan, dan test. Temuan yang mengubah arah visual pemilik (warna, kepadatan, ikon, tema; `.ai/rules/js.md`, "antislop: arah dan pilihan pemilik") dijelaskan ke pemilik sebelum ditulis.
- [x] `.ai/rules/js.md`, `.ai/rules/tests.md`, `CLAUDE.md`, dan `memory/PRD.md` dibandingkan dua arah dengan kodenya; laporan menyebut selisih yang ditemukan dan diperbaiki (atau tidak ada).

## Di luar cakupan

- Cangkang aplikasi dan sidebar (`layouts/app-layout.tsx`, `Navigation`): fitur 5. Kerangka layar tamu: fitur 1 dan 2. Halaman error, `ErrorBoundary`, dan `NetworkStatus`: fitur 8. Lonceng notifikasi: fitur 7. Peta dan panel lokasi: fitur 20. Gerbang `verify.sh`, hook, dan `jscpd` sebagai alat: fitur 25 (di sini hanya dipakai sebagai bukti).
- Pemakaian komponen di tiap halaman: diperiksa bersama fitur pemilik halaman itu.
- Mengganti pustaka, tema gelap, atau mengubah arah visual yang sudah diputuskan pemilik.

## Asumsi dan pertanyaan terbuka

- Audit dikerjakan dengan menjalankan komponen (test, peramban, pemeriksa kontras) dan mencoba merusaknya, bukan memercayai test yang sudah ada; suite hijau hanya titik awal.
- Perbaikan yang harus menyentuh halaman fitur lain dibuat di sana juga bila cacatnya sama, dengan test, dan dicatat untuk kedua fitur.
- Temuan diperbaiki langsung kecuali yang mengubah kebijakan atau arah visual: itu dijelaskan dulu (keputusan pemilik pada audit fitur 1, berlaku untuk seluruh seri).
- Tidak ada pertanyaan untuk pemilik saat ini.

## Daftar periksa

Setiap butir dicentang `[x]`; butir yang tidak berlaku dicentang dan diakhiri `N/A: alasan`. Tidak ada yang "selesai" selama masih ada `[ ]`. Aturan di balik tiap butir ada di berkas yang disebutnya; jangan menyalin aturannya ke sini.

Membaca dan cakupan

- [x] PRD dan berkas aturan yang cocok dengan jalur yang disentuh sudah dibaca (`AGENTS.md` bagian 1, `.ai/rules/index.md`)
- [x] Skill bagian 5 yang berlaku disebut dan dipakai
- [x] Orang yang menemui perubahan ini dan saat ia paling mungkin bingung disebut, dan ditangani (prinsip di awal `AGENTS.md`)
- [x] Di luar cakupan dihormati; temuan di luar tugas dilaporkan, tidak diperbaiki sambil lalu

Desain

- [x] Dampak perubahan fungsi, route, kolom, atau kontrak dicari (pemanggil, layar, dokumen, test) sebelum diubah
- [x] Nama mengikuti aturan bahasa dan URL sepadan judul layar (`.ai/rules/general.md`)
- [x] Siapa yang boleh diputuskan sebelum validasi, dan setiap batas masukan eksplisit (`.ai/rules/php.md`, pertanyaan 1 dan 2)
- [x] Setiap hasil punya satu jawaban, dan pesan untuk orang berupa kalimat Indonesia (`.ai/rules/php.md`, pertanyaan 3)
- [x] Konkurensi, sisa kegagalan, dan biaya ditangani (`.ai/rules/php.md`, pertanyaan 4 sampai 6)
- [x] Kode mudah dibaca, bisa ditingkatkan, bisa diperluas, dan bisa membesar seperti diminta `.ai/rules/php.md`, "Mutu desain"
- [x] Tidak ada yang bocor, dan perubahan tercatat di audit (`.ai/rules/php.md`, pertanyaan 7 dan 8; `.ai/rules/audit-log.md`); migrasi baru, tidak pernah disunting (`.ai/rules/database.md`) N/A: halaman acuan statis tanpa data pribadi dan tanpa perubahan data; tidak ada migrasi di perubahan ini
- [x] Tidak ada status berkas baru tanpa dibahas (`CLAUDE.md`)

Layar (N/A tanpa layar)

- [x] Keadaan (memuat, kosong, galat), hint isian sebagai tooltip, format, kata, dan konfirmasi aksi tak terbalikkan mengikuti `.ai/rules/js.md`
- [x] Diperiksa di peramban pada 1280 px dan 375 px, konsol bersih, tanpa gulir horizontal halaman
- [x] Delivery Gate antislop dijawab sebagai baris PASS/FAIL dengan bukti di laporan

Test dan dokumen

- [x] Tiap risiko dan tiap penolakan punya test (`.ai/rules/php.md`, pertanyaan 9; `.ai/rules/tests.md`), dan test penjaga hijau
- [x] Dokumen dan kode dibandingkan dua arah, teks basi diperbaiki, `memory/PRD.md` dan tabel arsitektur `CLAUDE.md` diperbarui
- [x] Catatan yang disimpan agent untuk dirinya sendiri (memorinya, bila ada) diperbarui dengan apa yang diputuskan atau dipelajari perubahan ini, tanpa menunggu diminta

Gerbang dan penutup

- [x] `scripts/verify.sh` hijau (Pint, PHPStan, semua test, `npm run check`, tipe, build)
- [x] Setiap kriteria penerimaan di atas diverifikasi dengan bukti di laporan
- [x] Pemilik diberi tahu apa yang berubah, apa yang tidak dikerjakan, dan mengapa; brief ini dihapus di commit penutup
