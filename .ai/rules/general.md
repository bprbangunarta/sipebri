---
paths:
    - '**/*'
---

# General

## Bahasa: kode Inggris, UI nanti Indonesia

Semua penulisan kode memakai bahasa Inggris: nama class/method/variabel/kolom/route/permission, komentar, PHPDoc, pesan commit, dan nama test.
Hanya teks yang tampil di UI yang nantinya berbahasa Indonesia. Terjemahan UI dilakukan bertahap per layar atas permintaan user; sampai diminta, jangan menerjemahkan teks UI yang sudah ada dan jangan mencampur bahasa di dalam kode. **URL/route tetap Inggris** tetapi harus sepadan dengan judul UI Indonesianya (mis. Data Resort → `/references/resorts`, Aturan khusus → `/committees/special-rules`). Teks yang sudah Indonesia: seluruh menu Referensi dan Pengaturan (Data Kantor/Resort/Wilayah/Kredit/Agunan/Komite, Perizinan, Peranan, Pengguna, Audit Log) beserta komponen bersama (dialog, tabel, paginasi, notifikasi).
Label sidebar sudah Indonesia (lihat App\Support\Navigation). Nama peran mengikuti Codex apa adanya (Indonesia) lewat App\Enums\RoleName; itu data, bukan bahasa kode.

## Standar kerja (dari code-standards & handling-references milik user)

- Siap produksi: tanpa stub diam-diam, tanpa melewati gate (izin, validasi, test) demi cepat selesai. Yang belum dikerjakan dinyatakan terang-terangan.
- Validasi di batas sistem (request, API luar) dan catat kegagalannya; tanpa secret di kode, sinkronkan `.env.example` bila menambah variabel env.
- Perubahan skema hanya lewat migrasi baru; kolom snake_case, identifier Inggris.
- Perbaiki akar masalah dan cek kode lain yang memakai pola yang sama, bukan hanya gejalanya.
- Batasan di UI (max/format) selalu berpasangan dengan validasi server; pakai komponen field yang sudah ada.
- Periksa tampilan konten pada minimal dua lebar layar (ponsel dan desktop).
- Saat mengadopsi sebuah referensi (proyek/dokumen lain), sebutkan bagian mana yang dipakai dan mana yang tidak.
- Tidak berlaku di proyek ini: kickoff template, `memory/PRD.md`, dan skill `ui-taste`/`ui-ux-pro-max` (tidak tersedia).

## Audit log (wajib, standar OJK)

- Semua perubahan data bisnis dicatat lewat trait `App\Audit\Auditable` (model baru yang memuat data kredit/master/akses **harus** memakainya). Kejadian non-model (login, MFA, perubahan izin, akses ditolak, cek nasabah, lihat berkas) lewat `App\Audit\Audit::record()`.
- Tabel `audit_logs` append-only dengan rantai hash HMAC; jangan pernah update/delete barisnya. Verifikasi: `php artisan audit:verify` (terjadwal harian) atau tombol di halaman Audit Log.
- Rahasia tidak boleh masuk log (`Audit::REDACTED`); NIK hanya 4 digit terakhir. Aksi baru yang mengubah data lewat query builder/pivot (tanpa event model) harus mencatat manual.

## Integritas relasi

- Setiap kolom relasi baru wajib punya FK dengan aturan hapus yang disengaja (restrict/cascade/nullOnDelete). Untuk relasi lewat kode string (tanpa FK), daftarkan relasi `hasMany` berkunci kode di `usage` config master data dan/atau tambahkan pemeriksaan di controller yang menghapus/mengganti nama, plus test di `DataIntegrityTest`.
- Data bisnis dihapus dengan soft delete; jangan `forceDelete` berkas atau pengguna.
- Penjaga otomatis: `ArchitectureTest` gagal bila sebuah kelas memakai penulisan tanpa event model (pivot `attach/detach/sync`, `update/delete` massal, `DB::table()->update`, `*Quietly`) tanpa memanggil `Audit::record()`. Kejadian non-model yang wajib dicatat: login/logout/gagal, MFA (kirim, gagal, dipakai, kode pemulihan), ganti password, perubahan peran/izin, akses ditolak, baca data sensitif (pengajuan, jaminan, survei, cek nasabah), ekspor/verifikasi/prune audit log.
