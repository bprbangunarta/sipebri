<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Data;
use App\Models\Agunan;
use App\Models\Resort;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use App\Models\Produk;
use App\Models\Survei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

use function Pest\Laravel\delete;

class PengajuanController extends Controller
{

    public function index(Request $request)
    {
        $name = request('name');
        $usr = Auth::user()->code_user;
        $query = DB::table('data_pengajuan')
                    ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                    ->select('data_pengajuan.kode_pengajuan as kode', 'data_pengajuan.nasabah_kode as kd_nasabah', 'data_pengajuan.plafon as plafon', 'data_pengajuan.jangka_waktu as jk', 'data_nasabah.nama_nasabah as nama', 'data_nasabah.alamat_ktp as alamat', 'data_nasabah.status',  'data_nasabah.is_entry as entry')
                    ->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->where('data_pengajuan.input_user', '=', $usr)
                    ->get();
        
        $auth = Auth::user()->code_user;
        foreach ($query as $item) {
            $item->kd_nasabah = Crypt::encrypt($item->kd_nasabah);
            $item->kd = Crypt::encrypt($item->kode);
        }
        
        return view('pengajuan.index', [
            'data' => $query,
            'auth' => $auth,
        ]);
    }

    public function storepengajuan(Request $request)
    {
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
            'input_user' => 'required',
        ]);
        $cek['is_entry'] = 1;

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
        $enc = Crypt::decrypt($req);
        
        $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
        
        $nasabah = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->get();

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
        } else {
            $peng->nama_resort = $query->ket;
        }

        //Data auth
        $us = Auth::user()->id;
        $user = DB::table('users')
                    ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.code_user')
                    ->where('users.id', '=', $us)->get();
        $peng->auth = $user[0]->code_user;

        //Data resort
        $resort = DB::connection('sqlsrv')
            ->table('resort')
            ->select('kode', 'ket')
            ->get();

        $nasabah[0]->kd_nasabah = Crypt::encrypt($nasabah[0]->kode_nasabah);
        $peng->kode_pengajuan = Crypt::encrypt($peng->kode_pengajuan);
        // dd($peng);
        //Produk All
        $pro = Produk::all();
        
        return view('pengajuan.edit', [
            'data' => $nasabah,
            'pengajuan' => $peng,
            'resort' => $resort,
            'produk' => $pro,
        ]);
    }

    public function agunan(Request $request)
    {
        $req = $request->query('nasabah');
        $enc = Crypt::decrypt($req);
        
        $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
        
        $cek = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->first();
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

        $us = Auth::user()->id;
        $user = DB::table('users')
                    ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.code_user')
                    ->where('users.id', '=', $us)->get();
        $cek->auth = $user[0]->code_user;
        $cek->kd_pengajuan = $req;
        
        return view('pengajuan.agunan', [
            'agunan' => $agunan,
            'dok' => $dok,
            'data' => $cek,
            'jaminan' => $jaminan,
            'pengajuan' => $pengajuan,
            'dati' => $kab,
        ]);
    }

    public function store(Request $request)
    {

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
            'input_user' => 'required',
        ]);
        $cek['is_entry'] = 1;

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

    public function editagunan($id)
    {
        $data = DB::table('data_jaminan')
            ->where('id', $id)
            ->get();

        //Ambil data agunan
        $agunan = Agunan::where('kode', $data[0]->jenis_agunan_kode)->get();

        //Ambil data dokumen
        $dok = DB::table('data_jenis_dokumen')
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
        $data[0]->masa_agunan = $carbonDate->format('Y-m-d');

        /* Menambahkan field baru ke variable data dari data agunan dan data dokumen */
        $data[0]->jenis_agunan = $agunan[0]->jenis_agunan;
        $data[0]->jenis_dokumen = $dok[0]->jenis_dokumen;
        $data[0]->nama_dati = $dati[0]->nama_dati;

        $data[0]->auth = Auth::user()->code_user;

        return response()->json([$data, $kabupaten, $agn, $dokumen]);
    }

    public function updateagunan(Request $request)
    {
        $cek = $request->validate([
            'jenis_agunan_kode' => 'required',
            'jenis_dokumen_kode' => 'required',
            'no_dokumen' => 'required',
            'atas_nama' => 'required',
            'masa_agunan' => 'required',
            'kode_dati' => 'required',
            'lokasi' => 'required',
            'catatan' => 'required',
            'input_user' => 'required',
        ]);
        $cek['is_entry'] = 1;

        // Merubah tanggal 
        $carbonDate = Carbon::createFromFormat('Y-m-d', $cek['masa_agunan']);
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

    public function destroy($pengajuan)
    {   
        $enc = Crypt::decrypt($pengajuan);
        
        $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
        $pendamping = Pendamping::where('pengajuan_kode', $enc)->get();
        $survei = Survei::where('pengajuan_kode', $enc)->get();
        $agunan = DB::table('data_jaminan')
                    ->where('pengajuan_kode', $enc)->first();
        
        if(!is_null($agunan)){
            DB::table('data_jaminan')
                    ->where('id', $agunan->id)
                    ->delete();
        }
        
        try {
            
            DB::transaction(function() use ($pengajuan, $pendamping, $survei){
                Pengajuan::where('id', $pengajuan[0]->id)->delete();
                Pendamping::where('id', $pendamping[0]->id)->delete();
                Survei::where('id', $survei[0]->id)->delete();
            });
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal dihapus');
        }
    }
}
