<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class FiduciaController extends Controller
{
    public function index()
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')
            ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
            ->where(function ($query) {
                $query->where('data_jaminan.jenis_jaminan', 'Kendaraan')
                    ->where('data_notifikasi.pengajuan_kode', 'pengajuan_kode');
            })
            ->select('data_notifikasi.*', 'data_pengajuan.*', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2', 'users.name');

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
