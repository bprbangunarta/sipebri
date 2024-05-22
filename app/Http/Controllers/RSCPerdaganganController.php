<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPerdaganganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                )
                ->orderBy('rsc_data_pengajuan.created_at', 'desc')
                ->paginate(10);
            //
            $perdagangan = DB::table('rsc_au_perdagangan')->where('kode_rsc', $enc_rsc)->get();

            foreach ($perdagangan as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
                $item->kd_pengajuan = $request->query('kode');
            }

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            return view('rsc.usaha_perdagangan.index', [
                'data' => $data[0],
                'perdagangan' => $perdagangan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_perdagangan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $kode_usaha = $this->kodeacak('AUP', 6);

            $data = [
                'kode_rsc' => $enc_rsc,
                'kode_usaha' => $kode_usaha,
                'nama_usaha' => $request->nama_usaha,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_au_perdagangan')->insert($data);
            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_rsc_perdagangan_identitas(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                )
                ->where('rsc_data_pengajuan.pengajuan_kode', $kode)
                ->get();
            //
            foreach ($data as $item) {
                $item->kode_usaha = $request->query('kode_usaha');
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            $perdagangan = DB::table('rsc_au_perdagangan')
                ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_au_perdagangan.kode_rsc')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_au_perdagangan.kode_usaha',
                    'rsc_au_perdagangan.nama_usaha',
                    'rsc_au_perdagangan.lokasi_usaha',
                    'rsc_au_perdagangan.lama_usaha',
                    'data_nasabah.alamat_ktp'
                )
                ->where('kode_usaha', $kode_usaha)->get();
            //

            return view('rsc.usaha_perdagangan.identitas', [
                'data' => $data[0],
                'perdagangan' => $perdagangan[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_perdagangan_identitas(Request $request)
    {
        $cek = $request->validate([
            'kode_usaha' => 'required',
            'lama_usaha' => 'required',
            'nama_usaha' => 'required',
            'lokasi_usaha' => 'required',
        ]);

        $insert = DB::table('rsc_au_perdagangan')->where('kode_usaha', $request->kode_usaha)->update($cek);

        if ($insert) {
            return redirect()->back()->with('success', 'Berhasil menyimpan data.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Data gagal disimpan.');
        }
    }

    public function barang(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                )
                ->orderBy('rsc_data_pengajuan.created_at', 'desc')
                ->paginate(10);
            //
            foreach ($data as $item) {
                $item->kode_usaha = $request->query('kode_usaha');
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            $barang = DB::table('rsc_du_perdagangan')->where('usaha_kode', $kode_usaha)->get();
            if (count($barang) <= 0) {
                return view('rsc.usaha_perdagangan.barang', [
                    'data' => $data[0],
                ]);
            }

            $perdagangan = DB::table('rsc_au_perdagangan')->where('kode_usaha', $kode_usaha)->get();

            return view('rsc.usaha_perdagangan.barang-edit', [
                'data' => $data[0],
                'barang' => $barang,
                'perdagangan' => $perdagangan[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_barang(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->kode_usaha);

            $data = [];
            for ($i = 1; $i <= 10; $i++) {
                $data = [
                    'usaha_kode' => $enc,
                    'nama_barang' => ucwords($request->input('nama_barang' . $i)),
                    'stok' => $request->input('stock' . $i),
                    'harga_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg' . $i)),
                    'harga_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('jual' . $i)),
                    'laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba' . $i)),
                    'presentase_laba' => sprintf("%.2f", $request->input('persen' . $i), 2),
                    'created_at' => now(),
                ];
                DB::table('rsc_du_perdagangan')->insert($data);
            }

            $data2 = [
                'total_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tbeli')),
                'total_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tjual')),
                'total_laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tlaba')),
                'total_stok' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tstock')),
                'total_pl' => sprintf("%.2f", $request->input('tpersen'), 2),
            ];
            $update = DB::table('rsc_au_perdagangan')->where('kode_usaha', $enc)->update($data2);

            if ($update) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data');
            } else {
                return redirect()->back()->with('success', 'Gagal menambahkan data');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_barang(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->kode_usaha);

            $data = [];
            for ($i = 1; $i <= 10; $i++) {
                $data = [
                    'usaha_kode' => $enc,
                    'nama_barang' => ucwords($request->input('nama_barang' . $i)),
                    'stok' => $request->input('stock' . $i),
                    'harga_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('hrg' . $i)),
                    'harga_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('jual' . $i)),
                    'laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba' . $i)),
                    'presentase_laba' => sprintf("%.2f", $request->input('persen' . $i), 2),
                    'created_at' => now(),
                ];
                DB::table('rsc_du_perdagangan')->where('id', $request->kode_barang . $i)->update($data);
            }

            $data2 = [
                'total_beli' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tbeli')),
                'total_jual' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tjual')),
                'total_laba' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tlaba')),
                'total_stok' => (int)str_replace(["Rp.", " ", "."], "", $request->input('tstock')),
                'total_pl' => sprintf("%.2f", $request->input('tpersen'), 2),
            ];
            $update = DB::table('rsc_au_perdagangan')->where('kode_usaha', $enc)->update($data2);

            if ($update) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data');
            } else {
                return redirect()->back()->with('success', 'Gagal menambahkan data');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                )
                ->orderBy('rsc_data_pengajuan.created_at', 'desc')
                ->paginate(10);
            //
            foreach ($data as $item) {
                $item->kode_usaha = $request->query('kode_usaha');
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            $perdagangan = DB::table('rsc_au_perdagangan')->where('kode_usaha', $kode_usaha)->get();

            $biaya_perdagangan = DB::table('rsc_bu_perdagangan')->where('usaha_kode', $kode_usaha)->first();

            return view('rsc.usaha_perdagangan.keuangan', [
                'data' => $data[0],
                'perdagangan' => $perdagangan[0],
                'biaya_perdagangan' => $biaya_perdagangan
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));

            $data = [
                'belanja_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('belanja_harian')) ?? 0,
                'omset_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('omset_harian')) ?? 0,
                'pokok_penjualan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pokok_penjualan')) ?? 0,
                'laba_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_harian')) ?? 0,
                'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan')) ?? 0,
                'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran')) ?? 0,
                'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('penambahan')) ?? 0,
                'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih')) ?? 0,
            ];

            $datas = [
                'usaha_kode' => $kode_usaha,
                'transportasi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('transportasi')) ?? 0,
                'bongkar_muat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('bongkar_muat')) ?? 0,
                'pegawai' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pegawai')) ?? 0,
                'gatel' => (int)str_replace(["Rp.", " ", "."], "", $request->input('gatel')) ?? 0,
                'retribusi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('retribusi')) ?? 0,
                'sewa_tempat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('sewa_tempat')) ?? 0,
                'created_at' => now(),
            ];

            $insert = DB::transaction(function () use ($data, $datas, $kode_usaha) {
                $insert1 = DB::table('rsc_au_perdagangan')->where('kode_usaha', $kode_usaha)->update($data);
                $insert2 = DB::table('rsc_bu_perdagangan')->insert($datas);

                return $insert1 && $insert2;
            });

            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil diupdate');
            } else {
                return redirect()->back()->with('error', 'Data gagal diupdate');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));

            $data = [
                'belanja_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('belanja_harian')) ?? 0,
                'omset_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('omset_harian')) ?? 0,
                'pokok_penjualan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pokok_penjualan')) ?? 0,
                'laba_harian' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_harian')) ?? 0,
                'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan')) ?? 0,
                'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran')) ?? 0,
                'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->input('penambahan')) ?? 0,
                'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih')) ?? 0,
            ];

            $datas = [
                'usaha_kode' => $kode_usaha,
                'transportasi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('transportasi')) ?? 0,
                'bongkar_muat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('bongkar_muat')) ?? 0,
                'pegawai' => (int)str_replace(["Rp.", " ", "."], "", $request->input('pegawai')) ?? 0,
                'gatel' => (int)str_replace(["Rp.", " ", "."], "", $request->input('gatel')) ?? 0,
                'retribusi' => (int)str_replace(["Rp.", " ", "."], "", $request->input('retribusi')) ?? 0,
                'sewa_tempat' => (int)str_replace(["Rp.", " ", "."], "", $request->input('sewa_tempat')) ?? 0,
                'updated_at' => now(),
            ];

            $update = DB::transaction(function () use ($data, $datas, $kode_usaha) {
                $update1 =  DB::table('rsc_au_perdagangan')->where('kode_usaha', $kode_usaha)->update($data);
                $update2 = DB::table('rsc_bu_perdagangan')->where('usaha_kode', $kode_usaha)->update($datas);

                return $update1 && $update2;
            });

            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil diupdate');
            } else {
                return redirect()->back()->with('error', 'Data gagal diupdate');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function delete(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $au_perdagangan = DB::table('rsc_au_perdagangan')->where('kode_usaha', $kode_usaha)->first();
            $bu_perdagangan = DB::table('rsc_bu_perdagangan')->where('usaha_kode', $kode_usaha)->first();
            $du_perdagangan = DB::table('rsc_du_perdagangan')->where('usaha_kode', $kode_usaha)->get();

            if (!is_null($au_perdagangan)) {
                DB::table('rsc_au_perdagangan')->where('kode_usaha', $kode_usaha)->delete();
            }

            if (!is_null($bu_perdagangan)) {
                DB::table('rsc_bu_perdagangan')->where('usaha_kode', $kode_usaha)->delete();
            }

            if (count($du_perdagangan) > 0) {
                foreach ($du_perdagangan as $value) {
                    DB::table('rsc_du_perdagangan')->where('id', $value->id)->delete();
                }
            }
            return redirect()->back()->with('success', 'Usaha perdagangan berhasil dihapus');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }



    private function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('rsc_au_perdagangan')->where('kode_usaha', $acak)->exists()) {
                return $acak;
            }
        }
        return null;
    }
}
