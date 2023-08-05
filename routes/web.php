<?php

use App\Http\Controllers\Admin\HakAksesController;
use App\Http\Controllers\Admin\KantorController;
use App\Http\Controllers\Admin\PekerjaanController;
use App\Http\Controllers\Admin\PendidikanController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PendampingController;
use App\Http\Controllers\PengajuanController;

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

        // Pendaftaran Nasabah

        Route::controller(PendaftaranController::class)->group(function(){
            // Route::get('/pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
            // Route::get('/pendaftaran/edit', [PendaftaranController::class, 'edit'])->name('pendaftaran.edit');
            // Route::get('/pendaftaran/pendamping', [PendaftaranController::class, 'pendamping'])->name('pendaftaran.pendamping');
            Route::get('/pendaftaran', 'index')->name('pendaftaran.index');
            Route::post('/pendaftaran', 'store')->name('pendaftaran.store');
            Route::get('/pendaftaran/edit', 'edit')->name('pendaftaran.edit');
            Route::get('/pendaftaran/pendamping', 'pendamping')->name('pendaftaran.pendamping');
        });

        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/nasabah/edit', [NasabahController::class, 'edit'])->name('nasabah.edit');
        Route::get('/pendamping/edit', [PendampingController::class, 'edit'])->name('pendamping.edit');
        Route::get('/pengajuan/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');

        // Validasi Pendaftaran
        Route::get('/nasabah/validasi', [NasabahController::class, 'validasi'])->name('nasabah.validasi');
    });
});

require __DIR__ . '/auth.php';
