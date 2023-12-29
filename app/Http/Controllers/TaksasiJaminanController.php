<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use App\Models\Agunan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class TaksasiJaminanController extends Controller
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

            $taksasi = Midle::taksasi_jaminan($enc);
            
            return view('analisa.taksasi-jaminan', [
                'data' => $cek[0],
                'taksasi' => $taksasi,
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        
        if (is_null($request->nilai_taksasi)) {
            return redirect()->back()->with('error', 'Data Taksasi harus diisi');
        }

        $data = [
            'nilai_taksasi' => (int)str_replace(["Rp", " ", "."], "", $request->nilai_taksasi),
        ];
        
        try {
            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Data Taksasi berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Taksasi gagal diubah');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
