<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Tambahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaTambahanController extends Controller
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
            $data = Tambahan::where('pengajuan_kode', $enc)->first();

            if (is_null($data)) {
                return view('analisa.analisa-tambahan', [
                    'data' => $cek[0],
                ]);
            }

            return view('analisa.analisa-tambahan-edit', [
                'data' => $cek[0],
                'tambahan' => $data,
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
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $name = 'AUT';
            $length = 5;
            $kode = Tambahan::kodeacak($name, $length);

            $data = [
                'kode_analisa' => $kode,
                'pengajuan_kode' => $enc,
                'modal_kerja' => (int)str_replace(["Rp", " ", "."], "", $request->modal_kerja),
                'investasi' => (int)str_replace(["Rp", " ", "."], "", $request->investasi),
                'konsumtif' => (int)str_replace(["Rp", " ", "."], "", $request->konsumtif),
                'pelunasan_kredit' => (int)str_replace(["Rp", " ", "."], "", $request->pelunasan_kredit),
                'take_over' => (int)str_replace(["Rp", " ", "."], "", $request->take_over),
                'administrasi' => (int)str_replace(["Rp", " ", "."], "", $request->administrasi),
                'asuransi' => (int)str_replace(["Rp", " ", "."], "", $request->asuransi),
                'kebutuhan_dana' => (int)str_replace(["Rp", " ", "."], "", $request->kebutuhan_dana),
                'nama_lain' => ucwords($request->nama_lain),
                'nilai_lain' => (int)str_replace(["Rp", " ", "."], "", $request->nilai_lain),
                'catatan' => $request->catatan,
            ];

            // dd($data);
            Tambahan::create($data);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal ditambahkan');
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $data = [
                'modal_kerja' => (int)str_replace(["Rp", " ", "."], "", $request->modal_kerja),
                'investasi' => (int)str_replace(["Rp", " ", "."], "", $request->investasi),
                'konsumtif' => (int)str_replace(["Rp", " ", "."], "", $request->konsumtif),
                'pelunasan_kredit' => (int)str_replace(["Rp", " ", "."], "", $request->pelunasan_kredit),
                'take_over' => (int)str_replace(["Rp", " ", "."], "", $request->take_over),
                'administrasi' => (int)str_replace(["Rp", " ", "."], "", $request->administrasi),
                'asuransi' => (int)str_replace(["Rp", " ", "."], "", $request->asuransi),
                'kebutuhan_dana' => (int)str_replace(["Rp", " ", "."], "", $request->kebutuhan_dana),
                'nama_lain' => ucwords($request->nama_lain),
                'nilai_lain' => (int)str_replace(["Rp", " ", "."], "", $request->nilai_lain),
            ];

            $tambah = Tambahan::where('pengajuan_kode', $enc)->first();
            Tambahan::where('id', $tambah->id)->update($data);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal ditambahkan');
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
