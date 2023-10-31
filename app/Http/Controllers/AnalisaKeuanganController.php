<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaKeuanganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $kemampuan = Midle::kemampuan_keuangan($enc);
            $data = Keuangan::data_keuangan($enc);

            $filter = array_filter($kemampuan, function ($value) {
                return $value !== null;
            });

            //Hasil penjumlahan analisa usaha
            $total = array_sum($filter);
            $kemampuan['total'] = $total;
            // dd($total);
            if ($data === 0) {
                return view('staff.analisa.keuangan', [
                    'data' => $cek[0],
                    'kemampuan' => $kemampuan
                ]);
            }

            /**Ambil data keuangan dengan filed biaya rumah tanggga
               biaya kewajiban lain dan keuangan perbulan untuk jadi pengurangan hasil
               secara realtime ketika ada perubahan nominal dianalisa usaha**/
            $keuangan = Keuangan::where('pengajuan_kode', $enc)->get();
            //cek ada datanya atau tidak
            if ($keuangan->isEmpty()) {
                $uang['rumah'] = 0;
                $uang['kewajiban'] = 0;
                $uang['perbulan'] = 0;
            } else {
                $uang = [];
                for ($m = 0; $m < count($keuangan); $m++) {
                    $uang['rumah'] = $keuangan[$m]->b_rumah_tangga ?? 0;
                    $uang['kewajiban'] = $keuangan[$m]->b_kewajiban_lainya ?? 0;
                    $uang['perbulan'] = $keuangan[$m]->keuangan_perbulan ?? 0;
                }
            }

            //hasil pengurangan berubah secara realtime
            $jml = $uang['rumah'] + $uang['kewajiban'];
            $tb = $kemampuan['total'] - $jml;
            $kemampuan['keuangan_perbulan'] = $tb;
            //data keuangan perbulan
            $kemampuan['data_perbulan'] = $uang['perbulan'];

            //simpan data jika ada perubahan data usaha saja
            if ($kemampuan['keuangan_perbulan'] !== $kemampuan['data_perbulan']) {
                $b_rumah = $uang['rumah'];
                $b_kewajiban = $uang['kewajiban'];
                $t_biaya = $b_rumah + $b_kewajiban;
                $hasil = $kemampuan['total'] - $t_biaya;
                $au = [
                    'p_usaha' => $kemampuan['total'],
                    'b_rumah_tangga' => $b_rumah,
                    'b_kewajiban_lainya' => $b_kewajiban,
                    'keuangan_perbulan' => $hasil,
                    'input_user' => Auth::user()->code_user,
                ];
                Keuangan::where('pengajuan_kode', $enc)->update($au);
            }

            return view('staff.analisa.keuangan-edit', [
                'data' => $cek[0],
                'kemampuan' => $kemampuan,
                'biaya' => $data,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function store(Request $request)
    {
        // dd($request);
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
                    'p_usaha' => (int)str_replace(["Rp", " ", "."], "", $request->p_usaha),
                    'b_rumah_tangga' => (int)str_replace(["Rp", " ", "."], "", $request->b_rumah_tangga),
                    'b_kewajiban_lainya' => (int)str_replace(["Rp", " ", "."], "", $request->b_kewajiban_lainya),
                    'keuangan_perbulan' => (int)str_replace(["Rp", " ", "."], "", $request->keuangan_perbulan),
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
                        'pengeluaran' => ucwords($request->input('nama' . $i)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('biaya' . $i)),
                    ];
                    DB::table('bu_keuangan')->insert($data);
                }

                //Kewajiban lain
                for ($j = 1; $j <= 3; $j++) {
                    $length = 10;
                    $k = Keuangan::du_kodeacak($length);
                    $data1 = [
                        'keuangan_kode' => $kode,
                        'kode_biaya' => $k,
                        'pengeluaran' => ucwords($request->input('data' . $j)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('kewajiban' . $j)),
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

    public function update(Request $request)
    {
        // dd($request);
        try {
            DB::transaction(function () use ($request) {
                $enc = $request->query('keuangan');
                //Update Biaya Rumah Tangga
                for ($i = 1; $i <= 7; $i++) {
                    $data = [
                        'keuangan_kode' => $enc,
                        'kode_biaya' => $request->input('kode' . $i),
                        'pengeluaran' => ucwords($request->input('nama' . $i)),
                        'nominal' => (int)str_replace(["Rp", " ", "."], "", $request->input('biaya' . $i)),
                    ];
                    $cek = DB::table('bu_keuangan')->where('kode_biaya', $request->input('kode' . $i))->get();
                    DB::table('bu_keuangan')->where('id', $cek[0]->id)->update($data);
                }

                //Update Biaya Rumah Tangga
                for ($j = 1; $j <= 3; $j++) {
                    $data1 = [
                        'keuangan_kode' => $enc,
                        'kode_biaya' => $request->input('kd' . $j),
                        'pengeluaran' => ucwords($request->input('data' . $j)),
                        'nominal' => (int)str_replace(["Rp", " ", "."], "", $request->input('kewajiban' . $j)),
                    ];
                    $cek1 = DB::table('bu_keuangan')->where('kode_biaya', $request->input('kd' . $j))->get();
                    DB::table('bu_keuangan')->where('id', $cek1[0]->id)->update($data1);
                }


                $au = [
                    'p_usaha' => (int)str_replace(["Rp", " ", "."], "", $request->p_usaha),
                    'b_rumah_tangga' => (int)str_replace(["Rp", " ", "."], "", $request->b_rumah_tangga),
                    'b_kewajiban_lainya' => (int)str_replace(["Rp", " ", "."], "", $request->b_kewajiban_lainya),
                    'keuangan_perbulan' => (int)str_replace(["Rp", " ", "."], "", $request->keuangan_perbulan),
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
