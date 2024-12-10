<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JadwalSurvei implements FromView
{
    public function view(): View
    {
        $tgl_survei = request('tgl_survei');
        $tgl_survei_sampai = request('tgl_survei_sampai');

        $data = DB::table('data_jadwal_survei')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_jadwal_survei.pengajuan_kode',)
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_pengajuan.tracking',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
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
            // ->where(function ($query) {
            //     $query->whereRaw("DATE(data_jadwal_survei.tgl_survei) = ?", [Carbon::today()->addDay()->toDateString()]);
            // })
            ->where(function ($query) use ($tgl_survei, $tgl_survei_sampai) {
                if (!empty($tgl_survei) && !empty($tgl_survei_sampai)) {
                    $query->where(function ($subQuery) use ($tgl_survei, $tgl_survei_sampai) {
                        $subQuery->whereRaw("DATE(data_jadwal_survei.tgl_survei) BETWEEN ? AND ?", [$tgl_survei, $tgl_survei_sampai]);
                    });
                } elseif (!empty($tgl_survei) && empty($tgl_survei_sampai)) {
                    $query->whereRaw("DATE(data_jadwal_survei.tgl_survei) = ?", [Carbon::today()->addDay()->toDateString()]);
                }
            })

            ->orderBy('data_nasabah.nama_nasabah', 'ASC')
            ->get();
        //

        $users = [];
        foreach ($data as $value) {
            $value->tanggal = Carbon::parse($value->tanggal)->translatedFormat('d F Y');
            $users[] = (object) [
                'kode_pengajuan' => $value->kode_pengajuan,
                'surveyor_kode'  => $value->surveyor_kode,
                'nama_user'      => $value->nama_user,
            ];
        }

        return view('analisa.exports.jadwal_survei', compact('data', 'users'));
    }
}
