<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Survei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaController extends Controller
{
    public function index()
    {
        $usr = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->where('data_survei.surveyor_kode', '=', $usr)
            ->where('data_pengajuan.tracking', '=', 'Proses Survei')
            ->orWhere('data_pengajuan.tracking', '=', 'Proses Analisa')
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.tracking', 'data_pengajuan.kategori', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2', 'users.name');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }
        // dd($data);
        return view('staff.analisa.index', [
            'data' => $data
        ]);
    }

    public function data_jadul($pengajuan)
    {
        $data = DB::table('data_survei')
            ->leftJoin('data_pengajuan', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.nama_nasabah', 'data_survei.id')
            ->where('data_survei.pengajuan_kode', '=', $pengajuan)->get();
        return response()->json($data[0]);
    }

    public function simpanjadul(Request $request)
    {

        try {
            $survei = Survei::where('id', $request->id)->first();

            // dd($survei->tgl_survei);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
