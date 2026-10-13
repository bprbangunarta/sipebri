<?php

use App\Http\Controllers\KPMI\AmaterasuController;
use Illuminate\Support\Facades\Route;


Route::prefix('kpmi')->middleware('auth')->group(function () {
   Route::get('/analisa/{kode_pengajuan}', [AmaterasuController::class, 'show'])->name('kpmi.amaterasi.show');
});
