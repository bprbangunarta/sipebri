<?php

namespace App\Http\Controllers;

use App\Models\Perdagangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PerdaganganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $name = 'AUPG';
            $length = 5;
            $kode = Perdagangan::kodeacak($name, $length);
            
            if ($kode !== null) {
                $data = $request->validate([
                    'nama_usaha' => 'required'
                ]);
                $data['kode_usaha'] = $kode;
                $data['pengajuan_kode'] = $enc;
                $data['input_user'] = Auth::user()->code_user;
                $data['nama_usaha'] = ucwords($data['nama_usaha']); //Kapital depannya saja

                try {
                    Perdagangan::create($data);
                    return redirect()->back()->with('success', 'Nama usaha berhasil ditambahkan');
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Nama usaha gagal ditambahkan');
                }
                
            }else{
                $kode = Perdagangan::kodeacak($name, $length);
            }


        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Perdagangan  $perdagangan
     * @return \Illuminate\Http\Response
     */
    public function show(Perdagangan $perdagangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Perdagangan  $perdagangan
     * @return \Illuminate\Http\Response
     */
    public function edit(Perdagangan $perdagangan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Perdagangan  $perdagangan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Perdagangan $perdagangan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Perdagangan  $perdagangan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Perdagangan $perdagangan)
    {
        //
    }

    public function detail_store(Request $request)
    {
        $request->validate([
            'nama_barang1' => 'required', 'hrg1' => 'required', 'jual1' => 'required', 'stock1' => 'required',
            'nama_barang2' => 'required', 'hrg2' => 'required', 'jual2' => 'required', 'stock2' => 'required',
            'nama_barang3' => 'required', 'hrg3' => 'required', 'jual3' => 'required', 'stock3' => 'required',
            'nama_barang4' => 'required', 'hrg4' => 'required', 'jual4' => 'required', 'stock4' => 'required',
            'nama_barang5' => 'required', 'hrg5' => 'required', 'jual5' => 'required', 'stock5' => 'required',
            'nama_barang6' => 'required', 'hrg6' => 'required', 'jual6' => 'required', 'stock6' => 'required',
            'nama_barang7' => 'required', 'hrg7' => 'required', 'jual7' => 'required', 'stock7' => 'required',
            'nama_barang8' => 'required', 'hrg8' => 'required', 'jual8' => 'required', 'stock8' => 'required',
            'nama_barang9' => 'required', 'hrg9' => 'required', 'jual9' => 'required', 'stock9' => 'required',
            'nama_barang10' => 'required', 'hrg10' => 'required', 'jual10' => 'required', 'stock10' => 'required',
        ]);

        try {
            $enc = Crypt::decrypt($request->query('usaha'));
            for ($i=1; $i <= 10; $i++) { 
                $data = [
                    'usaha_kode' => $enc,
                    'nama_barang' => ucwords($request->input('nama_barang'.$i)),
                    'stok' => $request->input('stock'.$i),
                    'harga_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg'.$i)),
                    'harga_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('jual'.$i)),
                    'laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba'.$i)),
                    'presentase_laba' => sprintf("%.2f", $request->input('persen'.$i), 2),
                ];
                DB::table('du_perdagangan')->insert($data);
            }
            
            $data2 = [
                'lokasi_usaha' => ucwords($request->input('lokasi_usaha')),
                'lama_usaha' => $request->input('lama_usaha'),
                'belanja_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('belanja_harian')),
                'total_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tbeli')),
                'total_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tjual')),
                'total_laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tlaba')),
                'total_stok' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tstock')),
                'total_pl' => sprintf("%.2f", $request->input('tpersen'), 2),
                'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan')),
                'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran')),
                'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('penambahan')),
                'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih')),
            ];
            

            $data3 = [
                'usaha_kode' => $enc,
                'transportasi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('transportasi')),
                'bongkar_muat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('bongkar_muat')),
                'pegawai' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pegawai')),
                'gatel' => (int)str_replace(["Rp.", " ", "."], "", $request->input('gatel')),
                'retribusi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('retribusi')),
                'sewa_tempat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('sewa_tempat')),
            ];
            
            DB::transaction(function() use ($enc, $data2, $data3){
                Perdagangan::where('kode_usaha', $enc)->update($data2);
                DB::table('bu_perdagangan')->insert($data3);
            });

            return redirect()->back()->with('success', 'Data barang berhasil ditambahkan');
            
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', 'Data barang gagal ditambahkan');
        }
    }

    public function detail_update(Request $request)
    {
        $request->validate([
            'nama_barang1' => 'required', 'hrg1' => 'required', 'jual1' => 'required', 'stock1' => 'required',
            'nama_barang2' => 'required', 'hrg2' => 'required', 'jual2' => 'required', 'stock2' => 'required',
            'nama_barang3' => 'required', 'hrg3' => 'required', 'jual3' => 'required', 'stock3' => 'required',
            'nama_barang4' => 'required', 'hrg4' => 'required', 'jual4' => 'required', 'stock4' => 'required',
            'nama_barang5' => 'required', 'hrg5' => 'required', 'jual5' => 'required', 'stock5' => 'required',
            'nama_barang6' => 'required', 'hrg6' => 'required', 'jual6' => 'required', 'stock6' => 'required',
            'nama_barang7' => 'required', 'hrg7' => 'required', 'jual7' => 'required', 'stock7' => 'required',
            'nama_barang8' => 'required', 'hrg8' => 'required', 'jual8' => 'required', 'stock8' => 'required',
            'nama_barang9' => 'required', 'hrg9' => 'required', 'jual9' => 'required', 'stock9' => 'required',
            'nama_barang10' => 'required', 'hrg10' => 'required', 'jual10' => 'required', 'stock10' => 'required',
        ]);
        
        try {
            $enc = Crypt::decrypt($request->query('usaha'));

            for ($i=1; $i <= 10; $i++) { 
                $data = [
                    'usaha_kode' => $enc,
                    'nama_barang' => ucwords($request->input('nama_barang'.$i)),
                    'stok' => $request->input('stock'.$i),
                    'harga_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg'.$i)),
                    'harga_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('jual'.$i)),
                    'laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba'.$i)),
                    'presentase_laba' => sprintf("%.2f", $request->input('persen'.$i), 2),
                ];
                DB::table('du_perdagangan')
                    ->where('usaha_kode', '=', $enc)
                    ->insert($data);
            }

            $data2 = [
                'total_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tbeli')),
                'total_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tjual')),
                'total_laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tlaba')),
                'total_stok' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tstock')),
                'total_pl' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tpersen')),
            ];
            
            Perdagangan::where('kode_usaha', $enc)->update($data2);
            dd($data2);

            return redirect()->back()->with('success', 'Data barang berhasil diupdate');
            
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', 'Data barang gagal diupdate');
        }
    }
}
