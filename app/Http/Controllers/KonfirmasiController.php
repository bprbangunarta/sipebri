<?php

namespace App\Http\Controllers;

use Throwable;
use Carbon\Carbon;
use App\Models\Data;
use App\Models\Midle;
use App\Models\Agunan;
use App\Models\Produk;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class KonfirmasiController extends Controller
{
    public function index(Request $request)
    {
        $nasabah = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($nasabah);
            $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
            $cek = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->get();
            $konfirmasi = DB::table('v_validasi_pengajuan')
                ->where('kode_pengajuan', $enc)->get();
            $cek[0]->kd_pengajuan = $nasabah;

            //Cek agunan
            $agunan = DB::table('data_jaminan')
                ->where('pengajuan_kode', '=', $enc)->first();

            if (is_null($agunan)) {
                $konfirmasi[0]->agunan = "0";
            } else {
                $konfirmasi[0]->agunan = "1";
            }

            $dt = Midle::analisa_usaha($enc);
            $cek[0]->plafon = $dt[0]->plafon;
            $cek[0]->jangka_waktu = $dt[0]->jangka_waktu;

            //validasi Produk KTA
            if ($dt[0]->produk_kode == "KTA") {
                $konfirmasi[0]->agunan = "1";
            }

            return view('pengajuan.data-konfirmasi', [
                'data' => $cek[0],
                'konfirmasi' => $konfirmasi[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function konfirmasi(Request $request)
    {
        $nasabah = $request->query('konfirmasi');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($nasabah);
            $cek = [
                'nasabah' => $request->nasabah,
                'pendamping' => $request->pendamping,
                'pengajuan' => $request->pengajuan,
                'survei' => $request->survei,
            ];

            //Cek data apakah sudah ceklis semua apa belum
            foreach ($cek as $key => $value) {
                if ($value == "0") {
                    return redirect()->back()->with('error', 'Data harus diisi sesuai dengan ketentuan');
                }
            }

            //Data auth
            $data = [
                'auth_user' => Auth::user()->code_user,
                'status' => 'Minta Otorisasi',
            ];

            //Data Tracking
            $trc = DB::table('data_tracking')->where('pengajuan_kode', $enc)->first();

            if (is_null($trc)) {
                $name = 'TRK';
                $length = 5;
                $kode = Midle::kode_tracking($name, $length);
                $tracking = [
                    'kode_tracking' => $kode,
                    'pengajuan_kode' => $enc,
                    'pemeriksaan_dokumen' => now(),
                ];

                DB::table('data_tracking')->insert($tracking);
            }

            try {
                $nas = Pengajuan::where('kode_pengajuan', $enc)->get();
                Pengajuan::where('id', $nas[0]->id)->update($data);
                return redirect()->route('pengajuan.index')->with('success', 'Status telah diperbaharui');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Ada data yang tidak terisi');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function otorisasi(Request $request)
    {
        $nasabah = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($nasabah);

            $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
            $cek = Nasabah::where('kode_nasabah', $pengajuan[0]->nasabah_kode)->get();
            // $otorisasi = DB::table('v_validasi_pengajuan')
            //                 ->where('kode_pengajuan', $enc)->get();

            $otorisasi = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->select('data_pengajuan.otorisasi as otorpengajuan', 'data_nasabah.otorisasi as otornasabah', 'data_pendamping.otorisasi as otorpendamping', 'data_survei.otorisasi as otorsurvei')
                ->where('kode_pengajuan', '=', $enc)->get();

            //Cek data agunan apakah sudah otorisasi
            $agunan = DB::table('data_jaminan')->select('otorisasi')->where('pengajuan_kode', '=', $enc)->get();
            if (count($agunan) == 0) {
                $otor = 'N';
            } else {
                foreach ($agunan as $value) {
                    // $otor["otorisasi"] = $value;
                    $otor = 'A';
                    if ($value->otorisasi === "N") {
                        $otor = "N";
                        break; // Keluar dari loop jika sudah ditemukan "N"
                    }
                }
            }

            $otorisasi[0]->otoragunan = $otor;

            $cek[0]->kd_pengajuan = $nasabah;
            $dt = Midle::analisa_usaha($enc);
            $cek[0]->plafon = $dt[0]->plafon;
            $cek[0]->jangka_waktu = $dt[0]->jangka_waktu;

            //Validasi Produk KTA
            if ($dt[0]->produk_kode == "KTA") {
                $otorisasi[0]->otoragunan = "A";
            }

            return view('pengajuan.data-otorisasi', [
                'data' => $cek[0],
                'otorisasi' => $otorisasi[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function validasiotor(Request $request)
    {
        $nasabah = $request->query('validasiotor');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($nasabah);
            $data = Pengajuan::where('kode_pengajuan', $enc)->get();

            $cek = [
                'nasabah' => $request->nasabah,
                'pendamping' => $request->pendamping,
                'pengajuan' => $request->pengajuan,
                'survei' => $request->survei,
            ];

            //Cek data agunan apakah sudah otorisasi
            $agunan = DB::table('data_jaminan')->select('otorisasi')->where('pengajuan_kode', '=', $data[0]->kode_pengajuan)->get();
            foreach ($agunan as $value) {
                if ($value->otorisasi == "N") {
                    return redirect()->route('pengajuan.agunan', ['nasabah' => $nasabah])->with('error', 'Data agunan ada yang belum diotorisasi');
                }
            }

            $data = [
                'auth_user' => Auth::user()->code_user,
                'status' => 'Sudah Otorisasi',
                'tracking' => 'Penjadwalan',
            ];

            //Cek data apakah sudah ceklis semua apa belum
            foreach ($cek as $value) {
                if ($value == "N") {
                    return redirect()->back()->with('error', 'Ada data yang belum diotorisasi');
                }
            }

            try {
                $nas = Pengajuan::where('kode_pengajuan', $enc)->get();
                Pengajuan::where('id', $nas[0]->id)->update($data);
                return redirect()->route('otor.pengajuan')->with('success', 'Status telah diperbaharui');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Ada data yang tidak terisi');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function otornasabah(Request $request)
    {
        $req = $request->query('otorisasi');
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);

            $data = [
                'otorisasi' => 'A',
                'auth_user' => Auth::user()->code_user,
            ];

            try {
                $nasabah = Nasabah::where('kode_nasabah', $enc)->get();
                Nasabah::where('id', $nasabah[0]->id)->update($data);
                return redirect()->route('pendamping.edit', ['nasabah' => $request->kode_pengajuan])->with('success', 'Data Nasabah berhasil diotorisasi');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Data Nasabah gagal diotorisasi');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function otorpendamping(Request $request)
    {
        $req = $request->query('otorisasi');
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);

            $data = [
                'otorisasi' => 'A',
                'auth_user' => Auth::user()->code_user,
            ];
            // dd($enc);
            try {
                $pendamping = Pendamping::where('pengajuan_kode', $enc)->get();
                Pendamping::where('id', $pendamping[0]->id)->update($data);
                return redirect()->route('pengajuan.edit', ['nasabah' => $request->kode_pengajuan])->with('success', 'Data Pendamping berhasil diotorisasi');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Data Pendamping gagal diotorisasi');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function otorpengajuan(Request $request)
    {
        $req = $request->query('otorisasi');
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);

            $data = [
                'otorisasi' => "A",
                'auth_user' => Auth::user()->code_user,
            ];

            try {
                $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
                Pengajuan::where('id', $pengajuan[0]->id)->update($data);
                return redirect()->route('pengajuan.agunan', ['nasabah' => $request->kode_pengajuan])->with('success', 'Data Pengajuan berhasil diotorisasi');
            } catch (Throwable $th) {
                return redirect()->back()->with('error', 'Data Pengajuan gagal diotorisasi');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function otorsurvei(Request $request)
    {
        $req = $request->query('otorisasi');
        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);

            $data = [
                'otorisasi' => 'A',
                'auth_user' => Auth::user()->code_user,
            ];

            try {
                $survei = Survei::where('pengajuan_kode', $enc)->get();
                Survei::where('id', $survei[0]->id)->update($data);
                return redirect()->route('pengajuan.otorisasi', ['nasabah' => $request->kode_pengajuan])->with('success', 'Data Survei berhasil diotorisasi');
            } catch (\Throwable $th) {
                return redirect()->back()->with('error', 'Data Survei gagal diotorisasi');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }


    public function dokumen_nasabah(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);


            return view('staff.analisa.konfirmasi.dokumen', [
                'data' => $cek[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function konfirmasi_analisa(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);


            return view('staff.analisa.konfirmasi.analisa', [
                'data' => $cek[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function ubah_analisa(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $collateral = DB::table('a5c_collateral')->where('pengajuan_kode', $enc)->first();
            if (is_null($collateral)) {
                return redirect()->back()->with('error', 'Data Collateral tidak boleh kosong');
            }

            //By pas jika produk KTA
            $pengajuan = DB::table('data_pengajuan')->where('kode_pengajuan', $enc)->first();

            if ($pengajuan->produk_kode == 'KTA') {
                $name = 'ADM';
                $length = 5;
                $kode = Midle::kodeacak_adm($name, $length);
                $data = [
                    'kode_analisa' => $kode,
                    'pengajuan_kode' => $enc,
                    'administrasi' => 0,
                    'provisi' =>  0,
                    'materai' => 0,
                    'asuransi_jiwa_menurun1' =>  0,
                    'asuransi_jiwa_menurun2' =>  0,
                    'asuransi_jiwa_menurun3' =>  0,
                    'asuransi_jiwa_tetap1' =>  0,
                    'asuransi_jiwa_tetap2' =>  0,
                    'asuransi_jiwa' =>  0,
                    'asuransi_kendaraan_motor' =>  0,
                    'transaksi_kredit' =>  0,
                    'proses_shm' => 0,
                    'polis_materai' =>  0,
                    'pajak_stnk' =>  0,
                    'proses_apht' =>  0,
                    'lainnya' =>  0,
                    'input_user' => Auth::user()->code_user,
                    'created_at' => now(),
                ];

                $data3 = ['tracking' => 'Persetujuan Komite'];
                $data2 = ['analisa_kredit' => now()];

                $adm = DB::table('a_administrasi')->where('pengajuan_kode', $enc)->first();
                if (is_null($adm)) {
                    DB::transaction(function () use ($enc, $data, $data3, $data2) {
                        DB::table('data_tracking')->where('pengajuan_kode', $enc)->update($data2);
                        DB::table('a_administrasi')->insert($data);
                        Pengajuan::where('kode_pengajuan', $enc)->update($data3);
                    });
                }
            }


            $data = ['tracking' => 'Persetujuan Komite'];
            $data2 = ['analisa_kredit' => now()];

            DB::transaction(function () use ($enc, $data, $data2) {
                DB::table('data_tracking')->where('pengajuan_kode', $enc)->update($data2);
                Pengajuan::where('kode_pengajuan', $enc)->update($data);
            });

            return redirect()->route('komite.kredit')->with('success', 'Konfirmasi berhasil');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Konfirmasi gagal');
    }

    public function otorkendaraan(Request $request)
    {
        try {
            $data = [
                'otorisasi' => 'A',
                'auth_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];

            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Data Kendaraan berhasil diotorisasi');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Data Kendaraan gagal diotorisasi');
        }
    }

    public function otortanah(Request $request)
    {
        try {
            $data = [
                'otorisasi' => 'A',
                'auth_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];

            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Data Tanah berhasil diotorisasi');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Data Tanah gagal diotorisasi');
        }
    }

    public function otorlain(Request $request)
    {
        try {
            $data = [
                'otorisasi' => 'A',
                'auth_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];

            DB::table('data_jaminan')->where('id', $request->id)->update($data);
            return redirect()->back()->with('success', 'Data Lainnya berhasil diotorisasi');
        } catch (Throwable $e) {
            return redirect()->back()->with('error', 'Data Lainnya gagal diotorisasi');
        }
    }

    public function otor_perjanjian_kredit(Request $request)
    {
        $name = request('keyword');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->join('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')

            ->where('data_spk.otorisasi', 'N')
            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            
            ->select(
                'data_spk.*',
                'data_pengajuan.*',
                'data_notifikasi.*',
                'data_pengajuan.*',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name',
                'data_spk.created_at as tanggal',
                'data_produk.nama_produk'
            )
            ->orderBy('data_spk.created_at', 'desc');


        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }

        return view('otor-pk.index', [
            'data' => $data
        ]);
    }

    public function get_otor_perjanjian_kredit($kode)
    {
        $data = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.produk_kode', 'data_nasabah.nama_nasabah', 'data_nasabah.no_cif', 'data_spk.no_spk')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)->get();
        //

        $produk = Produk::where('kode_produk', $data[0]->produk_kode)->first();

        $count = (int) $produk->no_spk + 1;
        $lengths = 4;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);


        $now = Carbon::now();
        $bulan = $now->month;
        $romawi = Data::romawi($bulan);

        $notif = $kodes . '/' . $data[0]->produk_kode . '/' . $romawi . '/' . $now->year;

        $data[0]->kode_notif = $notif;

        $data[0]->kode_produk = $data[0]->produk_kode;


        return response()->json($data[0]);
    }
    public function simpan_otor_perjanjian_kredit(Request $request)
    {
        try {
            $cek = DB::table('data_spk')->where('pengajuan_kode', $request->kode_pengajuan)->get();
            if (count($cek) != 0) {
                $user = Auth::user()->code_user;
                $data = [
                    'otorisasi' => 'A',
                    'auth_user' => $user,
                    'updated_at' => now(),
                ];
                DB::table('data_spk')->where('pengajuan_kode', $request->kode_pengajuan)->update($data);
            }

            return redirect()->back()->with('success', 'Berhasil Otorisasi data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal Otorisasi data');
        }
    }
}
