<?php

namespace App\Http\Controllers;

use App\Models\RSC;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPenjadwalanController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_pengajuan.plafon',
                'data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', ['Penjadwalan'])
            ->where('rsc_data_pengajuan.kasi_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);
        //

        //===Handle Data Eksternal===//
        foreach ($data as $value) {
            if (strpos($value->status_rsc, 'EKS') !== false) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
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
                    $value->plafon = trim($data_eks->plafond_awal);
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
        }
        //===Handle Data Eksternal===//

        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.penjadwalan.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    public function index_penjadwalan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('v_users', 'v_users.code_user', '=', 'rsc_data_pengajuan.surveyor_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                    'data_survei.kantor_kode',
                    'v_users.nama_user as surveyor',
                    'v_users.code_user',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)
                ->orderBy('rsc_data_pengajuan.created_at', 'desc')->get();

            //===Handle Data Eksternal===//
            foreach ($data as $value) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
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
                    $value->plafon = $data_eks->plafond_awal;
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
            //===Handle Data Eksternal===//

            foreach ($data as $item) {
                $item->kode = Crypt::encrypt($item->kode_pengajuan);
                $item->rsc = Crypt::encrypt($item->kode_rsc);
            }

            $surveyor = DB::table('v_users')
                ->where('role_name', 'Staff Analis')
                ->where('is_active', 1)
                ->get();
            //

            $data_survei = DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->first();

            return view('rsc.penjadwalan.penjadwalan', [
                'data' => $data[0],
                'surveyor' => $surveyor,
                'data_survei' => $data_survei,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_penjadwalan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            if (is_null($request->tgl_survei)) {
                return redirect()->back()->with('error', 'Tanggal survei harus diisi.');
            }

            $data = [
                'kode_rsc' => $enc_rsc,
                'direksi_kode' => 'MMN',
                'kabag_kode' => 'SAR',
                'kasi_kode' => Auth::user()->code_user,
                'surveyor_kode' => $request->kode_petugas,
                'kantor_kode' => $request->kantor_kode,
                'tgl_survei' => $request->tgl_survei,
                'tgl_jadul_1' => $request->tgl_jadul_1,
                'tgl_jadul_2' => $request->tgl_jadul_2,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $data2 = [
                'status' => 'Proses Survei'
            ];

            DB::transaction(function () use ($data, $data2, $enc_rsc) {
                $insert = DB::table('rsc_data_survei')->insert($data);
                $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
            });

            return redirect()->back()->with('success', 'Data berhasil disimpan.');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
    public function update_penjadwalan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            if (is_null($request->tgl_survei)) {
                return redirect()->back()->with('error', 'Tanggal survei harus diisi.');
            }

            $data = [
                'kode_rsc' => $enc_rsc,
                'direksi_kode' => 'MMN',
                'kabag_kode' => 'SAR',
                'kasi_kode' => Auth::user()->code_user,
                'surveyor_kode' => $request->kode_petugas,
                'kantor_kode' => $request->kantor_kode,
                'tgl_survei' => $request->tgl_survei,
                'tgl_jadul_1' => $request->tgl_jadul_1,
                'tgl_jadul_2' => $request->tgl_jadul_2,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $data2 = [
                'status' => 'Proses Survei'
            ];

            DB::transaction(function () use ($data, $data2, $enc_rsc) {
                $insert = DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->update($data);
                $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
            });

            return redirect()->back()->with('success', 'Data berhasil diubah.');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
