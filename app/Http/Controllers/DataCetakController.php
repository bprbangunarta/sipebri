<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Data;
use NumberFormatter;
use App\Models\Midle;
use App\Models\Produk;
use App\Models\Survei;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
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
        $user = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')
            ->where(function ($query) use ($user) {
                $query->where('data_pengajuan.tracking', '=', 'Selesai')
                    ->where('data_survei.surveyor_kode', '=', $user)
                    ->where('data_pengajuan.status', '=', 'Disetujui')
                    ->where('data_spk.no_spk', '=', null);
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

    public function cetak_notifikasi_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('v_users', 'data_survei.surveyor_kode', '=', 'v_users.code_user')
                ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')
                ->leftJoin('a_memorandum', 'data_pengajuan.kode_pengajuan', '=', 'a_memorandum.pengajuan_kode')
                ->leftJoin('bi_sektor_ekonomi', 'a_memorandum.bi_sek_ekonomi_kode', '=', 'bi_sektor_ekonomi.sandi')
                ->leftJoin('a_administrasi', 'data_pengajuan.kode_pengajuan', '=', 'a_administrasi.pengajuan_kode')
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->select(
                    'data_pengajuan.*',
                    'data_nasabah.*',
                    'data_notifikasi.*',
                    'data_survei.*',
                    'a_administrasi.*',
                    'a_memorandum.*',
                    'v_users.*',
                    'data_notifikasi.created_at as tgl_notifikasi',
                    'bi_sektor_ekonomi.sandi as sandi_sektor_ekonomi',
                    'bi_sektor_ekonomi.keterangan as keterangan_sektor_ekonomi',
                )->first();
            //


            if ($cek->produk_kode == 'KTA') {
                $hari = $cek->tgl_notifikasi;
                $cek->tgl_notifikasi = Carbon::parse($hari)->translatedFormat('d F Y');
                // dd($cek);
                return view('cetak-berkas.notifikasi-kredit.kta', [
                    'data' => $cek,
                ]);
            } else {
                $notifikasi_general = Midle::notifikasi_general($enc);

                if ($cek->proses_apht > 0 && $cek->by_fiducia == 0) {
                    $cek->persen_apht = 0.75;
                    $cek->persen_fiducia = 0.00;
                } elseif ($cek->proses_apht == 0 && $cek->by_fiducia > 0) {
                    $cek->persen_fiducia = 0.75;
                    $cek->persen_apht = 0.00;
                } elseif ($cek->proses_apht == 0 && $cek->by_fiducia == 0) {
                    $cek->persen_fiducia = 0.00;
                    $cek->persen_apht = 0.00;
                }

                $hari = Carbon::now();
                $cek->tgl_notifikasi_hari_ini = Carbon::parse($hari)->translatedFormat('d F Y');
                $cek_jaminan = (object)Midle::cek_jaminan($enc);
                // dd($cek);
                return view('cetak-berkas.notifikasi-kredit.general', [
                    'data' => $cek,
                    'agunan' => $notifikasi_general,
                    'jaminan' => $cek_jaminan,
                ]);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return view('cetak-berkas.notifikasi-kredit.kta');
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
            ->where('data_pengajuan.on_current', '0')
            ->where('data_survei.kantor_kode', '=', Auth::user()->kantor_kode)
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
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->where('data_pengajuan.status', 'Disetujui')
            ->where('data_pengajuan.on_current', '1')
            ->whereNull('data_tracking.selesai')
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_spk.pengajuan_kode')
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
        // dd($data);
        return view('cetak.realisasi-kredit.index', [
            'data' => $data
        ]);
    }

    public function get_realisasi($kode)
    {
        $data = DB::table('data_realisasi')->where('pengajuan_kode', $kode)->first();

        if (is_null($data)) {
            $data = [
                'pengajuan_kode' => null,
                'foto_pemohon' => null,
                'foto_pendamping' => null,
                'catatan' => null,
            ];
        }
        return response()->json($data);
    }

    public function get_foto_realisasi($kode)
    {
        $data = $kode;
        $array_data = explode(",", $data);
        $realisasi = DB::table('data_realisasi')->where('pengajuan_kode', $array_data[0])->first();

        if (is_null($realisasi)) {
            $foto = null;
        } else {
            if ($array_data[1] == 'pemohon') {
                $foto = $realisasi->foto_pemohon ? asset('storage/image/photo_realisasi/' . $realisasi->foto_pemohon) : null;
            } else if ($array_data[1] == 'pendamping') {
                $foto = $realisasi->foto_pendamping ? asset('storage/image/photo_realisasi/' . $realisasi->foto_pendamping) : null;
            }
        }



        return response()->json($foto);
    }

    public function konfirmasi_realisasi(Request $request)
    {
        try {
            $data = ['selesai' => now()];
            $data2 = ['tracking' => 'Selesai'];
            $data3 = ['status' => 'A'];

            DB::transaction(function () use ($request, $data, $data2, $data3) {
                DB::table('data_realisasi')->where('pengajuan_kode', $request->kode_pengajuan)->update($data3);
                DB::table('data_pengajuan')->where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($data);
            });
            return redirect()->back()->with('success', 'Berhasil melakukan konfirmasi');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal melakukan konfirmasi');
        }
    }

    public function simpan_realisasi(Request $request)
    {

        try {
            $cek = $request->validate([
                'foto_pemohon' => 'image|mimes:jpeg,png,jpg|max:5120',
                'foto_pendamping' => 'image|mimes:jpeg,png,jpg|max:5120',
            ]);

            $tanggalSekarang = Carbon::now();
            $tanggal = $tanggalSekarang->format('dmY');

            if ($request->file('foto_pemohon')) {
                if ($request->foto1) {
                    Storage::delete('public/image/photo_realisasi/' . $request->foto1);
                }

                $ekstensi = $cek['foto_pemohon']->getClientOriginalExtension();
                $new1 =  'realisasi' . '_' . $tanggal .  '_' . 'pemohon' . '.' . $ekstensi;
                $cek['foto_pemohon'] = $request->file('foto_pemohon')->storeAs('image/photo_realisasi', $new1, 'public');
                $cek['foto_pemohon'] = $new1;
            } else {
                $realisasi = DB::table('data_realisasi')->where('pengajuan_kode', $request->kode_pengajuan)->first();
                $cek['foto_pemohon'] = $realisasi->foto_pemohon;
            }

            if ($request->file('foto_pendamping')) {
                if ($request->foto2) {
                    Storage::delete('public/image/photo_realisasi/' . $request->foto2);
                }

                $ekstensi = $cek['foto_pendamping']->getClientOriginalExtension();
                $new1 =  'realisasi' . '_' . $tanggal .  '_' . 'pendamping' . '.' . $ekstensi;
                $cek['foto_pendamping'] = $request->file('foto_pendamping')->storeAs('image/photo_realisasi', $new1, 'public');
                $cek['foto_pendamping'] = $new1;
            } else {
                $realisasi = DB::table('data_realisasi')->where('pengajuan_kode', $request->kode_pengajuan)->first();
                $cek['foto_pendamping'] = $realisasi->foto_pendamping;
            }

            $cek['pengajuan_kode'] = $request->kode_pengajuan;
            $cek['input_user'] = Auth::user()->code_user;
            $cek['catatan'] = strtoupper($request->catatan);
            $cek['created_at'] = now();
            // dd($cek);
            $realisasi = DB::table('data_realisasi')->where('pengajuan_kode', $request->kode_pengajuan)->first();
            if (is_null($realisasi)) {
                $ds = ['pencairan_dana' => now()];
                DB::table('data_realisasi')->insert($cek);
                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($ds);
            } else {
                $ds = ['pencairan_dana' => now()];
                DB::table('data_realisasi')->where('pengajuan_kode', $request->kode_pengajuan)->update($cek);
                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($ds);
            }

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
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

            $data3 = [
                'akad_kredit' => now(),
            ];
            $data4 = [
                'tracking' => 'Realisasi',
                // 'on_current' => '1',
            ];
            DB::transaction(function () use ($data, $request, $data2, $data3, $data4) {
                DB::table('data_spk')->insert($data);
                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($data3);
                Produk::where('kode_produk', $request->kode_produk)->update($data2);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data4);
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

    public function analisa_kredit(Request $request)
    {
        $usr = Auth::user()->code_user;
        $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();

        $name = request('name');
        $usr = Auth::user()->code_user;
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->where(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Persetujuan Komite');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Naik Kasi');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Naik Komite I');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Naik Komite II');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.status', 'Disetujui');
            })

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_kantor.kode_kantor',
            );

        $c = $query->get();
        $count = count($c);
        $data = $query->paginate(10);
        $usul1 = "Staff Analis";
        $usul2 = "Kasi Analis";
        $usul3 = "Kabag Analis";
        $usul4 = "Direksi";
        for ($i = 0; $i < $count; $i++) {
            $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
            $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
            $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
            $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
        }

        if ($user->role_name == 'Customer Service') {
            $usul1 = "Customer Service";
            for ($i = 0; $i < $count; $i++) {
                if ($data->isNotEmpty()) {
                    $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
                    $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
                    $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
                    $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
                    $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
                }
            }
        } elseif ($user->role_name == 'Kepala Kantor Kas') {
            $usul1 = "Kepala Kantor Kas";
            for ($i = 0; $i < $count; $i++) {
                if ($data->isNotEmpty()) {
                    $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
                    $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
                    $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
                    $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
                    $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
                }
            }
        }
        // dd($data, $user);
        return view('cetak.analisa-kredit.index', [
            'data' => $data,
        ]);
    }

    public function cetak_analisa_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = Midle::cetak_dokumen_analisa($enc);
            $perdagangan = Midle::cetak_dokumen_analisa_usaha_perdagangan($enc);
            if (count($perdagangan) != 0) {
                for ($i = 0; $i < count($perdagangan); $i++) {
                    $biaya_perdagangan = DB::table('du_perdagangan')->where('usaha_kode', $perdagangan[$i]->kode_usaha)->get();
                }
            } else {
                $biaya_perdagangan = null;
            }

            $pertanian = Midle::cetak_dokumen_analisa_usaha_pertanian($enc);
            if (count($pertanian) != 0) {
                for ($i = 0; $i < count($pertanian); $i++) {
                    $jml = ((int)$pertanian[$i]->laba_bersih * 70) / 100;
                    $pertanian[$i]->saving = $jml;
                }
            }

            $jasa = Midle::cetak_dokumen_analisa_usaha_jasa($enc);
            $lain = Midle::cetak_dokumen_analisa_usaha_lain($enc);
            if (count($lain) != 0) {
                for ($i = 0; $i < count($lain); $i++) {
                    $bahan = DB::table('bu_bahan_baku_lainnya')->where('usaha_kode', $lain[$i]->kode_usaha)->get();
                    $bu = DB::table('bu_lainnya')->where('usaha_kode', $lain[$i]->kode_usaha)->get();
                    $du = DB::table('du_lainnya')->where('usaha_kode', $lain[$i]->kode_usaha)->get();
                }
            }

            $keuangan = Midle::cetak_dokumen_analisa_keuangan($enc);
            if (count($keuangan) != 0) {
                $nominal = [];

                for ($i = 0; $i < count($keuangan); $i++) {
                    $bu_keuangan = DB::table('bu_keuangan')->where('keuangan_kode', $keuangan[$i]->kode_keuangan)->get();
                    $nominal[$i] = $bu_keuangan[$i]->nominal;

                    $jaminan = Midle::cetak_dokumen_jaminan_analisa_keuangan($keuangan[$i]->pengajuan_kode);
                }
                $arr = array_sum($nominal) ?? 0;
                for ($i = 0; $i < count($keuangan); $i++) {
                    $keuangan[$i]->bu_total = $arr ?? 0;
                }

                $jml_jaminan = [];
                for ($i = 0; $i < count($jaminan); $i++) {
                    $jml_jaminan[$i] = $jaminan[$i]->nilai_taksasi;
                }

                $dj = array_sum($jml_jaminan) ?? 0;
                for ($i = 0; $i < count($jaminan); $i++) {
                    $jaminan[$i]->total_taksasi = $dj ?? 0;
                }
            } else {
                $jaminan = (object) [
                    'nilai_taksasi' => 0,
                    'total_taksasi' => 0,
                ];

                for ($i = 0; $i < count($keuangan); $i++) {
                    $keuangan[$i]->total_taksasi = 0;
                }
            }

            $character = Midle::cetak_data_analisa5C_character($enc);
            $capacity = Midle::cetak_data_analisa5C_capacity($enc);
            $collateral = Midle::cetak_data_analisa5C_collateral($enc);
            $condition = Midle::cetak_data_analisa5C_condition($enc);
            $kualitatif = Midle::cetak_data_kualitatif($enc);
            $memorandum = Midle::cetak_data_memorandum($enc);
            $swot = Midle::cetak_data_swot($enc);
            $kebutuhan_dana = DB::table('a_kebutuhan_dana')->where('pengajuan_kode', $enc)->first();

            return view('cetak-berkas.analisa-kredit.index', [
                'data' => $request->query('pengajuan'),
                'cetak' => $data[0],
                'perdagangan' => $perdagangan,
                'pertanian' => $pertanian,
                'jasa' => $jasa,
                'lain' => $lain,
                'bahan' => $bahan ?? null,
                'bu' => $bu ?? null,
                'du' => $du ?? null,
                'keuangan' => $keuangan,
                'bu_keuangan' => $bu_keuangan,
                'jaminan' => $jaminan,
                'character' => $character,
                'capacity' => $capacity,
                'collateral' => $collateral,
                'kualitatif' => $kualitatif,
                'kebutuhan_dana' => $kebutuhan_dana,
                'condition' => $condition,
                'biayaperdagangan' => $biaya_perdagangan,
                'memorandum' => $memorandum,
                'swot' => $swot,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function data_penolakan_kredit(Request $request)
    {
        $usr = Auth::user()->code_user;
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('data_penolakan', 'data_pengajuan.kode_pengajuan', '=', 'data_penolakan.pengajuan_kode')
            // ->where('data_pengajuan.status', '=', 'Ditolak')
            // ->where('data_pengajuan.kode_pengajuan', '=', 'data_penolakan.pengajuan_kode')
            ->where(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.status', '=', 'Ditolak')
                    ->where('data_pengajuan.on_current', '=', '0');
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_kantor.kode_kantor',
            );

        $c = $query->get();
        $count = count($c);
        $data = $query->paginate(10);
        $usul1 = "Staff Analis";
        $usul2 = "Kasi Analis";
        $usul3 = "Kabag Analis";
        $usul4 = "Direksi";
        for ($i = 0; $i < $count; $i++) {
            $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
            $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
            $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
            $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
        }
        // dd($data);
        return view('cetak.penolakan-kredit.penolakan_kredit', [
            'data' => $data,
        ]);
    }

    public function persetujuan_kredit()
    {
        $usr = Auth::user()->code_user;
        $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();

        $name = request('name');
        $usr = Auth::user()->code_user;
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->where('data_pengajuan.status', 'Disetujui')
            ->where('data_pengajuan.on_current', '0')
            ->where(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Persetujuan Komite');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Naik Kasi');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Naik Komite I');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.tracking', 'Naik Komite II');
            })
            ->orWhere(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.on_current', '=', '0')
                    ->where('data_pengajuan.status', 'Disetujui');
            })

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_kantor.kode_kantor',
            );

        $c = $query->get();
        $count = count($c);
        $data = $query->paginate(10);
        $usul1 = "Staff Analis";
        $usul2 = "Kasi Analis";
        $usul3 = "Kabag Analis";
        $usul4 = "Direksi";
        for ($i = 0; $i < $count; $i++) {
            $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
            $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
            $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
            $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
        }

        if ($user->role_name == 'Customer Service') {
            $usul1 = "Customer Service";
            for ($i = 0; $i < $count; $i++) {
                if ($data->isNotEmpty()) {
                    $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
                    $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
                    $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
                    $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
                    $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
                }
            }
        } elseif ($user->role_name == 'Kepala Kantor Kas') {
            $usul1 = "Kepala Kantor Kas";
            for ($i = 0; $i < $count; $i++) {
                if ($data->isNotEmpty()) {
                    $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
                    $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
                    $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
                    $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
                    $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
                }
            }
        }

        return view('cetak.persetujuan-kredit.index', [
            'data' => $data,
        ]);
    }

    public function cetak_persetujuan_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('a_administrasi', 'data_pengajuan.kode_pengajuan', '=', 'a_administrasi.pengajuan_kode')
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->select(
                    'data_pengajuan.*',
                    'data_nasabah.*',
                    'a_administrasi.administrasi as biaya_admin',
                )->first();

            //Taksasi
            $taksasi = DB::table('data_jaminan')->where('pengajuan_kode', $enc)->get();
            if (count($taksasi) != 0) {
                $total = [];
                for ($i = 0; $i < count($taksasi); $i++) {
                    $total[] = $taksasi[$i]->nilai_taksasi;
                }
                $total_taksasi = array_sum($total) ?? 0;
            }
            $cek->total_taksasi = $total_taksasi ?? 0;

            //Data Usulan
            $usulan = DB::table('data_usulan')->where('pengajuan_kode', $enc)->get();
            if (count($usulan) != 0) {
                $data = [];
                $rc = [];
                for ($i = 0; $i < count($usulan); $i++) {
                    $data[] = $usulan[$i];
                    $rc[] = $usulan[$i]->rc;
                }
                // $total_taksasi = array_sum($total) ?? 0;

                //RC
                $rc_akhir = end($rc);
                $data_akhir = end($data);
            }
            $cek->total_taksasi = $total_taksasi ?? 0;
            $cek->rc_akhir = $rc_akhir ?? 0;
            $cek->plafon_usulan = $data_akhir->usulan_plafon ?? 0;
            $cek->sb_usulan = $data_akhir->suku_bunga ?? null;

            // dd($usulan);
            return view('cetak.persetujuan-kredit.cetak-persetujuan-kredit', [
                'data' => $cek,
                'usulan' => $usulan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
