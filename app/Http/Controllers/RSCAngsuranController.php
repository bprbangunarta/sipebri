<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RSCAngsuranController extends Controller
{
    public function index()
    {
        $data = DB::table('rsc_spk')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_spk.kode_rsc')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'rsc_data_pengajuan.suku_bunga',
                'rsc_data_pengajuan.jangka_waktu',
                'rsc_data_pengajuan.penentuan_plafon',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.status_rsc',
                'data_pengajuan.produk_kode',
                'rsc_spk.created_at as tgl_realisasi',
                'rsc_spk.no_spk',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.no_cif',
                DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE())), '%d-%m-%Y') as tgl_realisasi")
            )
            ->orderBy('rsc_spk.created_at', 'desc')->paginate(10);
        //

        foreach ($data as $item) {
            if ($item->status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                        'm_cif.alamat',
                        'm_cif.nocif',
                        'setup_loan.ket',
                    )
                    ->where('noacc', $item->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $item->nama_nasabah = trim($data_eks->fnama);
                    $item->alamat_ktp = trim($data_eks->alamat);
                    $item->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $item->no_cif = trim($data_eks->nocif);
                    $item->no_tab = trim($data_eks->noacdroping);
                    $item->no_kredit = trim($data_eks->noacc);
                }
            } else {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->select(
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                    )
                    ->where('nocif', $item->no_cif)->first();
                //
                if ($data_eks) {
                    $item->no_tab = trim($data_eks->noacdroping);
                    $item->no_kredit = trim($data_eks->noacc);
                }
            }
        }
        dd($data);
        return view('rsc.angsuran.index', [
            'data' => $data
        ]);
    }
}
