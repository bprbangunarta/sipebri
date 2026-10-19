# Audit fitur 1: login Codex dan sinkron pengguna

Status: approved
Approved: 2026-10-19

## Tujuan

Login adalah pintu semua fitur lain (setiap orang, peran, dan jejak audit bermula dari sini), jadi ia fitur pertama dari 25 yang diperiksa terhadap seluruh kerangka kerja seperti yang berlaku sekarang: sembilan pertanyaan standar produksi, tinjauan masukan berbahaya, Delivery Gate antislop untuk layarnya, test, dan dokumen. Sesudahnya pemilik tahu apa yang ditemukan, apa yang diperbaiki, dan apa yang diterima, tidak hanya menganggap.

Bagian dari seri 25 fitur (satu brief per fitur, satu per satu, urutannya ditentukan pemilik: "fitur N"). Daftar fitur dan nomornya ada di `memory/PRD.md`, "Langkah berikutnya", butir 1.

## Kriteria penerimaan

Yang diperiksa (seluruh permukaan fitur): `app/Auth/{CodexAuthClient,CodexUnavailable,CodexUserSync}.php`; `app/Http/Controllers/Auth/LoginController.php` (`create`, `store`, `destroy`) dan `LoginRequest`; rute `login`, `login.store`, `logout` dan middleware `guest`/`auth` serta `redirectUsersTo` di `bootstrap/app.php`; pembatas percobaan (`RateLimiter`, 5 kali); pendengar `Login`/`Logout` di `AppServiceProvider`; kejadian audit `auth.*` dan `users.*` yang ditulis di sini (`auth.login`, `auth.logout`, `auth.login_failed`, `auth.locked_out`, `auth.password_verified`, `users.role_changed`, `users.id_migrated`); blok `codex` di `config/services.php` dan variabel `CODEX_*` di `.env.example`; konfigurasi sesi; layar `resources/js/pages/auth/login.tsx`; peralihan ke tantangan dua langkah di `LoginController` (hanya serah-terimanya; tantangan itu fitur 2); dan test `AuthTest`, bagian auth di `AuditLogTest`. Perubahan yang sudah ada di commit 405b019 (tolak jawaban Codex tanpa id/username, satu transaksi sinkron, `connectTimeout`, batas sandi 255) termasuk permukaan ini dan diperiksa seperti kode lain, bukan dianggap benar.

- [x] Sembilan pertanyaan `.ai/rules/php.md` dijawab untuk seluruh permukaan di atas dengan bukti (test, kueri, perintah yang dijalankan), jawabannya masuk laporan; butir yang tidak berlaku menyebut alasannya.
- [x] Jenis masukan berbahaya (`.ai/rules/php.md`, pertanyaan 2) dicoba terhadap `username`, `password`, `remember`, dan alamat tujuan `intended` (teks panjang, larik, null byte, Unicode dan huruf besar/kecil, spasi, bentuk email, alamat luar atau skema lain), terhadap kirim ganda, dan terhadap jawaban Codex yang bermusuhan lewat `Http::fake` (bidang hilang atau salah tipe, badan sangat besar, `success` salah, 4xx/5xx/200 aneh, lambat), tanpa pernah memanggil Codex asli.
- [x] Pembatas percobaan diserang dengan sengaja di test: kunci `username|ip` tidak bisa diakali (variasi huruf, spasi, email versus username), percobaan yang gagal karena Codex mati dihitung atau tidak (diputuskan, ditulis, diuji), batas pulih sendiri, dan satu orang tidak mengunci orang lain.
- [x] Sesi: id sesi diganti saat masuk dan setelah tantangan dua langkah (session fixation), keluar mengakhiri sesi dan token ingat-saya, dan pengguna yang dinonaktifkan di Codex berhenti pada permintaan berikutnya; masing-masing dengan test.
- [x] Setiap hasil meninggalkan tepat satu entri audit yang benar (siapa, apa, mengapa, request id) tanpa kata sandi: berhasil, sandi salah, Codex mati, jawaban tidak layak, akun tidak aktif, dikunci, keluar, perpindahan id, perubahan peran; sinkron tanpa perubahan tidak menulis apa pun. Contoh nyata tiap hasil dicek ujung ke ujung di test, termasuk penolakan.
- [x] Sinkron pengguna: cermin lokal cocok dengan Codex (nama, email, peran, kantor, NIK, aktif), tetap utuh bila gagal di tengah, dua login pertama orang baru yang sama bersamaan tidak berakhir galat (atau risikonya ditulis di PRD dengan alasan), bentrok email dan alias kantor ditangani, dan peran tak dikenal menjadi Guest dengan jejak.
- [x] Layar login diperiksa terhadap `.ai/rules/js.md` dan Delivery Gate antislop dengan baris PASS/FAIL beserta bukti: keadaan (memuat/mengirim, galat per kolom, Codex mati, akun tidak aktif), keyboard dan fokus, kontras (jalankan pemeriksanya), `autocomplete`, kata ("pengguna"), dan peramban pada 1280 px dan 375 px dengan konsol bersih. Keadaan galat diperiksa di peramban (Codex tak terjangkau atau ditolak); jalur berhasil dibuktikan feature test, karena kredensial pemilik tidak pernah diketik.
- [x] Temuan diperbaiki di perubahan ini, masing-masing dengan test yang gagal sebelum perbaikan dan lulus sesudahnya; laporan memuat tiap temuan dengan tingkat keparahan, perbaikan, dan test. Perbaikan yang butuh keputusan desain dibuat atas rekomendasi dan disebut di laporan; perbaikan yang mengubah kebijakan (mis. cara menghitung kunci) dijelaskan ke pemilik sebelum ditulis.
- [x] Tiap risiko dan tiap penolakan yang ternyata belum punya test mendapat test; test penjaga tetap hijau.
- [x] `CLAUDE.md` (baris Login dan Nasabah), `AGENTS.md`, `memory/PRD.md`, `.ai/rules/general.md`, `.ai/rules/php.md`, dan `.ai/rules/audit-log.md` dibandingkan dua arah dengan kodenya; laporan menyebut selisih yang ditemukan dan diperbaiki (atau tidak ada).
- [x] `php artisan audit:verify` hijau di akhir, setelah seluruh test dijalankan.

## Di luar cakupan

- MFA dan profil (fitur 2), peran dan izin (3), Data Pengguna (4), pencarian nasabah Codex (15); temuan yang menunjuk ke sana dicatat di PRD pada unitnya, tidak diperbaiki di sini.
- Mengubah arsitektur login (Codex sebagai satu-satunya penyedia identitas, sesi Laravel) atau menambah login lokal.
- Server Codex itu sendiri dan konfigurasi servernya.

## Asumsi dan pertanyaan terbuka

- Audit dikerjakan dengan membaca kode terhadap aturan tertulis dan mencoba merusaknya lewat test, bukan memercayai test yang sudah ada; suite hijau hanya titik awal.
- Perbaikan yang harus menyentuh kode fitur lain dibuat di sana juga bila cacatnya sama, dengan test, dan dicatat di laporan untuk kedua fitur.
- Commit 405b019 tetap berlaku sebagai kode sekarang dan diperiksa seperti lainnya; bila pemilik menolak salah satu perubahannya, saya batalkan dengan commit baru (tanpa force-push).
- **Pertanyaan untuk pemilik:** di codex pemilik memutuskan "setiap temuan diperbaiki langsung, tidak hanya dilaporkan, kecuali yang mengubah apa yang dijanjikan". Berlaku sama di sini? Usul saya ya, dengan pengecualian kebijakan (butir kedelapan di atas). Dua kebijakan yang akan saya tanyakan lebih dulu bila temuannya muncul: apakah percobaan yang gagal karena Codex mati dihitung ke batas 5 kali, dan apa yang terjadi bila `CODEX_VERIFY=false` di produksi.

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
- [x] Tidak ada yang bocor, dan perubahan tercatat di audit (`.ai/rules/php.md`, pertanyaan 7 dan 8; `.ai/rules/audit-log.md`); migrasi baru, tidak pernah disunting (`.ai/rules/database.md`): N/A: tanpa migrasi
- [x] Tidak ada status berkas baru tanpa dibahas (`CLAUDE.md`): N/A: tidak ada status baru

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
- [ ] Setiap kriteria penerimaan di atas diverifikasi dengan bukti di laporan
- [ ] Pemilik diberi tahu apa yang berubah, apa yang tidak dikerjakan, dan mengapa; brief ini dihapus di commit penutup
