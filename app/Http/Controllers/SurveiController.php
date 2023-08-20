<?php

namespace App\Http\Controllers;

use App\Models\CGC;
use App\Models\Kantor;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SurveiController extends Controller
{
    public function edit(Request $request)
    {
        $req = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $req)->first();     
        
        //Data pengajuan
        $pengajuan = Pengajuan::where('nasabah_kode', $cek->kode_nasabah)->first();                

        //Ambil data CGC        
        $cgc = CGC::select('*')->get();  
        $cek->kode_pengajuan = $pengajuan->kode_pengajuan;

        //Data survey
        $survey = Survei::where('pengajuan_kode', $cek->kode_pengajuan)->first();        
        
        //Data kantor
        $ktr = Kantor::where('kode_kantor', $survey->kantor_kode)->first();
        
        //inisialisasi variable ketika data null
        if (is_null($ktr)) {
            $survey->nama_kantor = "";
        } else {
            $survey->nama_kantor = $ktr->nama_kantor;
        }
                
        //Data kasi
        $ks = DB::table('users')
                ->select('name')
                ->where('code_user', $survey->kasi_kode)->first();
        $survey->nama_kasi = $ks->name;

        //Data surveyor
        $st = DB::table('users')
                ->select('name')
                ->where('code_user', $survey->kasi_kode)->first();
        $survey->nama_surveyor = $st->name;
        
        //Inisialisasi data        
        $survey->tabungan_cgc = $pengajuan->tabungan_cgc;   
        
        //Data kasi
        $kasi = DB::table('users')
                    ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.code_user', 'users.name as nama')
                    ->where('roles.name', '=', 'Kasi Analis')->get();  
        
        //Data Staff Analis
        $staff = DB::table('users')
                    ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.code_user', 'users.name as nama')
                    ->where('roles.name', '=', 'Staff Analis')->get();
        
        //Data Kantor
        $kantor = Kantor::all();

        //Data Auth
        $us = Auth::user()->id;
        $user = DB::table('users')
                    ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.code_user')
                    ->where('users.id', '=', $us)->get();
        $cek->auth = $user[0]->code_user;
                    
        return view('survei.edit', [
            'data' => $cek,
            'cgc' => $cgc,
            'kasi' => $kasi,
            'staff' => $staff,
            'survey' => $survey,
            'kantor' => $kantor,
        ]);
    }
    
    public function update(Request $request){                
        $cek = $request->validate([
            'kantor_kode' => 'required',
            'kasi_kode' => 'required',
            'surveyor_kode' => 'required',
            'input_user' => 'required',
        ]);

        $cek['is_entry'] = 1;
        $cgc = $request->validate(['tabungan_cgc' => '']);
    
        $kode_pengajuan = $request->pengajuan_kode;    
        
        if ($cek) {
            DB::transaction(function () use ($cek, $kode_pengajuan, $cgc) {                
                Survei::where('pengajuan_kode', $kode_pengajuan)->update($cek);                        
                Pengajuan::where('kode_pengajuan', $kode_pengajuan)->update($cgc);                        
            });            
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        }else{
            return redirect()->back()->with('error', 'Data gagal diupdate');
        }

    }
}
