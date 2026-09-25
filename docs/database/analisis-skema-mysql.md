# Analisis Skema MySQL Existing SIPEBRI

Dokumentasi identifikasi **EXISTING versi 1.0**, tanggal **2026-09-16**. Dokumen ini mencatat struktur ekspor, perilaku yang terbaca pada source, keterangan pengguna, dan batas bukti untuk [identifikasi sistem existing](../README.md). Cakupannya hanya kondisi existing, bukan rencana perubahan atau laporan hasil import oleh asisten.

**[B]** menandai keterangan pengguna, sedangkan hasil inspeksi source/DDL merupakan bukti statis, bukan verifikasi runtime. Rujukan silang tersedia pada [temuan dan risiko existing](../temuan-dan-risiko.md) serta [bukti, batas pemeriksaan, dan traceability](../validasi-dan-traceability.md).

## 1. Provenance dan batas pemeriksaan

| Atribut                 | Bukti                                                                                                                                                                       |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Artefak dari pengguna   | ekspor schema-mysql.sql dari pengguna (berkas mentah tidak disimpan di repository)                                                                                                                                        |
| Ukuran saat diperiksa   | 154.222 byte; 2.328 baris menurut pemisahan baris lokal                                                                                                                     |
| SHA-256                 | `ABA07B34AD2E31B8E86B5698FD9069FCCC065C19A38A1BC22E82FBDF4DBEB586`                                                                                                          |
| Metadata ekspor         | Navicat; MySQL 8.0.45; waktu ekspor 16 September 2026 pukul 15:18:05, zona waktu tidak disebutkan                                                                           |
| Source pembanding       | Commit `b12bf32019fd0b23570065dcc7a9954bfbf004a1`                                                                                                                           |
| Metode                  | Inspeksi DDL, inventaris nama objek/constraint, dependensi view, dan perbandingan jalur kode terpilih                                                                       |
| Asal ekspor [B]         | Pengguna mengonfirmasi ekspor diambil langsung dari database produksi, 16 September 2026                                                                                    |
| Lingkungan proyek [B]   | Database yang terhubung pada proyek adalah lokal dan sudah direstore oleh pengguna; target koneksi efektif tidak diperiksa oleh asisten                                     |
| Automation database [B] | Pengguna tidak menggunakan trigger, stored procedure/function, dan event database                                                                                           |
| Status `on_current` [B] | Flag serah-terima CBS lama, diisi setelah pengajuan dimasukkan dan rekening kredit dibuat; kini tidak digunakan bisnis. Kolom dan filter residual masih ada pada source/DDL |
| API dropping CBS baru   | Integrasi pengiriman data siap realisasi melalui API CBS baru untuk dropping tidak tersedia pada source yang diidentifikasi                                                 |
| Batas verifikasi        | Versi source yang berjalan di produksi, perubahan setelah ekspor/restore, SQL mode efektif, dan isolasi layanan eksternal tidak diverifikasi dalam pemeriksaan statis ini   |

**Asisten tidak mengubah, mengimpor, atau mengeksekusi berkas SQL.** Pengguna telah melakukan restore lokal sebelumnya; tidak ada koneksi MySQL/SQL Server, migration, seeder, atau test aplikasi yang dijalankan oleh asisten. Restore lokal tidak diasumsikan anonim/disposable dan tidak boleh dijadikan target reset otomatis. Eksekusi SQL destruktif maupun test terhadap restore tidak diizinkan dalam pekerjaan dokumentasi ini. Isolasi SQL Server/Codex/Sheets dan efek samping layanan lain tidak tercakup dalam bukti yang tersedia.

### Pemeriksaan privasi dan keselamatan

- Tidak ditemukan pernyataan baris `INSERT`, `REPLACE INTO`, `LOAD DATA`, pembuatan akun/grant, penanda private key, atau klausa eksplisit `DEFINER=akun` dalam pemeriksaan pola dan DDL terarah. Ini bukan sertifikasi bahwa setiap literal bebas informasi sensitif.
- **Header masih memuat alamat server internal dan nama schema sumber.** Default tertentu juga memuat kode petugas. Nilainya sengaja tidak disalin ke laporan ini. Samarkan metadata tersebut pada salinan yang akan dibagikan/di-commit ke repository bersama atau publik; keberadaan schema-only tidak menjamin anonim.
- Semua 26 view memakai **`SQL SECURITY DEFINER`**. Tidak adanya `DEFINER=akun` bukan berarti mode eksekusi `INVOKER`; identitas definer dan privilege efektif pada database hasil restore tidak diverifikasi.
- Ada `DROP TABLE`, `DROP VIEW`, dan penonaktifan sementara pemeriksaan foreign key. **Jangan menjalankan dump ini pada database existing.**
- Counter `AUTO_INCREMENT` bukan jumlah baris. Tanpa data tabel tidak dapat dihitung jumlah nasabah, duplikasi aktual, orphan, kualitas nilai, atau volume transaksi.
- Tidak ada definisi trigger, stored procedure/function, atau event pada berkas; pengguna mengonfirmasi objek automation tersebut **tidak digunakan**. Keterangan ini tidak berarti tidak ada layanan/job eksternal yang menulis data. Fungsi bawaan SQL pada view, misalnya `CURDATE()`, tetap ada dan bukan stored function buatan pengguna.

## 2. Inventaris fisik ekspor

| Jenis                               | Jumlah / hasil                                                                            |
| ----------------------------------- | ----------------------------------------------------------------------------------------- |
| Tabel nyata                         | **104**; tidak ada nama tabel placeholder yang bertumpang tindih dengan view              |
| View                                | **26**                                                                                    |
| Tabel RSC                           | **24**, termasuk dalam 104 tabel                                                          |
| Foreign key                         | **4**, seluruhnya pada pivot permission/role                                              |
| Engine / collation tabel            | Seluruh deklarasi memakai InnoDB, `utf8mb4_unicode_ci`, row format Dynamic                |
| Tipe `DECIMAL/NUMERIC/FLOAT/DOUBLE` | Tidak ditemukan pada deklarasi kolom                                                      |
| `CHECK` constraint                  | Tidak ditemukan                                                                           |
| Dependensi nama view                | Seluruh nama objek langsung pada `FROM/JOIN` terpetakan ke ekspor; tidak ditemukan siklus |

Penelusuran nama literal tabel/view lokal pada jalur Query Builder, SQL dan mapping model yang diperiksa tidak mengonfirmasi objek lokal yang hilang. **Ini tidak membuktikan kesesuaian seluruh kolom/query.** Koneksi `sqlsrv` dan target tabel dinamis tidak dicampurkan ke pemeriksaan cakupan MySQL ini.

### 2.1 Daftar 104 tabel

| Kelompok                      | Jumlah | Nama tabel                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           |
| ----------------------------- | -----: | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| 5C                            |      4 | `a5c_capacity`, `a5c_character`, `a5c_collateral`, `a5c_condition`                                                                                                                                                                                                                                                                                                                                                                                                                                                                   |
| Analisa tambahan/dokumen      |      7 | `a_administrasi`, `a_dokumen`, `a_kebutuhan_dana`, `a_komite`, `a_memorandum`, `a_swot`, `a_tambahan`                                                                                                                                                                                                                                                                                                                                                                                                                                |
| Header analisa usaha/keuangan |      6 | `au_jasa`, `au_keuangan`, `au_kualitatif`, `au_lainnya`, `au_perdagangan`, `au_pertanian`                                                                                                                                                                                                                                                                                                                                                                                                                                            |
| Detail biaya                  |      5 | `bu_bahan_baku_lainnya`, `bu_keuangan`, `bu_lainnya`, `bu_perdagangan`, `bu_pertanian`                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| Detail usaha                  |      3 | `du_lainnya`, `du_perdagangan`, `du_pertanian`                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       |
| Data/master kredit            |     29 | `data_alasan_penolakan`, `data_berkas`, `data_dati`, `data_dokumen`, `data_dokumen_ceklis`, `data_jadwal_survei`, `data_jaminan`, `data_jenis_agunan`, `data_jenis_dokumen`, `data_kantor`, `data_kepemilikan`, `data_metode_rps`, `data_nasabah`, `data_notifikasi`, `data_pekerjaan`, `data_penanggungjawab`, `data_pendamping`, `data_pendidikan`, `data_pengajuan`, `data_penolakan`, `data_produk`, `data_prosfek`, `data_realisasi`, `data_resort`, `data_spk`, `data_survei`, `data_tabungan`, `data_tracking`, `data_usulan` |
| RSC                           |     24 | `rsc_agunan`, `rsc_analisa_keuangan`, `rsc_au_jasa`, `rsc_au_lain`, `rsc_au_perdagangan`, `rsc_au_pertanian`, `rsc_bahan_baku_lain`, `rsc_biaya`, `rsc_bu_perdagangan`, `rsc_bu_pertanian`, `rsc_data_asuransi`, `rsc_data_jaminan`, `rsc_data_pengajuan`, `rsc_data_spk`, `rsc_data_survei`, `rsc_data_usulan`, `rsc_du_perdagangan`, `rsc_kondisi_usaha`, `rsc_notifikasi`, `rsc_pendapatan_lain`, `rsc_pengeluaran_lain`, `rsc_penolakan`, `rsc_spk`, `rsc_syarat_tambahan`                                                       |
| Referensi BI/SLIK             |      9 | `bi_golongan_debitur`, `bi_golongan_debitur_slik`, `bi_golongan_penjamin`, `bi_jenis_usaha`, `bi_penggunaan_debitur`, `bi_sektor_ekonomi`, `bi_sektor_ekonomi_slik`, `bi_sifat`, `bi_sumber_dana_pelunasan`                                                                                                                                                                                                                                                                                                                          |
| Log, identitas dan sistem     |     17 | `failed_jobs`, `log_kemampuan_keuangan`, `log_persetujuan`, `log_taksasi_jaminan`, `migrations`, `model_has_permissions`, `model_has_roles`, `password_reset_tokens`, `password_resets`, `pengembalian_berkas`, `permissions`, `personal_access_tokens`, `role_has_permissions`, `roles`, `sessions`, `sites`, `users`                                                                                                                                                                                                               |

### 2.2 Foreign key yang benar-benar dideklarasikan

| Sumber                                | Referensi        | Aksi                            |
| ------------------------------------- | ---------------- | ------------------------------- |
| `model_has_permissions.permission_id` | `permissions.id` | DELETE CASCADE; UPDATE RESTRICT |
| `model_has_roles.role_id`             | `roles.id`       | DELETE CASCADE; UPDATE RESTRICT |
| `role_has_permissions.permission_id`  | `permissions.id` | DELETE CASCADE; UPDATE RESTRICT |
| `role_has_permissions.role_id`        | `roles.id`       | DELETE CASCADE; UPDATE RESTRICT |

**Tidak ada foreign key domain kredit/RSC dalam ekspor.** Empat constraint di atas merupakan relasi fisik yang dideklarasikan. Relasi logis bisnis pada source mencakup `data_pengajuan.nasabah_kode → data_nasabah.kode_nasabah`, `data_pengajuan.kode_pengajuan → pengajuan_kode` pada tabel terkait, dan kode petugas yang merujuk `users.code_user`. Join tersebut bergantung pada konsistensi aplikasi/data, bukan jaminan FK. `model_id` polymorphic, `sessions.user_id`, dan `data_spk.pj_id` juga tidak memiliki FK pada ekspor.

## 3. Default dan domain workflow existing

| Objek / kolom                        | Deklarasi pada ekspor                                         | Makna pada struktur existing                                                                                                                                                                  |
| ------------------------------------ | ------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `data_pengajuan.status`              | ENUM NOT NULL, default `Lengkapi Data`                        | Anggota: `Lengkapi Data`, `Minta Otorisasi`, `Sudah Otorisasi`, `Batal`, `Disetujui`, `Ditolak`, `Dibatalkan`                                                                                 |
| `data_pengajuan.tracking`            | ENUM NOT NULL, default `Verifikasi Data`                      | Anggota: `Verifikasi Data`, `Penjadwalan`, `Proses Survei`, `Proses Analisa`, `Persetujuan Komite`, `Naik Kasi`, `Naik Komite I`, `Naik Komite II`, `Naik Komite III`, `Realisasi`, `Selesai` |
| `data_pengajuan.kategori`            | ENUM NOT NULL, default `BARU`                                 | `BARU`, `TOPUP`, `RELOAN`, `RSC`, `KARYAWAN`                                                                                                                                                  |
| `data_pengajuan.kondisi_khusus`      | ENUM nullable, default NULL                                   | `PERLELEAN`, `PERPADIAN`; berbeda dari kategori                                                                                                                                               |
| `data_pengajuan.status_pengembalian` | ENUM nullable, default `TIDAK`                                | `YA`, `TIDAK`, `DONE`                                                                                                                                                                         |
| `data_pengajuan.otorisasi`           | ENUM `N`/`A`, NOT NULL, default `N`                           | Default DDL berlaku ketika kolom tidak disertakan pada insert                                                                                                                                 |
| `data_pengajuan.is_entry/on_current` | Masing-masing VARCHAR(1), NOT NULL, default `'0'`             | Bukan tipe boolean atau constraint yang hanya mengizinkan 0/1                                                                                                                                 |
| `data_nasabah.otorisasi/is_entry`    | VARCHAR(255) default `N`; VARCHAR(1) default `'0'`            | Otorisasi nasabah bukan ENUM seperti beberapa bagian lain                                                                                                                                     |
| `data_pendamping` dan `data_survei`  | `otorisasi` ENUM default `N`; `is_entry` default `'0'`        | Kolom identitas/penugasan tertentu tetap NOT NULL tanpa default                                                                                                                               |
| `data_jaminan`                       | `otorisasi=N`, `is_entry='1'`, `on_current='0'`, `status='1'` | Default entri agunan berbeda dari pengajuan/nasabah                                                                                                                                           |
| `data_spk.otorisasi/pj_id`           | ENUM default `N`; BIGINT UNSIGNED default `1`                 | `auth_user` nullable; default tidak membuktikan keberadaan baris master penanggung jawab                                                                                                      |
| `data_realisasi.status`              | **Tidak ada**                                                 | Bertentangan dengan penulisan pada `konfirmasi_realisasi()`                                                                                                                                   |
| `data_tracking`                      | Tujuh timestamp milestone nullable default NULL               | Tidak ada pemeriksaan urutan waktu atau histori kejadian append-only                                                                                                                          |

`Proses Survey` bukan anggota ENUM tracking yang diekspor. Ketidaksesuaian pembacaan dashboard juga terbukti terhadap domain DDL. Default `'0'` pada `on_current` tetap fakta DDL, tetapi pengguna telah menjelaskan sejarahnya: **CBS lama** mengisi flag setelah pengajuan masuk dan nomor rekening kredit dibuat. **Kini `on_current` tidak digunakan bisnis**; kolom/filter legacy masih terdapat pada source dan ekspor. Keterangan non-use bisnis tidak berarti filter residual tidak berjalan. Nilai historis tersebut bukan bukti tersendiri bahwa dana cair atau status API CBS baru.

## 4. Kunci bisnis, keunikan, dan relasi

Tabel inti umumnya mempunyai primary key `id BIGINT UNSIGNED AUTO_INCREMENT`; `data_jadwal_survei.id` memakai INT. Kunci bisnis berikut terpisah dari PK tersebut.

| Tabel                                   | Unique index / batas relasi penting                                                                                                                                                  |
| --------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `data_pengajuan`                        | `kode_pengajuan VARCHAR(8)` unik; `no_loan` nullable unik. `nasabah_kode` tidak memiliki indeks/FK dalam ekspor                                                                      |
| `data_nasabah`                          | Unique terpisah pada `kode_nasabah`, **`no_identitas`**, dan `no_cif` nullable; bukan unique gabungan identitas+tanggal lahir                                                        |
| `data_pendamping`, `data_jadwal_survei` | `pengajuan_kode` nullable unik                                                                                                                                                       |
| `data_survei`, `data_realisasi`         | `pengajuan_kode` NOT NULL unik                                                                                                                                                       |
| `data_spk`                              | Unique terpisah pada **`no_spk`** dan **`pengajuan_kode VARCHAR(8)`**; `nomor` bukan unique                                                                                          |
| `data_tracking`, `a_komite`             | Kode masing-masing unik dan `pengajuan_kode` unik; snapshot per pengajuan, bukan banyak event                                                                                        |
| `data_jaminan`                          | `no_registrasi` nullable unik; `pengajuan_kode` tidak unik/tidak diindeks; banyak agunan per pengajuan diperbolehkan                                                                 |
| `data_notifikasi`                       | `nomor` dan `no_notifikasi` unik, **`pengajuan_kode` tidak unik**; cek duplikasi aplikasi bukan constraint DB ekuivalen                                                              |
| `data_usulan`                           | Hanya PK `id`; pasangan pengajuan+petugas/role tidak unik                                                                                                                            |
| `data_berkas`                           | `pengajuan_kode` unik; tidak mendukung banyak baris per pengajuan sebagai ledger perpindahan                                                                                         |
| `users`                                 | `email` dan `code_user CHAR(3)` unik, `username` nullable unik; default `code_user` berupa literal konstan (nilai tidak disalin), sehingga tidak dapat dipakai banyak user sekaligus |
| `rsc_spk`, `rsc_data_spk`               | Masing-masing punya unique `kode_rsc`, tetapi merupakan tabel berbeda                                                                                                                |

Unique pada child berarti **maksimal satu baris berkunci sama**, bukan “setiap pengajuan wajib memiliki satu child”. Unique nullable di MySQL mengizinkan banyak NULL; string kosong bukan NULL. Tanpa FK, keberadaan parent tidak dijamin. Unique key juga **tidak membuat proses baca-counter-lalu-increment atomik**: benturan dapat berubah menjadi error duplicate-key, bukan dua baris identik yang tersimpan.

## 5. Representasi nilai uang dan tanggal

- `data_pengajuan.plafon/temp_plafon` dan nilai agunan menggunakan VARCHAR; bunga/biaya persentase memiliki lebar string berbeda antara pengajuan dan usulan. Nilai `'00.10'` adalah default literal `b_denda`, bukan bukti interpretasi satuannya.
- `a_memorandum`/`a_kebutuhan_dana` memakai BIGINT untuk sebagian nominal; `a_administrasi` dan bagian analisa lain memakai signed INT. Signed INT mempunyai batas atas 2.147.483.647; dampaknya bergantung satuan dan nilai aktual.
- Tidak ditemukan tipe fixed-point DECIMAL ataupun CHECK untuk nilai positif, format tanggal/angka atau urutan milestone. Deklarasi tipe saja tidak membuktikan satuan nominal, ketelitian angka, aturan pembulatan, atau konsistensi nilai historis.
- Tanggal lahir nasabah adalah VARCHAR(8), pendamping VARCHAR(255); kode memformat `Ymd`. Masa identitas memakai sentinel string `99999999`. Tanggal agunan tertentu juga VARCHAR(8).
- Jadwal survei/resurvei memakai enam kolom DATE nullable; milestone memakai TIMESTAMP nullable. Kolom timestamp inti yang diperiksa tidak mempunyai `ON UPDATE CURRENT_TIMESTAMP`.

## 6. View: dependensi dan aturan tersembunyi

### 6.1 Peta seluruh view

| View                                     | Sumber langsung                                                                                                                                                                   |
| ---------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `da_kendaraan`, `da_lainnya`, `da_tanah` | `data_jenis_dokumen`                                                                                                                                                              |
| `ja_kendaraan`, `ja_lainnya`, `ja_tanah` | `data_jenis_agunan`                                                                                                                                                               |
| `data_droping`                           | `data_pengajuan`, `data_nasabah`, `data_survei`, `users`, `data_kantor`, `data_notifikasi`, `data_spk`, `data_tracking`                                                           |
| `send_cif`                               | `data_pengajuan`, `data_nasabah`, `data_pendamping`, `data_notifikasi`                                                                                                            |
| `send_jaminan`                           | `data_jaminan`, `data_pengajuan`, `data_nasabah`, `data_survei`, `data_jenis_agunan`, `data_jenis_dokumen`, `data_spk`, `users`                                                   |
| `send_kredit`                            | `data_pengajuan`, `data_nasabah`, `data_produk`, `data_spk`, `data_survei`, `data_kantor`, `users`, `data_metode_rps` (dua join), `a_administrasi`, `a_memorandum`, `data_resort` |
| `total_bahan_baku`                       | `au_lainnya`, `bu_bahan_baku_lainnya`, `data_pengajuan`, `data_nasabah`                                                                                                           |
| `v_agunan`                               | Sumber `send_jaminan` kecuali `data_spk`                                                                                                                                          |
| `v_dati`, `view_dati`                    | `data_dati`                                                                                                                                                                       |
| `v_kabupaten`                            | `v_dati`, `data_nasabah`                                                                                                                                                          |
| `v_kecamatan`, `v_kelurahan`             | `data_nasabah`                                                                                                                                                                    |
| `v_resort`                               | `data_resort`                                                                                                                                                                     |
| `v_tabungan`, `view_tabungan`            | `data_tabungan`                                                                                                                                                                   |
| `v_users`                                | `users`, `model_has_roles`, `roles`                                                                                                                                               |
| `v_validasi_pengajuan`                   | `data_pengajuan`, `data_nasabah`, `data_pendamping`, `data_survei`                                                                                                                |
| `view_jaminan`                           | `data_spk`, `data_pengajuan`, `data_jaminan`                                                                                                                                      |
| `view_penggunaan`                        | `data_spk`, `data_pengajuan`, `data_nasabah`, `data_pendamping`                                                                                                                   |
| `view_realisasi`                         | `data_pengajuan`, `data_spk`, `data_nasabah`                                                                                                                                      |
| `view_spk`                               | Sumber `send_kredit` kecuali `data_resort`; `data_metode_rps` satu join                                                                                                           |

Satu-satunya dependensi view-ke-view yang ditemukan adalah `v_kabupaten → v_dati`. Resolusi nama hanya membuktikan keberadaan objek dalam ekspor; validitas eksekusi query, privilege efektif, SQL mode, dan cardinality hasil tidak diverifikasi runtime.

### 6.2 Semantik yang memengaruhi proses

- **`v_users`:** user tanpa role tidak muncul karena join lanjutan ke roles; multi-role dapat menghasilkan beberapa baris. Tidak memfilter `model_type`, active/deleted user, atau guard. Caller `first()` mengambil satu baris hasil; definisi view sendiri tidak menetapkan prioritas role efektif.
- **`v_validasi_pengajuan`:** memeriksa empat `is_entry=1`, bukan otorisasi/agunan. Inner join membuat child yang hilang menghasilkan **tidak ada baris**, bukan baris berlabel invalid. Controller mempunyai pemeriksaan agunan tersendiri pada jalur tampilan.
- **`v_tabungan/v_resort`:** proyeksi tabel lokal; bukan view langsung ke SQL Server. Jalur salinan CGC ada di controller; frekuensi dan pelaksanaan sinkronisasi periodik tidak tercakup dalam bukti statis.
- **`send_cif`:** filter disetujui dan CIF NULL; **`send_jaminan`:** disetujui, CIF tidak NULL, `on_current` agunan 0; **`send_kredit`:** disetujui dan `on_current` pengajuan 0. Ini definisi/filter legacy yang masih tersedia, bukan kebutuhan bisnis terkini atau kontrak API CBS baru. Ketiganya tidak menetapkan `on_current=1`.
- Tanggal tertentu pada `send_jaminan/send_kredit` berasal dari **`CURDATE()`** plus tenor, bukan tanggal akad tersimpan. Pembacaan di hari berbeda dapat menghasilkan tanggal berbeda tanpa update row sumber.
- `send_kredit` melakukan dua join metode; nama metode tidak unique pada ekspor, sehingga lebih dari satu baris metode yang cocok dapat memperbanyak row hasil. Format bunga menambahkan `'.00'` pada input string; operasi ini menggabungkan string, bukan memformat ulang angka desimal secara numerik. Nilai hasil aktual tidak diperiksa.
- **`data_droping` adalah view**, menampilkan `on_current AS droping` dan milestone, tidak membaca `data_realisasi` atau menyaring status. Penulis flag pengajuan pada integrasi historis diketahui dari pengguna: CBS lama. Keberadaan view/query residual bukan bukti penggunaan bisnis flag saat ini. Frekuensi dan pemakaian operasional setiap view tidak tercakup dalam pemeriksaan statis. API dropping CBS baru tidak tersedia pada source yang diidentifikasi.
- **`view_realisasi`:** disetujui + SPK terotorisasi, tanggal memakai `data_spk.updated_at`. Itu bukan bukti konfirmasi pencairan. `view_spk` juga menggunakan timestamp SPK dengan fallback tanggal sekarang.

## 7. Ketidaksesuaian source–DDL dan observasi existing

### R-22 — Kolom status realisasi tidak tersedia

`DataCetakController::konfirmasi_realisasi()` lebih dahulu menjalankan update `data_realisasi.status='A'` dalam transaksi. DDL tabel tersebut **tidak mendefinisikan `status`**. Jika source ini dijalankan persis terhadap skema ekspor, update mengalami unknown-column; update tracking/milestone berikutnya tidak tercapai, dan metode masuk penanganan error.

Ini mengoreksi prediksi v0.1 yang hanya membaca maksud penulisan source. Asal ekspor produksi sudah dikonfirmasi pengguna, sehingga konflik merupakan source lokal versus struktur yang diekspor dari produksi. **Ini bukan bukti insiden produksi.** Versi source yang dideploy, penggunaan operasional jalur konfirmasi, dan perubahan setelah ekspor/restore tidak diverifikasi. Kesimpulan unknown-column merupakan konsekuensi statis pasangan source–DDL tersebut, bukan hasil eksekusi oleh asisten.

### R-23 — Pendaftaran parsial, strict mode, dan identitas unik

`NasabahController::store()` membuat pengajuan/pendamping/survei sebelum seluruh kolom NOT NULL tanpa default terisi. Contohnya produk/metode/interval pada pengajuan, identitas pendamping, serta kantor/Kasi/surveyor pada survei.

`config/database.php` yang diperiksa menyatakan `strict=false`; dump tidak menetapkan `SQL_MODE`. MySQL non-strict dapat menggunakan nilai implisit seperti string kosong untuk kolom yang tidak diisi, sedangkan strict mode menolak. Nilai implisit itu **bukan DEFAULT yang dideklarasikan DDL**. SQL mode koneksi efektif dan hasil insert runtime tidak diverifikasi; konfigurasi source bukan bukti konfigurasi yang sedang berjalan.

Pencarian pasangan identitas+tanggal lahir di controller juga berbeda dari unique `no_identitas` tunggal. Identitas sama dengan tanggal lahir berbeda masuk cabang nasabah baru, tetapi berbenturan dengan unique key jika constraint ekspor berlaku; bukan dua nasabah valid yang pasti tersimpan.

### R-24 — Pembatalan PK melewati panjang kunci

`Administratif/DataPerjanjianKreditController::batal_perjanjian_kredit()` menambahkan `XX` ke kode yang normalnya delapan karakter, padahal `data_spk.pengajuan_kode` **VARCHAR(8)**. Strict mode menolak panjang berlebih; non-strict berpotensi memotong suffix sehingga hubungan lama tetap ada. Reset agunan dilakukan sebelum transaksi pembaruan SPK sehingga ada risiko perubahan parsial.

`DroppingController::hapus_spk()` berbeda: **mengganti** dua karakter awal, tidak menambah panjang, tetapi tetap mengubah kunci relasi dan dapat berbenturan dengan unique key lain. Temuan panjang berlebih pada jalur administratif tidak berlaku dengan alasan yang sama pada jalur penggantian prefix. Dampak aktual kedua jalur tidak diverifikasi runtime.

### R-25 / R-26 — Observasi konseptual integritas dan semantik view

**R-25:** tidak adanya FK bisnis, indeks join tertentu, dan keunikan pengajuan pada notifikasi/usulan mencatat batas jaminan struktur. Orphan, multiplikasi row, dan benturan penulisan serentak merupakan risiko konseptual yang bergantung data serta jalur aplikasi; bukan bukti kerusakan data atau gangguan performa aktual.

**R-26:** mode `SQL SECURITY DEFINER`, tanggal dinamis, pemilihan role, dan agregasi merupakan semantik view existing. `v_kabupaten` memilih `kode_dati` sambil mengelompokkan hanya `nama_dati`; keberterimaan query bergantung pada SQL mode termasuk `ONLY_FULL_GROUP_BY` dan dependensi fungsional yang dikenali MySQL. Kompatibilitas runtime view agregasi dan cardinality hasil tidak tercakup dalam pemeriksaan ini. **Terpetakannya semua nama dependensi bukan bukti import berhasil.**

## 8. Cakupan migrasi repository, RSC, dan data ekspor

- Enam migrasi repository tidak mencakup pembentukan seluruh 104 tabel/26 view. Ekspor merupakan bukti struktur tersendiri, bukan isi migrasi repository.
- DDL `users` mempunyai `code_user`, bukan `user_code`; kolom tambahan pada migrasi dan ekspor juga berbeda. Tabel `migrations` tanpa row tidak menunjukkan riwayat migration yang telah dijalankan.
- `sessions`, `sites`, dan `password_reset_tokens` ada dalam ekspor; `password_reset_tokens` hidup berdampingan dengan `password_resets`.
- RSC bukan hilang dari ekspor: seluruh 24 tabel terinventarisasi. `rsc_spk` dan `rsc_data_spk` tetap terpisah; perbandingan kolom/jalur RSC hanya mencakup bagian source yang diperiksa, bukan keseluruhannya. Cabang existing `simpan_spk_rsc()` masih insert, sehingga terhadap unique `rsc_data_spk.kode_rsc`, simpan ulang kunci non-null yang sama gagal duplicate-key, bukan memperbarui atau menyimpan duplikat (R-20).
- Tidak ada baris data master/role/produk/penanggung jawab atau formula eksternal dalam schema-only. Keberadaan, isi, dan konsistensi data tersebut pada database lokal maupun produksi tidak dapat disimpulkan dari ekspor ini. DDL juga tidak menjelaskan implementasi client survei di luar repository.

## 9. Sumber pembanding dan batas bukti

- Ekspor DDL schema-mysql.sql dari pengguna (berkas mentah tidak disimpan di repository): bagian `CREATE TABLE`/`CREATE VIEW` dengan nama pada tabel inventaris.
- [NasabahController](../../app/Http/Controllers/NasabahController.php): `store`.
- [DataCetakController](../../app/Http/Controllers/DataCetakController.php): `simpan_spk`, `konfirmasi_realisasi`.
- [Pembatalan PK administratif](../../app/Http/Controllers/Administratif/DataPerjanjianKreditController.php): `batal_perjanjian_kredit`.
- [DroppingController](../../app/Http/Controllers/DroppingController.php): `hapus_spk` dan pembacaan view `send_*`.
- [Konfigurasi database source](../../config/database.php): `strict`; bukan verifikasi config cache/deployment.
- [Migrasi user](../../database/migrations/2023_08_04_051330_modify_users_table.php): perbedaan kolom terhadap ekspor.
- [Indeks dokumentasi existing](../README.md), [temuan dan risiko existing](../temuan-dan-risiko.md), serta [bukti, batas pemeriksaan, dan traceability](../validasi-dan-traceability.md).

Seluruh kesimpulan dibatasi pada artefak yang diidentifikasi dan keterangan pengguna per 2026-09-16. Tidak ada verifikasi runtime, import oleh asisten, atau pemeriksaan isi database dalam pekerjaan ini; restore lokal merupakan tindakan pengguna sebelumnya.
