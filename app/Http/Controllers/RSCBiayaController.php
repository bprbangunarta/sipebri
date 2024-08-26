<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
                    ->orWhere('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->whereIn('rsc_data_pengajuan.status', ['Perjanjian Kredit', 'Selesai'])
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

            $data_rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $rsc)->first();

            // $selisih = abs($data_rsc->tunggakan_bunga - (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga));

            // $bunga_dibayar_baru = $biaya->bunga_dibayar + $selisih;

            $selisih  = abs($data_rsc->penentuan_plafon - $data_rsc->baki_debet);

            $bunga_dibayar_baru = abs($selisih - (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga));

            $total = $biaya->administrasi_nominal + $biaya->asuransi_jiwa +
                $biaya->asuransi_tlo + $bunga_dibayar_baru + $biaya->poko_dibayar + (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda);

            $data = [
                'bunga_dibayar' => (int)str_replace(["Rp", " ", "."], "", $bunga_dibayar_baru),
                'denda_dibayar' =>  (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda),
                'total' =>  $total,
            ];

            $data2 = [
                'tunggakan_bunga' => (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga),
            ];


            DB::transaction(function () use ($data2, $data, $rsc) {
                DB::table('rsc_biaya')->where('kode_rsc', $rsc)->update($data);
                DB::table('rsc_data_pengajuan')->where('kode_rsc', $rsc)->update($data2);
            });

            return redirect()->back()->with('success', 'Biaya RSC berhasil diubah.');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function get_biaya(Request $request)
    {
        $kode_rsc = Crypt::decrypt($request->input('data'));

        $rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $kode_rsc)->first();

        $data = DB::table('rsc_biaya')
            ->where('kode_rsc', $kode_rsc)->first();

        $data->bunga_dibayar = $rsc->tunggakan_bunga;

        return response()->json($data);
    }

    public function cetak_biaya(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->query('rsc'));

            $data = DB::table('rsc_biaya')
                ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_biaya.kode_rsc')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('rsc_spk', 'rsc_spk.kode_rsc', '=', 'rsc_biaya.kode_rsc')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_data_pengajuan.baki_debet',
                    'rsc_data_pengajuan.tunggakan_bunga',
                    'rsc_data_pengajuan.tunggakan_poko',
                    'rsc_data_pengajuan.jangka_waktu',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.suku_bunga',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.jenis_persetujuan',
                    'rsc_biaya.administrasi_nominal',
                    'rsc_biaya.poko_dibayar',
                    'rsc_biaya.bunga_dibayar',
                    'rsc_biaya.denda_dibayar',
                    'rsc_biaya.ujroh',
                    'rsc_biaya.asuransi_tlo',
                    'rsc_biaya.asuransi_jiwa',
                    'rsc_biaya.ujroh',
                    'rsc_biaya.total',
                    DB::raw("DATE_FORMAT(COALESCE(rsc_spk.created_at, CURDATE()), '%Y%m%d') as tgl_mulai_rsc"),
                    DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE()) + INTERVAL rsc_data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir_rsc")
                )
                ->where('rsc_biaya.kode_rsc', $rsc)->first();
            //

            if ($data->status_rsc == "IN") {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->select(
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                    )
                    ->where('fnama', $data->nama_nasabah)
                    ->where('plafond', '>', 0)->first();
                //
                $data->no_loan = $data_eks->noacc;
                $data->no_acc_dropping = $data_eks->noacdroping;
            } else {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.noacdroping',
                    )
                    ->where('noacc', $data->pengajuan_kode)->first();
                //
                $data->no_loan = $data->pengajuan_kode;
                $data->no_acc_dropping = $data_eks->noacdroping;
                $data->nama_nasabah = trim($data_eks->fnama);
            }

            $tgl_mulai_rsc = Carbon::parse($data->tgl_mulai_rsc);
            $data->tgl_mulai_rsc = $tgl_mulai_rsc->isoFormat('D MMMM Y');

            $tgl_jth_tempo = Carbon::parse($data->tgl_akhir_rsc);
            $data->tgl_akhir_rsc = $tgl_jth_tempo->isoFormat('D MMMM Y');

            $data->kapitalisasi = ($data->penentuan_plafon - $data->baki_debet) + $data->poko_dibayar;

            return view('rsc.biaya.detail_biaya', [
                'data' => $data
            ]);
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda ditolak.');
        }
    }
}
