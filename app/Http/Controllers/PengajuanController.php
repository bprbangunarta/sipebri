<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Agunan;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{

    public function index(Request $request)
    {
       $query = Pengajuan::join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_pengajuan.kode_pengajuan as kode','data_pengajuan.nasabah_kode as kd_nasabah', 'data_pengajuan.plafon as plafon', 'data_pengajuan.jangka_waktu as jk', 'data_nasabah.nama_nasabah as nama', 'data_nasabah.alamat_ktp as alamat')->get();

        return view('pengajuan.index',[
            'data' => $query,
        ]);
    }

    public function edit(Request $request)
    {
        $req = $request->query('nasabah');
        $nasabah = Nasabah::where('kode_nasabah', $req)->get();

        $pengajuan = Pengajuan::where('nasabah_kode', $nasabah[0]->kode_nasabah)->get();
        
        return view('pengajuan.edit',[
            'data' => $nasabah,
            'pengajuan' => $pengajuan,
        ]);
    }

    public function agunan(Request $request)
    {
        $req = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $req)->first();

        $pengajuan = Pengajuan::where('nasabah_kode', $cek->kode_nasabah)->get();
        $jaminan = DB::table('data_pengajuan')
                    ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                    ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                    ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                    ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.no_dokumen', 'data_jaminan.atas_nama', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                    ->where('data_pengajuan.kode_pengajuan', '=', $pengajuan[0]->kode_pengajuan)
                    ->get();

        //Data agunan
        $agunan = Agunan::all();
        
        //Data jenis dokumen
        $dok = DB::table('data_jenis_dokumen')->get();

        //Data dati
        $kab = DB::select('select distinct kode_dati, nama_dati from v_dati'); 
        
        return view('pengajuan.agunan', [
            'agunan' => $agunan,
            'dok' => $dok,
            'data' => $cek,
            'jaminan' => $jaminan,
            'pengajuan' => $pengajuan,
            'dati' => $kab,
        ]);
    }

    public function store(Request $request){
        
        $cek = $request->validate([
            'pengajuan_kode' => 'required',
            'jenis_agunan_kode' => 'required',
            'jenis_dokumen_kode' => 'required',
            'no_dokumen' => 'required',
            'atas_nama' => 'required',
            'masa_agunan' => 'required',
            'kode_dati' => 'required',
            'lokasi' => 'required',
            'catatan' => 'required',
        ]);

        $carbonDate = Carbon::createFromFormat('Y-m-d', $cek['masa_agunan']);
        $cek['masa_agunan'] = $carbonDate->format('Ymd');
      
        try {
            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }

    }
}
