<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Pertanian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UsahaPertanianController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Pertanian::au_pertanian($enc);

            foreach ($au as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
                $item->kode_id = Crypt::encrypt($item->id);
            }
            // dd($cek[0], $au);
            return view('staff.analisa.u-pertanian.index', [
                'data' => $cek[0],
                'pertanian' => $au,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

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
            } else {
                $kode = Pertanian::kodeacak($name, $length);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function informasi(Request $request)
    {
        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data pertanian
            $eneckode = Crypt::decrypt($request->query('kode_usaha'));

            $pertanian = Pertanian::where('kode_usaha', $eneckode)->first();
            $pertanian->kd_usaha = Crypt::encrypt($pertanian->kode_usaha);
            $du = DB::table('du_pertanian')->where('usaha_kode', $pertanian->kode_usaha)->first();

            if (is_null($du)) {
                return view('staff.analisa.u-pertanian.informasi', [
                    'data' => $cek[0],
                    'pertanian' => $pertanian,
                ]);
            }
            $du->total_luas = $du->luas_sendiri + $du->luas_sewa + $du->luas_gadai;

            return view('staff.analisa.u-pertanian.informasi-edit', [
                'data' => $cek[0],
                'pertanian' => $pertanian,
                'detail' => $du,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpaninformasi(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $data = [
                'usaha_kode' => $enc,
                'jenis_tanaman' => ucwords($request->jenis_tanaman),
                'luas_sendiri' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sendiri),
                'luas_sewa' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sewa),
                'luas_gadai' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_gadai),
                'hasil_panen' => (int)str_replace(["Rp.", " ", "."], "", $request->hasil_panen),
                'harga' => (int)str_replace(["Rp.", " ", "."], "", $request->harga),
            ];

            $data2 = [
                'jenis_usaha' => $request->jenis_usaha,
                'lokasi_usaha' => $request->lokasi_usaha,
            ];
            // dd($data2);
            DB::transaction(function () use ($enc, $data, $data2) {
                DB::table('du_pertanian')->insert($data);
                Pertanian::where('kode_usaha', $enc)->update($data2);
            });
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }


    public function updateinformasi(Request $request)
    {
        // dd($request);
        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $data = [
                'usaha_kode' => $enc,
                'jenis_tanaman' => ucwords($request->jenis_tanaman),
                'luas_sendiri' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sendiri),
                'luas_sewa' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sewa),
                'luas_gadai' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_gadai),
                'hasil_panen' => (int)str_replace(["Rp.", " ", "."], "", $request->hasil_panen),
                'harga' => (int)str_replace(["Rp.", " ", "."], "", $request->harga),
            ];

            $data2 = [
                'jenis_usaha' => $request->jenis_usaha,
                'lokasi_usaha' => $request->lokasi_usaha,
            ];

            DB::transaction(function () use ($enc, $data, $data2) {
                DB::table('du_pertanian')->where('usaha_kode', $enc)->update($data);
                Pertanian::where('kode_usaha', $enc)->update($data2);
            });
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }


    public function biaya(Request $request)
    {

        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data pertanian
            $eneckode = Crypt::decrypt($request->query('kode_usaha'));

            $pertanian = Pertanian::where('kode_usaha', $eneckode)->first();
            $pertanian->kd_usaha = Crypt::encrypt($pertanian->kode_usaha);
            //mengambil Luas sewa untuk pengecekan biaya amortisasi
            $du = DB::table('du_pertanian')->where('usaha_kode', $pertanian->kode_usaha)->first();

            // cek sudah ada data biaya pertanian apa belum
            $bu = DB::table('bu_pertanian')->where('usaha_kode', $pertanian->kode_usaha)->first();

            if (is_null($bu) || is_null($du)) {
                return view('staff.analisa.u-pertanian.biaya', [
                    'data' => $cek[0],
                    'pertanian' => $pertanian,
                ]);
            }
            $pertanian->luas_sewa = $du->luas_sewa;

            return view('staff.analisa.u-pertanian.biaya-edit', [
                'data' => $cek[0],
                'pertanian' => $pertanian,
                'biaya' => $bu,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpanbiaya(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $data = [
                'usaha_kode' => $enc,
                'pengolahan_tanah' => (int)str_replace(["Rp.", " ", "."], "", $request->pengolahan_tanah),
                'bibit' => (int)str_replace(["Rp.", " ", "."], "", $request->bibit),
                'pupuk' => (int)str_replace(["Rp.", " ", "."], "", $request->pupuk),
                'pestisida' => (int)str_replace(["Rp.", " ", "."], "", $request->pestisida),
                'pengairan' => (int)str_replace(["Rp.", " ", "."], "", $request->pengairan),
                'tenaga_kerja' => (int)str_replace(["Rp.", " ", "."], "", $request->tenaga_kerja),
                'panen' => (int)str_replace(["Rp.", " ", "."], "", $request->panen),
                'penggarap' => (int)str_replace(["Rp.", " ", "."], "", $request->penggarap),
                'pajak' => (int)str_replace(["Rp.", " ", "."], "", $request->pajak),
                'iuran_desa' => (int)str_replace(["Rp.", " ", "."], "", $request->iuran_desa),
                'amortisasi' => (int)str_replace(["Rp.", " ", "."], "", $request->amortisasi),
                'pinjaman_bank' => (int)str_replace(["Rp.", " ", "."], "", $request->pinjaman_bank),
            ];
            DB::table('bu_pertanian')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }


    public function updatebiaya(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $data = [
                'usaha_kode' => $enc,
                'pengolahan_tanah' => (int)str_replace(["Rp.", " ", "."], "", $request->pengolahan_tanah),
                'bibit' => (int)str_replace(["Rp.", " ", "."], "", $request->bibit),
                'pupuk' => (int)str_replace(["Rp.", " ", "."], "", $request->pupuk),
                'pestisida' => (int)str_replace(["Rp.", " ", "."], "", $request->pestisida),
                'pengairan' => (int)str_replace(["Rp.", " ", "."], "", $request->pengairan),
                'tenaga_kerja' => (int)str_replace(["Rp.", " ", "."], "", $request->tenaga_kerja),
                'panen' => (int)str_replace(["Rp.", " ", "."], "", $request->panen),
                'penggarap' => (int)str_replace(["Rp.", " ", "."], "", $request->penggarap),
                'pajak' => (int)str_replace(["Rp.", " ", "."], "", $request->pajak),
                'iuran_desa' => (int)str_replace(["Rp.", " ", "."], "", $request->iuran_desa),
                'amortisasi' => (int)str_replace(["Rp.", " ", "."], "", $request->amortisasi),
                'pinjaman_bank' => (int)str_replace(["Rp.", " ", "."], "", $request->pinjaman_bank),
            ];
            DB::table('bu_pertanian')->where('usaha_kode', $enc)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }


    public function keuangan(Request $request)
    {

        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data pertanian
            $eneckode = Crypt::decrypt($request->query('kode_usaha'));

            $pertanian = Pertanian::where('kode_usaha', $eneckode)->first();
            $pertanian->kd_usaha = Crypt::encrypt($pertanian->kode_usaha);
            //Detail biaya
            $du = DB::table('du_pertanian')->where('usaha_kode', $pertanian->kode_usaha)->first();
            if (is_null($du)) {
                return redirect()->back()->with('error', 'Isi data informasi terlebih dahulu');
            }
            //Data pengeluaran Biaya pertanian
            $bu = Pertanian::total_biaya($eneckode);
            if ($bu == 0) {
                return redirect()->back()->with('error', 'Isi biaya pertanian terlebih dahulu');
            }
            $bu2 = DB::table('bu_pertanian')->where('usaha_kode', $eneckode)->first();

            $pertanian->luas_sewa = $du->luas_sewa;
            $hasil_panen = $du->hasil_panen * $du->harga;
            $hasil_bersih = $hasil_panen - $bu;

            $ambil = ($hasil_bersih * 70) / 100;
            $jW = $cek[0]->jangka_waktu / 6;
            $saving = $cek[0]->plafon / $jW;
            $sisa_pendapatan = $ambil - $saving;
            $pendapatan_perbulan = $sisa_pendapatan / 6;

            $kalkulasi = [
                'pendapatan' => $hasil_panen,
                'pengeluaran' => $bu,
                'laba_bersih' => $hasil_bersih,
                'ambil' => $ambil,
                'pinjaman_bank' => $bu2->pinjaman_bank,
                'penambahan' => $pertanian->penambahan,
                'saving' => $saving,
                'laba_perbulan' => number_format($pendapatan_perbulan, 0, '', ''),
            ];

            //cek pendapatan ada atau tidak
            if (is_null($pertanian->pendapatan)) {
                return view('staff.analisa.u-pertanian.keuangan', [
                    'data' => $cek[0],
                    'pertanian' => $pertanian,
                    'kalkulasi' => $kalkulasi,
                ]);
            }

            //Cek ada perubahan atau tidak di biaya dan informasi
            if ($kalkulasi['pendapatan'] !== $pertanian->pendapatan || $kalkulasi['pengeluaran'] !== $pertanian->pengeluaran) {
                $data = [
                    'pendapatan' => $kalkulasi['pendapatan'],
                    'pengeluaran' => $kalkulasi['pengeluaran'],
                    'laba_bersih' => $kalkulasi['laba_bersih'],
                    'laba_perbulan' => $kalkulasi['laba_perbulan'],
                ];
                Pertanian::where('kode_usaha', $eneckode)->update($data);
            } elseif ($kalkulasi['laba_perbulan'] !== $pertanian->laba_perbulan && $pertanian->penambahan == 0) {
                $data2 = [
                    'laba_perbulan' => $kalkulasi['laba_perbulan'],
                ];
                Pertanian::where('kode_usaha', $eneckode)->update($data2);
            } elseif ($kalkulasi['laba_perbulan'] !== $pertanian->laba_perbulan && $pertanian->penambahan > 0) {
                $tot = $kalkulasi['laba_perbulan'] + $pertanian->penambahan;
                $data3 = [
                    'laba_perbulan' => $tot,
                ];
                Pertanian::where('kode_usaha', $eneckode)->update($data3);
            }

            $pertanian2 = Pertanian::where('kode_usaha', $eneckode)->first();
            $pertanian2->kd_usaha = Crypt::encrypt($pertanian->kode_usaha);
            $kalkulasi = [
                'pendapatan' => $pertanian2->pendapatan,
                'pengeluaran' => $pertanian2->pengeluaran,
                'laba_bersih' => $pertanian2->laba_bersih,
                'ambil' => $ambil,
                'pinjaman_bank' => $bu2->pinjaman_bank,
                'penambahan' => $pertanian2->penambahan,
                'saving' => $saving,
                'laba_perbulan' => number_format($pertanian2->laba_perbulan, 0, '', ''),
            ];

            return view('staff.analisa.u-pertanian.keuangan-edit', [
                'data' => $cek[0],
                'pertanian' => $pertanian2,
                'kalkulasi' => $kalkulasi,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpankeuangan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));

            $data = [
                'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->hasil_panen) ?? 0,
                'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->total_biaya) ?? 0,
                'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_bersih) ?? 0,
                'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->penambahan) ?? 0,
                'laba_perbulan' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_perbulan) ?? 0,
            ];

            $data2 = [
                'pinjaman_bank' => (int)str_replace(["Rp.", " ", "."], "", $request->pinjaman_bank) ?? 0,
            ];

            DB::transaction(function () use ($data, $data2, $enc) {
                Pertanian::where('kode_usaha', $enc)->update($data);
                DB::table('bu_pertanian')->where('usaha_kode', $enc)->update($data2);
            });

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function destroy(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));

            $au = Pertanian::where('kode_usaha', $enc)->pluck('id');

            $bu = DB::table('bu_pertanian')->where('usaha_kode', $enc)->get();
            $du = DB::table('du_pertanian')->where('usaha_kode', $enc)->get();

            DB::transaction(function () use ($au, $bu, $du) {
                if (count($au) !== 0) {
                    Pertanian::where('id', $au[0])->delete();
                }

                if (count($bu) !== 0) {
                    DB::table('bu_pertanian')->where('id', $bu[0]->id)->delete();
                }

                if (count($du) !== 0) {
                    DB::table('du_pertanian')->where('id', $du[0]->id)->delete();
                }
            });

            return redirect()->back()->with('success', 'Usaha pertanian berhasil dihapus');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Usaha pertanian gagal dihapus');
    }
}
