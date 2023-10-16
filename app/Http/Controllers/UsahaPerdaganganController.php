<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Perdagangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UsahaPerdaganganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $au = Perdagangan::au_perdagangan($enc);

            foreach ($au as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = Crypt::encrypt($item->pengajuan_kode);
                $item->kode_id = Crypt::encrypt($item->id);
            }
            // dd($au);
            return view('staff.analisa.u-perdagangan.index', [
                'data' => $cek[0],
                'perdagangan' => $au,
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
            } else {
                $kode = Perdagangan::kodeacak($name, $length);
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

            // $cek[0]->kd_nasabah = $request->query('nasabah');

            //Data perdagangan
            $enecpdg = Crypt::decrypt($request->query('kode_usaha'));

            $perdagangan = Perdagangan::where('kode_usaha', $enecpdg)->first();
            $perdagangan->kd_usaha = Crypt::encrypt($perdagangan->kode_usaha);
            // dd($perdagangan);
            return view('staff.analisa.u-perdagangan.identitas', [
                'data' => $cek[0],
                'perdagangan' => $perdagangan,
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
            Perdagangan::where('id', $request->id)->update($req);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function barang(Request $request)
    {
        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data barang
            $enecpdg = Crypt::decrypt($request->query('kode_usaha'));
            $barang = DB::table('du_perdagangan')->where('usaha_kode', $enecpdg)->get();
            //Data perdagangan
            $perdagangan = Perdagangan::where('kode_usaha', $enecpdg)->first();
            $perdagangan->kd_usaha = Crypt::encrypt($perdagangan->kode_usaha);

            //cek data barang
            if (count($barang) == 0) {
                return view('staff.analisa.u-perdagangan.barang', [
                    'data' => $cek[0],
                    'barang' => $perdagangan,
                ]);
            }
            // dd($perdagangan);
            return view('staff.analisa.u-perdagangan.barang-edit', [
                'data' => $cek[0],
                'perdagangan' => $perdagangan,
                'barang' => $barang,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }


    public function simpanbarang(Request $request)
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
            $enc = Crypt::decrypt($request->kode_usaha);

            $data = [];
            for ($i = 1; $i <= 10; $i++) {
                $length = 10;
                $kode = Perdagangan::du_kodeacak($length);
                $data = [
                    'usaha_kode' => $enc,
                    'kode_barang' => $kode,
                    'nama_barang' => ucwords($request->input('nama_barang' . $i)),
                    'stok' => $request->input('stock' . $i),
                    'harga_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg' . $i)),
                    'harga_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('jual' . $i)),
                    'laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba' . $i)),
                    'presentase_laba' => sprintf("%.2f", $request->input('persen' . $i), 2),
                ];
                DB::table('du_perdagangan')->insert($data);
            }

            $data2 = [
                'total_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tbeli')),
                'total_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tjual')),
                'total_laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tlaba')),
                'total_stok' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tstock')),
                'total_pl' => sprintf("%.2f", $request->input('tpersen'), 2),
            ];
            Perdagangan::where('kode_usaha', $enc)->update($data2);

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('serror', 'Gagal menambahkan data');
    }


    public function updatebarang(Request $request)
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
            $enc = Crypt::decrypt($request->kode_usaha);

            $data = [];
            for ($i = 1; $i <= 10; $i++) {
                $length = 10;
                $kode = Perdagangan::du_kodeacak($length);
                $data = [
                    'usaha_kode' => $enc,
                    'kode_barang' => $kode,
                    'nama_barang' => ucwords($request->input('nama_barang' . $i)),
                    'stok' => $request->input('stock' . $i),
                    'harga_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg' . $i)),
                    'harga_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('jual' . $i)),
                    'laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba' . $i)),
                    'presentase_laba' => sprintf("%.2f", $request->input('persen' . $i), 2),
                ];
                $cekbarang = DB::table('du_perdagangan')->where('kode_barang', $request->input('kode_barang' . $i))->get();
                DB::table('du_perdagangan')->where('id', $cekbarang[0]->id)->update($data);
            }

            $data2 = [
                'total_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tbeli')),
                'total_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tjual')),
                'total_laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tlaba')),
                'total_stok' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tstock')),
                'total_pl' => sprintf("%.2f", $request->input('tpersen'), 2),
            ];
            Perdagangan::where('kode_usaha', $enc)->update($data2);

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('serror', 'Gagal menambahkan data');
    }

    public function keuangan(Request $request)
    {
        try {
            $encpengajuan = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($encpengajuan);

            //Data keuangan
            $enecpdg = Crypt::decrypt($request->query('kode_usaha'));
            $keuangan = DB::table('bu_perdagangan')->where('usaha_kode', $enecpdg)->get();

            //Data perdagangan
            $perdagangan = Perdagangan::where('kode_usaha', $enecpdg)->first();
            $perdagangan->kd_usaha = Crypt::encrypt($perdagangan->kode_usaha);

            if ($perdagangan->belanja_harian == 0) {
                return view('staff.analisa.u-perdagangan.keuangan', [
                    'data' => $cek[0],
                    'perdagangan' => $perdagangan,
                ]);
            }
            // dd($keuangan);
            return view('staff.analisa.u-perdagangan.keuangan-edit', [
                'data' => $cek[0],
                'perdagangan' => $perdagangan,
                'keuangan' => $keuangan[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }



    public function simpankeuangan(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('kode_usaha'));
            $user = Auth::user()->code_user;

            $data = [
                'usaha_kode' => $enc,
                'transportasi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('transportasi')) ?? 0,
                'bongkar_muat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('bongkar_muat')) ?? 0,
                'pegawai' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pegawai')) ?? 0,
                'gatel' => (int)str_replace(["Rp.", " ", "."], "", $request->input('gatel')) ?? 0,
                'retribusi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('retribusi')) ?? 0,
                'sewa_tempat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('sewa_tempat')) ?? 0,
            ];


            $data2 = [
                'belanja_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('belanja_harian')) ?? 0,
                'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan')) ?? 0,
                'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran')) ?? 0,
                'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('penambahan')) ?? 0,
                'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih')) ?? 0,
                'input_user' => $user,
            ];

            DB::transaction(function () use ($enc, $data, $data2) {
                DB::table('bu_perdagangan')->insert($data);
                Perdagangan::where('kode_usaha', $enc)->update($data2);
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
            $user = Auth::user()->code_user;

            $data = [
                'usaha_kode' => $enc,
                'transportasi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('transportasi')) ?? 0,
                'bongkar_muat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('bongkar_muat')) ?? 0,
                'pegawai' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pegawai')) ?? 0,
                'gatel' => (int)str_replace(["Rp.", " ", "."], "", $request->input('gatel')) ?? 0,
                'retribusi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('retribusi')) ?? 0,
                'sewa_tempat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('sewa_tempat')) ?? 0,
            ];


            $data2 = [
                'belanja_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('belanja_harian')) ?? 0,
                'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan')) ?? 0,
                'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran')) ?? 0,
                'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('penambahan')) ?? 0,
                'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih')) ?? 0,
                'input_user' => $user,
            ];
            // dd($data, $data2);
            DB::transaction(function () use ($enc, $data, $data2) {
                DB::table('bu_perdagangan')->where('usaha_kode', $enc)->update($data);
                Perdagangan::where('kode_usaha', $enc)->update($data2);
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

            $au = Perdagangan::where('kode_usaha', $enc)->get();
            $bu = DB::table('bu_perdagangan')->where('usaha_kode', $enc)->get();
            $du = DB::table('du_perdagangan')->where('usaha_kode', $enc)->get();
            // dd($au, $bu, $du);
            if (count($au) !== 0) {
                Perdagangan::where('id', $au[0]->id)->delete();
            }
            if (count($bu) !== 0) {
                DB::table('bu_perdagangan')->where('id', $bu[0]->id)->delete();
            }
            if (count($du) !== 0) {

                for ($i = 0; $i < count($du); $i++) {
                    DB::table('du_perdagangan')->where('id', $du[$i]->id)->delete();
                }
            }
            return redirect()->back()->with('success', 'Usaha perdagangan berhasil dihapus');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Usaha perdagangan gagal dihapus');
    }
}
