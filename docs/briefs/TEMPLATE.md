# Templat brief

Untuk siapa pun yang memulai fitur atau perubahan apa pun yang menyentuh perilaku, kontrak, skema, atau keamanan. Salin berkas ini ke `docs/briefs/NNNN-judul-singkat.md` (nomor berikutnya, kebab-case, Inggris), isi, tunjukkan ke pemilik di chat, dan jangan ubah apa pun sampai pemilik berkata "setuju". Berkas ini satu-satunya berkas tetap di folder ini; berkas lain di sini adalah pekerjaan yang belum selesai.

Cara brief dipakai ditulis sekali di `AGENTS.md`, bagian 3. Daftar periksa di bawah adalah satu-satunya daftar yang harus terpenuhi sebelum pekerjaan disebut selesai.

---

# Judul perubahan

Status: draft
Approved: (hanya setelah pemilik berkata "setuju" di chat untuk brief ini: tulis tanggalnya, YYYY-MM-DD)

## Tujuan

Satu atau dua kalimat: apa yang bisa dilakukan orang yang memakainya sesudahnya, dan mengapa.

## Kriteria penerimaan

Tiap baris bisa dijawab "terpenuhi" atau "tidak" dengan bukti (test, perintah, layar yang diperiksa di peramban).

- [ ] ...

## Di luar cakupan

Apa yang sengaja tidak disentuh perubahan ini.

## Asumsi dan pertanyaan terbuka

Bawaan yang dipilih untuk hal kecil, dan pertanyaan untuk pemilik (tidak ada yang tersisa saat brief disetujui).

## Daftar periksa

Setiap butir dicentang `[x]`; butir yang tidak berlaku dicentang dan diakhiri `N/A: alasan`. Tidak ada yang "selesai" selama masih ada `[ ]`. Aturan di balik tiap butir ada di berkas yang disebutnya; jangan menyalin aturannya ke sini.

Membaca dan cakupan

- [ ] PRD dan berkas aturan yang cocok dengan jalur yang disentuh sudah dibaca (`AGENTS.md` bagian 1, `.ai/rules/index.md`)
- [ ] Skill bagian 5 yang berlaku disebut dan dipakai
- [ ] Orang yang menemui perubahan ini dan saat ia paling mungkin bingung disebut, dan ditangani (prinsip di awal `AGENTS.md`)
- [ ] Di luar cakupan dihormati; temuan di luar tugas dilaporkan, tidak diperbaiki sambil lalu

Desain

- [ ] Dampak perubahan fungsi, route, kolom, atau kontrak dicari (pemanggil, layar, dokumen, test) sebelum diubah
- [ ] Nama mengikuti aturan bahasa dan URL sepadan judul layar (`.ai/rules/general.md`)
- [ ] Siapa yang boleh diputuskan sebelum validasi, dan setiap batas masukan eksplisit (`.ai/rules/php.md`, pertanyaan 1 dan 2)
- [ ] Setiap hasil punya satu jawaban, dan pesan untuk orang berupa kalimat Indonesia (`.ai/rules/php.md`, pertanyaan 3)
- [ ] Konkurensi, sisa kegagalan, dan biaya ditangani (`.ai/rules/php.md`, pertanyaan 4 sampai 6)
- [ ] Kode mudah dibaca, bisa ditingkatkan, bisa diperluas, dan bisa membesar seperti diminta `.ai/rules/php.md`, "Mutu desain"
- [ ] Tidak ada yang bocor, dan perubahan tercatat di audit (`.ai/rules/php.md`, pertanyaan 7 dan 8; `.ai/rules/audit-log.md`); migrasi baru, tidak pernah disunting (`.ai/rules/database.md`)
- [ ] Tidak ada status berkas baru tanpa dibahas (`CLAUDE.md`)

Layar (N/A tanpa layar)

- [ ] Keadaan (memuat, kosong, galat), hint isian sebagai tooltip, format, kata, dan konfirmasi aksi tak terbalikkan mengikuti `.ai/rules/js.md`
- [ ] Diperiksa di peramban pada 1280 px dan 375 px, konsol bersih, tanpa gulir horizontal halaman
- [ ] Delivery Gate antislop dijawab sebagai baris PASS/FAIL dengan bukti di laporan

Test dan dokumen

- [ ] Tiap risiko dan tiap penolakan punya test (`.ai/rules/php.md`, pertanyaan 9; `.ai/rules/tests.md`), dan test penjaga hijau
- [ ] Dokumen dan kode dibandingkan dua arah, teks basi diperbaiki, `memory/PRD.md` dan tabel arsitektur `CLAUDE.md` diperbarui
- [ ] Catatan yang disimpan agent untuk dirinya sendiri (memorinya, bila ada) diperbarui dengan apa yang diputuskan atau dipelajari perubahan ini, tanpa menunggu diminta

Gerbang dan penutup

- [ ] `scripts/verify.sh` hijau (Pint, PHPStan, semua test, `npm run check`, tipe, build)
- [ ] Setiap kriteria penerimaan di atas diverifikasi dengan bukti di laporan
- [ ] Pemilik diberi tahu apa yang berubah, apa yang tidak dikerjakan, dan mengapa; brief ini dihapus di commit penutup
