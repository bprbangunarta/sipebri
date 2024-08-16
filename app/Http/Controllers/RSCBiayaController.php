<?php

namespace App\Http\Controllers;

use App\Models\RSC;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCBiayaController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $keyword_sqlsrv = RSC::get_sqlsrv(request('keyword'));

        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Perjanjian Kredit')
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        $data = $data->paginate(10);

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
                    'm_cif.alamat',
                    'm_loan.jkwaktu',
                    'setup_loan.ket',
                    'wilayah.ket as wil',
                )
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                $value->jangka_waktu = $data_eks->jkwaktu;
                $value->metode_rps = null;
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.biaya.index', [
            'data' => $data
        ]);
    }

    public function simpan_biaya(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->kode_rsc);

            $biaya = DB::table('rsc_biaya')->where('kode_rsc', $rsc)->first();
            $total = $biaya->administrasi_nominal + $biaya->asuransi_jiwa +
                $biaya->asuransi_tlo + $biaya->poko_dibayar + $biaya->poko_dibayar +
                (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga) + (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda);


            $data = [
                'bunga_dibayar' => (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga),
                'denda_dibayar' =>  (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda),
                'total' =>  $total,
            ];

            $update = DB::table('rsc_biaya')->where('kode_rsc', $rsc)->update($data);

            if ($update) {
                return response()->back()->with('success', 'Biaya RSC berhasil diubah.');
            } else {
                return response()->back()->with('error', 'Biaya RSC gagal diubah.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function get_biaya(Request $request)
    {
        $kode_rsc = Crypt::decrypt($request->input('data'));

        $data = DB::table('rsc_biaya')
            ->where('kode_rsc', $kode_rsc)->first();

        return response()->json($data);
    }

    public function cetak_biaya(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->query('rsc'));

            // $data = DB::table('rsc_biaya_rsc')
            //     ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_biaya_rsc.kode_rsc')
            //     ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_biaya_rsc.kode_rsc')

            return view('rsc.biaya.detail_biaya');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda ditolak.');
        }
    }
}
