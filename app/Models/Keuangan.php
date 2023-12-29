<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Crypt;

class Keuangan extends Model
{
    use HasFactory;
    protected $table = 'au_keuangan';
    protected $guarded = ['id'];
    
    public static function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!self::where('kode_keuangan', $acak)->exists()) {
                return $acak;
            }
        }

        return null; // Jika tidak ada kode yang unik ditemukan
    }

    public static function du_kodeacak($length)
    {
        
        $kode_acak = '';
        for ($j=0; $j < $length; $j++) { 
            $digit = rand(0, 9); // Menghasilkan angka acak dari 0 hingga 9
            $kode_acak .= $digit;
        }

        // Cek apakah kode sudah ada dalam database
        if (!DB::table('bu_keuangan')->where('kode_biaya', $kode_acak)->exists()) {
            return $kode_acak;
        }else{
            return null;
        }
        
    }

    public static function data_keuangan($data)
    {
        $db = self::where('pengajuan_kode', $data)->get();
        if (count($db) == 0) {
            return 0;
        }
        $cek = DB::table('au_keuangan')
                ->leftJoin('bu_keuangan', 'au_keuangan.kode_keuangan', '=', 'bu_keuangan.keuangan_kode')
                ->select('au_keuangan.*', 'bu_keuangan.*')
                ->where('au_keuangan.kode_keuangan', '=', $db[0]->kode_keuangan)->get();
        
        
        for ($i=0; $i < 10 ; $i++) { 
            $cek[$i]->kode_keuangan = Crypt::encrypt($cek[$i]->kode_keuangan);
        }        
        if (count($cek) == 0) {
            return 0;
        }else{
            return $cek;
        }
    }

    public static function cetak_keuangan($data)
    {
        $cek = DB::table('data_pengajuan')
                    ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                    ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                    ->select('data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.no_telp', 'data_pengajuan.penggunaan', 'data_survei.kasi_kode', 'data_survei.surveyor_kode')
                    ->where('data_pengajuan.kode_pengajuan', '=', $data)->get();
        //
        $kasi = DB::table('v_users')->where('code_user', $cek[0]->kasi_kode)->first();
        $surveyor = DB::table('v_users')->where('code_user', $cek[0]->surveyor_kode)->first();       
        $cek[0]->kasi = $kasi->nama_user;
        $cek[0]->surveyor = $surveyor->nama_user;
        $cek[0]->kode_surveyor = $surveyor->code_user;
        // dd($cek);
        return $cek;
    }
}
