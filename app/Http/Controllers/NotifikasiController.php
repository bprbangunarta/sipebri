<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    public function data_penolakan()
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->where('data_pengajuan.status', '=', 'Ditolak')
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.tracking', 'data_pengajuan.plafon', 'data_pengajuan.updated_at', 'data_pengajuan.kategori', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2');
        //

        $data = $cek->paginate(10);
        // dd($data);
        return view('notifikasi.penolakan.index', [
            'data' => $data,
        ]);
    }

    public function tambah_penolakan()
    {
        return view('notifikasi.penolakan.tambah');
    }

    public function edit_penolakan()
    {
        return view('notifikasi.penolakan.edit');
    }

    public function cetak_penolakan()
    {
        return view('notifikasi.penolakan.cetak');
    }
}
