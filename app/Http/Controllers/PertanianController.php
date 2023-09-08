<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Pertanian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class PertanianController extends Controller
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
            $au = Pertanian::au_pertanian($enc); 
            
            foreach($au as $item){
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }
            
            return view('analisa.usaha.pertanian', [
                'data' => $cek[0],
                'pertanian' => $au
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
            $name = 'AUP';
            $length = 5;
            $kode = Pertanian::kodeacak($name, $length);
            
            if ($kode !== null) {
                $data = $request->validate([
                    'nama_usaha' => 'required'
                ]);
                $data['kode_usaha'] = $kode;
                $data['pengajuan_kode'] = $enc;
                $data['input_user'] = Auth::user()->code_user;
                $data['nama_usaha'] = ucwords($data['nama_usaha']); //Kapital depannya saja

                try {
                    Pertanian::create($data);
                    return redirect()->back()->with('success', 'Nama usaha berhasil ditambahkan');
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Nama usaha gagal ditambahkan');
                }
                
            }else{
                $kode = Pertanian::kodeacak($name, $length);
            }


        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pertanian  $pertanian
     * @return \Illuminate\Http\Response
     */
    public function show(Pertanian $pertanian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pertanian  $pertanian
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $ush = Crypt::decrypt($request->query('usaha'));
            $cek = Midle::analisa_usaha($enc);
            
            //Data pertanian
            $pertanian = Midle::pertanian_detail($ush);
            $du = DB::table('du_pertanian')->where('usaha_kode', $ush)->get();
            if (count($du) == 0) {
                dd($du);
            }
            $pertanian[0]->total_luas = $pertanian[0]->luas_sendiri + $pertanian[0]->luas_sewa + $pertanian[0]->luas_gadai;

            foreach($pertanian as $item){
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }
            // dd($pertanian);
            return view('analisa.usaha.pertanian-detail-edit', [
                'data' => $cek[0],
                'pertanian' => $pertanian[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pertanian  $pertanian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('usaha'));

            DB::transaction(function () use ($enc, $request) {
                $cek = Pertanian::where('kode_usaha', $enc)->get();
                $data = [
                    'jenis_usaha' => ucwords($request->jenis_usaha),
                    'jumlah_musim' => $request->jumlah_musim,
                    'lokasi_usaha' => '',
                    'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->pendapatan),
                    'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->pengeluaran),
                    'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->penambahan),
                    'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_bersih),
                    'input_user' => Auth::user()->code_user,
                ];
                Pertanian::where('id', $cek[0]->id)->update($data);

                $data2 = [
                    'usaha_kode' => $enc,
                    'pengolahan_tanah' => (int)str_replace(["Rp.", " ", "."], "", $request->pengolahan_tanah),
                    'bibit' => (int)str_replace(["Rp.", " ", "."], "", $request->bibit),
                    'pupuk' => (int)str_replace(["Rp.", " ", "."], "", $request->pupuk),
                    'pestisida' => (int)str_replace(["Rp.", " ", "."], "", $request->pestisida),
                    'pengairan' => (int)str_replace(["Rp.", " ", "."], "", $request->pengairan),
                    'tenaga_kerja' => (int)str_replace(["Rp.", " ", "."], "", $request->tenaga_kerja),
                    'panen' => (int)str_replace(["Rp.", " ", "."], "", $request->panen),
                    'pajak' => (int)str_replace(["Rp.", " ", "."], "", $request->pajak),
                    'iuran_desa' => (int)str_replace(["Rp.", " ", "."], "", $request->iuran_desa),
                    'amortisasi' => (int)str_replace(["Rp.", " ", "."], "", $request->amortisasi),
                    'pinjaman_bank' => (int)str_replace(["Rp.", " ", "."], "", $request->pinjaman_bank),
                ];
                DB::table('bu_pertanian')->insert($data2);

                $data3 = [
                    'usaha_kode' => $enc,
                    'jenis_tanaman' => ucwords($request->jenis_tanaman),
                    'luas_sendiri' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sendiri),
                    'luas_sewa' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sewa),
                    'luas_gadai' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_gadai),
                    'hasil_panen' => (int)str_replace(["Rp.", " ", "."], "", $request->hasil_panen),
                    'harga' => (int)str_replace(["Rp.", " ", "."], "", $request->harga),
                ];
                DB::table('du_pertanian')->insert($data3);
            
            });
           return redirect()->back()->with('success', 'Data usaha berhasil ditambahkan');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data usaha gagal ditambahkan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pertanian  $pertanian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pertanian $pertanian)
    {
        //
    }
}
