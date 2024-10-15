<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Lain extends Model
{
    use HasFactory;
    protected $table = 'au_lainnya';
    protected $guarded = ['id'];

    public static function kodeacak($name, $length)
    {
        // for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
        //     $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

        //     // Cek apakah kode sudah ada dalam database
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
        } while (self::where('kode_usaha', $acak)->exists());

        return $acak;
    }

    public static function au_lain($data)
    {
        $cek = self::where('kode_usaha', $data)->get();
        return $cek;
    }

    public static function index_lain($data)
    {
        $cek = self::where('pengajuan_kode', $data)->get();
        return $cek;
    }

    public static function du_kodeacak($length)
    {

        $kode_acak = '';
        for ($j = 0; $j < $length; $j++) {
            $digit = rand(0, 9); // Menghasilkan angka acak dari 0 hingga 9
            $kode_acak .= $digit;
        }

        // Cek apakah kode sudah ada dalam database
        if (!DB::table('du_lainnya')->where('kode_lain', $kode_acak)->exists()) {
            return $kode_acak;
        } else {
            return null;
        }
    }

    public static function bu_kodeacak($length)
    {

        $kode_acak = '';
        for ($j = 0; $j < $length; $j++) {
            $digit = rand(0, 9); // Menghasilkan angka acak dari 0 hingga 9
            $kode_acak .= $digit;
        }

        // Cek apakah kode sudah ada dalam database
        if (!DB::table('bu_lainnya')->where('kode_lain', $kode_acak)->exists()) {
            return $kode_acak;
        } else {
            return null;
        }
    }

    public static function bahan_baku_kodeacak($length)
    {

        $kode_acak = '';
        for ($j = 0; $j < $length; $j++) {
            $digit = rand(0, 9); // Menghasilkan angka acak dari 0 hingga 9
            $kode_acak .= $digit;
        }

        // Cek apakah kode sudah ada dalam database
        if (!DB::table('bu_bahan_baku_lainnya')->where('kode_barang', $kode_acak)->exists()) {
            return $kode_acak;
        } else {
            return null;
        }
    }


    public static function editlainnya($data)
    {
        $biaya = DB::table('bu_lainnya')->where('usaha_kode', $data)->get();
        $data2 = DB::table('du_lainnya')->where('usaha_kode', $data)->get();

        return [$biaya, $data2];
    }
}
