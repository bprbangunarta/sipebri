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
use App\Http\Controllers\Admin\PekerjaanController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\DatiController;
use App\Http\Controllers\TabunganController;
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
    return redirect('login');
});

Route::get('/tabungan', [TabunganController::class, 'index'])->name('tabungan.index');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('has.role')->group(function () {
        // Admin
        Route::prefix('admin')->group(function () {
            // Data Role
            Route::get('/role', [RoleController::class, 'index'])->name('role.index');
            Route::post('/role/create', [RoleController::class, 'create'])->name('role.create');
            Route::post('/role/edit', [RoleController::class, 'edit'])->name('role.edit');
            Route::post('/role/{id}/update', [RoleController::class, 'update'])->name('role.update');
            Route::post('/role/delete', [RoleController::class, 'delete'])->name('role.delete');
            Route::post('/role/{id}/destroy', [RoleController::class, 'destroy'])->name('role.destroy');

            // Data User
            Route::resource('/user', UserController::class);
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

        //Pendaftaran Nasabah
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
        Route::put('/pengajuan/simpan', [PengajuanController::class, 'storepengajuan'])->name('pengajuan.storepengajuan');
        Route::get('/pengajuan/agunan', [PengajuanController::class, 'agunan'])->name('pengajuan.agunan');
        Route::get('/pengajuan/editagunan/{id}/edit', [PengajuanController::class, 'editagunan'])->name('pengajuan.editagunan');
        Route::put('/pengajuan/editagunan/update', [PengajuanController::class, 'updateagunan'])->name('pengajuan.updateagunan');
        Route::delete('/pengajuan/{pengajuan}/delete', [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
        Route::post('/pengajuan/store', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::post('/nasabah', [NasabahController::class, 'store'])->name('nasabah.store');
        Route::get('/nasabah/edit', [NasabahController::class, 'edit'])->name('nasabah.edit');
        Route::put('/nasabah/update', [NasabahController::class, 'update'])->name('nasabah.update');
        Route::get('/pendamping/edit', [PendampingController::class, 'edit'])->name('pendamping.edit');
        Route::put('/pendamping/update', [PendampingController::class, 'update'])->name('pendamping.update');
        Route::get('/survei/edit', [SurveiController::class, 'edit'])->name('survei.edit');
        Route::put('/survei/{survei}/update', [SurveiController::class, 'update'])->name('survei.update');

        //Dati
        Route::controller(DatiController::class)->group(function () {
            Route::post('/nasabah/kabupaten', 'kabupaten')->name('kabupaten');
            Route::post('/nasabah/kecamatan', 'kecamatan')->name('kecamatan');
        });

        // Validasi Pendaftaran
        Route::get('/nasabah/validasi', [NasabahController::class, 'validasi'])->name('nasabah.validasi');
        Route::view('/konfirmasi/pengajuan', 'pengajuan.konfirmasi')->name('pengajuan.konfirmasi');
        Route::view('/otorisasi/pengajuan', 'pengajuan.otorisasi');
    });
});

require __DIR__ . '/auth.php';
