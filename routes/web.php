<?php

use App\Models\Lain;
use App\Models\Nasabah;
use App\Models\Kepemilikan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRController;
use App\Http\Controllers\DatiController;
use App\Http\Controllers\JasaController;
use App\Http\Controllers\LainController;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\AnalisaTambahan;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\AgunanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\KomiteController;
use App\Http\Controllers\SurveiController;
use App\Http\Controllers\AnalisaController;
use App\Http\Controllers\FiduciaController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AsuransiController;
use App\Http\Controllers\KeuanganController;
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
use App\Http\Controllers\KepemilikanController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\PerdaganganController;
use App\Http\Controllers\PerhitunganController;
use App\Http\Controllers\Admin\KantorController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\AdministrasiController;
use App\Http\Controllers\CetakAnalisaController;
use App\Http\Controllers\CetakLaporanController;
use App\Http\Controllers\UsahaLainnyaController;
use App\Http\Controllers\DataAnalisa5CController;
use App\Http\Controllers\Admin\HakAksesController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\AnalisaJaminanController;
use App\Http\Controllers\TaksasiJaminanController;
use App\Http\Controllers\UsahaPertanianController;
use App\Http\Controllers\Admin\PekerjaanController;
use App\Http\Controllers\AnalisaKeuanganController;
use App\Http\Controllers\AnalisaTambahanController;
use App\Http\Controllers\DashboardAnalisController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\UsahaPerdaganganController;
use App\Http\Controllers\AnalisaKualitatifController;
use App\Http\Controllers\AnalisaMemorandumController;
use App\Http\Controllers\AnalisaKepemilikanController;
use App\Http\Controllers\FrontController;

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

Route::get('/give-permission', function () {
    $role = Role::find(11);
    $permission = Permission::find(49);

    $role->givePermissionTo($permission);
    $permission->assignRole($role);
    dd($permission);
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
                Route::post('/role/edit', [RoleController::class, 'edit'])->name('role.edit');
                Route::post('/role/{id}/update', [RoleController::class, 'update'])->name('role.update');
                Route::post('/role/delete', [RoleController::class, 'delete'])->name('role.delete');
                Route::post('/role/{id}/destroy', [RoleController::class, 'destroy'])->name('role.destroy');

                // Data Role
                Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
                Route::post('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
                Route::get('/permission/{id}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
                Route::put('/permission/update/{id}', [PermissionController::class, 'update'])->name('permission.update');
                Route::delete('/permission/{id}/delete', [PermissionController::class, 'destroy'])->name('permission.delete');

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
            Route::get('/pengajuan/edit', 'edit')->name('pengajuan.edit');
            Route::put('/pengajuan/simpan', 'storepengajuan')->name('pengajuan.storepengajuan');
            Route::get('/pengajuan/agunan', 'agunan')->name('pengajuan.agunan');
            Route::get('/pengajuan/editagunan/{id}/edit', 'editagunan')->name('pengajuan.editagunan');
            Route::delete('/pengajuan/editagunan/{pengajuan}/delete', 'deleteagunan')->name('pengajuan.deleteagunan');
            Route::put('/pengajuan/editagunan/update', 'updateagunan')->name('pengajuan.updateagunan');
            Route::put('/pengajuan/editagunan/validasi', 'validasiagunan')->name('pengajuan.validasiagunan');
            Route::delete('/pengajuan/{pengajuan}/delete', 'destroy')->name('pengajuan.destroy');
            Route::post('/pengajuan/store', 'store')->name('pengajuan.store');
        });

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
        Route::group(['middleware' => ['role:Customer Service|Kepala Kantor Kas']], function () {
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
        Route::get('/cetak/monitoring/kredit', [CetakController::class, 'monitoring'])->name('cetak.monitoring');

        // Cetak Notifikasi Kredit
        Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direksi|Customer Service|Kepala Kantor Kas|Staff Admin Kredit|Realisasi']], function () {
            Route::get('/cetak/notifikasi-kredit', [CetakController::class, 'index_notifikasi_kredit'])->name('cetak.notifikasi.index');
        });

        // Cetak Perjanjian Kredit
        Route::get('/cetak/perjanjian-kredit', [CetakController::class, 'index_perjanjian_kredit'])->name('cetak.perjanjian.index');

        //Penjadawlan
        Route::controller(PenjadwalanController::class)->prefix('analisa')->group(function () {

            Route::group(['middleware' => ['role:Kasi Analis|Kepala Kantor Kas']], function () {
                Route::get('/penjadwalan', 'index')->name('analisa.penjadwalan');
            });

            Route::get('/penjadwalan/{id}', 'edit')->name('analisa.editpenjadwalan');
            Route::put('/penjadwalan', 'update')->name('analisa.updatepenjadwalan');
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

        Route::controller(UsahaJasaController::class)->group(function () {
            Route::get('/analisa/usaha/jasa', 'index')->name('usahajasa.ind');
            Route::post('/analisa/usaha/jasa', 'store')->name('usahajasa.store');
            Route::get('/analisa/keuangan/usaha/jasa', 'keuangan')->name('usahajasa.keuangan');
            Route::post('/analisa/keuangan/usaha/jasa', 'simpankeuangan')->name('usahajasa.simpankeuangan');
            Route::delete('/analisa/usaha/jasa/{id}', 'destroy')->name('usahajasa.destroy');
        });

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

        Route::controller(AnalisaKeuanganController::class)->group(function () {
            Route::get('/analisa/keuangan', 'index')->name('keuangan.index');
            Route::post('/analisa/keuangan', 'store')->name('keuangan.simpan');
            Route::put('/analisa/keuangan', 'update')->name('keuangan.update');
        });

        Route::controller(AnalisaKepemilikanController::class)->group(function () {
            Route::get('/analisa/kepemilikan', 'index')->name('kepemilikan.index');
            Route::post('/analisa/kepemilikan', 'store')->name('kepemilikan.store');
            Route::put('/analisa/kepemilikan', 'update')->name('kepemilikan.update');
        });

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
        });

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

        Route::controller(AnalisaKualitatifController::class)->group(function () {
            Route::get('/analisa/kualitatif/karakter', 'karakter')->name('kualitatif.karakter');
            Route::post('/analisa/kualitatif/karakter', 'simpankarakter')->name('kualitatif.simpankarakter');
            Route::put('/analisa/kualitatif/karakter', 'updatekarakter')->name('kualitatif.updatekarakter');
            Route::get('/analisa/kualitatif/usaha', 'usaha')->name('kualitatif.usaha');
            Route::put('/analisa/kualitatif/usaha', 'updateusaha')->name('kualitatif.updateusaha');
            Route::get('/analisa/kualitatif/swot', 'analisa_swot')->name('kualitatif.analisa_swot');
            Route::POST('/analisa/kualitatif/swot', 'simpan_analisa_swot')->name('simpan_.analisa_swot');
        });

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

        Route::controller(AdministrasiController::class)->group(function () {
            Route::get('/analisa/administrasi', 'index')->name('administrasi.index');
            Route::post('/analisa/administrasi', 'simpan')->name('administrasi.simpan');
            Route::put('/analisa/administrasi', 'update')->name('administrasi.update');
        });

        Route::controller(KonfirmasiController::class)->group(function () {
            Route::get('/analisa/konfirmasi/analisa', 'konfirmasi_analisa')->name('konfirmasi.analisa');
            Route::post('/analisa/konfirmasi/analisa', 'ubah_analisa')->name('konfirmasi.ubah_analisa');
            // Jangan digunakan dulu
            Route::get('/analisa/konfirmasi/dokumen', 'dokumen_nasabah')->name('konfirmasi.dokumen');
        });

        Route::controller(KomiteController::class)->group(function () {

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direksi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/komite/kredit', 'index')->name('komite.kredit');
            });

            Route::post('/komite/kredit/data', 'getdata')->name('komite.getdata');
            Route::post('/komite/kredit', 'simpan')->name('komite.simpan');
            Route::get('/komite/kredit/catatan/{pengajuan}', 'catatan')->name('komite.catatan');

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direksi']], function () {
                Route::get('/komite/kredit/survei/analisa', 'survei_analisa')->name('survei.analisa');
            });
        });

        Route::controller(NotifikasiController::class)->group(function () {

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direksi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/penolakan/pengajuan', 'data_penolakan')->name('penolakan.pengajuan');
                Route::post('/penolakan/tambah', 'tambah_penolakan')->name('penolakan.tambah');
                Route::post('/penolakan/edit', 'edit_penolakan')->name('penolakan.edit');
                Route::put('/penolakan/update', 'update_penolakan')->name('penolakan.update');
            });

            Route::post('/penolakan/simpan', 'simpan_penolakan')->name('penolakan.simpan');
        });


        Route::controller(DataCetakController::class)->group(function () {

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direksi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/notifikasi/kredit', 'notifikasi_kredit')->name('notifikasi_kredit');
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

            Route::group(['middleware' => ['role:Staff Analis|Customer Service|Kepala Kantor Kas|Kasi Analis|Kabag Analis|Direksi']], function () {
                Route::get('/cetak/analisa/kredit', 'analisa_kredit')->name('analisa.kredit');
            });

            Route::get('/cetak/analisa/kredit/detail', 'cetak_analisa_kredit')->name('cetak.analisa_kredit');

            Route::get('/cetak/notifikasi/kredit/detail', 'cetak_notifikasi_kredit')->name('cetak.notifikasi_kredit');

            Route::group(['middleware' => ['role:Staff Analis|Kasi Analis|Kabag Analis|Direksi|Customer Service|Kepala Kantor Kas|Staff Admin Kredit']], function () {
                Route::get('/cetak/penolakan/kredit', 'data_penolakan_kredit')->name('data_penolakan.kredit');
                Route::get('/cetak-berkas/penolakan/kredit', 'cetak_penolakan_kredit')->name('cetak.penolakan.kredit');
            });
        });


        Route::controller(FiduciaController::class)->group(function () {
            Route::group(['middleware' => ['role:Realisasi|Customer Service|Kepala Kantor Kas']], function () {
                Route::get('/fiducia', 'index')->name('fiducia');
                Route::get('/cetak/fiducia', 'cetak_fiducia')->name('cetak.fiducia');
            });
        });
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
        Route::get('/perhitungan/simulasi', 'simulasi')->name('simulasi_ajk');
        Route::get('/perhitungan/premi', 'add')->name('premi');
        Route::get('/perhitungan/simulasi_ajk', 'sheet')->name('sheet');
    });

    //====Route Cetak Laporan====//
    Route::controller(CetakLaporanController::class)->group(function () {
        Route::get('/laporan/fasilitas', 'laporan_fasilitas')->name('laporan.fasilitas');
        Route::get('/filter/laporan/fasilitas', 'post_laporan_fasilitas')->name('filter.laporan.fasilitas');

        Route::get('/laporan/realisasi', 'laporan_realisasi')->name('laporan.realisasi');
        Route::post('z', 'post_laporan_realisasi')->name('laporan.realisasi-kredit');

        Route::get('/laporan/siap-realisasi', 'siap_realisasi')->name('laporan.siap-realisasi');
        Route::get('/laporan/siap-realisasi/kredit', 'post_siap_realisasi')->name('filter.laporan.siap-realisasi');

        Route::get('/laporan/pendaftaran', 'laporan_pendaftaran')->name('laporan.pendaftaran');
        // Route::get('/laporan/pendaftaran/kredit', 'post_laporan_pendaftaran')->name('filter.laporan.pendaftaran');

        Route::get('/laporan/penolakan', 'laporan_penolakan')->name('laporan.penolakan');

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
    });
});

//====FRONT END====//
// Route::get('/', [FrontController::class, 'index']);
Route::get('/pengajuan/kredit', [FrontController::class, 'pengajuan']);
Route::get('/pengajuan/tracking', [FrontController::class, 'tracking']);
Route::get('/verifikasi', [FrontController::class, 'verifikasi']);
//====FRONT END====//

Route::view('/anuitas', 'perhitungan.anuitas');
Route::view('/rekap/analisa', 'rekap.analisa');

require __DIR__ . '/auth.php';
