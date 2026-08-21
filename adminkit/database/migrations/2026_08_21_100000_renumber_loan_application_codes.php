<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Nomor berkas dipindah dari seri 008xxxxx ke 007xxxxx (angka 8 mirip 0). */
return new class extends Migration
{
    public function up(): void
    {
        $this->renumber('008', '007');
    }

    public function down(): void
    {
        $this->renumber('007', '008');
    }

    /** Dilakukan per baris agar portabel di SQLite maupun MySQL. */
    private function renumber(string $from, string $to): void
    {
        DB::table('loan_applications')
            ->where('application_code', 'like', "{$from}%")
            ->select('id', 'application_code')
            ->orderBy('id')
            ->chunk(200, function ($rows) use ($from, $to) {
                foreach ($rows as $row) {
                    DB::table('loan_applications')->where('id', $row->id)->update([
                        'application_code' => $to.substr($row->application_code, strlen($from)),
                    ]);
                }
            });
    }
};
