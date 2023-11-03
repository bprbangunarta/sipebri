<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Data;
use App\Models\Survei;
use App\Models\Pengajuan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class DataCetakController extends Controller
{
    public function slik(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah', 'data_nasabah.tempat_lahir', 'data_nasabah.tanggal_lahir', 'data_nasabah.no_telp', 'data_nasabah.alamat_ktp', 'data_survei.kasi_kode', 'data_survei.surveyor_kode')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            //Surveyor dan Kasi
            $kasi = DB::table('v_users')
                ->where('code_user', $data[0]->kasi_kode)
                ->select('nama_user')->get();

            $surveyor = DB::table('v_users')
                ->where('code_user', $data[0]->surveyor_kode)
                ->select('nama_user')->get();

            //Rubah tanggal
            $carbonDate = Carbon::createFromFormat('Ymd', $data[0]->tanggal_lahir);
            $data[0]->tanggal_lahir = $carbonDate->isoformat('D MMMM Y');

            //Ubah format String
            $data[0]->tempat_lahir = ucfirst(strtolower($data[0]->tempat_lahir));

            $data[0]->kasi_kode = $kasi[0]->nama_user;
            $data[0]->surveyor_kode = $surveyor[0]->nama_user;

            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');

            return view('cetak.layouts.slik', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function notifikasi_kredit(Request $request)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')
            ->where(function ($query) {
                $query->where('data_pengajuan.tracking', '=', 'Selesai')
                    ->where('data_pengajuan.status', '=', 'Disetujui');
            })
            ->select(
                'data_notifikasi.*',
                'data_pengajuan.*',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name',
                'data_survei.kantor_kode',
            );

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }

        return view('cetak.notifikasi-kredit.index', [
            'data' => $data
        ]);
    }

    public function get_notifikasi($kode)
    {
        $data = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.nama_nasabah')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)->get();
        //

        $lasts = DB::table('data_notifikasi')->latest('nomor')->first();
        if (is_null($lasts)) {
            $count = 0000;
        } else {
            $count = (int) $lasts->nomor + 1;
        }
        $lengths = 4;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);


        $now = Carbon::now();
        $bulan = $now->month;
        $romawi = Data::romawi($bulan);

        $notif = $kodes . '/' . 'NK' . '/' . 'PBA' . '/' . $romawi . '/' . $now->year;

        $data[0]->kode_notif = $notif;
        $data[0]->nomor = $kodes;

        return response()->json($data[0]);
    }

    public function simpan_notifikasi(Request $request)
    {
        try {
            $cek = DB::table('data_notifikasi')->where('pengajuan_kode', $request->kode_pengajuan)->first();

            if ($cek) {
                return back()->with('error', "Anda Sudah Memiliki Nomor Notifikasi");
            }

            $data = [
                'nomor' => $request->nomor,
                'no_notifikasi' => $request->kode_notifikasi,
                'pengajuan_kode' => $request->kode_pengajuan,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            DB::table('data_notifikasi')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }


    public function perjanjian_kredit(Request $request)
    {
        // $user = DB::table('v_users')->where('code_user', Auth::user()->code_user)->first();
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->where('data_pengajuan.status', 'Disetujui')
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
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
                'users.name'
            );

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }

        return view('cetak.perjanjian-kredit.index', [
            'data' => $data
        ]);
    }

    public function realisasi_kredit()
    {

        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->where('data_pengajuan.status', 'Disetujui')
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_spk.pengajuan_kode')
            ->select('data_spk.*', 'data_pengajuan.*', 'data_notifikasi.*', 'data_pengajuan.*', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2', 'users.name');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }

        return view('cetak.realisasi-kredit.index', [
            'data' => $data
        ]);
    }

    public function get_realisasi($kode)
    {
        $data = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            // ->leftJoin('data_produk', 'data_pengajuan.produk_kode', '=', 'data_produk.kode_produk')
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.produk_kode', 'data_nasabah.nama_nasabah')
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
        $data[0]->nomor = $kodes;

        return response()->json($data[0]);
    }

    public function get_spk($kode)
    {
        $data = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            // ->leftJoin('data_produk', 'data_pengajuan.produk_kode', '=', 'data_produk.kode_produk')
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.produk_kode', 'data_nasabah.nama_nasabah', 'data_nasabah.no_cif')
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
        $data[0]->nomor = $kodes;
        $data[0]->kode_produk = $data[0]->produk_kode;


        return response()->json($data[0]);
    }

    public function simpan_spk(Request $request)
    {
        try {
            $cek = DB::table('data_spk')
                ->where('pengajuan_kode', $request->kode_pengajuan)
                ->orWhere('nomor', $request->nomor)->first();

            if ($cek) {
                return back()->with('error', "Anda Sudah Memiliki Nomor PK");
            }

            $data = [
                'nomor' => $request->nomor,
                'no_spk' => $request->kode_spk,
                'pengajuan_kode' => $request->kode_pengajuan,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            //validasi CIF harus ada
            if (is_null($request->no_cif)) {
                return redirect()->back()->with('error', 'No CIF tidak boleh kosong');
            }

            $data2 = [
                'no_spk' => $request->nomor,
            ];

            DB::transaction(function () use ($data, $request, $data2) {
                DB::table('data_spk')->insert($data);
                Produk::where('kode_produk', $request->kode_produk)->update($data2);
            });


            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function penolakan_kredit(Request $request)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_penolakan', 'data_pengajuan.kode_pengajuan', '=', 'data_penolakan.pengajuan_kode')
            ->where(function ($query) {
                $query->where('data_pengajuan.tracking', '=', 'Selesai')
                    ->where('data_pengajuan.status', '=', 'Ditolak');
            })
            ->select('data_penolakan.*', 'data_pengajuan.*', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2', 'users.name');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }
        // dd($data);
        return view('cetak.penolakan-kredit.index', [
            'data' => $data
        ]);
    }

    public function get_penolakan($kode)
    {
        $data = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.nama_nasabah')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)->get();
        //

        $lasts = DB::table('data_penolakan')->latest('nomor')->first();
        if (is_null($lasts)) {
            $count = 0000;
        } else {
            $count = (int) $lasts->nomor + 1;
        }
        $lengths = 4;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);


        $now = Carbon::now();
        $bulan = $now->month;
        $romawi = Data::romawi($bulan);

        $notif = $kodes . '/' . '03' . '/' . 'KABAG.ANALIS' . '/' . 'PBA' . '/' . $romawi . '/' . $now->year;

        $data[0]->kode_notif = $notif;
        $data[0]->nomor = $kodes;

        return response()->json($data[0]);
    }

    public function simpan_penolakan(Request $request)
    {
        try {
            $cek = DB::table('data_penolakan')->where('pengajuan_kode', $request->kode_pengajuan)->first();

            if ($cek) {
                return back()->with('error', "Anda Sudah Memiliki Nomor Penolakan");
            }

            $data = [
                'nomor' => $request->nomor,
                'no_penolakan' => $request->kode_penolakan,
                'pengajuan_kode' => $request->kode_pengajuan,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            DB::table('data_penolakan')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }
}
