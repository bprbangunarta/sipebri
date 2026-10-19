---
paths:
    - 'database/migrations/**'
---

# Skema database

## Migrasi tidak diubah

Buat migrasi dengan `php artisan make:migration`. Jangan pernah menyunting migrasi yang sudah dijalankan di lingkungan mana pun, termasuk database lokal pengembang lain: buat migrasi baru (`Schema::rename()`, migrasi `alter`, ...). Migrasi boleh disunting di tempat hanya dalam sesi yang sama, sebelum dijalankan di mana pun yang dipakai bersama. Database tidak pernah direset (`CLAUDE.md`).

## Aman di-deploy

Migrasi bekerja dengan kode yang berjalan sebelum deploy maupun kode baru (tambah dulu, pindahkan data, hapus belakangan di rilis terpisah). Migrasi yang menghancurkan data atau tidak bisa dibatalkan (drop, truncate, kolom yang menyusut) dibicarakan dengan pemilik sebelum ditulis. Seeder dan factory hanya berisi data karangan: tidak pernah salinan produksi, dan tidak pernah password nyata atau bawaan. Seeder idempoten (diulang hanya menambah yang belum ada dan tidak menimpa yang diubah Super Admin di layar).

## Penamaan

Tabel dan kolom `snake_case` berbahasa Inggris: tabel jamak, kolom tunggal. Hanya nilai yang dilihat orang yang berbahasa Indonesia. Nama indeks MySQL paling banyak 64 karakter: beri nama eksplisit pada indeks panjang.

## Relasi

Setiap kolom relasi baru punya FK dengan aturan hapus yang disengaja (`restrictOnDelete` untuk data referensi, `cascadeOnDelete` hanya untuk anak yang tak berarti tanpa induk, `nullOnDelete` untuk penanda pembuat/petugas). Relasi lewat kode string dijaga di aplikasi (`.ai/rules/general.md`, "Integritas relasi").
