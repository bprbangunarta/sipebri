<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Penjadwalan implements FromView
{
    public function view(): View
    {
        $kasi = request('kode_kasi');

        $data = DB::table('data_survei')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode',)
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->leftJoin('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_pengajuan.tracking',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kecamatan',
                'data_nasabah.kelurahan',
                'data_nasabah.kota',
                'data_survei.kantor_kode',
                'v_users.nama_user',
                'data_survei.surveyor_kode',
                'data_survei.latitude',
                'data_survei.longitude',
                'data_survei.foto',
                DB::raw("DATE_FORMAT(data_survei.tgl_survei, '%d-%m-%y') as tgl_survei"),
                DB::raw("DATE_FORMAT(data_pengajuan.created_at, '%d-%m-%Y') as tanggal"),
                'data_survei.catatan_survei',
                DB::raw("DATE_FORMAT(data_survei.tgl_jadul_1, '%d-%m-%y') as tgl_jadul_1"),
                'data_survei.catatan_jadul_1',
                DB::raw("DATE_FORMAT(data_survei.tgl_jadul_2, '%d-%m-%y') as tgl_jadul_2"),
                'data_survei.catatan_jadul_2',
            )
            ->whereNot('data_pengajuan.produk_kode', 'KTA')
            ->where('data_survei.kasi_kode', '!=', '')
            ->where('data_pengajuan.status', '=', 'Sudah Otorisasi')
            ->where('data_pengajuan.tracking',  'Penjadwalan')
            ->where('data_survei.kasi_kode', $kasi)

            ->orderBy('data_nasabah.nama_nasabah', 'ASC')
            ->get();

        $users = DB::table('v_users')
            ->where('role_name', '=', 'Staff Analis')
            ->where('is_active', 1)
            ->select('nama_user')->get();

        return view('analisa.exports.penjadwalan', compact('data', 'users'));
    }
}
