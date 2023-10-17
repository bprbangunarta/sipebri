<?php

use App\Models\Lain;
use App\Models\Nasabah;
use App\Models\Kepemilikan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatiController;
use App\Http\Controllers\JasaController;
use App\Http\Controllers\LainController;
use App\Http\Controllers\AnalisaTambahan;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\KomiteController;
use App\Http\Controllers\SurveiController;
use App\Http\Controllers\AnalisaController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AsuransiController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\TabunganController;
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
use App\Http\Controllers\PendampingController;
use App\Http\Controllers\KepemilikanController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\PerdaganganController;
use App\Http\Controllers\Admin\KantorController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\CetakAnalisaController;
use App\Http\Controllers\UsahaLainnyaController;
use App\Http\Controllers\Admin\HakAksesController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\TaksasiJaminanController;
use App\Http\Controllers\UsahaPertanianController;
use App\Http\Controllers\Admin\PekerjaanController;
use App\Http\Controllers\AnalisaKeuanganController;
use App\Http\Controllers\AnalisaTambahanController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\AnalisaJaminanController;
use App\Http\Controllers\AnalisaKepemilikanController;
use App\Http\Controllers\DataAnalisa5CController;
use App\Http\Controllers\UsahaPerdaganganController;

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
    // $role = Role::find(7);
    // $role->givePermissionTo('analisa input');
    // dd($role);
    return redirect('login');
});

Route::get('/login', function () {
    return redirect('login');
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
            Route::get('/pengajuan', 'index')->name('pengajuan.index');
            Route::get('/pengajuan/edit', 'edit')->name('pengajuan.edit');
            Route::put('/pengajuan/simpan', 'storepengajuan')->name('pengajuan.storepengajuan');
            Route::get('/pengajuan/agunan', 'agunan')->name('pengajuan.agunan');
            Route::get('/pengajuan/editagunan/{id}/edit', 'editagunan')->name('pengajuan.editagunan');
            Route::delete('/pengajuan/editagunan/{id}/delete', 'deleteagunan')->name('pengajuan.deleteagunan');
            Route::put('/pengajuan/editagunan/update', 'updateagunan')->name('pengajuan.updateagunan');
            Route::put('/pengajuan/editagunan/validasi', 'validasiagunan')->name('pengajuan.validasiagunan');
            Route::delete('/pengajuan/{pengajuan}/delete', 'destroy')->name('pengajuan.destroy');
            Route::post('/pengajuan/store', 'store')->name('pengajuan.store');
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
        });

        // Cetak Berkas Pengajuan
        Route::get('/cetak/pengajuan', [CetakController::class, 'pengajuan'])->name('cetak.pengajuan');
        Route::get('/cetak/slik', [DataCetakController::class, 'slik'])->name('data.slik');
        Route::get('/cetak/nik', [CetakController::class, 'nik'])->name('cetak.nik');
        Route::get('/cetak/pendamping', [CetakController::class, 'pendamping'])->name('cetak.pendamping');
        Route::get('/cetak/motor', [CetakController::class, 'motor'])->name('cetak.motor');
        Route::get('/cetak/tanah', [CetakController::class, 'tanah'])->name('cetak.tanah');

        //Penjadawlan
        Route::controller(PenjadwalanController::class)->prefix('analisa')->group(function () {
            Route::get('/penjadwalan', 'index')->name('analisa.penjadwalan');
            Route::get('/penjadwalan/{id}', 'edit')->name('analisa.editpenjadwalan');
            Route::put('/penjadwalan', 'update')->name('analisa.updatepenjadwalan');
        });
    });

    Route::group(['middleware' => ['role:Staff Analis']], function () {
        //Analisa Proses
        Route::get('/analisa/proses', [AnalisaController::class, 'index'])->name('analisa.proses');
        //Analisa Usaha Perdagangan
        // Route::resource('/analisa/usaha/perdagangan/tambah', PerdaganganController::class);
        // Route::post('/analisa/usaha/perdagangan', [PerdaganganController::class, 'detail_store'])->name('tambah.detail_store');
        // Route::put('/analisa/usaha/perdagangan', [PerdaganganController::class, 'detail_update'])->name('tambah.detail_update');
        //Analisa Usaha Pertanian
        // Route::resource('/analisa/usaha/pertanian', PertanianController::class);
        // Route::put('/analisa/usaha/pertanian', [PertanianController::class, 'update_detail'])->name('pertanian.update_detail');
        //Analisa Usaha Jasa
        // Route::resource('/analisa/usaha/jasa', JasaController::class);
        //Analisa Usaha Lainnya
        // Route::resource('/analisa/usaha/lain', LainController::class);
        // Route::put('/analisa/usaha/lain', [LainController::class, 'update_edit'])->name('lain.update_edit');
        //Analisa Keuangan
        // Route::resource('/analisa/keuangan', KeuanganController::class);
        // Route::put('/analisa/keuangan', [KeuanganController::class, 'update_detail'])->name('keuangan.update_detail');
        //Analisa kepemilikan
        // Route::resource('/analisa/harta/kepemilikan', KepemilikanController::class);
        //Analisa Taksasi Kepemilikan
        // Route::resource('/analisa/taksasi/jaminan', TaksasiJaminanController::class);
        //Analisa 5C
        // Route::resource('/analisa/a5c', Analisa5cController::class);
        //Memorandum
        Route::resource('/analisa/memorandum', MemorandumController::class);
        //Analisa Kualitatif
        Route::resource('/analisa/kualitatif', KualitatifController::class);
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
    });

    //Komite
    Route::get('/komite', [KomiteController::class, 'index'])->name('komite.komite');


    Route::prefix('themes')->group(function () {
        Route::view('/dashboard', 'dashboard.index');

        Route::get('/data-analisa', [AnalisaController::class, 'index']);
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
            Route::get('/analisa/jaminan/fhoto/kendaraan/{id}/edit', 'previewkendaraan')->name('taksasi.previewkendaraan');
            Route::get('/analisa/jaminan/kendaraan/{id}/edit', 'editkendaraan')->name('taksasi.editkendaraan');
            Route::get('/analisa/jaminan/tanah', 'tanah')->name('taksasi.tanah');
            Route::get('/analisa/jaminan/lainnya', 'lain')->name('taksasi.lain');
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
            Route::post('/analisa/5c/collateral', 'simpancollateral')->name('analisa5c.simpancollateral');
            Route::put('/analisa/5c/collateral', 'updatecollateral')->name('analisa5c.updatecollateral');
            Route::get('/analisa/5c/condition', 'condition')->name('analisa5c.condition');
            Route::post('/analisa/5c/condition', 'simpancondition')->name('analisa5c.simpancondition');
            Route::post('/analisa/5c/condition', 'updatecondition')->name('analisa5c.updatecondition');
        });

        // Route::view('/analisa/usaha/perdagangan', 'staff.analisa.u-perdagangan.index');
        // Route::view('/analisa/identitas/usaha/perdagangan', 'staff.analisa.u-perdagangan.identitas');
        // Route::view('/analisa/barang/usaha/perdagangan', 'staff.analisa.u-perdagangan.barang');
        // Route::view('/analisa/keuangan/usaha/perdagangan', 'staff.analisa.u-perdagangan.keuangan');

        // Route::view('/analisa/usaha/pertanian', 'staff.analisa.u-pertanian.index');
        // Route::view('/analisa/informasi/usaha/pertanian', 'staff.analisa.u-pertanian.informasi');
        // Route::view('/analisa/biaya/usaha/pertanian', 'staff.analisa.u-pertanian.biaya');
        // Route::view('/analisa/keuangan/usaha/pertanian', 'staff.analisa.u-pertanian.keuangan');

        // Route::view('/analisa/usaha/jasa', 'staff.analisa.u-jasa.index');
        // Route::view('/analisa/keuangan/usaha/jasa', 'staff.analisa.u-jasa.keuangan');

        // Route::view('/analisa/usaha/lainnya', 'staff.analisa.u-lainnya.index');
        // Route::view('/analisa/identitas/usaha/lainnya', 'staff.analisa.u-lainnya.identitas');
        // Route::view('/analisa/keuangan/usaha/lainnya', 'staff.analisa.u-lainnya.keuangan');

        // Route::view('/analisa/keuangan', 'staff.analisa.keuangan');
        // Route::view('/analisa/kepemilikan', 'staff.analisa.kepemilikan');

        // Route::view('/analisa/jaminan/kendaraan', 'staff.analisa.jaminan.kendaraan');
        // Route::view('/analisa/jaminan/tanah', 'staff.analisa.jaminan.tanah');
        // Route::view('/analisa/jaminan/lainnya', 'staff.analisa.jaminan.lainnya');
    });
});

Route::view('/analisa/index', 'analisa.index');

require __DIR__ . '/auth.php';
