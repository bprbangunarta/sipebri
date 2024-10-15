<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pertanian extends Model
{
    use HasFactory;
    protected $table = 'au_pertanian';
    protected $guarded = ['id'];

    public static function kodeacak($name, $length)
    {
        // for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
        //     $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

        //     if (!self::where('kode_usaha', $acak)->exists()) {
        //         return $acak;
        //     }
        // }

        // return null;

        do {
            $lastCode = self::whereNotNull('kode_usaha')
                ->orderBy('kode_usaha', 'desc')
                ->value('kode_usaha');

            if (!$lastCode) {
                $prefix = $name;
                $newNumber = 1;
            } else {

                $prefix = substr($lastCode, 0, 3);
                $numberPart = substr($lastCode, 3);

                $newNumber = (int) $numberPart + 1;
            }

            $acak = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        } while (!self::where('kode_usaha', $acak)->exists());

        return $acak;
    }

    public static function au_pertanian($data)
    {
        $c = self::where('pengajuan_kode', $data)->get();

        for ($k = 0; $k < count($c); $k++) {
            $bu = DB::table('bu_pertanian')->where('usaha_kode', $c[$k]->kode_usaha)->first();
            if (!is_null($bu)) {
                $c[$k]->total_pengeluaran = $c[$k]->pengeluaran + $bu->amortisasi + $bu->pinjaman_bank;
            } else {
                $c[$k]->total_pengeluaran = 0;
            }
        }

        return $c;
    }

    public static function total_biaya($data)
    {
        $bu = DB::table('bu_pertanian')->where('usaha_kode', $data)->first();
        if ($bu) {
            $total = array_sum(array_slice([
                (int)$bu->pengolahan_tanah,
                (int)$bu->bibit,
                (int)$bu->pupuk,
                (int)$bu->pestisida,
                (int)$bu->pengairan,
                (int)$bu->tenaga_kerja,
                (int)$bu->panen,
                (int)$bu->penggarap,
                (int)$bu->pajak,
                (int)$bu->iuran_desa,
                (int)$bu->amortisasi,
                (int)$bu->pinjaman_bank
            ], 0, 13));
        } else {
            return 0;
        }
        return $total;
    }
}
