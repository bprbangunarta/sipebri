<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Carbon\Carbon;

class FiduciaController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $cek = DB::table('data_jaminan')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
            ->join('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_jenis_agunan', 'data_jenis_agunan.kode', '=', 'data_jaminan.jenis_agunan_kode')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')

            ->where('data_jaminan.jenis_jaminan', 'Kendaraan')
            ->whereNotNull('data_notifikasi.no_notifikasi')

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.no_loan', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.username', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->select(
                'data_pengajuan.*',
                'data_spk.created_at as tanggal',
                'data_spk.*',
                'data_nasabah.*',
                'data_survei.*',
                'data_jaminan.*',
                'data_jenis_agunan.jenis_agunan as jenis_kendaraan'
            )
            ->orderBy('data_spk.created_at', 'desc');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
        }

        return view('cetak.fiducia.index', [
            'data' => $data
        ]);
    }

    public function cetak_fiducia(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->join('data_pekerjaan', 'data_pekerjaan.kode_pekerjaan', '=', 'data_nasabah.pekerjaan_kode')
                ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->leftJoin('data_jenis_agunan', 'data_jenis_agunan.kode', '=', 'data_jaminan.jenis_agunan_kode')
                ->select(
                    'data_pengajuan.*',
                    'data_nasabah.*',
                    'data_pekerjaan.nama_pekerjaan',
                    'data_jaminan.*',
                    'data_spk.no_spk',
                    'data_jaminan.atas_nama as nama_pemilik_bpkb',
                    'data_jenis_agunan.jenis_agunan as nama_jenis_jaminan',
                )
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();
            //
            $hari = Carbon::now();
            $data[0]->hari_ini = Carbon::parse($hari)->translatedFormat('d F Y');

            return view('cetak-berkas.fiducia.cetak-fiducia', [
                'data' => $data[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
