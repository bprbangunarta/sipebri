<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Nomor berkas dipindah dari seri 008xxxxx ke 007xxxxx (angka 8 mirip 0). */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('loan_applications')
            ->where('application_code', 'like', '008%')
            ->update([
                'application_code' => DB::raw("'007' || substr(application_code, 4)"),
            ]);
    }

    public function down(): void
    {
        DB::table('loan_applications')
            ->where('application_code', 'like', '007%')
            ->update([
                'application_code' => DB::raw("'008' || substr(application_code, 4)"),
            ]);
    }
};
