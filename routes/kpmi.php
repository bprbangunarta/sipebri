<?php

use App\Http\Controllers\KPMI\AmaterasuController;
use Illuminate\Support\Facades\Route;


Route::prefix('kpmi')->middleware('auth')->group(function () {
   Route::get('/analisa/{kode_pengajuan}', [AmaterasuController::class, 'show'])->name('kpmi.amaterasi.show');
   Route::post('/analisa/{kode_pengajuan}', [AmaterasuController::class, 'store'])->name('kpmi.amaterasi.store');
   Route::get('/analisa/{kode_pengajuan}/cetak', [AmaterasuController::class, 'cetak'])->name('kpmi.amaterasi.cetak');
   Route::post('/analisa/{kode_pengajuan}/komite', [AmaterasuController::class, 'komite'])->name('kpmi.amaterasi.komite');
   Route::post('/analisa/{kode_pengajuan}/administrasi', [AmaterasuController::class, 'administrasiSimpan'])->name('kpmi.amaterasi.administrasi.simpan');
   Route::post('/analisa/{kode_pengajuan}/memorandum/kebutuhan', [AmaterasuController::class, 'memorandumKebutuhan'])->name('kpmi.amaterasi.memorandum.kebutuhan');
   Route::post('/analisa/{kode_pengajuan}/memorandum/usulan', [AmaterasuController::class, 'memorandumUsulan'])->name('kpmi.amaterasi.memorandum.usulan');
   Route::post('/analisa/{kode_pengajuan}/memorandum/sandi', [AmaterasuController::class, 'memorandumSandi'])->name('kpmi.amaterasi.memorandum.sandi');

   // dipakai modal persetujuan pada halaman daftar komite (alur lama); peran sama dengan route komite.kredit
   Route::post('/komite/rc', [AmaterasuController::class, 'komiteRc'])
      ->middleware('role:Staff Analis|Kasi Analis|Kabag Analis|Direktur Bisnis|Direksi|Customer Service|Kepala Kantor Kas')
      ->name('kpmi.komite.rc');
   Route::get('/analisa/{kode_pengajuan}/dokumen/{jenis}', [AmaterasuController::class, 'dokumenLihat'])->name('kpmi.amaterasi.dokumen.lihat');
   Route::post('/analisa/{kode_pengajuan}/dokumen/{jenis}', [AmaterasuController::class, 'dokumenSimpan'])->name('kpmi.amaterasi.dokumen.simpan');
   Route::delete('/analisa/{kode_pengajuan}/dokumen/{jenis}', [AmaterasuController::class, 'dokumenHapus'])->name('kpmi.amaterasi.dokumen.hapus');
});
