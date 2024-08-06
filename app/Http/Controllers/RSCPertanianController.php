<?php

namespace App\Http\Controllers;

use App\Models\RSC;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPertanianController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_pertanian_all_rsc($enc_rsc);

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $status_rsc;
            }

            $pertanian = DB::table('rsc_au_pertanian')->where('kode_rsc', $enc_rsc)->get();

            foreach ($pertanian as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
            }

            return view('rsc.usaha_pertanian.index', [
                'data' => $data[0],
                'pertanian' => $pertanian
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_pertanian(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $kode_usaha = $this->kodeacak('AUT', 6);

            $cek = $request->validate([
                'nama_usaha' => 'required'
            ]);

            $data = [
                'kode_rsc' => $enc_rsc,
                'kode_usaha' => $kode_usaha,
                'nama_usaha' => strtoupper($request->nama_usaha),
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $data2 = [
                'usaha_kode' => $kode_usaha,
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_au_pertanian')->insert($data);
            $insert2 = DB::table('rsc_bu_pertanian')->insert($data2);

            if ($insert && $insert2) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_rsc_pertanian_informasi(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_pertanian_rsc($kode);

            foreach ($data as $item) {
                $item->kode_usaha = $request->query('kode_usaha');
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $status_rsc;
            }

            $pertanian = DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->get();
            $detail = DB::table('rsc_bu_pertanian')->where('usaha_kode', $kode_usaha)->get();

            if (count($detail) > 0) {
                $detail[0]->total_luas = $detail[0]->luas_sendiri + $detail[0]->luas_sewa + $detail[0]->luas_gadai;
            }

            return view('rsc.usaha_pertanian.informasi', [
                'data' => $data[0],
                'pertanian' => $pertanian[0],
                'detail' => $detail[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_rsc_pertanian_informasi(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('rsc'));
            $kode_usaha = $request->kode_usaha;
            $data = [
                'usaha_kode' => $request->kode_usaha,
                'jenis_tanaman' => $request->jenis_tanaman,
                'luas_sendiri' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sendiri) ?? 0,
                'luas_sewa' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_sewa) ?? 0,
                'luas_gadai' => (int)str_replace(["Rp.", " ", "."], "", $request->luas_gadai) ?? 0,
                'hasil_panen' => (int)str_replace(["Rp.", " ", "."], "", $request->hasil_panen) ?? 0,
                'harga' => (int)str_replace(["Rp.", " ", "."], "", $request->harga) ?? 0,
            ];

            $data2 = [
                'jenis_usaha' => strtoupper($request->jenis_usaha),
                'nama_usaha' => strtoupper($request->nama_usaha),
                'lokasi_usaha' => strtoupper($request->lokasi_usaha),
            ];

            DB::transaction(function () use ($kode_usaha, $data, $data2) {
                DB::table('rsc_bu_pertanian')->where('usaha_kode', $kode_usaha)->update($data);
                DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->update($data2);
            });

            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function rsc_pertanian_biaya(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_pertanian_rsc($kode);

            foreach ($data as $item) {
                $item->kode_usaha = $request->query('kode_usaha');
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $status_rsc;
            }

            $pertanian = DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->get();
            $detail = DB::table('rsc_bu_pertanian')->where('usaha_kode', $kode_usaha)->get();

            return view('rsc.usaha_pertanian.biaya', [
                'data' => $data[0],
                'pertanian' => $pertanian[0],
                'detail' => $detail[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_rsc_pertanian_biaya(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));

            $data = [
                'pengolahan_tanah' => (int)str_replace(["Rp.", " ", "."], "", $request->pengolahan_tanah) ?? 0,
                'bibit' => (int)str_replace(["Rp.", " ", "."], "", $request->bibit) ?? 0,
                'pupuk' => (int)str_replace(["Rp.", " ", "."], "", $request->pupuk) ?? 0,
                'pestisida' => (int)str_replace(["Rp.", " ", "."], "", $request->pestisida) ?? 0,
                'pengairan' => (int)str_replace(["Rp.", " ", "."], "", $request->pengairan) ?? 0,
                'tenaga_kerja' => (int)str_replace(["Rp.", " ", "."], "", $request->tenaga_kerja) ?? 0,
                'panen' => (int)str_replace(["Rp.", " ", "."], "", $request->panen) ?? 0,
                'penggarap' => (int)str_replace(["Rp.", " ", "."], "", $request->penggarap) ?? 0,
                'pajak' => (int)str_replace(["Rp.", " ", "."], "", $request->pajak) ?? 0,
                'iuran_desa' => (int)str_replace(["Rp.", " ", "."], "", $request->iuran_desa) ?? 0,
                'amortisasi' => (int)str_replace(["Rp.", " ", "."], "", $request->amortisasi) ?? 0,
                'pinjaman_bank' => (int)str_replace(["Rp.", " ", "."], "", $request->pinjaman_bank) ?? 0,
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_bu_pertanian')->where('usaha_kode', $kode_usaha)->update($data);
            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil diupdate');
            } else {
                return redirect()->back()->with('error', 'Data gagal diupdate');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function rsc_pertanian_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.kondisi_khusus',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.jangka_waktu',
                )
                ->where('rsc_data_pengajuan.pengajuan_kode', $kode)
                ->get();
            //

            foreach ($data as $value) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_loan.jkwaktu',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $value->kode_pengajuan)->first();
                //
                if ($data_eks) {
                    $value->nama_nasabah = trim($data_eks->fnama);
                    $value->alamat_ktp = trim($data_eks->alamat);
                    $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $value->jangka_waktu = $data_eks->jkwaktu;
                    $value->metode_rps = null;
                    $value->plafon = $data_eks->plafond_awal;
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }

            foreach ($data as $item) {
                $item->kode_usaha = $request->query('kode_usaha');
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $status_rsc;
            }

            if (count($data) <= 0) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            $pertanian = DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->first();
            $detail = DB::table('rsc_bu_pertanian')->where('usaha_kode', $kode_usaha)->first();

            if ($data[0]->kondisi_khusus == 'PERPADIAN') {
                $bu = $this->total_biaya($kode_usaha);
                $hasil_panen = $detail->hasil_panen * $detail->harga;
                $hasil_bersih = $hasil_panen - $bu;
                $ambil = ($hasil_bersih * 70) / 100;
                $jW = $data[0]->jangka_waktu / 6;
                $saving = $data[0]->penentuan_plafon / $jW;
                $sisa_pendapatan = $saving;
                $pendapatan_perbulan = $sisa_pendapatan / 6;
            } else {
                $bu = $this->total_biaya($kode_usaha);
                $hasil_panen = $detail->hasil_panen * $detail->harga;
                $hasil_bersih = $hasil_panen - $bu;
                $ambil = ($hasil_bersih * 70) / 100;
                $jW = $data[0]->jangka_waktu / 6;
                $saving = $data[0]->penentuan_plafon / $jW;
                $sisa_pendapatan = $ambil - $saving;
                $pendapatan_perbulan = $sisa_pendapatan / 6;
            }

            //cek data
            if (is_null($pertanian)) {
                $kalkulasi = [
                    'pendapatan' => null,
                    'pengeluaran' => null,
                    'laba_bersih' => null,
                    'ambil' => null,
                    'pinjaman_bank' => null,
                    'penambahan' => null,
                    'saving' => null,
                    'laba_perbulan' => null,
                ];
            } else {
                $kalkulasi = [
                    'pendapatan' => $hasil_panen,
                    'pengeluaran' => $bu,
                    'laba_bersih' => $hasil_bersih,
                    'ambil' => $ambil,
                    'pinjaman_bank' => $detail->pinjaman_bank,
                    'penambahan' => $pertanian->penambahan,
                    'saving' => $saving,
                    'laba_perbulan' => $pendapatan_perbulan,
                ];

                if ($kalkulasi['pendapatan'] !== $pertanian->pendapatan || $kalkulasi['laba_perbulan'] !== $pertanian->laba_perbulan) {
                    $dt = [
                        'pendapatan' => (int)$kalkulasi['pendapatan'] ?? 0,
                        'pengeluaran' => (int)$kalkulasi['pengeluaran'] ?? 0,
                        'penambahan' => (int)$kalkulasi['penambahan'] ?? 0,
                        'laba_bersih' => (int)$kalkulasi['laba_bersih'] ?? 0,
                        'laba_perbulan' => (int)$kalkulasi['laba_perbulan'] ?? 0,
                    ];

                    DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->update($dt);
                }
            }
            return view('rsc.usaha_pertanian.keuangan', [
                'data' => $data[0],
                'pertanian' => $pertanian,
                'kalkulasi' => $kalkulasi,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_rsc_pertanian_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $data = [
                'pendapatan' => (int)str_replace(["Rp.", " ", "."], "", $request->hasil_panen) ?? 0,
                'pengeluaran' => (int)str_replace(["Rp.", " ", "."], "", $request->total_biaya) ?? 0,
                'penambahan' => (int)str_replace(["Rp.", " ", "."], "", $request->penambahan) ?? 0,
                'laba_bersih' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_bersih) ?? 0,
                'laba_perbulan' => (int)str_replace(["Rp.", " ", "."], "", $request->laba_perbulan) ?? 0,
            ];
            $update = DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->update($data);

            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function delete_rsc_pertanian(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $au_pertanian = DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->get();
            $bu_pertanian = DB::table('rsc_bu_pertanian')->where('usaha_kode', $kode_usaha)->get();

            if (count($au_pertanian) > 0) {
                DB::table('rsc_au_pertanian')->where('kode_usaha', $kode_usaha)->delete();
            }

            if (count($bu_pertanian) > 0) {
                DB::table('rsc_bu_pertanian')->where('usaha_kode', $kode_usaha)->delete();
            }
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }



    //==Privat Function==
    private function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('rsc_au_pertanian')->where('kode_usaha', $acak)->exists()) {
                return $acak;
            }
        }
        return null;
    }

    private static function total_biaya($data)
    {
        $bu = DB::table('rsc_bu_pertanian')->where('usaha_kode', $data)->first();
        if ($bu) {
            $total = array_sum(array_slice([
                (int)$bu->pengolahan_tanah,
                (int)$bu->bibit,
                (int)$bu->pupuk,
                (int)$bu->pestisida,
                (int)$bu->pengairan,
                (int)$bu->tenaga_kerja,
                (int)$bu->panen,
                (int)$bu->penggarap,
                (int)$bu->pajak,
                (int)$bu->iuran_desa,
                (int)$bu->amortisasi,
                (int)$bu->pinjaman_bank
            ], 0, 13));
        } else {
            return 0;
        }
        return $total;
    }
    //==Privat Function==
}
