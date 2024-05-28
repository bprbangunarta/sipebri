<?php

namespace App\Http\Controllers;

use App\Models\RSC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPersetujuanController extends Controller
{
    public function index()
    {
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
            )
            ->whereIn('rsc_data_pengajuan.status', ['Proses Persetujuan'])
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.persetujuan.index', [
            'data' => $data,
        ]);
    }

    public function informasi(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.jangka_waktu',
                    'rsc_data_pengajuan.penentuan_plafon as plafon',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.produk_kode',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)
                ->orderBy('rsc_data_pengajuan.created_at', 'desc')
                ->get();

            foreach ($data as $item) {
                $item->kode = Crypt::encrypt($item->kode_pengajuan);
                $item->rsc = Crypt::encrypt($item->kode_rsc);
            }
            // dd($data);
            return view('rsc.persetujuan.informasi', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
