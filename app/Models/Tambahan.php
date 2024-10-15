<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tambahan extends Model
{
    use HasFactory;

    protected $table = 'a_kebutuhan_dana';
    protected $guarded = ['id'];

    public static function kodeacak($name, $length)
    {
        // for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
        //     $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

        //     if (!DB::table('a_kebutuhan_dana')->where('kode_analisa', $acak)->exists()) {
        //         return $acak;
        //     }
        // }

        // return null;

        do {
            $lastCode = self::whereNotNull('kode_analisa')
                ->orderBy('kode_analisa', 'desc')
                ->value('kode_analisa');

            if (!$lastCode) {
                $prefix = $name;
                $newNumber = 1;
            } else {

                $prefix = substr($lastCode, 0, 3);
                $numberPart = substr($lastCode, 3);

                $newNumber = (int) $numberPart + 1;
            }
            $acak = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        } while (self::where('kode_analisa', $acak)->exists());

        return $acak;
    }
}
