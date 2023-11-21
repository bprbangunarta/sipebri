<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakLaporanController extends Controller
{
    public function laporan_fasilitas()
    {
        $name = request('name');
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where('data_pengajuan.status', 'Disetujui')
            ->where(function ($query) use ($name) {
                $query->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*'
            );
        $data = $query->paginate(7);

        return view('laporan.fasilitas', [
            'data' => $data,
        ]);
    }

    public function laporan_realisasi()
    {
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Disetujui')
                    ->where('data_spk.pengajuan_kode', '!=', null);
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            );
        $data = $query->paginate(7);

        return view('laporan.realisasi', [
            'data' => $data,
        ]);
    }

    public function laporan_penolakan()
    {
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Dibatalkan')
                    ->orWhere('data_pengajuan.status', '=', 'Ditolak');
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            );
        $data = $query->paginate(7);

        return view('laporan.penolakan', [
            'data' => $data,
        ]);
    }

    public function laporan_pendaftaran()
    {
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.status', ['Dibatalkan', 'Ditolak', 'Disetujui'])
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            );
        $data = $query->paginate(7);
        // dd($data);
        return view('laporan.pendaftaran', [
            'data' => $data,
        ]);
    }

    public function laporan_survey_analisa()
    {
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.tracking', [
                'Penjadwalan',
                'Proses Survei',
                'Proses Analisa',
                'Naik Kasi',
                'Naik Komite I',
                'Naik Komite II'
            ])
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            );
        $data = $query->paginate(7);
        // dd($data);
        return view('laporan.survey-analisa', [
            'data' => $data,
        ]);
    }
}
