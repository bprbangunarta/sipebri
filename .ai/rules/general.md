---
paths:
    - '**/*'
---

# General

## Bahasa: kode Inggris, layar Indonesia

Semua penulisan kode memakai bahasa Inggris: nama class/method/variabel/kolom/route/permission, komentar, PHPDoc, pesan commit, dan nama test.
Semua teks yang tampil di layar berbahasa Indonesia, tanpa tanda pisah panjang: dashboard, Pengajuan, Jaminan, Penjadwalan, Proses Survey, Analisa Kredit, Persetujuan, seluruh menu Referensi dan Pengaturan, login, verifikasi dua langkah, profil, email OTP, halaman error, dan komponen bersama (dialog, tabel, paginasi, notifikasi). Teks baru langsung ditulis Indonesia; jangan mencampur bahasa di dalam kode. **URL/route tetap Inggris** tetapi harus sepadan dengan judul UI Indonesianya (mis. Data Resort → `/references/resorts`, Aturan khusus → `/committees/special-rules`). Label sidebar ada di `App\Support\Navigation`. Nama peran mengikuti Codex apa adanya (Indonesia) lewat `App\Enums\RoleName`; itu data, bukan bahasa kode. Aturan bahasa layar yang lebih rinci (kata, nada, tanggal dan angka): `.ai/rules/ui.md` dan `.ai/rules/js.md`.

## Standar kerja

Ringkasan yang memang berlaku di sini; semuanya sudah ada di aturan standar (`quality.md`, `laravel.md`, `ui.md`) dan **tidak mengubahnya**: bila kata-katanya berbeda, yang standar menang.

- Siap produksi: tanpa stub diam-diam, tanpa melewati gate (izin, validasi, test) demi cepat selesai. Yang belum dikerjakan dinyatakan terang-terangan.
- Validasi di batas sistem (request, API luar) dan catat kegagalannya; tanpa secret di kode, sinkronkan `.env.example` bila menambah variabel env.
- Perubahan skema hanya lewat migrasi baru; kolom snake_case, identifier Inggris.
- Perbaiki akar masalah dan cek kode lain yang memakai pola yang sama, bukan hanya gejalanya.
- Batasan di UI (max/format) selalu berpasangan dengan validasi server; pakai komponen field yang sudah ada.
- Periksa tampilan konten pada lebar yang diminta `.ai/rules/ui.md` ("Validate the screen": 1280, 768, dan 375 px).
- Saat mengadopsi sebuah referensi (proyek/dokumen lain), sebutkan bagian mana yang dipakai dan mana yang tidak.
- Memori proyek satu-satunya: `memory/PRD.md` (`AGENTS.md`, section 2).

## Integritas relasi

- Setiap kolom relasi baru wajib punya FK dengan aturan hapus yang disengaja (restrict/cascade/nullOnDelete). Untuk relasi lewat kode string (tanpa FK), daftarkan relasi `hasMany` berkunci kode di `usage` config master data dan/atau tambahkan pemeriksaan di controller yang menghapus/mengganti nama, plus test di `DataIntegrityTest`.
- Data bisnis dihapus dengan soft delete; jangan `forceDelete` berkas atau pengguna.
- Penjaga otomatis: `ArchitectureTest` gagal bila sebuah kelas memakai penulisan tanpa event model (pivot `attach/detach/sync`, `::query()->...->update/delete`, `DB::table(...)->insert/update/delete/upsert` langsung, `*Quietly`) tanpa memanggil `Audit::record()` di kelas yang sama. Bentuk lain (`Model::where(...)->update()`, rantai `DB::table(...)->where(...)->update()`, `delete()` lewat relasi) belum dikenali pemeriksa itu: tulis `Audit::record()` dan tinjau manual. Kejadian non-model yang wajib dicatat: login/logout/gagal, MFA (kirim, gagal, dipakai, kode pemulihan), ganti password, perubahan peran/izin, akses ditolak, baca data sensitif (pengajuan, jaminan, survei, cek nasabah), ekspor/verifikasi/prune audit log.
