<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class KeuanganController extends Controller
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
            
            $kemampuan = Midle::kemampuan_keuangan($enc);
            $data = Keuangan::data_keuangan($enc);
            
            $filter = array_filter($kemampuan, function ($value) {
                return $value !== null ? $value : 0;
            });
            
            //Hasil penjumlahan analisa usaha
            $total = array_sum($filter);
            $kemampuan['total'] = $total;
            
            if ($data === 0) {
                return view('analisa.keuangan', [
                'data' => $cek[0],
                'kemampuan' => $kemampuan
            ]);
            }
            
            return view('analisa.keuangan-edit', [
                'data' => $cek[0],
                'kemampuan' => $kemampuan,
                'biaya' => $data,
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
            DB::transaction(function () use ($request) {
                $enc = Crypt::decrypt($request->query('pengajuan'));
                $name = 'AUK';
                $length = 5;
                $kode = Keuangan::kodeacak($name, $length);
                //au_keuangan
                $au = [
                    'kode_keuangan' => $kode,
                    'pengajuan_kode' => $enc,
                    'p_usaha' => (int)str_replace(["Rp.", " ", "."], "", $request->p_usaha),
                    'b_rumah_tangga' => (int)str_replace(["Rp.", " ", "."], "", $request->b_rumah_tangga),
                    'b_kewajiban_lainya' => (int)str_replace(["Rp.", " ", "."], "", $request->b_kewajiban_lainya),
                    'keuangan_perbulan' => (int)str_replace(["Rp.", " ", "."], "", $request->keuangan_perbulan),
                    'input_user' => Auth::user()->code_user,
                ];
                Keuangan::create($au);

                //Biaya Rumah Tangga
                for ($i = 1; $i <= 7; $i++) {
                    $length = 10;
                    $kod = Keuangan::du_kodeacak($length);
                        $data = [
                            'keuangan_kode' => $kode,
                            'kode_biaya' => $kod,
                            'pengeluaran' => ucwords($request->input('nama'.$i)),
                            'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('biaya'.$i)),
                        ];
                         DB::table('bu_keuangan')->insert($data);
                }
               
                //Kewajiban lain
                for ($j=1; $j <= 3; $j++) { 
                    $length = 10;
                    $k = Keuangan::du_kodeacak($length);
                        $data1 = [
                            'keuangan_kode' => $kode,
                            'kode_biaya' => $k,
                            'pengeluaran' => ucwords($request->input('data'.$j)),
                            'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('kewajiban'.$j)),
                        ];
                    DB::table('bu_keuangan')->insert($data1);
                    
                }
            });
            return redirect()->back()->with('success', 'Kemampuan Keuangan berhasil ditambahkan');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        } 
        return redirect()->back()->with('error', 'Kemampuan Keuangan gagal ditambahkan');       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Keuangan  $keuangan
     * @return \Illuminate\Http\Response
     */
    public function show(Keuangan $keuangan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Keuangan  $keuangan
     * @return \Illuminate\Http\Response
     */
    public function edit(Keuangan $keuangan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Keuangan  $keuangan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Keuangan $keuangan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Keuangan  $keuangan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Keuangan $keuangan)
    {
        //
    }

    public function update_detail(Request $request)
    {
        
        try {
            DB::transaction(function () use ($request) {
                $enc = Crypt::decrypt($request->query('keuangan'));
                //Update Biaya Rumah Tangga
                for ($i = 1; $i <= 7; $i++) {
                    $data = [
                        'keuangan_kode' => $enc,
                        'kode_biaya' => $request->input('kode_biaya'.$i),
                        'pengeluaran' => ucwords($request->input('nama'.$i)),
                        'nominal' => (int)str_replace(["Rp", " ", "."], "", $request->input('biaya'.$i)),
                    ];
                    $cek = DB::table('bu_keuangan')->where('kode_biaya', $request->input('kode_biaya'.$i))->get();
                    DB::table('bu_keuangan')->where('id', $cek[0]->id)->update($data);
                }
                

                //Update Biaya Rumah Tangga
                for ($j = 1; $j <= 3; $j++) {
                    $data1 = [
                        'keuangan_kode' => $enc,
                        'kode_biaya' => $request->input('kd_biaya'.$j),
                        'pengeluaran' => ucwords($request->input('data'.$j)),
                        'nominal' => (int)str_replace(["Rp", " ", "."], "", $request->input('kewajiban'.$j)),
                    ];
                    $cek1 = DB::table('bu_keuangan')->where('kode_biaya', $request->input('kd_biaya'.$j))->get();
                    DB::table('bu_keuangan')->where('id', $cek1[0]->id)->update($data1);
                }


                $au = [
                    'p_usaha' => (int)str_replace(["Rp", " ", "."], "", $request->p_usaha),
                    'b_rumah_tangga' => (int)str_replace(["Rp", " ", "."], "", $request->b_rumah_tangga),
                    'b_kewajiban_lainya' => (int)str_replace(["Rp.", " ", "."], "", $request->b_kewajiban_lainya),
                    'keuangan_perbulan' => (int)str_replace(["Rp.", " ", "."], "", $request->keuangan_perbulan),
                    'input_user' => Auth::user()->code_user,
                ];
                $au1 = Keuangan::where('kode_keuangan', $enc)->get();
                Keuangan::where('id', $au1[0]->id)->update($au);

            });
            return redirect()->back()->with('success', 'Kemampuan Keuangan berhasil diubah');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Kemampuan Keuangan gagal diubah');
    }
}
