<?php

namespace App\Http\Controllers;

use App\Models\RSC;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCLainController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_rsc();

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $status_rsc;
            }

            $lain = DB::table('rsc_au_lain')->where('kode_rsc', $enc_rsc)->get();

            foreach ($lain as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
            }

            return view('rsc.usaha_lainnya.index', [
                'data' => $data[0],
                'lain' => $lain,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_lain(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $kode_usaha = $this->kodeacak('AUL', 6);

            $data = [
                'kode_rsc' => $enc_rsc,
                'kode_usaha' => $kode_usaha,
                'nama_usaha' => strtoupper($request->nama_usaha),
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_au_lain')->insert($data);
            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function rsc_lain_identitas(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_rsc();

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->kode_usaha = $request->query('kode_usaha');
                $item->status_rsc = $status_rsc;
            }

            $lain = DB::table('rsc_au_lain')->where('kode_usaha', $kode_usaha)->first();

            return view('rsc.usaha_lainnya.identitas', [
                'data' => $data[0],
                'lain' => $lain,

            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_rsc_lain_identitas(Request $request)
    {
        try {
            $cek = $request->validate([
                'kode_usaha' => 'required',
                'nama_usaha' => 'required',
            ]);

            $data = [
                'nama_usaha' => strtoupper($request->nama_usaha),
                'jenis_usaha' => $request->jenis_usaha,
                'lama_usaha' => $request->lama_usaha,
                'lokasi_usaha' => $request->lokasi_usaha,
            ];

            $update = DB::table('rsc_au_lain')->where('kode_usaha', $request->kode_usaha)->update($data);
            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function rsc_lain_bahan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_rsc();

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->kode_usaha = $request->query('kode_usaha');
                $item->status_rsc = $status_rsc;
            }

            $bahan = DB::table('rsc_bahan_baku_lain')->where('usaha_kode', $kode_usaha)->get();
            // dd($bahan);
            if (count($bahan) <= 0) {
                return view('rsc.usaha_lainnya.bahan-baku', [
                    'data' => $data[0],
                ]);
            } else {
                return view('rsc.usaha_lainnya.bahan-baku-edit', [
                    'data' => $data[0],
                    'bahan' => $bahan,
                ]);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_lain_bahan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            for ($i = 1; $i <= 10; $i++) {
                $data = [
                    'usaha_kode' => $kode_usaha,
                    'bahan_baku' => strtoupper($request->input('bahan_baku' . $i)) ?? null,
                    'jumlah' => $request->input('jumlah' . $i) ?? 0,
                    'harga' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg' . $i)) ?? 0,
                    'total' => (int)str_replace(["Rp.", " ", "."], "", $request->input('total' . $i)) ?? 0,
                    'created_at' => now(),
                ];
                DB::table('rsc_bahan_baku_lain')->insert($data);
            }

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_rsc_lain_bahan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            for ($i = 0; $i < 10; $i++) {
                $data = [
                    'usaha_kode' => $kode_usaha,
                    'bahan_baku' => strtoupper($request->input('bahan_baku' . $i)) ?? null,
                    'jumlah' => $request->input('jumlah' . $i) ?? 0,
                    'harga' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg' . $i)) ?? 0,
                    'total' => (int)str_replace(["Rp.", " ", "."], "", $request->input('total' . $i)) ?? 0,
                    'created_at' => now(),
                ];
                DB::table('rsc_bahan_baku_lain')->where('id', $request->input('kode_barang' . $i))->update($data);
            }
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function rsc_lain_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_rsc();

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->kode_usaha = $request->query('kode_usaha');
                $item->status_rsc = $status_rsc;
            }

            $lain = DB::table('rsc_au_lain')->where('kode_usaha', $kode_usaha)->first();

            $bahan_baku = DB::table('rsc_bahan_baku_lain')->where('usaha_kode', $kode_usaha)->get();
            if (count($bahan_baku) > 0) {
                $total = [];
                foreach ($bahan_baku as $item) {
                    $total[] = $item->total;
                }
                $lain->biaya_bahan = array_sum($total);
            } else {
                $lain->biaya_bahan = 0;
            }
            // dd($lain, $total);

            $cek_pendapatan = DB::table('rsc_pendapatan_lain')->where('usaha_kode', $kode_usaha)->get();
            $cek_pengeluaran = DB::table('rsc_pengeluaran_lain')->where('usaha_kode', $kode_usaha)->get();

            if (count($cek_pendapatan) <= 0) {
                return view('rsc.usaha_lainnya.keuangan', [
                    'data' => $data[0],
                    'lain' => $lain,
                ]);
            } else {
                return view('rsc.usaha_lainnya.keuangan-edit', [
                    'data' => $data[0],
                    'lain' => $lain,
                    'pendapatan' => $cek_pendapatan,
                    'biaya' => $cek_pengeluaran,
                ]);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_lain_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            DB::transaction(function () use ($kode_usaha, $request) {
                for ($i = 1; $i <= 5; $i++) {

                    $data = [
                        'usaha_kode' => $kode_usaha,
                        'pengeluaran' => strtoupper($request->input('nampe' . $i)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran' . $i)),
                        'created_at' => now(),
                    ];
                    DB::table('rsc_pengeluaran_lain')->insert($data);
                }
                //Masuk ke tabel du_lainnya
                for ($j = 1; $j <= 5; $j++) {

                    $data2 = [
                        'usaha_kode' => $kode_usaha,
                        'penjualan' => strtoupper($request->input('nama' . $j)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nominal' . $j)),
                        'created_at' => now(),
                    ];
                    DB::table('rsc_pendapatan_lain')->insert($data2);
                }

                //Masuk ke tabel au_lainnya
                $data3 = [
                    'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->pendapatan),
                    'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->pengeluaran),
                    'proyeksi' => (int)str_replace(["Rp.", " ", "."], "", $request->proyeksi),
                    'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_bersih),
                    'updated_at' => now(),
                ];

                DB::table('rsc_au_lain')->where('kode_usaha', $kode_usaha)->update($data3);
            });
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_rsc_lain_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));

            DB::transaction(function () use ($kode_usaha, $request) {
                for ($i = 1; $i <= 5; $i++) {

                    $data = [
                        'usaha_kode' => $kode_usaha,
                        'pengeluaran' => strtoupper($request->input('nampe' . $i)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran' . $i)),
                        'updated_at' => now(),
                    ];
                    DB::table('rsc_pengeluaran_lain')->where('id', $request->input('kod' . $i))->update($data);
                }
                //Masuk ke tabel du_lainnya
                for ($j = 1; $j <= 5; $j++) {

                    $data2 = [
                        'usaha_kode' => $kode_usaha,
                        'penjualan' => strtoupper($request->input('nama' . $j)),
                        'nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->input('nominal' . $j)),
                        'updated_at' => now(),
                    ];
                    DB::table('rsc_pendapatan_lain')->where('id', $request->input('kode' . $j))->update($data2);
                }

                //Masuk ke tabel au_lainnya
                $data3 = [
                    'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->pendapatan),
                    'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->pengeluaran),
                    'proyeksi' => (int)str_replace(["Rp.", " ", "."], "", $request->proyeksi),
                    'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_bersih),
                    'updated_at' => now(),
                ];

                DB::table('rsc_au_lain')->where('kode_usaha', $kode_usaha)->update($data3);
            });
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function delete_rsc_lain(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $au_lain = DB::table('rsc_au_lain')->where('kode_usaha', $kode_usaha)->first();
            $pendapatan = DB::table('rsc_pendapatan_lain')->where('usaha_kode', $kode_usaha)->get();
            $pengeluaran = DB::table('rsc_pengeluaran_lain')->where('usaha_kode', $kode_usaha)->get();
            $bahan = DB::table('rsc_bahan_baku_lain')->where('usaha_kode', $kode_usaha)->get();

            if (!is_null($au_lain)) {
                DB::table('rsc_au_lain')->where('kode_usaha', $kode_usaha)->delete();
            }

            if (count($pendapatan) > 0) {
                foreach ($pendapatan as $value) {
                    DB::table('rsc_pendapatan_lain')->where('id', $value->id)->delete();
                }
            }

            if (count($bahan) > 0) {
                foreach ($bahan as $value) {
                    DB::table('rsc_bahan_baku_lain')->where('id', $value->id)->delete();
                }
            }

            if (count($pengeluaran) > 0) {
                foreach ($pengeluaran as $value) {
                    DB::table('rsc_pengeluaran_lain')->where('id', $value->id)->delete();
                }
            }
            return redirect()->back()->with('success', 'Usaha Lainnya berhasil dihapus');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }



    private function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('rsc_au_lain')->where('kode_usaha', $acak)->exists()) {
                return $acak;
            }
        }
        return null;
    }
}
