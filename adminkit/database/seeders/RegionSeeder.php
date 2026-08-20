<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Referensi wilayah (82k baris) dari database/data/regions.csv.gz. */
class RegionSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('regions')->exists()) {
            return;
        }

        $path = database_path('data/regions.csv.gz');
        $handle = gzopen($path, 'rb');
        gzgets($handle); // buang baris judul

        $now = now();
        $buffer = [];

        while (($line = gzgets($handle)) !== false) {
            $row = str_getcsv(trim($line));

            if (count($row) < 4 || $row[0] === '') {
                continue;
            }

            $buffer[] = [
                'code' => $row[0],
                'regency' => $row[1],
                'district' => $row[2],
                'village' => $row[3],
                'postal_code' => $row[4] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($buffer) === 1000) {
                DB::table('regions')->insert($buffer);
                $buffer = [];
            }
        }

        if ($buffer) {
            DB::table('regions')->insert($buffer);
        }

        gzclose($handle);
    }
}
