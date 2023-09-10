<?php

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveiController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PendampingController;
use App\Http\Controllers\Admin\KantorController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\HakAksesController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\PekerjaanController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\AnalisaController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataCetakController;
use App\Http\Controllers\DatiController;
use App\Http\Controllers\JasaController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KonfirmasiController;
use App\Http\Controllers\LainController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\PerdaganganController;
use App\Http\Controllers\TabunganController;
use App\Http\Controllers\PertanianController;
use App\Models\Lain;
use App\Models\Nasabah;

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

        // Validasi Pendaftaran
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

    Route::controller(AnalisaController::class)->prefix('analisa')->group(function () {
        Route::group(['middleware' => ['role:Staff Analis']], function () {
            Route::get('/proses', 'index')->name('analisa.proses');
        });
    });

    Route::group(['middleware' => ['role:Staff Analis']], function () {
        //Analisa Usaha Perdagangan
        Route::resource('/analisa/usaha/perdagangan/tambah', PerdaganganController::class);
        Route::post('/analisa/usaha/perdagangan/perdagangan', [PerdaganganController::class, 'detail_store'])->name('tambah.detail_store');
        Route::put('/analisa/usaha/perdagangan/perdagangan', [PerdaganganController::class, 'detail_update'])->name('tambah.detail_update');
        //Analisa Usaha Pertanian
        Route::resource('/analisa/usaha/pertanian/pertanian', PertanianController::class);
        Route::put('/analisa/usaha/pertanian/pertanian', [PertanianController::class, 'update_detail'])->name('pertanian.update_detail');
        //Analisa Usaha Jasa
        Route::resource('/analisa/usaha/jasa/jasa', JasaController::class);
        //Analisa Usaha Lainnya
        Route::resource('/analisa/usaha/lain/lain', LainController::class);
        Route::put('/analisa/usaha/lain/lain', [LainController::class, 'update_edit'])->name('lain.update_edit');
        //Analisa Keuangan
        Route::resource('/analisa/keuangan', KeuanganController::class);
        Route::put('/analisa/keuangan', [KeuanganController::class, 'update_detail'])->name('keuangan.update_detail');
    });
    // Add Layout
    Route::prefix('layout')->group(function () {
        Route::view('/harta/kepemilikan', 'analisa.harta-kemepilikan')->name('analisa.harta.kepemilikan');
    });
});


require __DIR__ . '/auth.php';
