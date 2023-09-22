<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Kepemilikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class KepemilikanController extends Controller
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

            $has = Kepemilikan::where('pengajuan_kode', $enc)->first();
            
            if (is_null($has)) {
                return view('analisa.harta-kepemilikan', [
                'data' => $cek[0],
                'milik' => $has,
            ]);
            }
           
            return view('analisa.harta-kepemilikan-edit', [
                'data' => $cek[0],
                'milik' => $has,
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
        
        try {
            $enc = Crypt::decrypt($request->kode_pengajuan);
            $name = 'HRK';
            $length = 5;
            $kode = Kepemilikan::kodeacak($name, $length);

            $data = [
                'kode_kepemilikan' => $kode,
                'pengajuan_kode' => $enc,
                'rumah' => ucwords($request->rumah),
                'mobil' => ucwords($request->mobil),
                'motor' => ucwords($request->motor),
                'televisi' => ucwords($request->tv),
                'komputer' => ucwords($request->komputer),
                'mesin_cuci' => ucwords($request->mesin_cuci),
                'kursi_tamu' => ucwords($request->kursi),
                'lemari_panjang' => ucwords($request->lemari),
                'nama_lainnya1' => ucwords($request->nama_lain1),
                'isi_lainnya1' => ucwords($request->lainnya1),
                'nama_lainnya2' => ucwords($request->nama_lain2),
                'isi_lainnya2' => ucwords($request->lainnya2),
            ];

            if ($data) {
                DB::table('data_kepemilikan')->insert($data);
                return redirect()->back()->with('success', 'Harta Kepemilikan berhasil ditambahkan');
            }

        } catch (DecryptException $th) {
             return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kepemilikan  $kepemilikan
     * @return \Illuminate\Http\Response
     */
    public function show(Kepemilikan $kepemilikan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kepemilikan  $kepemilikan
     * @return \Illuminate\Http\Response
     */
    public function edit(Kepemilikan $kepemilikan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kepemilikan  $kepemilikan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $kepemilikan)
    {
        
        try {
            $enc = Crypt::decrypt($request->kode_pengajuan);
            $data = [
                'rumah' => ucwords($request->rumah),
                'mobil' => ucwords($request->mobil),
                'motor' => ucwords($request->motor),
                'televisi' => ucwords($request->tv),
                'komputer' => ucwords($request->komputer),
                'mesin_cuci' => ucwords($request->mesin_cuci),
                'kursi_tamu' => ucwords($request->kursi),
                'lemari_panjang' => ucwords($request->lemari),
                'nama_lainnya1' => ucwords($request->nama_lain1),
                'isi_lainnya1' => ucwords($request->lainnya1),
                'nama_lainnya2' => ucwords($request->nama_lain2),
                'isi_lainnya2' => ucwords($request->lainnya2),
            ];

            if ($data) {
                $pemilik = Kepemilikan::where('pengajuan_kode', $enc)->get();
                DB::table('data_kepemilikan')->where('id', $pemilik[0]->id)->update($data);
                return redirect()->back()->with('success', 'Harta Kepemilikan berhasil ditambahkan');
            }

        } catch (DecryptException $th) {
             return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kepemilikan  $kepemilikan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kepemilikan $kepemilikan)
    {
        //
    }
}
