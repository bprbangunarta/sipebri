<?php

use App\Models\Lain;
use App\Models\Nasabah;
use App\Models\Kepemilikan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;
use App\Http\Controllers\CGCController;
use App\Http\Controllers\RSCController;
use App\Http\Controllers\DatiController;
use App\Http\Controllers\JasaController;
use App\Http\Controllers\LainController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\AnalisaTambahan;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\AgunanController;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KomiteController;
use App\Http\Controllers\SurveiController;
use App\Http\Controllers\AnalisaController;
use App\Http\Controllers\FiduciaController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProsfekController;
use App\Http\Controllers\RSCJasaController;
use App\Http\Controllers\RSCLainController;
use App\Http\Controllers\AsuransiController;
use App\Http\Controllers\DroppingController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\RSCBiayaController;
use App\Http\Controllers\RSCCetakController;
use App\Http\Controllers\SkriningController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\Analisa5cController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataCetakController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PertanianController;
use App\Http\Controllers\UsahaJasaController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\KonfirmasiController;
use App\Http\Controllers\KualitatifController;
use App\Http\Controllers\MemorandumController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PendampingController;
use App\Http\Controllers\RSCExsportController;
use App\Http\Controllers\RSCJaminanController;
use App\Http\Controllers\RSCLaporanController;
use App\Http\Controllers\KepemilikanController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\PerdaganganController;
use App\Http\Controllers\PerhitunganController;
use App\Http\Controllers\RSCAngsuranController;
use App\Http\Controllers\RSCKeuanganController;
use App\Http\Controllers\Admin\KantorController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\ResortController;
use App\Http\Controllers\AdministrasiController;
use App\Http\Controllers\CetakAnalisaController;
use App\Http\Controllers\CetakLaporanController;
use App\Http\Controllers\RSCPenilaianController;
use App\Http\Controllers\RSCPertanianController;
use App\Http\Controllers\UsahaLainnyaController;
use App\Http\Controllers\DataAnalisa5CController;
use App\Http\Controllers\RSCPengusulanController;
use App\Http\Controllers\Admin\HakAksesController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\AnalisaJaminanController;
use App\Http\Controllers\RSCPenjadwalanController;
use App\Http\Controllers\RSCPerdaganganController;
use App\Http\Controllers\RSCPersetujuanController;
use App\Http\Controllers\TaksasiJaminanController;
use App\Http\Controllers\UsahaPertanianController;
use App\Http\Controllers\Admin\PekerjaanController;
use App\Http\Controllers\AnalisaKeuanganController;
use App\Http\Controllers\AnalisaTambahanController;
use App\Http\Controllers\DashboardAnalisController;
use App\Http\Controllers\MonitoringStaffController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\UsahaPerdaganganController;
use App\Http\Controllers\Admin\AdminSurveiController;
use App\Http\Controllers\AnalisaKualitatifController;
use App\Http\Controllers\AnalisaMemorandumController;
use App\Http\Controllers\Admin\AdminJaminanController;
use App\Http\Controllers\Admin\RSCPengajuanController;
use App\Http\Controllers\AnalisaKepemilikanController;
use App\Http\Controllers\CheckListKendaraanController;
use App\Http\Controllers\MonitoringRSCStaffController;
use App\Http\Controllers\Admin\AdminPengajuanController;
use App\Http\Controllers\Admin\AdminPendampingController;
use App\Http\Controllers\Administratif\DenahLokasiController;
use App\Http\Controllers\Administratif\DataPerjanjianKreditController;
use App\Http\Controllers\Admin\NasabahController as AdminNasabahController;
use App\Http\Controllers\Administratif\DataBatalPerjanjianKreditController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // $role = Role::find(2);
    // $permission = Permission::find(47);

    // $role->givePermissionTo($permission);
    // $permission->assignRole($role);
    // dd($permission);
    return redirect('login');
});

// Route::get('/give-permission', function () {
//     $role = Role::find(3);
//     $permission = Permission::find(44);

//     $role->givePermissionTo($permission);
//     $permission->assignRole($role);
//     dd($permission);
// });

Route::get('/role', function () {
    // $role = Role::create(['name' => 'Kabag Kepatuhan']);
    // $role = Role::create(['name' => 'Staff Kepatuhan']);

    // $cek = Role::where('name', 'Staff Audit')->first();

    // if ($cek) {
    //     $cek->name = 'Staff Audit Internal';
    //     $cek->save();
    // }
    // dd($role);
});

Route::get('/login', function () {
    return redirect('login');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', function () {
        return redirect('login');
    });
});

Route::get('/tabungan', [TabunganController::class, 'index'])->name('tabungan.index');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/cif', [TabunganController::class, 'data_cif'])->name('cif.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('has.role')->group(function () {
        // Admin
        Route::prefix('admin')->group(function () {

            Route::group(['middleware' => ['role:Administrator']], function () {
                // Data Role
                Route::get('/role', [RoleController::class, 'index'])->name('role.index');
                Route::post('/role/create', [RoleController::class, 'create'])->name('role.create');
                Route::get('/role/{id}/edit', [RoleController::class, 'edit'])->name('role.edit');
                Route::post('/role/update', [RoleController::class, 'update'])->name('role.update');
                Route::post('/role/delete', [RoleController::class, 'delete'])->name('role.delete');
                Route::post('/role/{id}/destroy', [RoleController::class, 'destroy'])->name('role.destroy');

                // Data Permission
                Route::get('/data/permission', [PermissionController::class, 'index'])->name('permission.index');
                Route::post('/data/permission/create', [PermissionController::class, 'create'])->name('permission.create');
                Route::get('/data/permission/{id}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
                Route::put('/data/permission/update/{id}', [PermissionController::class, 'update'])->name('permission.update');
                Route::delete('/data/permission/{id}/delete', [PermissionController::class, 'destroy'])->name('permission.delete');

                // GivePermissionTo
                Route::get('/give/permission', [PermissionController::class, 'givepermission'])->name('permission.to');
                Route::post('/give/permission', [PermissionController::class, 'postpermission'])->name('permission.postpermission');
                Route::post('/give/permission/destroy', [PermissionController::class, 'destroypermission'])->name('permission.destroypermission');

                // Data User
                Route::resource('/user', UserController::class);
                //Reset Password
                Route::get('/user/reset/{user}/password', [PasswordController::class, 'datareset'])->name('datareset');
                Route::put('/user/reset/reset', [PasswordController::class, 'update'])->name('reset.update');
                // Data Akses
                Route::get('/akses/{akses}/editakses', [HakAksesController::class, 'editakses'])->name('editakses');
                Route::put('/akses/{akses}', [HakAksesController::class, 'updateakses'])->name('updateakses');
                // Data Kantor
                Route::resource('/kantor', KantorController::class);
                // Data Produk
                Route::resource('/produk', ProdukController::class);
                // Data Pekerjaan
                Route::resource('/pekerjaan', PekerjaanController::class);
                // Data Pendidikan
                Route::resource('/pendidikan', PendidikanController::class);

                // Data Nasabah
                Route::controller(AdminNasabahController::class)->group(function () {
                    Route::get('/data/nasabah', 'index')->name('admin.nasabah.index');
                    Route::get('/data/nasabah/{id}/edit', 'edit')->name('admin.nasabah.edit');
                    Route::put('/data/nasabah/{id}', 'update')->name('admin.nasabah.update');
                    Route::delete('/data/nasabah/{id}', 'destroy')->name('admin.nasabah.destroy');
                });

                // Data Pendamping
                Route::controller(AdminPendampingController::class)->group(function () {
                    Route::get('/data/pendamping', 'index')->name('admin.pendamping.index');
                    Route::get('/data/pendamping/{kode}/edit', 'edit')->name('admin.pendamping.edit');
                    Route::PUT('/data/pendamping/update', 'update')->name('admin.pendamping.update');
                });

                // Data Pengajuan
                Route::controller(AdminPengajuanController::class)->group(function () {
                    Route::get('/data/pengajuan', 'index')->name('admin.pengajuan.index');
                    Route::get('/data/pengajuan/{kode}/edit', 'edit')->name('admin.pengajuan.edit');
                    Route::PUT('/data/pengajuan/update', 'update')->name('admin.pengajuan.update');
                });

                // Data Jaminan
                Route::controller(AdminJaminanController::class)->group(function () {
                    Route::get('/data/jaminan', 'index')->name('admin.jaminan.index');
                    Route::get('/data/jaminan/{id}/edit', 'edit')->name('admin.jaminan.edit');
                    Route::PUT('/data/jaminan/update', 'update')->name('admin.jaminan.update');
                });

                // Data Resort
                Route::controller(ResortController::class)->group(function () {
                    Route::get('/data/resort', 'index')->name('admin.resort');
                    Route::post('/data/resort/simpan', 'simpan_resort')->name('admin.simpan.resort');
                });

                // Data CGC
                Route::controller(CGCController::class)->group(function () {
                    Route::get('/data/cgc', 'index')->name('admin.cgc');
                    Route::post('/data/cgc/simpan', 'simpan')->name('admin.simpan.cgc');
                });

                // Data Survei
                Route::controller(AdminSurveiController::class)->group(function () {
                    Route::get('/data/survei', 'index')->name('admin.survei.index');
                    Route::get('/data/survei/{kode}/edit', 'edit')->name('admin.survei.edit');
                    Route::PUT('/data/survei/update', 'update')->name('admin.survei.update');
                });

                // Data Pengajuan RSC
                Route::controller(RSCPengajuanController::class)->group(function () {
                    Route::get('/data/rsc/pengajuan/', 'index')->name('admin.rsc.pengajuan.index');
                    Route::get('/data/rsc/pengajuan/edit', 'edit_pengajuan_rsc')->name('admin.rsc.pengajuan.edit');
                    Route::post('/data/rsc/pengajuan/update', 'update_pengajuan_rsc')->name('admin.rsc.pengajuan.update');
                });
            });
        });

        //======Pendaftaran Nasabah======//
        Route::controller(PengajuanController::class)->group(function () {

            Route::group(['middleware' => ['role:Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/pengajuan', 'index')->name('pengajuan.index');
            });

            Route::group(['middleware' => ['role:Head Teller|Kabag Operasional']], function () {
                Route::get('/otor/pengajuan', 'otorisasi')->name('otor.pengajuan');
            });

            Route::get('/data/pengajuan', 'all')->name('pengajuan.data');
            Route::get('/data/info/{kode}', 'get_info_pengajuan')->name('pengajuan.info');
            Route::get('/pengajuan/edit', 'edit')->name('pengajuan.edit');
            Route::put('/pengajuan/simpan', 'storepengajuan')->name('pengajuan.storepengajuan');
            Route::get('/pengajuan/agunan', 'agunan')->name('pengajuan.agunan');
            Route::get('/pengajuan/editagunan/{id}/edit', 'editagunan')->name('pengajuan.editagunan');
            Route::delete('/pengajuan/editagunan/{pengajuan}/delete', 'deleteagunan')->name('pengajuan.deleteagunan');
            Route::put('/pengajuan/editagunan/update', 'updateagunan')->name('pengajuan.updateagunan');
            Route::put('/pengajuan/editagunan/validasi', 'validasiagunan')->name('pengajuan.validasiagunan');
            Route::delete('/pengajuan/{pengajuan}/delete', 'destroy')->name('pengajuan.destroy');
            Route::delete('/pengajuan/{pengajuan}/delete', 'destroy_batal')->name('batal.destroy');
            Route::post('/pengajuan/store', 'store')->name('pengajuan.store');
        });

        // Flow Berkas
        Route::controller(BerkasController::class)->middleware('permission:kirim berkas')->group(function () {
            Route::get('kirim/berkas/index', 'index')->name('kirim.berkas.index');
            Route::post('kirim/berkas/simpan', 'simpan_berkas')->name('kirim.berkas.simpan');

            Route::get('serah/terima/index', 'serah_terima')->name('serah.terima.index');
            Route::post('serah/terima/simpan', 'simpan_serah_terima')->name('serah.terima.simpan');
        });

        Route::controller(BerkasController::class)->middleware('permission:terima berkas')->group(function () {
            Route::get('terima/berkas/index', 'terima_berkas')->name('terima.berkas.index');
            Route::get('terima/berkas/get', 'get_berkas')->name('terima.berkas.get');
            Route::post('terima/berkas/simpan', 'simpan_terima_berkas')->name('terima.berkas.simpan');
        });

        Route::controller(BerkasController::class)->middleware('permission:terima berkas')->group(function () {
            Route::get('laporan/data/berkas', 'laporan_data_berkas')->name('laporan.data.berkas');
        });
        // Flow Berkas

        Route::controller(AgunanController::class)->group(function () {
            Route::post('/pengajuan/agunan/kendaraan', 'tambah_kendaraan')->name('kendaraan.simpan');
            Route::get('/pengajuan/agunan/{id}/edit', 'edit_agunan');
            Route::get('/pengajuan/agunan/tanah/{id}/edit', 'edit_agunan_tanah');
            Route::get('/pengajuan/agunan/lain/{id}/edit', 'edit_agunan_lain');
            Route::put('/pengajuan/agunan/kendaraan', 'update_kendaraan')->name('kendaraan.update');
            Route::post('/pengajuan/agunan/tanah', 'tambah_tanah')->name('tanah.simpan');
            Route::put('/pengajuan/agunan/tanah', 'update_tanah')->name('tanah.update');
            Route::post('/pengajuan/agunan/lainnya', 'tambah_lain')->name('lain.simpan');
            Route::put('/pengajuan/agunan/lainnya', 'update_lain')->name('lain.update');

            //Analisa Tambah Agunan
            Route::post('analisa/jaminan/kendaraan/tambah', 'analis_simpan_kendaraan')->name('analis.simpan_kendaraan');
            Route::post('analisa/jaminan/tanah/tambah', 'analis_simpan_tanah')->name('analis.simpan_tanah');
            Route::post('analisa/jaminan/lain/tambah', 'analis_simpan_lain')->name('analis.simpan_lain');
        });

        Route::post('/nasabah', [NasabahController::class, 'store'])->name('nasabah.store');
        Route::GET('/nasabah/cekdata/{kode}', [NasabahController::class, 'cekdata_nasabah'])->name('cekdata.nasabah');
        Route::get('/nasabah/edit', [NasabahController::class, 'edit'])->name('nasabah.edit');
        Route::put('/nasabah/update', [NasabahController::class, 'update'])->name('nasabah.update');
        Route::get('/pendamping/edit', [PendampingController::class, 'edit'])->name('pendamping.edit');
        Route::put('/pendamping/update', [PendampingController::class, 'update'])->name('pendamping.update');
        Route::get('/survei/edit', [SurveiController::class, 'edit'])->name('survei.edit');
        Route::put('/survei/{survei}/update', [SurveiController::class, 'update'])->name('survei.update');
        //======Pendaftaran Nasabah======//

        //Dati
        Route::controller(DatiController::class)->group(function () {
            Route::post('/nasabah/kabupaten', 'kabupaten')->name('kabupaten');
            Route::post('/nasabah/kecamatan', 'kecamatan')->name('kecamatan');
        });

        //Validasi Pendaftaran
        Route::get('/nasabah/validasi', [NasabahController::class, 'validasi'])->name('nasabah.validasi');

        //Konfirmasi dan Otorisasi
        Route::controller(KonfirmasiController::class)->group(function () {
            Route::get('/konfirmasi/pengajuan', 'index')->name('pengajuan.konfirmasi');
            Route::post('/konfirmasi/pengajuan', 'konfirmasi')->name('konfirmasi');
            Route::get('/otorisasi/pengajuan', 'otorisasi')->name('pengajuan.otorisasi');
            Route::post('/otorisasi/pengajuan', 'validasiotor')->name('validasiotor');
            Route::post('/nasabah/edit/otorisasi', 'otornasabah')->name('otornasabah');
            Route::post('/pendamping/edit/otorisasi', 'otorpendamping')->name('otorpendamping');
            Route::post('/pengajuan/edit/otorisasi', 'otorpengajuan')->name('otorpengajuan');
            Route::post('/survei/edit/otorisasi', 'otorsurvei')->name('otorsurvei');
            Route::post('/pengajuan/otorisasi/kendaraan', 'otorkendaraan')->name('otorkendaraan');
            Route::post('/pengajuan/otorisasi/tanah', 'otortanah')->name('otortanah');
            Route::post('/pengajuan/otorisasi/lain', 'otorlain')->name('otorlain');
        });

        //Tracking
        Route::controller(TrackingController::class)->group(function () {
            Route::get('/tracking/pengajuan', 'index')->name('tracking');
        });

        // Cetak Berkas Pengajuan
        Route::group(['middleware' => ['role:Customer Service|Kepala Kantor Kas|Kasi Analis']], function () {
            Route::get('/cetak/pengajuan', [CetakController::class, 'index_pengajuan'])->name('cetak.pengajuan.index');
        });

        Route::get('/cetak/pengajuan/detail', [CetakController::class, 'pengajuan'])->name('cetak.pengajuan');
        Route::get('/cetak/permohonan/kredit', [CetakController::class, 'permohonan_kredit'])->name('permohonan.kredit');
        Route::get('/cetak/slik', [DataCetakController::class, 'slik'])->name('data.slik');
        Route::get('/cetak/nik', [CetakController::class, 'nik'])->name('cetak.nik');
        Route::get('/cetak/pendamping', [CetakController::class, 'pendamping'])->name('cetak.pendamping');
        Route::get('/cetak/motor', [CetakController::class, 'motor'])->name('cetak.motor');
        Route::get('/cetak/mobil', [CetakController::class, 'mobil'])->name('cetak.mobil');
        Route::get('/cetak/tanah', [CetakController::class, 'tanah'])->name('cetak.tanah');
        Route::get('/cetak/surat/survei', [CetakController::class, 'surat_survei'])->name('cetak.surat.survei');
        Route::get('/cetak/monitoring/kredit', [CetakController::class, 'monitoring'])->name('cetak.monitoring');

        // Cetak Notifikasi Kredit
        Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi|Customer Service|Kepala Kantor Kas|Staff Admin Kredit|Realisasi|Admin Kredit']], function () {
            Route::get('/cetak/notifikasi-kredit', [CetakController::class, 'index_notifikasi_kredit'])->name('cetak.notifikasi.index');
        });

        // Cetak Perjanjian Kredit
        Route::get('/cetak/perjanjian-kredit', [CetakController::class, 'index_perjanjian_kredit'])->name('cetak.perjanjian.index');

        // Kartu Angsuran
        Route::get('/cetak/kartu/angsuran', [CetakController::class, 'index_kartu_angsuran'])->name('cetak.angsuran.index');
        Route::get('/cetak/kartu/angsuran/detail', [CetakController::class, 'detail_kartu_angsuran'])->name('cetak.angsuran.detail');

        // Cetak Lembar Konfirmasi Nasabah
        Route::get('/cetak/lembar/konfirmasi', [CetakController::class, 'cetak_lembar_konfirmasi'])->name('cetak.lembar.konfirmasi');
        Route::get('/cetak/konfirmasi/tabungan', [CetakController::class, 'cetak_konfirmasi_tabungan'])->name('cetak.konfirmasi.tabungan');
        Route::get('/cetak/konfirmasi/kredit', [CetakController::class, 'cetak_konfirmasi_kredit'])->name('cetak.konfirmasi.kredit');

        // Cetak Standing Intraction
        Route::get('/cetak/standing-interaction', [CetakController::class, 'standing_interaction'])->name('cetak.standing.interaction');
        Route::get('/standing-interaction/cetak', [CetakController::class, 'cetak_standing_interaction'])->name('cetak.data.standing.interaction');
        Route::get('/standing-interaction/cetak/wanayasa', [CetakController::class, 'cetak_standing_interaction_wanayasa'])->name('cetak.data.standing.interaction.wanayasa');

        // Cetak Data Realisasi
        Route::get('/cetak/photo/realisasi', [CetakController::class, 'cetak_photo_realisasi'])->name('cetak.photo.realisasi');

        //Penjadawlan
        Route::controller(PenjadwalanController::class)->prefix('analisa')->group(function () {

            Route::group(['middleware' => ['role:Kasi Analis|Kepala Kantor Kas|Kabag Analis|Direksi|Direktur Bisnis']], function () {
                Route::get('/penjadwalan', 'index')->name('analisa.penjadwalan');
            });

            Route::get('/penjadwalan/{id}', 'edit')->name('analisa.editpenjadwalan');
            Route::put('/penjadwalan', 'update')->name('analisa.updatepenjadwalan');
        });

        // Hasil Survei
        Route::get('/hasil/survei', [SurveiController::class, 'hasil_survei'])->name('hasil.survei');
        Route::get('/hasil/survey', [SurveiController::class, 'get_hasil_survei_spa'])->name('get.hasil.survei');
        Route::get('/hasil/pelaksanaan', [SurveiController::class, 'pelaksanaan_survei'])->name('pelaksanaan.survei');

        // Monitoring Staff Analis
        Route::controller(MonitoringStaffController::class)->prefix('monitoring')->group(function () {
            Route::get('/index', 'index')->name('monitoring.index');
            Route::get('/list', 'detail')->name('monitoring.detail');
            Route::get('/list/status', 'detail_status')->name('monitoring.detail.status');
        });
    });

    Route::group(['middleware' => ['role:Staff Analis']], function () {
        //Analisa Proses
        Route::get('/analisa/proses', [AnalisaController::class, 'index'])->name('analisa.proses');
        //Memorandum
        Route::resource('/analisa/memorandum', MemorandumController::class);
        //Analisa Kualitatif
        // Route::resource('/analisa/kualitatif', KualitatifController::class);
        //Asuransi
        Route::resource('/analisa/asuransi', AsuransiController::class);
        //Analisa Tambahan
        Route::resource('/analisa/tambahan', AnalisaTambahanController::class);

        //===Cetak Analisa===//
        Route::get('/cetak/analisa', [CetakAnalisaController::class, 'analisa5c'])->name('analisa5c.analisa');
        Route::get('/cetak/analisa/jasa', [CetakAnalisaController::class, 'usaha_jasa'])->name('usaha_jasa.usaha_jasa');
        Route::get('/cetak/analisa/kemampuan', [CetakAnalisaController::class, 'kemampuan_keuangan'])->name('kemampuan.kemampuan_keuangan');
        Route::get('/cetak/analisa/analisa5c', [CetakAnalisaController::class, 'cetakanalisa5c'])->name('cetakanalisa5c.analisa5c');
        Route::get('/cetak/analisa/crr', [CetakAnalisaController::class, 'crr'])->name('crr.crr');
        Route::get('/cetak/analisa/kualitatif', [CetakAnalisaController::class, 'kualitatif'])->name('kualitatif.kualitatif');
        Route::get('/cetak/analisa/tambahan', [CetakAnalisaController::class, 'tambahan'])->name('tambahan.tambahan');
        Route::get('/cetak/analisa/cetakmemorandum', [CetakAnalisaController::class, 'cetak_memorandum'])->name('memorandum.memorandum');
        //===Cetak Analisa===//

        //Dashboard Analis
        Route::get('/themes/dashboard', [DashboardAnalisController::class, 'index']);
    });

    //Komite
    Route::get('/komite', [KomiteController::class, 'index'])->name('komite.komite');

    //====Route Analisa====//
    Route::prefix('themes')->group(function () {
        // Route::view('/dashboard', 'dashboard.index');
        Route::controller(AnalisaController::class)->group(function () {

            Route::group(['middleware' => ['role:Staff Analis|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/permohonan/analisa', 'index')->name('permohonan.analisa');
                Route::get('/perbaikan/analisa', 'perbaikan_berkas')->name('perbaikan.analisa');
                Route::post('/perbaikan/analisa/simpan', 'simpan_perbaikan_berkas')->name('simpan.perbaikan.analisa');
            });


            Route::get('/permohonan/data_jadul/{pengajuan}', 'data_jadul')->name('permohonan.data_jadul');
            Route::post('/permohonan/data_jadul', 'simpanjadul')->name('permohonan.simpanjadul');
            Route::get('/tolak/permohonan/{kode}', 'get_data_penolakan')->name('tolak.data_penolakan');
            Route::post('/simpan/tolak/permohonan', 'simpan_penolakan')->name('tolak.simpan_penolakan');
        });

        //Analisa Usaha Perdagangan
        Route::controller(UsahaPerdaganganController::class)->group(function () {
            // Route::get('/analisa/usaha/perdagangan', [PerdaganganController::class, 'index'])->name('perdagangan.in');
            Route::get('/analisa/usaha/perdagangan', 'index')->name('perdagangan.in');
            Route::post('/analisa/usaha/perdagangan', 'store')->name('perdagangan.store');
            Route::get('/analisa/identitas/usaha/perdagangan/', 'identitas')->name('perdagangan.identitas');
            Route::post('/analisa/identitas/usaha/perdagangan', 'simpanidentitas')->name('perdagangan.simpanidentitas');
            Route::get('/analisa/barang/usaha/perdagangan', 'barang')->name('perdagangan.barang');
            Route::Post('/analisa/barang/usaha/perdagangan', 'simpanbarang')->name('perdagangan.simpanbarang');
            Route::put('/analisa/barang/usaha/perdagangan', 'updatebarang')->name('perdagangan.updatebarang');
            Route::get('/analisa/keuangan/usaha/perdagangan', 'keuangan')->name('perdagangan.keuangan');
            Route::post('/analisa/keuangan/usaha/perdagangan', 'simpankeuangan')->name('perdagangan.simpankeuangan');
            Route::put('/analisa/keuangan/usaha/perdagangan', 'updatekeuangan')->name('perdagangan.updatekeuangan');
            Route::delete('/analisa/keuangan/usaha/perdagangan/{id}', 'destroy')->name('perdagangan.hapus');
            // Route::get('/analisa/usaha/{perdagangan}/edit', 'edit')->name('perdagangan.edit');
        });

        //Analisa Usaha Pertanian
        Route::controller(UsahaPertanianController::class)->group(function () {
            Route::get('/analisa/usaha/pertanian', 'index')->name('pertanian.ind');
            Route::post('/analisa/usaha/pertanian', 'store')->name('pertanian.simpan');
            Route::get('/analisa/informasi/usaha/pertanian', 'informasi')->name('pertanian.informasi');
            Route::post('/analisa/informasi/usaha/pertanian', 'simpaninformasi')->name('pertanian.simpaninformasi');
            Route::put('/analisa/informasi/usaha/pertanian', 'updateinformasi')->name('pertanian.updateinformasi');
            Route::get('/analisa/biaya/usaha/pertanian', 'biaya')->name('pertanian.biaya');
            Route::post('/analisa/biaya/usaha/pertanian', 'simpanbiaya')->name('pertanian.simpanbiaya');
            Route::put('/analisa/biaya/usaha/pertanian', 'updatebiaya')->name('pertanian.updatebiaya');
            Route::get('/analisa/keuangan/usaha/pertanian', 'keuangan')->name('pertanian.keuangan');
            Route::post('/analisa/keuangan/usaha/pertanian', 'simpankeuangan')->name('pertanian.simpankeuangan');
            Route::delete('/analisa/keuangan/usaha/pertanian/{id}', 'destroy')->name('pertanian.destroy');
        });

        //Analisa Usaha Jasa
        Route::controller(UsahaJasaController::class)->group(function () {
            Route::get('/analisa/usaha/jasa', 'index')->name('usahajasa.ind');
            Route::post('/analisa/usaha/jasa', 'store')->name('usahajasa.store');
            Route::get('/analisa/keuangan/usaha/jasa', 'keuangan')->name('usahajasa.keuangan');
            Route::post('/analisa/keuangan/usaha/jasa', 'simpankeuangan')->name('usahajasa.simpankeuangan');
            Route::delete('/analisa/usaha/jasa/{id}', 'destroy')->name('usahajasa.destroy');
        });

        //Analisa Usaha Lainnya
        Route::controller(UsahaLainnyaController::class)->group(function () {
            Route::get('/analisa/usaha/lainnya', 'index')->name('lain.index');
            Route::post('/analisa/usaha/lainnya', 'simpanlain')->name('lain.simpanlain');
            Route::get('/analisa/identitas/usaha/lainnya', 'identitas')->name('lain.identitas');
            Route::put('/analisa/identitas/usaha/lainnya', 'simpanidentitas')->name('lain.simpanidentitas');
            Route::get('/analisa/bahanbaku/usaha/lainnya', 'bahan_baku')->name('lain.bahan_baku');
            Route::post('/analisa/bahanbaku/usaha/lainnya/simpan', 'simpan_bahan_baku')->name('simpan.bahan_baku');
            Route::put('/analisa/bahanbaku/usaha/lainnya/update', 'update_bahan_baku')->name('update.bahan_baku');
            Route::get('/analisa/keuangan/usaha/lainnya', 'keuangan')->name('lain.keuangan');
            Route::post('/analisa/keuangan/usaha/lainnya', 'simpankeuangan')->name('lain.simpankeuangan');
            Route::put('/analisa/keuangan/usaha/lainnya', 'updatekeuangan')->name('lain.updatekeuangan');
            Route::delete('/analisa/usaha/lainnya', 'destroy')->name('lain.destroy');
        });

        //Analisa Keuangan
        Route::controller(AnalisaKeuanganController::class)->group(function () {
            Route::get('/analisa/keuangan', 'index')->name('keuangan.index');
            Route::post('/analisa/keuangan', 'store')->name('keuangan.simpan');
            Route::put('/analisa/keuangan', 'update')->name('keuangan.update');
        });

        //Analisa Kepemilikan
        Route::controller(AnalisaKepemilikanController::class)->group(function () {
            Route::get('/analisa/kepemilikan', 'index')->name('kepemilikan.index');
            Route::post('/analisa/kepemilikan', 'store')->name('kepemilikan.store');
            Route::put('/analisa/kepemilikan', 'update')->name('kepemilikan.update');
        });

        //Analisa Jaminan
        Route::controller(AnalisaJaminanController::class)->group(function () {
            Route::get('/analisa/jaminan/kendaraan', 'kendaraan')->name('taksasi.kendaraan');
            Route::post('/analisa/jaminan/kendaraan', 'updatekendaraan')->name('taksasi.updatekendaraan');
            Route::post('/analisa/jaminan/fhoto/kendaraan', 'fhotokendaraan')->name('taksasi.fhotokendaraan');
            Route::post('/analisa/jaminan/fhoto/kendaraan/prev', 'previewkendaraan')->name('taksasi.previewkendaraan');
            Route::get('/analisa/jaminan/fhoto/kendaraan/data/{id}/edit', 'datakendaraan')->name('taksasi.datakendaraan');
            Route::get('/analisa/jaminan/kendaraan/{id}/edit', 'editkendaraan')->name('taksasi.editkendaraan');
            Route::get('/analisa/jaminan/tanah', 'tanah')->name('taksasi.tanah');
            Route::get('/analisa/jaminan/tanah/{id}/edit', 'edittanah')->name('taksasi.edittanah');
            Route::post('/analisa/jaminan/tanah', 'simpantanah')->name('taksasi.simpantanah');
            Route::get('/analisa/jaminan/lainnya', 'lain')->name('taksasi.lain');
            Route::get('/analisa/jaminan/lainnya/{id}/edit', 'editlain')->name('taksasi.editlain');
            Route::post('/analisa/jaminan/lainnya', 'simpanlain')->name('taksasi.simpanlain');

            Route::delete('/analisa/jaminan/kendaraan/{id}', 'delete_kendaraan')->name('taksasi.delete_kendaraan');
            Route::delete('/analisa/jaminan/lainnya/{id}', 'delete_lain')->name('taksasi.delete_lain');
        });

        //Analisa 5C
        Route::controller(DataAnalisa5CController::class)->group(function () {
            Route::get('/analisa/5c/character', 'character')->name('analisa5c.character');
            Route::post('/analisa/5c/character', 'simpancharacter')->name('analisa5c.simpancharacter');
            Route::put('/analisa/5c/character', 'updatecharacter')->name('analisa5c.updatecharacter');
            Route::get('/analisa/5c/capacity', 'capacity')->name('analisa5c.capacity');
            Route::post('/analisa/5c/capacity', 'simpancapacity')->name('analisa5c.simpancapacity');
            Route::put('/analisa/5c/capacity', 'updatecapacity')->name('analisa5c.updatecapacity');
            Route::get('/analisa/5c/capital', 'capital')->name('analisa5c.capital');
            Route::post('/analisa/5c/capital', 'simpancapital')->name('analisa5c.simpancapital');
            Route::get('/analisa/5c/collateral', 'collateral')->name('analisa5c.collateral');
            Route::post('/analisa/5c/collateral/simpan', 'simpancollateral')->name('analisa5c.simpancollateral');
            Route::put('/analisa/5c/collateral', 'updatecollateral')->name('analisa5c.updatecollateral');
            Route::get('/analisa/5c/condition', 'condition')->name('analisa5c.condition');
            Route::post('/analisa/5c/condition', 'simpancondition')->name('analisa5c.simpan_condition');
            Route::post('/analisa/5c/condition/update', 'updatecondition')->name('analisa5c.updatecondition');
        });

        //Analisa Kualitatif
        Route::controller(AnalisaKualitatifController::class)->group(function () {
            Route::get('/analisa/kualitatif/karakter', 'karakter')->name('kualitatif.karakter');
            Route::post('/analisa/kualitatif/karakter', 'simpankarakter')->name('kualitatif.simpankarakter');
            Route::put('/analisa/kualitatif/karakter', 'updatekarakter')->name('kualitatif.updatekarakter');
            Route::get('/analisa/kualitatif/usaha', 'usaha')->name('kualitatif.usaha');
            Route::put('/analisa/kualitatif/usaha', 'updateusaha')->name('kualitatif.updateusaha');
            Route::get('/analisa/kualitatif/swot', 'analisa_swot')->name('kualitatif.analisa_swot');
            Route::POST('/analisa/kualitatif/swot', 'simpan_analisa_swot')->name('simpan_.analisa_swot');
            Route::get('/analisa/kualitatif/tambahan', 'tambahan')->name('kualitatif.tambahan');
            Route::POST('/analisa/kualitatif/tambahan', 'simpan_tambahan')->name('kualitatif.simpan.tambahan');
        });

        //Analisa Memorandum
        Route::controller(AnalisaMemorandumController::class)->group(function () {
            Route::get('/analisa/memorandum/kebutuhan', 'kebutuhan')->name('memorandum.kebutuhan');
            Route::post('/analisa/memorandum/kebutuhan', 'simpankebutuhan')->name('memorandum.simpankebutuhan');
            Route::put('/analisa/memorandum/kebutuhan', 'updatekebutuhan')->name('memorandum.updatekebutuhan');
            Route::get('/analisa/memorandum/sandi', 'sandi')->name('memorandum.sandi');
            Route::post('/analisa/memorandum/sandi', 'simpansandi')->name('memorandum.simpansandi');
            Route::put('/analisa/memorandum/sandi', 'updatesandi')->name('memorandum.updatesandi');
            Route::get('/analisa/memorandum/usulan', 'usulan')->name('memorandum.usulan');
            Route::put('/analisa/memorandum/usulan', 'updateusulan')->name('memorandum.updateusulan');
        });

        //Administrasi
        Route::controller(AdministrasiController::class)->group(function () {
            Route::get('/analisa/administrasi', 'index')->name('administrasi.index');
            Route::post('/analisa/administrasi', 'simpan')->name('administrasi.simpan');
            Route::put('/analisa/administrasi', 'update')->name('administrasi.update');
        });

        //Konfirmasi
        Route::controller(KonfirmasiController::class)->group(function () {
            Route::get('/analisa/konfirmasi/analisa', 'konfirmasi_analisa')->name('konfirmasi.analisa');
            Route::post('/analisa/konfirmasi/analisa', 'ubah_analisa')->name('konfirmasi.ubah_analisa');
            // Jangan digunakan dulu
            Route::get('/analisa/konfirmasi/dokumen', 'dokumen_nasabah')->name('konfirmasi.dokumen');
        });

        //Check List Kendaraan
        Route::controller(CheckListKendaraanController::class)->group(function () {
            Route::get('/analisa/check/kelengkapan', 'index')->name('index.check_kelengkapan');
            Route::get('/report/kih', 'report_kih')->name('report.kih');
            Route::get('/report/kko', 'report_kko')->name('report.kko');
            Route::get('/report/kpj', 'report_kpj')->name('report.kpj');
            Route::get('/report/kpn', 'report_kpn')->name('report.kpn');
            Route::get('/report/kps', 'report_kps')->name('report.kps');
            Route::get('/report/krs-bpkb', 'report_krs_bpkb')->name('report.krs_bpkb');
            Route::get('/report/krs-bpkb-shm', 'report_krs_bpkb_shm')->name('report.krs_bpkb_shm');
            Route::get('/report/krs-shm', 'report_krs_shm')->name('report.krs_shm');
            Route::get('/report/kru-bpkb', 'report_kru_bpkb')->name('report.kru_bpkb');
            Route::get('/report/kru-bpkb-shm', 'report_kru_bpkb_shm')->name('report.kru_bpkb_shm');
            Route::get('/report/kru-shm', 'report_kru_shm')->name('report.kru_shm');
            Route::get('/report/kta', 'report_kta')->name('report.kta');
            Route::get('/report/kup', 'report_kup')->name('report.kup');
        });

        //Denah Lokasi
        Route::controller(DenahLokasiController::class)->group(function () {
            Route::get('/denah/lokasi', 'index')->name('denah.lokasi');
            Route::get('/denah/lokasi/{kode}', 'data_lokasi')->name('denah.data_lokasi');
        });

        Route::controller(KomiteController::class)->group(function () {

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/komite/kredit', 'index')->name('komite.kredit');
            });

            Route::post('/komite/kredit/data', 'getdata')->name('komite.getdata');
            Route::post('/komite/kredit', 'simpan')->name('komite.simpan');
            Route::post('/komite/tracking', 'update_tracking')->name('komite.update.tracking');
            Route::get('/komite/kredit/catatan/{pengajuan}', 'catatan')->name('komite.catatan');

            Route::post('/komite/pengembalian/berkas', 'pengembalian_berkas')->name('komite.pengembalian.berkas');
            Route::get('/komite/update/pengembalian/berkas', 'update_pengembalian_berkas')->name('komite.udpate.pengembalian.berkas');

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi']], function () {
                Route::get('/komite/kredit/survei/analisa', 'survei_analisa')->name('survei.analisa');
            });
        });

        Route::controller(NotifikasiController::class)->group(function () {

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/penolakan/pengajuan', 'data_penolakan')->name('penolakan.pengajuan');
                Route::post('/penolakan/tambah', 'tambah_penolakan')->name('penolakan.tambah');
                Route::post('/penolakan/edit', 'edit_penolakan')->name('penolakan.edit');
                Route::put('/penolakan/update', 'update_penolakan')->name('penolakan.update');
            });

            Route::post('/penolakan/simpan', 'simpan_penolakan')->name('penolakan.simpan');
        });


        Route::controller(DataCetakController::class)->group(function () {

            Route::group(['middleware' => ['role:Kabag Analis']], function () {
                Route::get('/notifikasi/kredit/update', 'index_update_notifikasi')->name('index.update_notifikasi');
                Route::get('/notifikasi/kredit/get', 'get_update_notifikasi')->name('get.update_notifikasi');
                Route::post('/notifikasi/kredit/update', 'update_update_notifikasi')->name('update.update_notifikasi');
            });

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/notifikasi/kredit', 'notifikasi_kredit')->name('notifikasi_kredit');
                Route::get('/notifikasi/kredit/catatan/{kode}', 'get_catatan_notifikasi_kredit')->name('get.catatan_notifikasi_kredit');
                Route::POST('/notifikasi/kredit/simpan/catatan', 'simpan_catatan_notifikasi_kredit')->name('simpan.catatan_notifikasi_kredit');
            });

            Route::get('/notifikasi/kredit/get/{kode}', 'get_notifikasi')->name('kode.notifikasi');
            Route::post('/notifikasi/kredit/simpan', 'simpan_notifikasi')->name('simpan.notifikasi');

            Route::group(['middleware' => ['role:Realisasi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/notifikasi/perjanjian/kredit', 'perjanjian_kredit')->name('perjanjian.kredit');
            });

            Route::get('/notifikasi/perjanjian/kredit/spk/{kode}', 'get_spk')->name('get.spk');
            Route::post('/notifikasi/perjanjian/simpan', 'simpan_spk')->name('simpan.spk');

            Route::group(['middleware' => ['role:Realisasi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/notifikasi/realisasi/kredit', 'realisasi_kredit')->name('realisasi.kredit');
            });

            Route::get('/otor/perjanjian/kredit/cetak', 'cetak_otor_perjanjian_kredit')->name('cetak.otor_perjanjian_kredit');

            Route::get('/notifikasi/realisasi/kredit/{kode}', 'get_realisasi')->name('get.realisasi');
            Route::get('/notifikasi/realisasi/kredit/foto/{kode}', 'get_foto_realisasi')->name('getfoto.realisasi');
            Route::post('/notifikasi/realisasi/kredit/simpan', 'simpan_realisasi')->name('simpan.realisasi');
            Route::post('/notifikasi/realisasi/kredit/konfirmasi', 'konfirmasi_realisasi')->name('konfirmasi.realisasi');
            Route::get('/notifikasi/penolakan/kredit', 'penolakan_kredit')->name('penolakan.kredit');
            Route::get('/notifikasi/penolakan/kredit/{kode}', 'get_penolakan')->name('kode.penolakan');
            Route::post('/notifikasi/penolakan/simpan', 'simpan_penolakan')->name('simpan.penolakan');
            Route::get('/persetujuan/kredit', 'persetujuan_kredit')->name('persetujuan.kredit');
            Route::get('/cetak/persetujuan/kredit', 'cetak_persetujuan_kredit')->name('cetak.persetujuan_kredit');

            Route::group(['middleware' => ['role:Staff Analis|Customer Service|Kepala Kantor Kas|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi']], function () {
                Route::get('/cetak/analisa/kredit', 'analisa_kredit')->name('analisa.kredit');
            });

            Route::get('/cetak/analisa/kredit/detail', 'cetak_analisa_kredit')->name('cetak.analisa_kredit');

            Route::get('/cetak/notifikasi/kredit/detail', 'cetak_notifikasi_kredit')->name('cetak.notifikasi_kredit');

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi|Customer Service|Kepala Kantor Kas|Staff Admin Kredit|Admin Kredit']], function () {
                Route::get('/cetak/penolakan/kredit', 'data_penolakan_kredit')->name('data_penolakan.kredit');
                Route::get('/cetak-berkas/penolakan/kredit', 'cetak_penolakan_kredit')->name('cetak.penolakan.kredit');
                Route::get('/cetak-berkas/penolakan/amplop', 'cetak_cover_amplop')->name('cetak.penolakan.amplop');
            });
        });

        Route::controller(DataPerjanjianKreditController::class)->group(function () {
            route::get('/data/perjanjian/kredit', 'index')->name('data.perjanjian_kredit');
            route::post('/data/perjanjian/kredit', 'batal_perjanjian_kredit')->name('data.batal_perjanjian_kredit');
        });

        Route::controller(DataBatalPerjanjianKreditController::class)->group(function () {
            route::get('/data/batal/perjanjian/kredit', 'index')->name('data.batal_perjanjian_kredit');
        });

        Route::controller(FiduciaController::class)->group(function () {
            Route::group(['middleware' => ['role:Realisasi|Customer Service|Kepala Kantor Kas|Admin Kredit']], function () {
                Route::get('/fiducia', 'index')->name('fiducia');
                Route::get('/cetak/fiducia', 'cetak_fiducia')->name('cetak.fiducia');
            });
        });

        // Prosfek
        Route::controller(ProsfekController::class)->group(function () {
            Route::get('/prosfek/index', 'index')->name('prosfek.index');
            Route::post('/prosfek/simpan', 'simpan_prosfek')->name('prosfek.simpan');
        });

        Route::controller(ProsfekController::class)->group(function () {
            Route::get('/data/prosfek/index', 'data_prosfek_index')->name('data.prosfek.index');
            Route::get('/data/prosfek/get', 'data_prosfek_get')->name('data.prosfek.get');
            Route::post('/data/prosfek/update', 'data_prosfek_update')->name('data.prosfek.update');
            Route::post('/data/prosfek/closing', 'data_prosfek_closing')->name('data.prosfek.closing');
        });
        // Prosfek

        //====Route Analisa RSC====//
        Route::group(['middleware' => ['role:Customer Service|Kepala Kantor Kas|Staff Analis']], function () {
            Route::get('/rsc/index', [RSCController::class, 'index'])->name('rsc.index');
            Route::post('/rsc/tambah', [RSCController::class, 'tambah_rsc'])->name('rsc.tambah.rsc');
            Route::post('/rsc/tambah/eksternal', [RSCController::class, 'tambah_rsc_eksternal'])->name('rsc.tambah.rsc.eksternal');
            Route::delete('/rsc/delete', [RSCController::class, 'delete_rsc'])->name('rsc.delete.rsc');
            Route::get('/rsc/eksternal/index', [RSCController::class, 'eksternal_index'])->name('rsc.eksternal.index');
        });

        Route::group(['middleware' => ['role:Staff Analis']], function () {
            Route::controller(RSCController::class)->group(function () {
                Route::get('/rsc/analisa', 'index_analisa')->name('rsc.index.analisa');
                Route::get('/rsc/data/kredit', 'data_kredit')->name('rsc.data.kredit');
                Route::put('/rsc/data/kredit', 'update_data_kredit')->name('rsc.update.data.kredit');
                Route::put('/rsc/biaya/rsc', 'update_biaya_rsc')->name('rsc.update.biaya.rsc');
                Route::get('/rsc/konfirmasi', 'konfirmasi_index')->name('rsc.konfirmasi');
                Route::post('/rsc/update/konfirmasi', 'konfirmasi_update')->name('rsc.konfirmasi.update');
                Route::post('/rsc/jadul', 'simpan_jadul')->name('rsc.simpan.jadul');
                Route::get('/rsc/permohonan/data_jadul/eks/{pengajuan}', 'data_jadul_eks')->name('rsc.jadul.eks');
            });

            Route::controller(RSCPerdaganganController::class)->group(function () {
                Route::get('/rsc/analisa/usaha/perdagangan', 'index')->name('rsc.usaha.perdagangan');
                Route::post('/rsc/analisa/usaha/perdagangan/simpan', 'simpan_rsc_perdagangan')->name('rsc.simpan.usaha.perdagangan');
                Route::get('/rsc/analisa/usaha/perdagangan/identitas', 'index_rsc_perdagangan_identitas')->name('rsc.index.usaha.perdagangan.identitas');
                Route::post('/rsc/analisa/usaha/perdagangan/identitas/simpan', 'simpan_rsc_perdagangan_identitas')->name('rsc.simpan.usaha.perdagangan.identitas');
                Route::get('/rsc/analisa/usaha/perdagangan/barang', 'barang')->name('rsc.usaha.perdagangan.barang');
                Route::post('/rsc/analisa/usaha/perdagangan/barang/simpan', 'simpan_barang')->name('rsc.usaha.perdagangan.barang.simpan');
                Route::put('/rsc/analisa/usaha/perdagangan/barang/update', 'update_barang')->name('rsc.usaha.perdagangan.barang.update');
                Route::get('/rsc/analisa/usaha/perdagangan/keuangan', 'keuangan')->name('rsc.usaha.perdagangan.keuangan');
                Route::post('/rsc/analisa/usaha/perdagangan/keuangan/simpan', 'simpan_keuangan')->name('rsc.usaha.perdagangan.keuangan.simpan');
                Route::post('/rsc/analisa/usaha/perdagangan/keuangan/update', 'update_keuangan')->name('rsc.usaha.perdagangan.keuangan.update');
                Route::post('/rsc/analisa/usaha/perdagangan/keuangan/update', 'update_keuangan')->name('rsc.usaha.perdagangan.keuangan.update');
                Route::delete('/rsc/analisa/usaha/perdagangan/delete', 'delete')->name('rsc.usaha.perdagangan.delete');
            });

            Route::controller(RSCPertanianController::class)->group(function () {
                Route::get('/rsc/analisa/usaha/pertanian', 'index')->name('rsc.usaha.pertanian');
                Route::post('/rsc/analisa/usaha/pertanian/simpan', 'simpan_rsc_pertanian')->name('rsc.usaha.pertanian.simpan');
                Route::get('/rsc/analisa/usaha/pertanian/informasi', 'index_rsc_pertanian_informasi')->name('rsc.usaha.pertanian.informasi');
                Route::post('/rsc/analisa/usaha/pertanian/update/informasi', 'update_rsc_pertanian_informasi')->name('rsc.usaha.pertanian.informasi.update');
                Route::get('/rsc/analisa/usaha/pertanian/biaya', 'rsc_pertanian_biaya')->name('rsc.usaha.pertanian.biaya');
                Route::post('/rsc/analisa/usaha/pertanian/update/biaya', 'update_rsc_pertanian_biaya')->name('rsc.usaha.pertanian.biaya.update');
                Route::get('/rsc/analisa/usaha/pertanian/keuangan', 'rsc_pertanian_keuangan')->name('rsc.usaha.pertanian.keuangan');
                Route::post('/rsc/analisa/usaha/pertanian/keuangan/update/', 'update_rsc_pertanian_keuangan')->name('rsc.usaha.pertanian.keuangan.update');
                Route::delete('/rsc/analisa/usaha/pertanian/delete', 'delete_rsc_pertanian')->name('rsc.usaha.pertanian.delete');
            });

            Route::controller(RSCJasaController::class)->group(function () {
                Route::get('/rsc/analisa/usaha/jasa', 'index')->name('rsc.usaha.jasa');
                Route::post('/rsc/analisa/usaha/jasa/simpan', 'simpan_rsc_jasa')->name('rsc.usaha.jasa.simpan');
                Route::get('/rsc/analisa/usaha/jasa/keuangan', 'rsc_jasa_keuangan')->name('rsc.usaha.jasa.keuangan');
                Route::post('/rsc/analisa/usaha/jasa/simpan/keuangan', 'simpan_rsc_jasa_keuangan')->name('rsc.usaha.jasa.keuangan.simpan');
                Route::delete('/rsc/analisa/usaha/jasa/delete/keuangan', 'delete_rsc_jasa_keuangan')->name('rsc.usaha.jasa.keuangan.delete');
            });

            Route::controller(RSCLainController::class)->group(function () {
                Route::get('/rsc/analisa/usaha/lain', 'index')->name('rsc.usaha.lain');
                Route::post('/rsc/analisa/usaha/lain/simpan', 'simpan_rsc_lain')->name('rsc.usaha.lain.simpan');
                Route::get('/rsc/analisa/usaha/lain/identitas', 'rsc_lain_identitas')->name('rsc.usaha.lain.identitas');
                Route::put('/rsc/analisa/usaha/lain/update/identitas', 'update_rsc_lain_identitas')->name('rsc.usaha.lain.identitas.update');
                Route::get('/rsc/analisa/usaha/lain/bahan', 'rsc_lain_bahan')->name('rsc.usaha.lain.bahan');
                Route::post('/rsc/analisa/usaha/lain/bahan/simpan', 'simpan_rsc_lain_bahan')->name('rsc.usaha.lain.bahan.simpan');
                Route::put('/rsc/analisa/usaha/lain/bahan/update', 'update_rsc_lain_bahan')->name('rsc.usaha.lain.bahan.update');
                Route::get('/rsc/analisa/usaha/lain/keuangan', 'rsc_lain_keuangan')->name('rsc.usaha.lain.keuangan');
                Route::post('/rsc/analisa/usaha/lain/simpan/keuangan', 'simpan_rsc_lain_keuangan')->name('rsc.usaha.lain.keuangan.simpan');
                Route::put('/rsc/analisa/usaha/lain/update/keuangan', 'update_rsc_lain_keuangan')->name('rsc.usaha.lain.keuangan.update');
                Route::delete('/rsc/analisa/usaha/lain/delete', 'delete_rsc_lain')->name('rsc.usaha.lain.delete');
            });

            Route::controller(RSCPenilaianController::class)->group(function () {
                Route::get('/rsc/penilaian/debitur', 'index')->name('rsc.penilaian.debitur');
                Route::post('/rsc/penilaian/debitur/simpan', 'simpan_kondisi_usaha')->name('rsc.simpan.kondisi.usaha');
                Route::put('/rsc/penilaian/debitur/update', 'update_kondisi_usaha')->name('rsc.update.kondisi.usaha');
                Route::post('/rsc/penilaian/debitur/simpan/agunan', 'simpan_kondisi_agunan')->name('rsc.simpan.kondisi.agunan');
                Route::post('/rsc/penilaian/debitur/update/agunan', 'update_kondisi_agunan')->name('rsc.update.kondisi.agunan');
            });

            Route::controller(RSCPengusulanController::class)->group(function () {
                Route::get('/rsc/data/pengusulan', 'index')->name('rsc.data.pengusulan');
                Route::post('/rsc/data/simpan/pengusulan', 'simpan_pengusulan')->name('rsc.data.pengusulan.simpan');
            });

            Route::controller(RSCKeuanganController::class)->group(function () {
                Route::get('/rsc/keuangan', 'index')->name('rsc.keuangan');
                Route::post('/rsc/simpan/keuangan', 'simpan_keuangan')->name('rsc.keuangan.simpan');
            });

            Route::controller(RSCJaminanController::class)->group(function () {
                Route::get('/rsc/jaminan/kendaraan', 'index_kendaraan')->name('rsc.jaminan.kendaraan');
                Route::POST('/rsc/jaminan/kendaraan/tambah', 'add_kendaraan')->name('rsc.jaminan.add.kendaraan');
                Route::get('/rsc/jaminan/kendaraan/get', 'get_kendaraan')->name('rsc.jaminan.get.kendaraan');
                Route::get('/rsc/jaminan/tanah', 'index_tanah')->name('rsc.jaminan.tanah');
                Route::POST('/rsc/jaminan/tanah/tambah', 'add_tanah')->name('rsc.jaminan.add.tanah');
                Route::get('/rsc/jaminan/lain', 'index_lain')->name('rsc.jaminan.lain');
                Route::POST('/rsc/jaminan/lain/tambah', 'add_lain')->name('rsc.jaminan.add.lain');
                Route::POST('/rsc/add/jaminan', 'add_jaminan')->name('rsc.jaminan.add.jaminan');
                Route::POST('/rsc/simpan/taksasi', 'simpan_taksasi')->name('rsc.simpan.taksasi');
            });
        });

        Route::group(['middleware' => ['role:Kabag Analis|Direktur Bisnis|Direksi|Kasi Analis']], function () {
            Route::controller(RSCController::class)->group(function () {
                Route::get('rsc/notifikasi/index', 'index_notifikasi')->name('rsc.notifikasi.index');
                Route::get('rsc/notifikasi/get', 'get_notifikasi')->name('rsc.notifikasi.get');
                Route::post('rsc/notifikasi/simpan', 'simpan_notifikasi')->name('rsc.notifikasi.simpan');
            });
        });

        Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi']], function () {
            Route::controller(RSCPersetujuanController::class)->group(function () {
                Route::get('/rsc/persetujuan', 'index')->name('rsc.persetujuan.index');
                Route::get('/rsc/persetujuan/informasi', 'informasi')->name('rsc.persetujuan.informasi');
                Route::get('/rsc/persetujuan/catatan', 'catatan')->name('rsc.persetujuan.catatan');
                Route::get('/rsc/persetujuan/index', 'persetujuan_index')->name('rsc.persetujuan.persetujuan_index');
                Route::post('/rsc/persetujuan/simpan', 'persetujuan_simpan')->name('rsc.persetujuan.simpan');
            });
        });
        Route::controller(RSCCetakController::class)->group(function () {
            Route::get('/rsc/cetakanalisa', 'cetakanalisa_index')->name('rsc.cetakanalisa.index');
            Route::get('/rsc/cetakanalisa/kredit', 'cetakanalisa_kredit_detail')->name('rsc.cetakanalisa_kredit.index');
            Route::get('/rsc/cetaknotifikasi', 'cetaknotifikasi_index')->name('rsc.cetaknotifikasi.index');
            Route::get('/rsc/cetaknotifikasi/detail', 'cetaknotifikasi_detail')->name('rsc.cetaknotifikasi.detail');
            Route::get('/rsc/cetakpersetujuan', 'cetakpersetujuan_index')->name('rsc.cetakpersetujuan.index');
            Route::get('/rsc/cetakpersetujuan/detail', 'cetakpersetujuan_detail')->name('rsc.cetakpersetujuan.detail');
            Route::get('/rsc/cetakpk', 'cetakpk_index')->name('rsc.cetakpk.index');
            Route::get('/rsc/cetakpk/detail', 'cetakpk_index_detail')->name('rsc.cetakpk.detail');
            Route::get('/rsc/cetakpenolakan/detail', 'cetak_penolakan')->name('rsc.cetak.penolakan');
            Route::get('/rsc/cetak/asuransi', 'cetak_asuransi_index')->name('rsc.cetak.asuransi.index');
            Route::get('/rsc/cetak/asuransi/cover', 'nonlanjut_asuransi')->name('rsc.cetak.asuransi.cover');
            Route::get('/rsc/cetak/asuransi/tidakikut', 'tidakikut_asuransi')->name('rsc.cetak.tidak.ikut');
            Route::post('/rsc/simpan/asuransi', 'simpan_asuransi')->name('rsc.simpan.asuransi');
        });

        Route::group(['middleware' => ['role:Kasi Analis']], function () {
            Route::controller(RSCPenjadwalanController::class)->group(function () {
                Route::get('/rsc/penjadwalan', 'index')->name('rsc.penjadwalan');
                Route::get('/rsc/penjadwalan/tambah', 'index_penjadwalan')->name('rsc.penjadwalan.index');
                Route::post('/rsc/penjadwalan/simpan', 'simpan_penjadwalan')->name('rsc.penjadwalan.simpan');
                Route::post('/rsc/penjadwalan/update', 'update_penjadwalan')->name('rsc.penjadwalan.update');
            });
        });

        Route::group(['middleware' => ['role:Kasi Analis|Kabag Analis']], function () {
            Route::controller(RSCController::class)->group(function () {
                Route::get('/rsc/penolakan', 'penolakan_index')->name('rsc.penolakan');
                Route::get('/rsc/add/penolakan', 'get_penolakan')->name('rsc.get_penolakan');
                Route::POST('/rsc/simpan/penolakan', 'simpan_penolakan')->name('rsc.simpan_penolakan');
                Route::POST('/rsc/update/penolakan', 'update_penolakan')->name('rsc.update_penolakan');
            });
        });

        // Monitoring RSC Staff Analis
        Route::controller(MonitoringRSCStaffController::class)->group(function () {
            Route::get('/monitoring/rsc/index', 'index')->name('monitoring.rsc.index');
            Route::get('/monitoring/rsc/detail', 'detail')->name('monitoring.rsc.detail');
            Route::get('/monitoring/rsc/status', 'detail_status')->name('monitoring.rsc.detail.status');
        });

        // Admin Kredit
        Route::controller(RSCController::class)->group(function () {
            Route::get('/rsc/perjanjian_kredit', 'pk_index')->name('rsc.perjanjian_kredit');
            Route::POST('/rsc/simpan/perjanjian_kredit', 'pk_simpan')->name('rsc.perjanjian_kredit.simpan');
            Route::POST('/rsc/add/perjanjian_kredit', 'simpan_spk_rsc')->name('rsc.add_spk.simpan');
            Route::get('/rsc/perjanjiankredit/get', 'pk_get')->name('rsc.perjanjian_kredit.get');
        });

        Route::controller(RSCBiayaController::class)->group(function () {
            Route::get('/rsc/biayarsc', 'index')->name('rsc.biaya.index');
            Route::get('/rsc/biayarsc/get', 'get_biaya')->name('rsc.biaya.get');
            Route::POST('/rsc/biayarsc/simpan', 'simpan_biaya')->name('rsc.simpan.biaya');
            Route::get('/rsc/cetak/biayarsc', 'cetak_biaya')->name('rsc.cetak.biaya');
        });

        Route::controller(RSCAngsuranController::class)->group(function () {
            Route::get('/rsc/angsuran', 'index')->name('rsc.angsuran');
            Route::get('/rsc/angsuran/detail', 'detail_angsuran')->name('rsc.detail.angsuran');
        });

        Route::controller(RSCCetakController::class)->group(function () {
            Route::get('/rsc/cetakbiaya', 'biaya_index')->name('rsc.cetakbiaya.index');
        });
        // Admin Kredit

        Route::controller(RSCLaporanController::class)->group(function () {
            Route::get('/rsc/laporan/pendaftaran', 'pendaftaran_rsc')->name('rsc.pendaftaran');
            Route::get('/rsc/laporan/tracking', 'tracking_rsc')->name('rsc.tracking');
            Route::get('/rsc/laporan/realisasi', 'realisasi_rsc')->name('rsc.realisasi');
        });

        Route::controller(RSCExsportController::class)->group(function () {
            Route::get('/rsc/laporanrealisasi/export', 'laporan_realisasi')->name('rsc.export.laporan.realisasi');
        });
        //====Route Analisa RSC====//

    });
    //====Route Analisa====//

    //====Route OTORISASI PK====//
    Route::controller(KonfirmasiController::class)->group(function () {
        Route::get('otor/perjanjian/kredit', 'otor_perjanjian_kredit')->name('otor.perjanjian_kredit');
        Route::POST('otor/perjanjian/kredit/simpan', 'simpan_otor_perjanjian_kredit')->name('otor.simpan_perjanjian_kredit');
        Route::get('otor/perjanjian/kredit/get/{kode}', 'get_otor_perjanjian_kredit')->name('otor.get_perjanjian_kredit');
    });
    //====Route OTORISASI PK====//

    Route::controller(PerhitunganController::class)->group(function () {
        Route::get('/perhitungan/flat', 'flat')->name('flat');
        Route::get('/perhitungan/efektif_musiman', 'efektif_musiman')->name('efektif_musiman');
        Route::get('/perhitungan/simulasi/pasific', 'simulasi')->name('simulasi_ajk_pasific');
        Route::get('/perhitungan/simulasi/bumida', 'simulasi_bumida')->name('simulasi_ajk_bumida');
        Route::get('/perhitungan/premi', 'add')->name('premi');
        Route::get('/perhitungan/simulasi_ajk_pasific', 'sheet')->name('sheet');
        Route::get('/perhitungan/simulasi_ajk_bumida', 'sheet_bumida')->name('sheet_bumida');
        Route::get('/perhitungan/simulasi_tlo', 'simulasi_tlo')->name('simulasi.tlo');
        Route::get('/perhitungan/tlo', 'perhitungan_tlo')->name('perhitungan.tlo');
    });

    Route::controller(SkriningController::class)->group(function () {
        Route::get('/skrining/nasabah', 'skrining_index')->name('skrining.index');
        Route::get('/skrining/cek', 'skrining_nasabah')->name('skrining.nasabah');
        Route::get('/daftar/analisa/skrining', 'daftar_analisa_skrining')->name('daftar.analisa.skrining');
        Route::get('/approve/analisa/skrining', 'approve_analisa_skrining')->name('approve.analisa.skrining');
        Route::get('/cetak/skrining', 'cetak_skrining')->name('skrining.cetak');
        Route::get('/data/skrining', 'data_skrining')->name('skrining.data');
        Route::get('/data/analisa/skrining', 'data_analisa_skrining')->name('skrining.data.analisa');
    });

    Route::group(['middlewae' => ['Kabag Kepatuhan', 'Staff Kepatuhan', 'Administrator']], function () {
        Route::controller(SkriningController::class)->group(function () {
            Route::get('/udpate/data/skrining', 'udpate_data_skrining_index')->name('update.skrining.data.index');
            Route::get('/udpate/skrining', 'udpate_data_skrining')->name('update.data.skrining');
            Route::get('/analisa/skrining', 'analisa_skrining_index')->name('analisa.skrining.index');
            Route::get('/proses/analisa/skrining', 'proses_analisa_skrining')->name('analisa.skrining.proses');
            Route::get('/cetak/analisa/skrining', 'cetak_analisa_skrining')->name('analisa.skrining.cetak');
        });
    });

    //====Route Cetak Laporan====//
    Route::controller(CetakLaporanController::class)->group(function () {
        Route::get('/laporan/pendaftaran', 'laporan_pendaftaran')->name('laporan.pendaftaran');
        Route::get('/laporan/sebelum/survey', 'laporan_sebelum_survey')->name('laporan.sebelum.survey');
        Route::get('/laporan/sesudah/survey', 'laporan_sesudah_survey')->name('laporan.sesudah.survey');
        Route::get('/laporan/penolakan', 'laporan_penolakan')->name('laporan.penolakan');
        Route::get('/laporan/pengajuan/disetujui', 'pengajuan_disetujui')->name('pengajuan.disetujui');
        Route::get('/laporan/siap-realisasi', 'siap_realisasi')->name('laporan.siap-realisasi');
        Route::get('/laporan/pencairan', 'laporan_pencairan')->name('laporan.pencairan');
        Route::get('/laporan/tracking/pengajuan', 'laporan_tracking_pengajuan')->name('laporan.tracking.pengajuan');


        // Hapus rout yang tidak digunakan
        Route::get('/laporan/fasilitas', 'laporan_fasilitas')->name('laporan.fasilitas');
        Route::get('/filter/laporan/fasilitas', 'post_laporan_fasilitas')->name('filter.laporan.fasilitas');
        Route::get('/filter/laporan/pencairan', 'post_laporan_pencairan')->name('filter.laporan.pencairan');
        Route::get('/laporan/siap-realisasi/kredit', 'post_siap_realisasi')->name('filter.laporan.siap-realisasi');
        Route::get('/laporan/survei', 'laporan_survey_analisa')->name('laporan.survey');
        Route::post('/laporan/survei/analisa', 'post_laporan_survey')->name('laporan.survey-analisa');
        Route::get('/laporan/penjadwalan', 'laporan_penjadwalan')->name('laporan.penjadwalan');
        Route::get('/laporan/notifikasi', 'laporan_notifikasi')->name('laporan.notifikasi');
    });

    // Export Data
    Route::controller(ExportController::class)->group(function () {
        Route::post('/export/laporan/fasilitas', 'data_laporan_fasilitas')->name('export.fasilitas');
        Route::post('/export/laporan/pendaftaran', 'data_laporan_pendaftaran')->name('export.pendaftaran');
        Route::post('/export/laporan/realisasi', 'data_laporan_realisasi')->name('export.realisasi');
        Route::post('/export/laporan/siap-realisasi', 'data_laporan_siap_realisasi')->name('export.siap-realisasi');
        Route::post('/export/laporan/export-filter', 'export_filter_realisasi')->name('export.export_filter');
        Route::post('/export/laporan/sesudah-survei', 'data_export_sesudah_survei')->name('export.sesudah_survei');
        Route::post('/export/laporan/sebelum-survei', 'data_export_sebelum_survei')->name('export.sebelum_survei');
        Route::post('/export/laporan/tracking', 'data_export_tracking')->name('export.tracking');
        Route::post('/export/standing/interaction', 'export_standing_interaction')->name('export.standing.interaction');
    });

    // Export Data
    Route::controller(DroppingController::class)->group(function () {
        Route::get('/droping/cif', 'data_cif')->name('dropping.cif');
        Route::get('/droping/agunan', 'data_agunan')->name('dropping.agunan');
        Route::get('/droping/kredit', 'data_kredit')->name('dropping.kredit');
        Route::delete('/hapus/kredit/{pengajuan}', 'hapus_spk')->name('hapus.spk');
    });

    // Update Password
    Route::controller(UserController::class)->group(function () {
        Route::get('/profile/password', 'password_index')->name('password.index');
        Route::POST('/profile/password/ubah', 'ubah_password')->name('ubah.password');
        Route::get('/perubahan/data', 'perubahan_data_index')->name('ubah.data');
        Route::post('/perubahan/data/tabel', 'ubah_data_tabel')->name('ubah.data_tabel');
    });
});

//====FRONT END====//
// Route::get('/', [FrontController::class, 'index']);
Route::get('/pengajuan/kredit', [FrontController::class, 'pengajuan']);
Route::get('/pengajuan/tracking', [FrontController::class, 'tracking']);
Route::get('/verifikasi', [FrontController::class, 'verifikasi']);
//====FRONT END====//

Route::view('/amplop', 'cetak-berkas.amplop.cover-depan');

Route::view('/anuitas', 'perhitungan.anuitas');
Route::view('/rekap/analisa', 'rekap.analisa');

Route::view('/error', 'errors.500');

require __DIR__ . '/auth.php';
