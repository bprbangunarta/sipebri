<?php

namespace App\Http\Controllers\Admin;

use App\Models\Survei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AdminSurveiController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');

        $survei = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')

            ->whereIn('data_pengajuan.tracking', ['Penjadwalan', 'Proses Survei', 'Proses Analisa', 'Persetujuan Komite', 'Naik Kasi', 'Naik Komite I', 'Naik Komite II', 'Realisasi', 'Selesai'])
            // ->whereNotNull('data_kantor')
            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_nasabah.no_telp',
                'data_survei.tgl_survei',
                'v_users.nama_user',
                'data_pengajuan.tracking',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_pengajuan.created_at', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.alamat_ktp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.plafon', 'like', '%' . $keyword . '%')
                    ->orWhere('data_nasabah.no_telp', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.tgl_survei', 'like', '%' . $keyword . '%')
                    ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.tracking', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_pengajuan.created_at', 'desc')->paginate(10);
        //

        return view('master.survei.index', ['data' => $survei]);
    }

    public function edit($kode)
    {

        $survei = Survei::where('pengajuan_kode', $kode)->first();
        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $analis = DB::table('v_users')->where('role_name', 'Staff Analis')->get();
        // $kantor = DB::table('data_kantor')->get();

        return view('master.survei.edit', [
            'data' => $survei,
            'kasi' => $kasi,
            'analis' => $analis,
            // 'kantor' => $kantor,
        ]);
    }

    public function update(Request $request)
    {
        try {

            $data = [
                'kantor_kode' => $request->kantor_kode,
                'kasi_kode' => $request->kasi_kode,
                'surveyor_kode' => $request->surveyor_kode,
                'tgl_survei' => $request->tgl_survei,
                'tgl_jadul_1' => $request->tgl_jadul_1,
                'tgl_jadul_2' => $request->tgl_jadul_2,
                'latitude' => $request->latitude,
                'longitude' => $request->longtitude,
            ];

            Survei::where('pengajuan_kode', $request->input('survei'))->update($data);
            return redirect()->back()->with('success', 'Data Berhasil Diubah');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Gagal Diubah');
        }
    }
}
