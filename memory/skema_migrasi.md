# Modul Skema Migrasi (`/schema-drafts`) — alat developer

Dibuat 21/06/2026 atas permintaan user: **rancang dulu struktur tabel di UI, tinjau bersama,
baru agent menulis migration**. Setiap tabel/menu baru wajib melewati alur ini.

## Prinsip
- Modul **hanya membaca** skema database. TIDAK menulis file migration, TIDAK menjalankan migration.
  (Keputusan user: pratinjau kode saja, agent yang menulis & menjalankan.)
- Cakupan: rancangan tabel yang dibuat sendiri di modul ini (bukan impor otomatis semua tabel).

## Struktur
- `schema_drafts`: `name`, `table_name` (unik, huruf kecil), `note`, flag `with_id`,
  `with_timestamps`, `with_soft_deletes`.
- `schema_draft_columns`: `sort`, `name`, `type`, `length`, `is_nullable`, `default_value`,
  `is_unique`, `is_index`, `foreign_table`, `comment`.
- Kode: `app/Http/Controllers/SchemaDraftController.php`, `app/Support/SchemaDesign.php`,
  `resources/js/pages/SchemaDrafts.vue`, `resources/js/pages/SchemaDraftDetail.vue`.
- Izin: `schema-drafts.view` / `schema-drafts.manage` (menu area Administrator).
- Seeder: `SchemaDraftSeeder` — rancangan `collateral_simulations` dicerminkan dari skema nyata
  (diff selalu bersih setelah seeding), `credit_applications` sebagai rangka Pengajuan Kredit.

## Tab pada halaman rancangan
1. **Kolom** — geser (HTML5 drag) untuk urutan (`PUT /schema-drafts/{id}/reorder`), tambah/ubah/hapus kolom.
2. **Diff** — bandingkan rancangan vs `Schema::getColumns()` + `Schema::getIndexes()`:
   status `baru` / `berubah` / `dihapus` / `sama`. Yang dibandingkan: tipe, nullable, default,
   unique, index. **Belum** dibandingkan: panjang kolom, komentar, urutan fisik, relasi FK.
3. **Migration** — pratinjau `Schema::create` (tabel belum ada) atau `Schema::table` dengan blok
   kolom baru / berubah (`->change()`) / dihapus, plus saran nama file.

## Batasan yang perlu diingat
- SQLite tidak bisa memindah kolom tanpa membangun ulang tabel → urutan kolom di rancangan
  dianggap acuan urutan tampilan form/tabel, bukan urutan fisik di database.
- Untuk perubahan nama kolom, tulis migration `renameColumn` agar data lama tetap utuh
  (contoh: `2026_08_21_020000_rename_collateral_simulation_columns.php`).
- Route tulis dibungkus `->scopeBindings()` agar `{schemaDraft}/{column}` tidak bisa dicampur.
