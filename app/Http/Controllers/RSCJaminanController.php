<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCJaminanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_telp',
                    'rsc_data_pengajuan.penentuan_plafon as plafon',
                    'data_pengajuan.produk_kode',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_data_pengajuan.jangka_waktu',
                    'data_spk.no_spk',
                    'data_spk.created_at',
                    'data_spk.updated_at',
                    'data_pengajuan.plafon as plafon_awal',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)
                ->get();
            //

            foreach ($data as $item) {
                if ($item->status_rsc == 'EKS') {
                    $data_eks = DB::connection('sqlsrv')->table('m_loan')
                        ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                        ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                        ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                        ->select(
                            'm_loan.fnama as nama_nasabah',
                            'm_cif.alamat as alamat_ktp',
                            'm_cif.nohp as no_telp',
                            'setup_loan.ket',
                            'm_loan.no_spk',
                            'm_loan.tgleff',
                            'm_loan.chgtgljam',
                            'm_loan.plafond_awal',
                        )
                        ->where('m_loan.noacc', $enc)->first();

                    if ($data_eks) {
                        $item->nama_nasabah = trim($data_eks->nama_nasabah);
                        $item->alamat_ktp = trim($data_eks->alamat_ktp);
                        $item->no_telp = trim($data_eks->no_telp);
                        $item->no_spk = trim($data_eks->no_spk);
                        $item->created_at = trim($data_eks->tgleff);
                        $item->updated_at = trim($data_eks->chgtgljam);
                        $item->plafon_awal = trim($data_eks->plafond_awal);
                    }
                }
            }

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                // $item->status_rsc = $data_rsc->status_rsc;
            }

            return view('rsc.jaminan.index', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
