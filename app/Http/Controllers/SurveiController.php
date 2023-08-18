<?php

namespace App\Http\Controllers;

use App\Models\CGC;
use App\Models\Kantor;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if ($survey->kasi_kode == 'DDN') {
            $survey->nama_kasi = 'Dede Doni';
        } elseif ($survey->kasi_kode == 'RND') {
            $survey->nama_kasi = 'Rian Destila';
        }

        //Data surveyor
        if ($survey->surveyor_kode == 'MHM') {
            $survey->nama_surveyor = 'Muhidin';
        } elseif ($survey->surveyor_kode == 'JAY') {
            $survey->nama_surveyor = 'Jaelani';
        }

        //Inisialisasi data        
        $survey->tabungan_cgc = $pengajuan->tabungan_cgc;                              
                                  
        
        $kantor = Kantor::all();
        return view('survei.edit', [
            'data' => $cek,
            'cgc' => $cgc,
            'survey' => $survey,
            'kantor' => $kantor,
        ]);
    }
    
    public function update(Request $request){                
        $cek = $request->validate([
            'kantor_kode' => 'required',
            'kasi_kode' => 'required',
            'surveyor_kode' => 'required',
        ]);

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
