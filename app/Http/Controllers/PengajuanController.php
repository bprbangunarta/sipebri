<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Data;
use App\Models\Agunan;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use App\Models\Resort;
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

    public function storepengajuan(Request $request){
        // dd($request);
        $cek = $request->validate([
            'kode_pengajuan' => 'required',
            'plafon' => 'required',
            'produk_kode' => 'required',
            'suku_bunga' => 'required',
            'jangka_waktu' => 'required',
            'metode_rps' => 'required',
            'jangka_pokok' => 'required',
            'jangka_bunga' => 'required',
            'resort_kode' => '',
            'penggunaan' => 'required',
            'keterangan' => 'required',
        ]);
        
        //Hapus format rupiah
        $remove = array("Rp", ".", " ");
        $cek['plafon'] = str_replace($remove, "", $cek['plafon']);

        try {
            Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function edit(Request $request)
    {
        $req = $request->query('nasabah');
        $nasabah = Nasabah::where('kode_nasabah', $req)->get();

        $pengajuan = Pengajuan::where('nasabah_kode', $nasabah[0]->kode_nasabah)->get();
        
        //Data produk
        $produk = Data::produk($pengajuan[0]->produk_kode);
        $pengajuan[0]->produk_nama = $produk;
        $peng = $pengajuan[0];
        
        // mencari nama
        $query = DB::connection('sqlsrv')
            ->table('resort')
            ->select('kode', 'ket')
            ->where('kode', $peng->resort_kode)
            ->first();

        if (is_null($query)) {
           $peng->nama_resort = null;
        }else{
            $peng->nama_resort = $query->ket;
        }

        //Data resort
        $resort = DB::connection('sqlsrv')
                ->table('resort')
                ->select('kode', 'ket')
                ->get();

     
        return view('pengajuan.edit',[
            'data' => $nasabah,
            // 'resort' => $resort,
            'pengajuan' => $peng,
            'resort' => $resort,
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
                    ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.id', 'data_jaminan.no_dokumen', 'data_jaminan.atas_nama', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                    ->where('data_pengajuan.kode_pengajuan', '=', $pengajuan[0]->kode_pengajuan)
                    ->get();

        //Data agunan
        $agunan = Agunan::all();
        
        //Data jenis dokumen
        $dok = DB::table('data_jenis_dokumen')->get();

        //Data dati
        $kab = DB::select('select distinct kode_dati, nama_dati from v_dati'); 
        // dd($jaminan);
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

        // Merubah tanggal 
        $carbonDate = Carbon::createFromFormat('Y-m-d', $cek['masa_agunan']);
        $cek['masa_agunan'] = $carbonDate->format('Ymd');
      
        try {
            DB::table('data_jaminan')->insert($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function editagunan($id){
        $data = DB::table('data_jaminan')
                ->where('id', $id)
                ->get();
        
        //Ambil data agunan
        $agunan = Agunan::where('kode', $data[0]->jenis_agunan_kode)->get();

        //Ambil data dokumen
        $dok= DB::table('data_jenis_dokumen')
                ->select('jenis_dokumen')
                ->where('kode', $data[0]->jenis_dokumen_kode)->get();

        //Data dati
        $dati = DB::table('v_dati')
                ->select('nama_dati')
                ->distinct()
                ->where('kode_dati', $data[0]->kode_dati)->get();

        //Dati all data
        $kabupaten = DB::table('v_dati')
                    ->select('kode_dati', 'nama_dati')
                    ->distinct()->get(); 

        //Agunan
        $agn = Agunan::select('kode', 'jenis_agunan')->get();

        //Dokumen
        $dokumen = DB::table('data_jenis_dokumen')
                    ->select('kode', 'jenis_dokumen')->get();
        
        //Format tanggal lahir
        $carbonDate = Carbon::createFromFormat('Ymd', $data[0]->masa_agunan);
        $data[0]->masa_agunan = $carbonDate->format('m-d-Y');

        /* Menambahkan field baru ke variable data dari data agunan dan data dokumen */
        $data[0]->jenis_agunan = $agunan[0]->jenis_agunan;
        $data[0]->jenis_dokumen = $dok[0]->jenis_dokumen;
        $data[0]->nama_dati = $dati[0]->nama_dati;

       
        
        return response()->json([$data, $kabupaten, $agn, $dokumen]);
    }

    public function updateagunan(Request $request){
        $cek = $request->validate([
            'jenis_agunan_kode' => 'required',
            'jenis_dokumen_kode' => 'required',
            'no_dokumen' => 'required',
            'atas_nama' => 'required',
            'masa_agunan' => 'required',
            'kode_dati' => 'required',
            'lokasi' => 'required',
            'catatan' => 'required',
        ]);

        // Merubah tanggal 
        $carbonDate = Carbon::createFromFormat('m-d-Y', $cek['masa_agunan']);
        $cek['masa_agunan'] = $carbonDate->format('Ymd');

        try {
            DB::table('data_jaminan')
                ->where('id', $request->data)
                ->update($cek);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function destroy($pengajuan){
        try {
            DB::table('data_jaminan')->where('id', $pengajuan)->delete();   
            return redirect()->back()->with('success', 'Data berhasil dihapus');           
        } catch (\Throwable $th) {
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        }
    }
}
