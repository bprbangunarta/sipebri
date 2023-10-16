<?php

namespace App\Http\Controllers;

use App\Models\Jasa;
use App\Models\Lain;
use App\Models\Midle;
use Illuminate\Http\Request;
use function Pest\Laravel\delete;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class LainController extends Controller
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
            $au = Lain::index_lain($enc);

            foreach ($au as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
            }

            return view('analisa.usaha.lainnya', [
                'data' => $cek[0],
                'lain' => $au,
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
            $name = 'AUL';
            $length = 5;
            $kode = Lain::kodeacak($name, $length);

            if ($kode !== null) {
                $data = $request->validate([
                    'nama_usaha' => 'required',
                    'jenis_usaha' => 'required',
                ]);
                $data['kode_usaha'] = $kode;
                $data['pengajuan_kode'] = $enc;
                $data['input_user'] = Auth::user()->code_user;
                $data['nama_usaha'] = ucwords($data['nama_usaha']); //Kapital depannya saja
                $data['jenis_usaha'] = ucwords($data['jenis_usaha']); //Kapital depannya saja

                try {
                    Lain::create($data);
                    return redirect()->back()->with('success', 'Nama usaha berhasil ditambahkan');
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Nama usaha gagal ditambahkan');
                }
            } else {
                $kode = Lain::kodeacak($name, $length);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
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
    public function edit(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $usaha = Crypt::decrypt($request->query('usaha'));
            $cek = Midle::analisa_usaha($enc);
            $au = Lain::au_lain($usaha);
            $data = Lain::editlainnya($au[0]->kode_usaha);

            $au[0]->kd_usaha = Crypt::encrypt($au[0]->kode_usaha);

            if (count($data[0]) == 0) {
                return view('analisa.usaha.lainnya-detail', [
                    'data' => $cek[0],
                    'lain' => $au[0],
                ]);
            }

            return view('analisa.usaha.lainnya-detail-edit', [
                'data' => $cek[0],
                'lain' => $au[0],
                'biaya' => $data[0],
                'omset' => $data[1],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $lain)
    {
        try {
            $enc = Crypt::decrypt($lain);
            //Masuk ke tabel bu_lainnya
            DB::transaction(function () use ($enc, $request) {
                for ($i = 1; $i <= 4; $i++) {
                    $length = 10;
                    $kode = Lain::bu_kodeacak($length);

                    $data = [
                        'usaha_kode' => $enc,
                        'kode_lain' => $kode,
                        'pengeluaran' => ucwords($request->input('nampe' . $i)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran' . $i)),
                    ];
                    DB::table('bu_lainnya')->insert($data);
                }
                //Masuk ke tabel du_lainnya
                for ($j = 1; $j <= 4; $j++) {
                    $leng = 10;
                    $kode2 = Lain::du_kodeacak($leng);

                    $data2 = [
                        'usaha_kode' => $enc,
                        'kode_lain' => $kode2,
                        'penjualan' => ucwords($request->input('nama' . $j)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nominal' . $j)),
                    ];
                    DB::table('du_lainnya')->insert($data2);
                }

                //Masuk ke tabel au_lainnya
                $data3 = [
                    'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->pendapatan),
                    'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->pengeluaran),
                    'proyeksi' => (int)str_replace(["Rp.", " ", "."], "", $request->proyeksi),
                    'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_bersih),
                    'input_user' => Auth::user()->code_user,
                ];

                $cek = Lain::where('kode_usaha', $enc)->get();
                Lain::where('id', $cek[0]->id)->update($data3);
            });

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
    public function destroy($lain)
    {
        try {
            $enc = Crypt::decrypt($lain);
            $data = Lain::where('kode_usaha', $enc)->get();
            $bu = DB::table('bu_lainnya')->where('usaha_kode', $enc)->get();
            $du = DB::table('du_lainnya')->where('usaha_kode', $enc)->get();

            Lain::where('id', $data[0]->id)->delete();
            if (count($bu) !== 0) {
                for ($i = 0; $i < count($bu); $i++) {
                    DB::table('bu_lainnya')->where('id', $bu[$i]->id)->delete();
                }
            }
            if (count($du) !== 0) {
                for ($j = 0; $j < count($du); $j++) {
                    DB::table('du_lainnya')->where('id', $du[$j]->id)->delete();
                }
            }

            Lain::where('id', $data[0]->id)->delete();
            return redirect()->back()->with('success', 'Usaha lainnya berhasil dihapus');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Usaha lainnya gagal dihapus');
    }


    public function update_edit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->input('lain'));
            //Masuk ke tabel bu_lainnya
            DB::transaction(function () use ($enc, $request) {

                for ($i = 1; $i <= 5; $i++) {
                    $data = [
                        'usaha_kode' => $enc,
                        'kode_lain' => $request->input('bu_kode' . $i),
                        'pengeluaran' => ucwords($request->input('nampe' . $i)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran' . $i)),
                    ];
                    $a = DB::table('bu_lainnya')->where('kode_lain', $request->input('bu_kode' . $i))->get();
                    DB::table('bu_lainnya')->where('id', $a[0]->id)->update($data);
                }
                //Masuk ke tabel du_lainnya
                for ($j = 1; $j <= 5; $j++) {
                    $data2 = [
                        'usaha_kode' => $enc,
                        'kode_lain' => $request->input('du_kode' . $j),
                        'penjualan' => ucwords($request->input('nama' . $j)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nominal' . $j)),
                    ];
                    $b = DB::table('du_lainnya')->where('kode_lain', $request->input('du_kode' . $j))->get();
                    DB::table('du_lainnya')->where('id', $b[0]->id)->update($data2);
                }

                //Masuk ke tabel au_lainnya
                $data3 = [
                    'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->pendapatan),
                    'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->pengeluaran),
                    'proyeksi' => (int)str_replace(["Rp.", " ", "."], "", $request->proyeksi),
                    'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_bersih),
                    'input_user' => Auth::user()->code_user,
                ];

                $cek = Lain::where('kode_usaha', $enc)->get();
                Lain::where('id', $cek[0]->id)->update($data3);
            });
            return redirect()->back()->with('success', 'Data berhasil diubah');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal diubah');
    }
}
