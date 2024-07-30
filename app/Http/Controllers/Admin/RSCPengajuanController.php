<?php

namespace App\Http\Controllers\Admin;

use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class RSCPengajuanController extends Controller
{
    public function index()
    {
        try {
            $keyword = request('keyword');
            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->leftJoin('rsc_notifikasi', 'rsc_notifikasi.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->leftJoin('rsc_kondisi_usaha', 'rsc_kondisi_usaha.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->leftJoin('rsc_spk', 'rsc_spk.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->leftJoin('v_users', 'v_users.code_user', '=', 'rsc_data_survei.surveyor_kode')
                ->select(
                    'rsc_data_pengajuan.created_at as tgl_rsc',
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_data_pengajuan.penentuan_plafon as plafon',
                    'rsc_data_pengajuan.jangka_waktu',
                    'rsc_data_pengajuan.status',
                    'data_pengajuan.produk_kode',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp as alamat',
                    'rsc_data_survei.kantor_kode',
                    'rsc_data_survei.created_at as tgl_survey',
                    'rsc_kondisi_usaha.created_at as tgl_analisa',
                    'rsc_notifikasi.created_at as tgl_notif',
                    'rsc_spk.created_at as tgl_realisasi',
                    'v_users.nama_user',
                    DB::raw('(SELECT MAX(created_at) FROM rsc_data_usulan WHERE rsc_data_usulan.kode_rsc = rsc_data_pengajuan.kode_rsc) as tgl_persetujuan')
                )
                ->where(function ($query) use ($keyword) {
                    $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                        ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                        ->orWhere('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . $keyword . '%')
                        ->orWhere('rsc_data_pengajuan.status', 'like', '%' . $keyword . '%')
                        ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                        ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                        ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
                })

                ->orderBy('rsc_data_pengajuan.created_at', 'desc')->paginate(10);
            //

            foreach ($data as $item) {
                if ($item->status_rsc == 'EKS') {
                    $data_eks = DB::connection('sqlsrv')->table('m_loan')
                        ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                        ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                        ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                        ->join('REF_PEKERJAAN', 'REF_PEKERJAAN.DESC1', '=', 'm_cif.pekerjaan')
                        ->select(
                            'm_loan.fnama',
                            'm_cif.alamat',
                            'setup_loan.ket',
                            'wilayah.ket as wil',
                        )
                        ->where('noacc', $item->pengajuan_kode)->first();
                    //
                    if ($data_eks) {
                        $item->nama_nasabah = trim($data_eks->fnama);
                        $item->alamat = trim($data_eks->alamat);
                        $item->produk_kode = Midle::data_produk(trim($data_eks->ket));
                        $item->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                    }
                }
            }

            return view('master.RSC.pengajuan.index', [
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function edit_pengajuan_rsc(Request $request)
    {
        try {
            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->select(
                    'rsc_data_pengajuan.created_at as tgl_rsc',
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_data_pengajuan.penentuan_plafon as plafon',
                    'rsc_data_pengajuan.jangka_waktu',
                    'rsc_data_pengajuan.status',
                    'data_pengajuan.produk_kode',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp as alamat',
                )

                ->where('rsc_data_pengajuan.kode_rsc', $request->kode_rsc)->get();
            //

            foreach ($data as $item) {
                if ($item->status_rsc == 'EKS') {
                    $data_eks = DB::connection('sqlsrv')->table('m_loan')
                        ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                        ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                        ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                        ->join('REF_PEKERJAAN', 'REF_PEKERJAAN.DESC1', '=', 'm_cif.pekerjaan')
                        ->select(
                            'm_loan.fnama',
                            'm_cif.alamat',
                            'setup_loan.ket',
                            'wilayah.ket as wil',
                        )
                        ->where('noacc', $item->pengajuan_kode)->first();
                    //
                    if ($data_eks) {
                        $item->nama_nasabah = trim($data_eks->fnama);
                        $item->alamat = trim($data_eks->alamat);
                        $item->produk_kode = Midle::data_produk(trim($data_eks->ket));
                        $item->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                    }
                }
            }
            // dd($data);
            return view('master.rsc.edit', [
                'data' => $data[0]
            ]);
        } catch (\Throwable $th) {
            //
        }
    }

    public function update_pengajuan_rsc(Request $request)
    {
        try {
            $data = ['status' => $request->status];

            $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $request->kode_rsc)->update($data);

            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal disimpan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ada kesalahan.');
        }
    }
}
