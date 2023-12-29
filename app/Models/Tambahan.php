<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tambahan extends Model
{
    use HasFactory;
    // protected $connection = 'sqlsrv';
    protected $table = 'a_kebutuhan_dana';
    protected $guarded = ['id'];

    public static function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('a_kebutuhan_dana')->where('kode_analisa', $acak)->exists()) {
                return $acak;
            }
        }

        return null; // Jika tidak ada kode yang unik ditemukan
    }
}
