<?php

namespace App\Http\Controllers;

use App\Models\Lain;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UsahaLainnyaController extends Controller
{
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

            return view('staff.analisa.u-lainnya.index', [
                'data' => $cek[0],
                'lain' => $au,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpanlain(Request $request)
    {
        try {
            $req = $request->query('pengajuan');
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

    public function identitas(Request $request)
    {
        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data USaha Lainnya
            $enc = Crypt::decrypt($request->query('kode_usaha'));

            $lain = Lain::where('kode_usaha', $enc)->first();
            $lain->kd_usaha = Crypt::encrypt($lain->kode_usaha);
            // dd($lain);
            return view('staff.analisa.u-lainnya.identitas', [
                'data' => $cek[0],
                'lain' => $lain,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpanidentitas(Request $request)
    {
        $req = $request->validate([
            'lama_usaha' => 'required',
            'lokasi_usaha' => 'required',
        ]);

        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            Lain::where('kode_usaha', $enc)->update($req);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function keuangan(Request $request)
    {

        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data keuangan
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $lain = Lain::where('kode_usaha', $enc)->first();
            $cek_bu = DB::table('bu_lainnya')->where('usaha_kode', $enc)->get();
            $lain->kd_usaha = Crypt::encrypt($lain->kode_usaha);

            //cek data biaya sudah ada atau tidak
            if (count($cek_bu) == 0) {
                return view('staff.analisa.u-lainnya.keuangan', [
                    'data' => $cek[0],
                    'lain' => $lain,
                ]);
            }

            $bu = DB::table('bu_lainnya')->where('usaha_kode', $enc)->get();
            $du = DB::table('du_lainnya')->where('usaha_kode', $enc)->get();

            return view('staff.analisa.u-lainnya.keuangan-edit', [
                'data' => $cek[0],
                'lain' => $lain,
                'biaya' => $bu,
                'pendapatan' => $du,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpankeuangan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            // dd($request);
            DB::transaction(function () use ($enc, $request) {
                for ($i = 1; $i <= 5; $i++) {
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
                for ($j = 1; $j <= 5; $j++) {
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
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function updatekeuangan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            // dd($request);
            DB::transaction(function () use ($enc, $request) {
                for ($i = 1; $i <= 4; $i++) {
                    $data = [
                        'usaha_kode' => $enc,
                        'kode_lain' => $request->input('kod' . $i),
                        'pengeluaran' => ucwords($request->input('nampe' . $i)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran' . $i)),
                    ];
                    DB::table('bu_lainnya')->where('kode_lain', $request->input('kod' . $i))->update($data);
                }
                //Masuk ke tabel du_lainnya
                for ($j = 1; $j <= 4; $j++) {
                    $data2 = [
                        'usaha_kode' => $enc,
                        'kode_lain' => $request->input('kode' . $j),
                        'penjualan' => ucwords($request->input('nama' . $j)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nominal' . $j)),
                    ];
                    DB::table('du_lainnya')->where('kode_lain', $request->input('kode' . $j))->update($data2);
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
            return redirect()->back()->with('success', 'Data berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal menambahkan data');
    }

    public function destroy(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));

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
}