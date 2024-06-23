<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class RSCCetakController extends Controller
{
    public function cetakanalisa_index()
    {
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('rsc_kondisi_usaha', 'rsc_kondisi_usaha.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc') // Perbaikan di sini
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon'
            )
            ->where(function ($query) {
                $query->whereNotIn('rsc_data_pengajuan.status', ['Proses Analisa', 'Proses Survei', 'Penjadwalan', 'Batal RSC']);
            })
            ->where('rsc_data_survei.surveyor_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);


        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }
        // dd($data);
        return view('rsc.cetak_analisa.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }
}
