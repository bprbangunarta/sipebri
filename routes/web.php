<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Admin\RoleController;

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
    // $role = Role::whereName('direksi')->first();
    // dd($role->givePermissionTo('lihat pendaftaran'));
    return view('welcome');
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
            // Role Management
            Route::get('/role', [RoleController::class, 'index'])->name('role.index');
            Route::post('/role/create', [RoleController::class, 'create'])->name('role.create');

            Route::post('/role/edit', [RoleController::class, 'edit'])->name('role.edit');
            Route::post('/role/{id}/update', [RoleController::class, 'update'])->name('role.update');

            Route::post('/role/delete', [RoleController::class, 'delete'])->name('role.delete');
            Route::post('/role/{id}/destroy', [RoleController::class, 'destroy'])->name('role.destroy');
        });
    });
});

require __DIR__ . '/auth.php';
