<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class JasaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Jasa::au_jasa($enc); 
            
            foreach($au as $item){
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }
            
            return view('analisa.usaha.jasa', [
                'data' => $cek[0],
                'jasa' => $au
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $req = $request->query('pengajuan');
        try {
            $enc = Crypt::decrypt($req);
            $name = 'AUJ';
            $length = 5;
            $kode = Jasa::kodeacak($name, $length);
            
            if ($kode !== null) {
                $data = $request->validate([
                    'nama_usaha' => 'required'
                ]);
                $data['kode_usaha'] = $kode;
                $data['pengajuan_kode'] = $enc;
                $data['input_user'] = Auth::user()->code_user;
                $data['nama_usaha'] = ucwords($data['nama_usaha']); //Kapital depannya saja
                
                try {
                    Jasa::create($data);
                    return redirect()->back()->with('success', 'Nama usaha berhasil ditambahkan');
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Nama usaha gagal ditambahkan');
                }
                
            }else{
                $kode = Jasa::kodeacak($name, $length);
            }


        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Jasa  $jasa
     * @return \Illuminate\Http\Response
     */
    public function show(Jasa $jasa)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Jasa  $jasa
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $usaha = Crypt::decrypt($request->query('usaha'));
            $cek = Midle::analisa_usaha($enc);

            $jasa = Midle::jasa_detail($usaha);
            $jasa[0]->kd_usaha = Crypt::encrypt($jasa[0]->kode_usaha);
            
            return view('analisa.usaha.jasa-detail', [
                'data' => $cek[0],
                'jasa' => $jasa[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jasa  $jasa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $cek = $request->validate([
                'kode_usaha' => 'required',
                'nama_usaha' => 'required',
                'pendapatan' => '',
                'b_pajak' => '',
                'b_lainnya' => '',
                'pengeluaran' => '',
                'laba_bersih' => '',
            ]);

            $cek['kode_usaha'] = Crypt::decrypt($request->kode_usaha);
            $cek['nama_usaha'] = ucwords($request->nama_usaha);
            $cek['pendapatan'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan'));
            $cek['b_pajak'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('b_pajak'));
            $cek['b_lainnya'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('b_lainnya'));
            $cek['pengeluaran'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran'));
            $cek['laba_bersih'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih'));
            $cek['input_user'] = Auth::user()->code_user;
            
                $enc = Crypt::decrypt($request->kode_usaha);
                Jasa::where('kode_usaha', $enc)->update($cek);
                return redirect()->back()->with('success', 'Data usaha jasa berhasil ditambahkan');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data usaha jasa gagal ditambahkan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Jasa  $jasa
     * @return \Illuminate\Http\Response
     */
    public function destroy($jasa)
    {
        try {
            $enc = Crypt::decrypt($jasa);
            $au = Jasa::where('kode_usaha', $enc)->get();

            Jasa::where('id', $au[0]->id)->delete();
            return redirect()->back()->with('success', 'Usaha jasa berhasil dihapus');

        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Usaha jasa gagal dihapus');
    }
}
