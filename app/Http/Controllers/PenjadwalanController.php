<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use App\Models\Kantor;
use App\Models\Survei;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class PenjadwalanController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $user = Auth::user()->code_user;

        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('users as cs', 'cs.code_user', '=', 'data_pengajuan.input_user')
            ->leftJoin('users as kasi', 'kasi.code_user', '=', 'data_survei.kasi_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('data_berkas', 'data_berkas.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')

            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_pengajuan.plafon',
                'data_pengajuan.jangka_waktu as jk',
                'data_pengajuan.metode_rps',
                'data_kantor.nama_kantor',
                'data_kantor.kode_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name',
                'users.code_user',
                'users.kantor_kode as daftar_di',
                'data_nasabah.kecamatan',
                'data_nasabah.kelurahan',
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kategori',
                'data_produk.*',
                'cs.username as nama_cs',
                'cs.kantor_kode as kantor_cs',
                'data_berkas.tgl_terima',
            )

            ->when(auth()->user()->roles[0]->name == 'Kasi Analis', function ($query) use ($user) {
                $query->where('data_survei.kasi_kode', '=', $user);
            })
            ->where('data_pengajuan.status', '=', 'Sudah Otorisasi')
            ->where('data_pengajuan.tracking', '=', 'Penjadwalan')
            ->where('kasi.is_active', '=', 1)
            // ->orWhere('data_pengajuan.tracking', '=', 'Proses Survei')

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_pengajuan.created_at', 'desc');;

        $datas = $cek->paginate(10);

        return view('analisa.penjadwalan', [
            'data' => $datas,
        ]);
    }

    public function edit($id)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->where('data_pengajuan.kode_pengajuan', '=', $id)
            ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.*', 'users.name')->get();

        //Surveyor
        $surveyor = DB::table('v_users')
            ->where('role_name', '=', 'Staff Analis')
            ->orWhere('role_name', '=', 'Customer Service')
            ->select('nama_user', 'code_user')->get();

        return response()->json([$cek, $surveyor]);
    }

    public function update(Request $request)
    {

        $req = $request->validate([
            'kode_pengajuan' => 'required',
            'nama_nasabah' => 'required',
            'alamat' => 'required',
        ]);

        $field = [
            'surveyor_kode' => $request->kode_petugas,
            'tgl_survei' => $request->tgl_survei,
            'tgl_jadul_1' => $request->tgl_jadul_1,
            'tgl_jadul_2' => $request->tgl_jadul_2,
            'auth_user' => Auth::user()->code_user,
            'updated_at' => now(),
        ];

        $filteredArray = array_filter($field, function ($value) {
            return $value !== "-" && !is_null($value);
        });

        $cek_produk = DB::table('data_pengajuan')->where('kode_pengajuan', $request->kode_pengajuan)->first();
        if ($cek_produk->produk_kode == 'KTA') {
            $tracking = [
                'proses_survey' => now(),
            ];

            $pengajuan = [
                'tracking' => 'Proses Analisa',
                'updated_at' => now(),
            ];

            DB::transaction(function () use ($tracking, $pengajuan, $request, $filteredArray) {
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($pengajuan);
                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($tracking);
                Survei::where('pengajuan_kode', $request->kode_pengajuan)->update($filteredArray);
            });
            return redirect()->route('permohonan.analisa')->with('success', "Berhasil Menambahkan Data");
        }

        //Data Tracking
        $trc = DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->first();
        if (!is_null($trc)) {
            $tracking = [
                'proses_survey' => now(),
            ];

            DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($tracking);
        }

        $pengajuan = [
            'tracking' => 'Proses Survei',
            'updated_at' => now(),
        ];

        try {
            DB::transaction(function () use ($filteredArray, $request, $pengajuan) {
                $survei = Survei::where('pengajuan_kode', $request->kode_pengajuan)->get();
                Survei::where('id', $survei[0]->id)->update($filteredArray);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($pengajuan);
            });
            return redirect()->back()->with('success', "Penjadwalan telah dibuat");
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Penjadwalan gagal dibuat");
        }
    }
}
