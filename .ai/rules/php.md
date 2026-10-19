---
paths:
    - 'app/**'
    - 'routes/**'
    - 'config/**'
    - 'bootstrap/**'
    - 'database/seeders/**'
    - 'database/factories/**'
---

# Kode aplikasi

Cara kode ditulis di proyek ini: standar mutu pemilik di atas panduan Laravel umum (blok Boost dan skill `laravel-best-practices`). Konsistensi dulu: ikuti pola berkas sejenis. Diambil dari codex (2026-10-19), disesuaikan dengan Inertia.

## Produksi, bukan prototipe

Tanpa tiruan, stub, atau pengganti diam-diam: tandai `// TODO: <reason>` dan beri tahu pemilik di laporan. Jangan pernah melewati gerbang (`--no-verify`, aturan lint atau tipe yang dimatikan, `@phpstan-ignore`, baseline); perbaiki akar masalahnya. `scripts/check-weakened.sh` menangkap cara-cara umum melewatinya pada setiap commit dan push: test yang dilewati atau difokuskan, komentar penekan PHPStan/TypeScript/linter, baseline PHPStan baru, berkas test yang dihapus, atau berkas test yang kehilangan kasus. Baris yang memang harus tetap memuat `weakening-allowed: <reason>`; commit yang sengaja melemahkan (fitur dibuang bersama test-nya) memuat baris `Weakened: <alasan>` di pesannya; keduanya butuh alasan nyata minimal sepuluh huruf. Utang teknis adalah keputusan sadar, dicatat di `memory/PRD.md`.

## Galat dan validasi

Tanpa `catch` kosong dan tanpa `catch (Throwable)` yang menyembunyikan kegagalan: tangkap eksepsi tersempit yang bisa ditangani, lalu catat atau lempar ulang. Satu-satunya `catch (Throwable)` yang boleh tidak mencatat adalah layanan pendamping yang tidak boleh menghalangi penyimpanan (mis. `ReverseGeocoder`), dan ia memuat komentar yang mengatakannya. Validasi di batas sistem dengan Form Request atau `$request->validate`, jawab dengan status dan pesan yang jelas (pesan standar dari `laravel-lang`, `CLAUDE.md`), tidak pernah jejak tumpukan mentah. Jangan menambah validasi atau cadangan untuk keadaan yang mustahil.

## Rahasia dan konfigurasi

Tanpa rahasia, kredensial, atau URL khusus lingkungan di kode. `env()` hanya dibaca di `config/*.php` (`ArchitectureTest`); `.env.example` selalu sinkron dengan variabel yang dipakai kode, dan `php artisan env:check` (bagian dari `scripts/verify.sh`) menolak `.env` lokal yang kekurangan variabel dari contoh. Konfigurasi yang tanpanya aplikasi tidak aman tidak diganti bawaan diam-diam yang menurunkan keamanan. Seeder tidak membawa password nyata atau bawaan.

## Standar produksi (setiap baris yang ditulis, setiap baris yang ditinjau)

Tidak ada yang ditulis atau disetujui sebagai "jalan di jalur bahagia". Sebelum kode selesai, dan lagi saat ditinjau, jawab setiap baris untuk kode yang bersangkutan (jawabannya masuk laporan; baris yang tidak berlaku menyebut alasannya):

1. **Siapa boleh melakukan ini, dan terhadap apa?** Otorisasi diputuskan sebelum validasi dan sebelum record dicari (middleware `permission:`/`role:` di route, policy, `authorize()` di Form Request), sehingga orang asing tidak tahu apa pun, bahkan apakah sebuah id ada. Seseorang tidak pernah bisa memberi, mengubah, atau menjangkau lebih dari yang ia pegang; pemohon anggota komite tidak memutus berkasnya sendiri.
2. **Masukan apa yang bisa datang?** Hilang, kosong, tipe salah, terlalu panjang, terlalu banyak, berbahaya, ganda, pengodean lain. Setiap batas eksplisit: panjang, rentang, ukuran halaman (daftar putih), jumlah baris, ukuran dan tipe berkas. Masukan berbahaya disebut menurut kelasnya: nama berkas atau jalur yang keluar dari foldernya (path traversal), alamat yang disuruh dibuka atau dialihkan server (SSRF, open redirect: daftar putih skema dan host, jangan ikuti pengalihan buta), dan apa pun yang diserahkan ke `unserialize` atau dirangkai ke query, perintah, atau template dari teks mentah (tidak pernah). Layar memblokir apa yang ditolak server (`.ai/rules/js.md`); server tidak pernah memercayai layar.
3. **Seperti apa setiap hasilnya?** Berhasil, tidak ditemukan, ditolak, tidak valid, dibatasi, terlalu besar, bentrok, gagal: masing-masing satu jawaban tetap (halaman error Inertia 403/404/419, galat validasi per kolom, toast). Pesan untuk orang adalah kalimat Indonesia; teks eksepsi, nama kelas, SQL, dan jalur tidak pernah sampai ke jawaban, log untuk orang, atau audit log.
4. **Bagaimana bila dua terjadi bersamaan, atau yang sama dua kali?** Apa pun yang diperiksa sebelum ditindak diperiksa di bawah kunci atau constraint database (unique, `lockForUpdate`, `Cache::lock`), dan mengulang permintaan aman (idempoten atau ditolak dengan jelas). Contoh: keputusan komite dan perpindahan status berkas.
5. **Apa yang ditinggalkan kegagalan?** Penulisan yang satu kesatuan berada dalam satu transaksi pendek (diulang saat deadlock); berkas, penghitung, kunci, dan baris setengah tertaut dibersihkan (foto survei di disk lampiran); orang bisa langsung mengulang.
6. **Berapa biayanya, dan bagaimana ia tumbuh?** Tanpa query di dalam perulangan (eager load atau batch), setiap daftar dipaginasi dengan batas, filter dan urutan memakai indeks (periksa dengan `EXPLAIN` bila filter atau pencarian baru muncul), pekerjaan besar mengalir atau dipotong, setiap panggilan keluar (Codex, Nominatim, S3, SMTP) punya batas waktu sambung dan total, percobaan ulang sedikit dan berjarak dan hanya untuk operasi yang aman diulang, jawaban sistem luar diperiksa sebelum dipakai, dan kegagalannya menurunkan satu fitur alih-alih menjatuhkan aplikasi; apa pun yang mengirim email atau menulis penyimpanan dibatasi per sasaran dan per waktu.
7. **Apa yang bocor?** Rahasia, data pribadi, dan keberadaan akun di jawaban, log, nilai audit, pesan galat, dan waktu. NIK tersamar (4 digit terakhir), tidak pernah masuk audit log.
8. **Apakah tercatat?** Setiap perubahan data, kejadian autentikasi, ekspor, dan baca data sensitif ada di audit log dengan siapa, apa, dan mengapa (`.ai/rules/audit-log.md`); perubahan sensitif membawa alasan. Penolakan juga tercatat (akses ditolak), dan mengulang sesuatu yang tidak mengubah apa pun (seeder) tidak menulis apa pun.
9. **Apakah setiap risiko di atas dibuktikan test?** Satu test per keputusan dan per mode kegagalan, termasuk penolakannya; test penjaga tetap hijau.

Peninjau mengajukan sembilan pertanyaan yang sama pada diff orang lain dan mengembalikan baris pertama yang gagal. "Tidak terpikir" adalah temuan, bukan alasan; bila risiko diterima alih-alih diperbaiki, ia ditulis di `memory/PRD.md` dengan alasannya.

## Perbaikan bug

Perbaiki akar masalah, lalu cari pola yang sama di tempat lain dan perbaiki juga (atau laporkan bila besar). Test regresi ditulis dulu dan dilihat gagal karena alasan yang benar, lalu lulus setelah perbaikan. Tambalan lokal yang meninggalkan bug kembar bukan perbaikan. Utamakan perbaikan struktural daripada diff kecil.

## Mutu desain

Tulis kode yang akan ditulis rekan yang cermat untuk produk yang bertahun-tahun di produksi: tidak ada yang bisa menebak dari membacanya bahwa alat yang menulisnya. Angka di bawah dijaga `DesignQualityTest`; sisanya dijaga laporan (baris "Desain" di brief) dan tinjauan.

- **Mudah dibaca dan bersih.** Satu alasan berubah per kelas. Method mengerjakan satu hal dan muat satu layar: paling banyak 80 baris (sasaran 40) dan empat tingkat sarang; berkas PHP paling banyak 400 baris, berkas TypeScript paling banyak 500. Controller tetap tipis: otorisasi, validasi, panggil, jawab; aturan dan query tinggal di model, kelas query, atau kelas `App\Support` yang dinamai menurut kerjanya (`ApprovalFlow`, `RepaymentCapacity`), dan controller tidak membaca tabel dengan `DB::table`. Nama menyebut apa benda itu atau apa kerjanya: `Helper`, `Util`, `Manager`, `Misc`, dan `Common` bukan nama. Return awal daripada kondisi bersarang; flag yang mengubah kerja method berarti dua method; enum, bukan string, untuk himpunan keadaan tertutup (`LoanStatus`, `RoleName`); tanpa kode mati.
- **Bisa ditingkatkan.** Pakai API publik framework dan titik ekstensinya, jangan internal dan jangan berkas vendor yang disalin. Pustaka dipakai lewat satu kelas milik kita (`CodexClient`, `ReverseGeocoder`), sehingga menggantinya menyentuh satu berkas. Versi tinggal di `composer.json` dan `package.json`, tidak di kode. Nilai yang boleh diubah operator dibaca dari `config/`; nilai kebijakan (batas, aturan) ditulis sekali, di config atau konstanta bernama, dengan keputusannya di `memory/PRD.md`.
- **Bisa diperluas.** Kasus baru dari sesuatu yang sudah punya keluarga ditambahkan di satu tempat: registri (`config/references.php`, `App\Support\Modules`, `Navigation::definition()`), kasus enum, atau baris config. Bila kasus ke-N berarti menyunting beberapa berkas, atau menyalin cabang lalu mengganti string, bangun registrinya dulu.
- **Bisa membesar.** Pertanyaan 6 standar produksi, ditambah: tidak ada yang disimpan di proses antar permintaan (tanpa state statis di luar konteks satu permintaan); pekerjaan yang tumbuh bersama data mengalir atau dipotong, punya batas yang dinyatakan, dan tidak memuat semuanya.
- **Ditulis orang.** Komentar mengatakan mengapa, tidak pernah apa yang dikerjakan baris berikutnya; tanpa spanduk, tanpa narasi langkah (`antislop-code`, dijaga `CommentHygieneTest`). Tanpa abstraksi untuk satu pemakaian dan tanpa pembungkus tanpa pemakai kedua; tanpa kode defensif untuk keadaan mustahil; gaya sama dengan berkas di sampingnya. Aturan yang ada dua kali diekstrak sekali (salinan kedua adalah tandanya), dan perbaikan dicari di setiap salinan. `npm run duplicates` (`jscpd`, diatur di `.jscpd.json`, bagian dari `scripts/verify.sh`) gagal pada blok salinan 20 baris atau lebih di `app` dan `resources/js`; pengulangan lebih pendek urusan peninjau.

`DesignQualityTest` juga gagal pada kelas model, support, atau enum yang bergantung pada controller atau middleware. Kode yang sudah melanggar batas sebelum 2026-10-19 tercantum di test dengan alasan minimal sepuluh huruf (utang yang jujur, dibayar saat kodenya disentuh); pengecualian baru butuh persetujuan pemilik, dan entri yang tidak lagi cocok dengan apa pun membuat test gagal sehingga daftarnya tidak membusuk.

## Komentar dan penamaan

Apa yang boleh dikatakan komentar adalah `antislop-code`, dijaga `CommentHygieneTest`; bahasanya Inggris (`.ai/rules/general.md`). Nama deskriptif; tanpa abstraksi prematur (tiga baris mirip lebih baik daripada abstraksi dini yang salah); tanpa kode mati atau dikomentari. PHPDoc untuk tipe, bentuk array, dan kontrak; komentar sebaris hanya untuk alasan tersembunyi. Komentar bawaan Laravel dan stub-nya serta komentar di migrasi yang sudah dijalankan dibiarkan apa adanya.

## Dependency dan cakupan

Jangan menambah atau mengubah dependency tanpa persetujuan. Utamakan paket yang dirawat dan hindari dependency berat untuk kebutuhan kecil. Permintaan persetujuan menyebut kebutuhan, alternatif yang ditimbang, lisensi (harus mengizinkan pemakaian di produk internal tertutup), keadaan perawatan, dan artinya bagi keamanan. `scripts/check-dependencies.sh` menjalankan `composer audit` dan `npm audit --omit=dev` pada setiap push (lewat `scripts/verify.sh`) dan berhenti bila ada temuan; tanpa jaringan ia memperingatkan bahwa audit tidak dilakukan, dan peringatan itu masuk laporan. Temuan yang tidak bisa diperbaiki ditulis sebagai risiko yang diterima di `memory/PRD.md`, tidak pernah dibungkam; buang dependency yang tidak dipakai lagi. Jangan menjalankan skrip dari sumber tak dikenal (`curl | sh`).

## Sebelum selesai

Jalankan `vendor/bin/pint --dirty --format agent` dan `vendor/bin/phpstan analyse --memory-limit=1G`, lalu baca ulang diff terhadap berkas aturan yang dipetakan skill `laravel-best-practices`.
