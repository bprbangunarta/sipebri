<?php

use App\Http\Controllers\KPMI\AmaterasuController;
use Illuminate\Support\Facades\Route;


Route::prefix('kpmi')->middleware('auth')->group(function () {
   Route::get('/analisa/{kode_pengajuan}', [AmaterasuController::class, 'show'])->name('kpmi.amaterasi.show');
   Route::post('/analisa/{kode_pengajuan}', [AmaterasuController::class, 'store'])->name('kpmi.amaterasi.store');
   Route::get('/analisa/{kode_pengajuan}/cetak', [AmaterasuController::class, 'cetak'])->name('kpmi.amaterasi.cetak');
});
