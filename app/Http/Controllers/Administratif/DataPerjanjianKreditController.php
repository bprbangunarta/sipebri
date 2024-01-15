<?php

namespace App\Http\Controllers\Administratif;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;

class DataPerjanjianKreditController extends Controller
{
    public function index(Request $request)
    {

        $name = request('keyword');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->where('data_pengajuan.status', 'Disetujui')
            ->where('data_survei.kantor_kode', '=', Auth::user()->kantor_kode)
            ->whereNotNull('data_spk.no_spk')
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_spk.*',
                'data_pengajuan.*',
                'data_notifikasi.*',
                'data_pengajuan.*',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_survei.surveyor_kode',
                'data_spk.created_at as tanggal',
                'data_spk.otorisasi as otorpk',
                'users.name'
            )
            ->orderBy('data_spk.created_at', 'desc');

        //Enkripsi kode pengajuan
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
        }
        // dd($data);
        return view('administratif.data-perjanjian-kredit.index', [
            'data' => $data
        ]);
    }

    public function batal_perjanjian_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->kode);

            $data_spk = DB::table('data_spk')->where('pengajuan_kode', $enc)->first();
            if (!is_null($data_spk)) {
                $data = [
                    'pengajuan_kode' => $data_spk->pengajuan_kode . 'XX',
                    'input_user' => Auth::user()->code_user,
                    'updated_at' => now(),
                ];

                $data2 = [
                    'on_current' => '0',
                ];

                $data3 = [
                    'on_current' => 0,
                ];

                $agunan = DB::table('data_jaminan')->where('pengajuan_kode', $enc)->get();
                if (count($agunan) != 0) {
                    foreach ($agunan as $item) {
                        DB::table('data_jaminan')->where('id', $item->id)->update($data3);
                    }
                }

                DB::transaction(function () use ($data, $data2, $enc) {
                    DB::table('data_spk')->where('pengajuan_kode', $enc)->update($data);
                    DB::table('data_pengajuan')->where('kode_pengajuan', $enc)->update($data2);
                });
                return redirect()->back()->with('success', "No Perjanjian Kredit Berhasil Dibatalkan");
            } else {
                return redirect()->back()->with('error', "Data Tidak Ditemukan");
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "No Perjanjian Kredit Gagal Dibatalkan");
        }
    }
}
