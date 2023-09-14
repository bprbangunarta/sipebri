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
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!self::where('kode_usaha', $acak)->exists()) {
                return $acak;
            }
        }

        return null; // Jika tidak ada kode yang unik ditemukan
    }

    public static function au_pertanian($data)
    {
        $c = self::where('pengajuan_kode', $data)->get();
        
        for ($k=0; $k < count($c); $k++) { 
            $bu = DB::table('bu_pertanian')->where('usaha_kode', $c[$k]->kode_usaha)->first();
            if (!is_null($bu)) {
                $c[$k]->total_pengeluaran = $c[$k]->pengeluaran + $bu->amortisasi + $bu->pinjaman_bank;
            }else{
                $c[$k]->total_pengeluaran = 0;
            }
        }
        
        return $c;
    }
}
