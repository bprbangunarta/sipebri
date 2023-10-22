<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Midle;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class KomiteController extends Controller
{
    public function index()
    {
        $usr = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->orWhere('data_survei.surveyor_kode', '=', $usr)
            // ->orWhere('data_pengajuan.tracking', '=', 'Persetujuan Komite')
            ->where('data_pengajuan.tracking', '=', 'Persetujuan Komite')
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.tracking', 'data_pengajuan.plafon', 'data_pengajuan.created_at', 'data_pengajuan.kategori', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2', 'users.name');

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
        return view('komite.index', [
            'data' => $data,
        ]);
    }

    public function getdata(Request $request)
    {
        $kode = $request->input('field');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.plafon', 'data_nasabah.nama_nasabah')->get();

        //User
        $usr = Auth::user()->code_user;
        $user = DB::table('v_users')->where('code_user', $usr)->get();
        $cek[0]->role_user = $user[0]->role_name;
        return response()->json($cek);
    }

    public function simpan(Request $request)
    {

        try {
            $usr = Auth::user()->code_user;
            $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();

            //Cek role user
            if ($user->role_name == 'Staff Analis') {
                $komite = 'komite1';
                $catatan = 'catatan1';
                $waktu = 'waktu1';
            } elseif ($user->role_name == 'Kasi Analis') {
                $komite = 'komite2';
                $catatan = 'catatan2';
                $waktu = 'waktu2';
            } elseif ($user->role_name == 'Kabag Analis') {
                $komite = 'komite3';
                $catatan = 'catatan3';
                $waktu = 'waktu3';
            } elseif ($user->role_name == 'Direksi') {
                $komite = 'komite4';
                $catatan = 'catatan4';
                $waktu = 'waktu4';
            }

            $name = 'KMT';
            $length = 5;
            $kode = Midle::kode_komite($name, $length);
            $data = [
                'kode_analisa' => $kode,
                'pengajuan_kode' => $request->kode_pengajuan,
                $komite => $usr,
                $catatan => $request->catatan,
                $waktu => now(),
            ];

            DB::table('a_komite')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function catatan($pengajuan)
    {
        $data = DB::table('a_komite')->where('pengajuan_kode', $pengajuan)->first();
        return response()->json($data);
    }
}
