<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Midle;
use App\Models\Agunan;
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
            if (is_null($agunan)) {
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
            // dd($otorisasi[0]);

            $cek[0]->kd_pengajuan = $nasabah;
            $dt = Midle::analisa_usaha($enc);
            $cek[0]->plafon = $dt[0]->plafon;
            $cek[0]->jangka_waktu = $dt[0]->jangka_waktu;

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
                return redirect()->route('pengajuan.index')->with('success', 'Status telah diperbaharui');
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
                return redirect()->back()->with('success', 'Data Nasabah berhasil diotorisasi');
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
                return redirect()->back()->with('success', 'Data Pendamping berhasil diotorisasi');
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
                return redirect()->back()->with('success', 'Data Pengajuan berhasil diotorisasi');
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
                return redirect()->back()->with('success', 'Data Survei berhasil diotorisasi');
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

            $data = ['tracking' => 'Persetujuan Komite'];

            Pengajuan::where('kode_pengajuan', $enc)->update($data);

            return redirect()->back()->with('success', 'Konfirmasi berhasil');
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
}
