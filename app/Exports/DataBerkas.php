<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DataBerkas implements FromView
{
    public function view(): View
    {
        $tgl_kirim = request('tgl_kirim') ? Carbon::parse(request('tgl_kirim'))->startOfDay() : null;
        $tgl_kirim_sampai = request('tgl_kirim_sampai') ? Carbon::parse(request('tgl_kirim_sampai'))->endOfDay() : null;
        $tgl_terima = request('tgl_terima') ? Carbon::parse(request('tgl_terima'))->startOfDay() : null;
        $tgl_terima_sampai = request('tgl_terima_sampai') ? Carbon::parse(request('tgl_terima_sampai'))->endOfDay() : null;
        // $tgl_kirim = Carbon::parse(request('tgl_kirim'))->startOfDay()->format('Y-m-d H:i:s');
        // $tgl_kirim_sampai = request('tgl_kirim_sampai') ? Carbon::parse(request('tgl_kirim_sampai'))->endOfDay()->format('Y-m-d H:i:s') : null;
        // $tgl_terima = Carbon::parse(request('tgl_terima'))->startOfDay()->format('Y-m-d H:i:s');
        // $tgl_terima_sampai = request('tgl_terima_sampai') ? Carbon::parse(request('tgl_terima_sampai'))->endOfDay()->format('Y-m-d H:i:s') : null;
        $darikantor = request('dari_kantor');
        $kekantor = request('ke_kantor');
        $produk = request('kode_produk');

        $data = DB::table('data_berkas')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_berkas.pengajuan_kode')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('v_users as pengirim', 'pengirim.code_user', '=', 'data_berkas.user_pengirim')
            ->leftJoin('v_users as penerima', 'penerima.code_user', '=', 'data_berkas.user_penerima')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_berkas.pengajuan_kode')
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'pengirim.nama_user as user_pengirim',
                'penerima.nama_user as user_penerima',
                'data_berkas.user_tujuan',
                'data_berkas.dari_kantor',
                'data_berkas.ke_kantor',
                DB::raw("DATE_FORMAT(data_berkas.tgl_kirim, '%Y-%m-%d') as tgl_kirim"),
                DB::raw("DATE_FORMAT(data_berkas.tgl_terima, '%Y-%m-%d') as tgl_terima")
            )

            ->where(function ($query) use ($tgl_kirim, $tgl_kirim_sampai) {
                if (!empty($tgl_kirim) && !empty($tgl_kirim_sampai)) {
                    $query->where(function ($subQuery) use ($tgl_kirim, $tgl_kirim_sampai) {
                        $subQuery->whereRaw("DATE(data_berkas.tgl_kirim) BETWEEN ? AND ?", [$tgl_kirim, $tgl_kirim_sampai]);
                    });
                } elseif (!empty($tgl_kirim) && empty($tgl_kirim_sampai)) {
                    $query->where(function ($subQuery) use ($tgl_kirim) {
                        $subQuery->whereRaw("DATE(data_berkas.tgl_kirim) = ?", [$tgl_kirim]);
                    });
                }
            })

            ->orWhere(function ($query) use ($tgl_terima, $tgl_terima_sampai) {
                if (!empty($tgl_terima) && !empty($tgl_terima_sampai)) {
                    $query->where(function ($subQuery) use ($tgl_terima, $tgl_terima_sampai) {
                        $subQuery->whereRaw("DATE(data_berkas.tgl_terima) BETWEEN ? AND ?", [$tgl_terima, $tgl_terima_sampai]);
                    });
                } elseif (!empty($tgl_terima) && empty($tgl_terima_sampai)) {
                    $query->where(function ($subQuery) use ($tgl_terima) {
                        $subQuery->whereRaw("DATE(data_berkas.tgl_terima) = ?", [$tgl_terima]);
                    });
                }
            })

            ->where(function ($query) use ($darikantor, $kekantor, $produk) {
                $query->where(function ($subQuery) use ($produk, $darikantor, $kekantor) {
                    if (!empty($produk)) {
                        $subQuery->where('data_pengajuan.produk_kode', 'like', '%' . $produk . '%');
                    }
                    if (!empty($darikantor)) {
                        $subQuery->where('data_berkas.dari_kantor', 'like', '%' . $darikantor . '%');
                    }
                    if (!empty($kekantor)) {
                        $subQuery->where('data_berkas.ke_kantor', 'like', '%' . $kekantor . '%');
                    }
                });
            })

            ->orderBy('data_berkas.tgl_kirim', 'DESC')
            ->get();
        //

        return view('analisa.exports.data_berkas', compact('data'));
    }
}
