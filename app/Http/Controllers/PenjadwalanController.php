<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Kantor;
use App\Models\Survei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjadwalanController extends Controller
{
    public function index(){

        $cek = DB::table('data_pengajuan')
                    ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                    ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                    ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
                    ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
                    ->where('data_pengajuan.status', '=', 'Sudah Otorisasi')
                    ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2','users.name');
        
        $datas = $cek->paginate(10);
        
        return view('analisa.penjadwalan', [
            'data' => $datas,
        ]);

    }

    public function edit($id){
        $cek = DB::table('data_pengajuan')
                    ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                    ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                    ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
                    ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
                    ->where('data_pengajuan.kode_pengajuan', '=', $id)
                    ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2','users.name')->get();
        
        //Surveyor
        $surveyor = DB::table('v_users')
                    ->where('role_name', '=', 'Staff Analis')
                    ->select('nama_user', 'code_user')->get();
                 
        return response()->json([$cek, $surveyor]);
    }

    public function update(Request $request){
        
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
        ];

        
        try {
            $survei = Survei::where('pengajuan_kode', $request->kode_pengajuan)->get();
            Survei::where('id', $survei[0]->id)->update($field);
            return redirect()->back()->with('success', "Penjadwalan telah dibuat");
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', "Penjadwalan gagal dibuat");
        }

    }
}
