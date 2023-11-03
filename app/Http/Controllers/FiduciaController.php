<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class FiduciaController extends Controller
{
    public function index()
    {
        $cek = DB::table('data_jaminan')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
            ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_jenis_agunan', 'data_jenis_agunan.kode', '=', 'data_jaminan.jenis_agunan_kode')
            ->where(function ($query) {
                $query->where('data_jaminan.jenis_jaminan', 'Kendaraan')
                    ->where('data_pengajuan.status', 'Disetujui');
            })
            ->select(
                'data_pengajuan.*',
                'data_spk.*',
                'data_nasabah.*',
                'data_survei.*',
                'data_jaminan.*',
                'data_jenis_agunan.jenis_agunan as agunan_jenis'
            );

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }

        return view('cetak.fiducia.index', [
            'data' => $data
        ]);
    }
}
