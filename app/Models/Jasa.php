<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jasa extends Model
{
    use HasFactory;
    protected $table = 'au_jasa';
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

    public static function au_jasa($data)
    {
        $cek = self::where('pengajuan_kode', $data)->get();
        return $cek;
    }

    public static function cetak_jasa($data)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->select('data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.no_telp', 'data_pengajuan.penggunaan', 'data_survei.kasi_kode', 'data_survei.surveyor_kode')
            ->where('data_pengajuan.kode_pengajuan', '=', $data)->get();
        //
        $kasi = DB::table('v_users')->where('code_user', $cek[0]->kasi_kode)->first();
        $surveyor = DB::table('v_users')->where('code_user', $cek[0]->surveyor_kode)->first();

        //Data Usaha Jasa
        $jasa = Jasa::where('pengajuan_kode', $data)->get();
        //Total Pendapatan Usaha Jasa / laba bersih
        $total = [];
        for ($i = 0; $i < count($jasa); $i++) {
            $total[] = $jasa[$i]->laba_bersih;
        }
        $jasa->lababersih = array_sum($total) ?? 0;

        //Total Biaya Usaha Jasa
        $totalb = [];
        for ($j = 0; $j < count($jasa); $j++) {
            $totalb[] = $jasa[$j]->pengeluaran;
        }
        $jasa->totalpengeluaran = array_sum($totalb) ?? 0;
        // Total Pendapatan
        $totalpen = [];
        for ($k = 0; $k < count($jasa); $k++) {
            $totalpen[] = $jasa[$k]->pendapatan;
        }
        $jasa->totalpendapatan = array_sum($totalpen) ?? 0;
        //Total Biaya Pajak
        $totalp = [];
        for ($m = 0; $m < count($jasa); $m++) {
            $totalp[] = $jasa[$m]->b_pajak;
        }
        $jasa->totalpajak = array_sum($totalp) ?? 0;
        //Total Biaya Lain
        $totall = [];
        for ($l = 0; $l < count($jasa); $l++) {
            $totall[] = $jasa[$l]->b_lainnya;
        }
        $jasa->totallain = array_sum($totall) ?? 0;

        $cek[0]->kasi = $kasi->nama_user;
        $cek[0]->surveyor = $surveyor->nama_user;
        $cek[0]->kode_surveyor = $surveyor->code_user;
        return [$cek, $jasa];
    }
}
