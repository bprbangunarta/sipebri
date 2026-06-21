<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/codex-login', [AuthController::class, 'authenticate'])->name('codex.login');
Route::post('/codex-logout', [AuthController::class, 'logout'])->name('codex.logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    // User Profile
    Route::post('/profil/changer-password', [AuthController::class, 'changePassword'])->name('codex.profile.password');
    Route::post('/profil/logout-session', [AuthController::class, 'logoutSession'])->name('codex.profile.session');
});
