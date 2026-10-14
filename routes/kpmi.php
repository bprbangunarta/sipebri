<?php

use App\Http\Controllers\KPMI\AmaterasuController;
use Illuminate\Support\Facades\Route;


Route::prefix('kpmi')->middleware('auth')->group(function () {
   Route::get('/analisa/{kode_pengajuan}', [AmaterasuController::class, 'show'])->name('kpmi.amaterasi.show');
   Route::post('/analisa/{kode_pengajuan}', [AmaterasuController::class, 'store'])->name('kpmi.amaterasi.store');
   Route::get('/analisa/{kode_pengajuan}/cetak', [AmaterasuController::class, 'cetak'])->name('kpmi.amaterasi.cetak');
   Route::post('/analisa/{kode_pengajuan}/komite', [AmaterasuController::class, 'komite'])->name('kpmi.amaterasi.komite');
   Route::get('/analisa/{kode_pengajuan}/dokumen/{jenis}', [AmaterasuController::class, 'dokumenLihat'])->name('kpmi.amaterasi.dokumen.lihat');
   Route::post('/analisa/{kode_pengajuan}/dokumen/{jenis}', [AmaterasuController::class, 'dokumenSimpan'])->name('kpmi.amaterasi.dokumen.simpan');
   Route::delete('/analisa/{kode_pengajuan}/dokumen/{jenis}', [AmaterasuController::class, 'dokumenHapus'])->name('kpmi.amaterasi.dokumen.hapus');
});
