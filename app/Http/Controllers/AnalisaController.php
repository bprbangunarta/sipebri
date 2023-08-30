<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalisaController extends Controller
{
    public function index(){
        $usr = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
                        ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                        ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                        ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
                        ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
                        ->where('data_survei.surveyor_kode', '=', $usr)
                        ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2','users.name')->get();
        
        return view('analisa.proses', [
            'data' => $cek
        ]);
    }
}
