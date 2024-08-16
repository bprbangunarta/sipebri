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
                ->leftJoin('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->select(
                    'data_pendamping.no_identitas as no_identitas_p',
                    'data_pendamping.nama_pendamping',
                    'data_pendamping.tempat_lahir as tempat_lahir_p',
                    'data_pendamping.tanggal_lahir as tanggal_lahir_p',
                    'data_pendamping.no_hp as no_telp_p',
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kasi_kode',
                    'data_survei.surveyor_kode'
                )
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
        $name = request('keyword');
        $user = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')

            ->where('data_pengajuan.on_current', 0)
            ->where('data_pengajuan.status', '=', 'Disetujui')
            // ->whereNull('data_notifikasi.no_notifikasi')

            ->where(function ($query) use ($user) {
                $query->where('data_survei.surveyor_kode', '=', $user)
                    ->orWhere('data_survei.kasi_kode', '=', $user)
                    ->orWhere('data_survei.kabag_kode', '=', $user)
                    ->orWhere('data_survei.direksi_kode', '=', $user);
            })

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_notifikasi.*',
                'data_pengajuan.*',
                'data_tracking.keputusan_komite as tanggal',
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
                'data_produk.nama_produk',
            )
            ->orderBy('data_tracking.keputusan_komite', 'desc');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
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
                ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')
                ->leftJoin('v_users', 'data_notifikasi.input_user', '=', 'v_users.code_user')
                ->leftJoin('a_memorandum', 'data_pengajuan.kode_pengajuan', '=', 'a_memorandum.pengajuan_kode')
                ->leftJoin('bi_sektor_ekonomi', 'a_memorandum.bi_sek_ekonomi_kode', '=', 'bi_sektor_ekonomi.sandi')
                ->leftJoin('a_administrasi', 'data_pengajuan.kode_pengajuan', '=', 'a_administrasi.pengajuan_kode')
                ->leftJoin('bi_penggunaan_debitur', 'a_memorandum.bi_penggunaan_kode', '=', 'bi_penggunaan_debitur.sandi')
                ->leftJoin('data_produk', 'data_pengajuan.produk_kode', '=', 'data_produk.kode_produk')
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->select(
                    'data_pengajuan.*',
                    'data_pengajuan.jangka_waktu as jwt',
                    'data_nasabah.*',
                    'data_notifikasi.*',
                    'data_survei.*',
                    'a_administrasi.*',
                    'a_memorandum.*',
                    'a_memorandum.jangka_waktu as jw',
                    'bi_penggunaan_debitur.keterangan as penggunaan_debitur',
                    'data_produk.kode_produk',
                    'data_produk.nama_produk',
                    'data_notifikasi.created_at as tgl_notifikasi',
                    'data_notifikasi.created_at as tgl_asli',
                    'data_notifikasi.updated_at as tgl_update',
                    'v_users.nama_user as nama_user_notif',
                    'v_users.code_user as code_user_notif',
                    'bi_sektor_ekonomi.sandi as sandi_sektor_ekonomi',
                    'bi_sektor_ekonomi.keterangan as keterangan_sektor_ekonomi',
                )->first();
            //

            if ($cek->produk_kode == 'KTA') {
                $hari = $cek->tgl_notifikasi;
                $cek->tgl_notifikasi = Carbon::parse($hari)->translatedFormat('d F Y');

                //QRCode 
                $qr = Midle::get_qrcode($enc, 'Notifikasi Disetujui', $cek->code_user_notif);
                // dd($cek);
                return view('cetak-berkas.notifikasi-kredit.kta', [
                    'data' => $cek,
                    'qr' => $qr,
                ]);
            } else if ($cek->produk_kode == 'KBT' && ($cek->kondisi_khusus == 'PERLELEAN' || $cek->kondisi_khusus == 'PERPADIAN') || ($cek->jangka_pokok == $cek->jangka_bunga)) {
                $notifikasi_general = Midle::notifikasi_general($enc);
                if ($cek->proses_apht > 0 && $cek->by_fiducia == 0) {
                    $cek->persen_apht = ($cek->proses_apht / $cek->plafon) * 100;
                    $cek->persen_fiducia = 0.00;
                } elseif ($cek->proses_apht == 0 && $cek->by_fiducia > 0) {
                    $cek->persen_apht = 0.00;
                    $cek->persen_fiducia = ($cek->by_fiducia / $cek->plafon) * 100;
                } elseif ($cek->proses_apht > 0 && $cek->by_fiducia > 0) {
                    $cek->persen_fiducia = 0.00;
                    $cek->persen_apht = 0.00;
                } elseif ($cek->proses_apht == 0 && $cek->by_fiducia == 0) {
                    $cek->persen_fiducia = 0.00;
                    $cek->persen_apht = 0.00;
                }

                $hari = Carbon::now();

                if (is_null($cek->tgl_update)) {
                    $cek->tgl_notifikasi_hari_ini = Carbon::parse($cek->tgl_notifikasi)->translatedFormat('d F Y');
                } else {
                    $cek->tgl_notifikasi_hari_ini = Carbon::parse($cek->tgl_update)->translatedFormat('d F Y');
                }

                $cek_jaminan = (object)Midle::cek_jaminan($enc);
                $cek->count_jaminan = count($notifikasi_general);

                $cek->biaya_kredit = (float)$cek->b_provisi + (float)$cek->b_admin + (float)$cek->persen_fiducia;

                //Narasi Angsuran Graceperiode
                $cek->jw = ($cek->jangka_waktu - $cek->grace_period) / $cek->jangka_pokok;
                $cek->awal_angsuran = $cek->jangka_pokok + $cek->grace_period;

                //QRCode 
                $qr = Midle::get_qrcode($enc, 'Notifikasi Disetujui', $cek->code_user_notif);
                // dd($cek);
                return view('cetak-berkas.notifikasi-kredit.kbt-perlelean', [
                    'data' => $cek,
                    'agunan' => $notifikasi_general,
                    'jaminan' => $cek_jaminan,
                    'qr' => $qr,
                ]);
            } else {
                $notifikasi_general = Midle::notifikasi_general($enc);
                if ($cek->proses_apht > 0 && $cek->by_fiducia == 0) {
                    $cek->persen_apht = ($cek->proses_apht / $cek->plafon) * 100;
                    $cek->persen_fiducia = 0.00;
                } elseif ($cek->proses_apht == 0 && $cek->by_fiducia > 0) {
                    $cek->persen_apht = 0.00;
                    $cek->persen_fiducia = ($cek->by_fiducia / $cek->plafon) * 100;
                } elseif ($cek->proses_apht > 0 && $cek->by_fiducia > 0) {
                    $cek->persen_fiducia = 0.00;
                    $cek->persen_apht = 0.00;
                } elseif ($cek->proses_apht == 0 && $cek->by_fiducia == 0) {
                    $cek->persen_fiducia = 0.00;
                    $cek->persen_apht = 0.00;
                }

                $hari = Carbon::now();

                if (is_null($cek->tgl_update)) {
                    $cek->tgl_notifikasi_hari_ini = Carbon::parse($cek->tgl_notifikasi)->translatedFormat('d F Y');
                } else {
                    $cek->tgl_notifikasi_hari_ini = Carbon::parse($cek->tgl_update)->translatedFormat('d F Y');
                }

                $cek_jaminan = (object)Midle::cek_jaminan($enc);
                $cek->count_jaminan = count($notifikasi_general);

                $cek->biaya_kredit = (float)$cek->b_provisi + (float)$cek->b_admin + (float)$cek->persen_fiducia;

                //QRCode 
                $qr = Midle::get_qrcode($enc, 'Notifikasi Disetujui', $cek->code_user_notif);

                return view('cetak-berkas.notifikasi-kredit.general', [
                    'data' => $cek,
                    'agunan' => $notifikasi_general,
                    'jaminan' => $cek_jaminan,
                    'qr' => $qr,
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
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.metode_rps',
                'data_produk.nama_produk',
                'data_pengajuan.jangka_waktu',
                'data_nasabah.nama_nasabah'
            )
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

    public function index_update_notifikasi()
    {
        return view('cetak.notifikasi-kredit.index-update-notifikasi', [
            'data' => "data"
        ]);
    }

    public function get_update_notifikasi(Request $request)
    {
        $data_ajax = $request->input('data');

        // $cb = DB::table('data_notifikasi')->where('pengajuan_kode', '00342084')->first();

        $data = DB::table('data_notifikasi')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->select(
                'data_notifikasi.created_at as input',
                'data_notifikasi.updated_at as update',
                'data_notifikasi.no_notifikasi',
                'data_pengajuan.plafon',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode'
            )
            ->where('data_notifikasi.pengajuan_kode', '=', $data_ajax)
            ->get();

        return response()->json($data);
    }

    public function update_update_notifikasi(Request $request)
    {
        $data_ajax = $request->input('data');

        $data = DB::table('data_notifikasi')->where('pengajuan_kode', $data_ajax)->first();

        if (!is_null($data)) {
            DB::table('data_notifikasi')
                ->where('pengajuan_kode', $data_ajax)
                ->update([
                    'updated_at' => now()
                ]);
            //
            $data = DB::table('data_notifikasi')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_notifikasi.pengajuan_kode')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->select(
                    'data_notifikasi.created_at as input',
                    'data_notifikasi.updated_at as update',
                    'data_notifikasi.no_notifikasi',
                    'data_pengajuan.plafon',
                    'data_pengajuan.kode_pengajuan',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode'
                )
                ->where('data_notifikasi.pengajuan_kode', '=', $data_ajax)
                ->get();

            return response()->json(['berhasil', $data]);
        } else {
            return response()->json('gagal');
        }
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
        $name = request('keyword');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')

            ->where('data_pengajuan.status', 'Disetujui')
            ->where('data_pengajuan.on_current', '0')
            ->where('data_survei.kantor_kode', '=', Auth::user()->kantor_kode)
            ->whereColumn('data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->whereNull('data_spk.no_spk')

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
                'data_nasabah.no_cif',
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
                'data_tracking.keputusan_komite as tanggal',
                'data_produk.nama_produk'
            )
            ->orderBy('data_tracking.keputusan_komite', 'desc');

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
        }

        return view('cetak.perjanjian-kredit.index', [
            'data' => $data
        ]);
    }

    public function realisasi_kredit()
    {

        $name = request('name');
        $cek = DB::table('data_spk')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_notifikasi', 'data_pengajuan.kode_pengajuan', 'data_notifikasi.pengajuan_kode')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->where('data_pengajuan.status', 'Disetujui')
            ->where('data_pengajuan.on_current', '1')
            ->where('data_survei.kantor_kode', Auth::user()->kantor_kode)
            ->whereNull('data_tracking.selesai')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
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
                'users.name'
            );

        //Enkripsi kode pengajuan
        $data = $cek->paginate(10);

        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan);
        }

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
                ->where('nomor', $request->nomor)->first();

            if (!is_null($cek)) {
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
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode as wilayah',
                'data_survei.surveyor_kode as surveyor',
                'data_pengajuan.created_at as tanggal',
            )
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')
            ->leftJoin('data_usulan', 'data_pengajuan.kode_pengajuan', '=', 'data_usulan.pengajuan_kode')

            ->whereNotNull('data_tracking.analisa_kredit')
            ->whereNotNull('data_usulan.pengajuan_kode')

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.no_loan', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.username', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })
            ->orderBy('data_pengajuan.created_at', 'desc')
            ->groupBy('data_pengajuan.kode_pengajuan');
        $data = $query->paginate(10);
        if ($data->isNotEmpty()) {
            foreach ($data as $item) {
                $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
            }
        }
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
                    $biaya_perdagangan[] = DB::table('du_perdagangan')->where('usaha_kode', $perdagangan[$i]->kode_usaha)->get();
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
                    $bahan[] = DB::table('bu_bahan_baku_lainnya')->where('usaha_kode', $lain[$i]->kode_usaha)->get();
                    $bu[] = DB::table('bu_lainnya')->where('usaha_kode', $lain[$i]->kode_usaha)->get();
                    $du[] = DB::table('du_lainnya')->where('usaha_kode', $lain[$i]->kode_usaha)->get();
                }

                for ($i = 0; $i < count($lain); $i++) {
                    $bb = DB::table('total_bahan_baku')->where('kode_usaha', $lain[$i]->kode_usaha)->first();
                    if ($bb !== null && isset($bb->total_bahan_baku)) {
                        $lain[$i]->total_bahan = $bb->total_bahan_baku;
                        $lain[$i]->total_pengeluaran = $lain[$i]->total_bahan + $lain[$i]->pengeluaran;
                    } else {
                        $lain[$i]->total_bahan = 0;
                        $lain[$i]->total_pengeluaran = 0 + $lain[$i]->pengeluaran;
                    }
                }
            }
            // dd($bu, $lain);
            $keuangan = Midle::cetak_dokumen_analisa_keuangan($enc);
            $total_usaha = Midle::cetak_dokumen_analisa_usaha_total($enc);

            if (count($keuangan) != 0) {
                $nominal = [];

                $bu_keuangan = DB::table('bu_keuangan')->where('keuangan_kode', $keuangan[0]->kode_keuangan)->get();
                for ($i = 0; $i < count($bu_keuangan); $i++) {
                    $nominal[$i] = $bu_keuangan[$i]->nominal;
                }

                $jaminan = Midle::cetak_dokumen_jaminan_analisa_keuangan($enc);
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

                // for ($i = 0; $i < 2; $i++) {
                //     $jaminan[$i]->total_taksasi = 0;
                // }
            }

            $character = Midle::cetak_data_analisa5C_character($enc);
            $capacity = Midle::cetak_data_analisa5C_capacity($enc);
            $collateral = Midle::cetak_data_analisa5C_collateral($enc);
            $condition = Midle::cetak_data_analisa5C_condition($enc);
            $kualitatif = Midle::cetak_data_kualitatif($enc);
            $memorandum = Midle::cetak_data_memorandum($enc);

            if (is_null($memorandum)) {
                return redirect()->back()->with('error', 'Memorandum belum diisi');
            }

            $memorandum->biaya_denda = $data[0]->b_denda ?? 0;
            $swot = Midle::cetak_data_swot($enc);
            $kebutuhan_dana = DB::table('a_kebutuhan_dana')->where('pengajuan_kode', $enc)->first();
            if (is_null($kebutuhan_dana)) {
                return redirect()->back()->with('error', 'Memorandum Kebutuhan Dana belum diisi');
            }

            //QR
            $qr = Midle::get_qrcode($enc, 'Analisa Kredit', $data[0]->input_user_survei);

            //Asuransi Kendaraan dan Jiwa
            $adm = DB::table('a_administrasi')->where('pengajuan_kode', $enc)->first();
            if (is_null($adm)) {
                $adm = (object) [
                    'asuransi_jiwa_menurun2' => 0,
                    'asuransi_kendaraan_motor' => 0,
                ];
            }
            // dd($data[0]);
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
                'total_usaha' => $total_usaha,
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
                'qr' => $qr,
                'adm' => $adm,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function data_penolakan_kredit(Request $request)
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_survei.kantor_kode as wilayah',
                'data_survei.surveyor_kode as surveyor',
                'data_pengajuan.created_at as tanggal',
                'data_penolakan.no_penolakan',
            )
            ->join('data_penolakan', 'data_penolakan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.no_loan', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.username', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);
        if ($data->isNotEmpty()) {
            foreach ($data as $item) {
                $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
            }
        }

        return view('cetak.penolakan-kredit.index', [
            'data' => $data,
        ]);
    }

    public function cetak_penolakan_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->join('data_penolakan', 'data_penolakan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->join('data_alasan_penolakan', 'data_alasan_penolakan.id', '=', 'data_penolakan.alasan_id')
                ->select(
                    'data_pengajuan.*',
                    'data_nasabah.*',
                    'data_penolakan.no_penolakan',
                    'data_penolakan.created_at as tgl_tolak',
                    'data_penolakan.keterangan as alasan_internal',
                    'data_tracking.*',
                    'data_penolakan.input_user as nama_user_penolak',
                    'data_alasan_penolakan.alasan'
                )
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();
            //
            $kabag = DB::table('v_users')->where('role_name', 'Kabag Analis')->get();
            $data[0]->kabag_analis = $kabag[0]->nama_user;

            $qr = Midle::get_qrcode($enc, 'Penolakan Kredit', $kabag[0]->code_user);
            return view('cetak.penolakan-kredit.cetak', [
                'data' => $data[0],
                'qr' => $qr,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
    public function cetak_cover_amplop(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->join('data_penolakan', 'data_penolakan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->join('data_alasan_penolakan', 'data_alasan_penolakan.id', '=', 'data_penolakan.alasan_id')
                ->select(
                    'data_penolakan.no_penolakan',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_telp',
                )
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->get();
            //
            $kabag = DB::table('v_users')->where('role_name', 'Kabag Analis')->get();
            $data[0]->kabag_analis = $kabag[0]->nama_user;

            return view('cetak.penolakan-kredit.amplop', [
                'data' => $data[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function persetujuan_kredit()
    {
        $keyword = request('keyword');
        $query = DB::table('data_pengajuan')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_notifikasi.no_notifikasi',
                'data_pengajuan.created_at as tanggal',
                'data_survei.kantor_kode as wilayah',
                'data_survei.surveyor_kode as surveyor',
            )
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')

            ->where('data_pengajuan.status', 'Disetujui')

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.no_loan', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('users.username', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->orderBy('data_tracking.keputusan_komite', 'desc');
        $data = $query->paginate(10);
        if ($data->isNotEmpty()) {
            foreach ($data as $item) {
                $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
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
                ->leftJoin('a_memorandum', 'data_pengajuan.kode_pengajuan', '=', 'a_memorandum.pengajuan_kode')
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->select(
                    'data_pengajuan.*',
                    'data_nasabah.*',
                    'a_administrasi.administrasi as biaya_admin',
                    'a_administrasi.provisi as biaya_provisi',
                    'a_administrasi.proses_apht as biaya_apht',
                    'a_memorandum.by_fiducia as biaya_fiducia',
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
            $usulan = DB::table('data_usulan')
                ->leftJoin('v_users', 'v_users.code_user', '=', 'data_usulan.input_user')
                ->select(
                    'data_usulan.*',
                    'v_users.nama_user',
                )
                ->where('pengajuan_kode', $enc)->get();
            if (count($usulan) != 0) {
                $data = [];
                $rc = [];
                for ($i = 0; $i < count($usulan); $i++) {
                    $data[] = $usulan[$i];
                    $rc[] = $usulan[$i]->rc;

                    //QRCode 
                    $usulan[$i]->qr = Midle::get_qrcode($enc, 'Perjanjian Kredit', $usulan[$i]->input_user);
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

            // dd($cek);
            return view('cetak.persetujuan-kredit.cetak-persetujuan-kredit', [
                'data' => $cek,
                'usulan' => $usulan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetak_otor_perjanjian_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('a_administrasi', 'data_pengajuan.kode_pengajuan', '=', 'a_administrasi.pengajuan_kode')
                ->join('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
                ->leftJoin('data_pekerjaan', 'data_nasabah.pekerjaan_kode', '=', 'data_pekerjaan.kode_pekerjaan')
                ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
                ->leftJoin('a_memorandum', 'data_pengajuan.kode_pengajuan', '=', 'a_memorandum.pengajuan_kode')
                ->leftJoin('bi_penggunaan_debitur', 'a_memorandum.bi_penggunaan_kode', '=', 'bi_penggunaan_debitur.sandi')
                ->leftJoin('data_resort', 'data_resort.kode_resort', '=', 'data_pengajuan.resort_kode')
                ->where('data_pengajuan.kode_pengajuan', $enc)
                // ->where('data_pengajuan.on_current', 0)
                ->select(
                    'data_pengajuan.*',
                    'data_pengajuan.jangka_waktu as jwt',
                    'data_pengajuan.created_at as tgl_pengajuan',
                    'data_nasabah.*',
                    'data_spk.*',
                    'data_spk.updated_at as update_spk',
                    'data_pekerjaan.*',
                    'a_memorandum.*',
                    'data_tracking.keputusan_komite as keputusan_komite',
                    'a_administrasi.*',
                    'bi_penggunaan_debitur.keterangan as penggunaan_debitur',
                    'data_pendamping.status as status_pendamping',
                    'data_pendamping.no_identitas as no_identitas_pendamping',
                    'data_pendamping.nama_pendamping',
                    'a_administrasi.administrasi as biaya_admin',
                    'data_resort.kode_resort',
                    'data_resort.nama_resort',

                    // DB::raw("DATE_FORMAT(CURDATE(), '%Y%m%d') as tgl_mulai"),
                    // DB::raw("DATE_FORMAT(COALESCE(data_spk.updated_at, CURDATE()) + INTERVAL data_pengajuan.jangka_bunga MONTH), '%Y%m%d') as tgl_bayar"),
                    // DB::raw("DATE_FORMAT((CURDATE() + INTERVAL data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir")

                    DB::raw("DATE_FORMAT(COALESCE(data_spk.updated_at, CURDATE()), '%Y%m%d') as tgl_mulai"),
                    DB::raw("DATE_FORMAT((COALESCE(data_spk.updated_at, CURDATE()) + INTERVAL data_pengajuan.jangka_bunga MONTH), '%Y%m%d') as tgl_bayar"),
                    DB::raw("DATE_FORMAT((COALESCE(data_spk.updated_at, CURDATE()) + INTERVAL data_pengajuan.jangka_pokok MONTH), '%Y%m%d') as tgl_bayar_pokok"),
                    DB::raw("DATE_FORMAT((COALESCE(data_spk.updated_at, CURDATE()) + INTERVAL data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir")
                )->first();
            //

            //Tanggal Terbit PK
            $tgl_update = $cek->update_spk;
            $carbonUpdatedAt = Carbon::parse($tgl_update);
            if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                $tgtDate = Carbon::now();
            } else {
                $tgtDate = $carbonUpdatedAt;
            }
            $cek->tgl_bln_thn = $tgtDate->isoformat('D MMMM Y');

            //Tanggal Bayar
            $tgl_update = $cek->update_spk;
            $carbonUpdatedAt = Carbon::parse($tgl_update);
            if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                $targetDate = Carbon::parse($cek->tgl_bayar);
            } else {
                $targetDate = Carbon::parse($cek->tgl_bayar);
            }

            $jth_tempo = Carbon::parse($cek->tgl_bayar);
            $cek->tgl_bln_thn_tempo = $jth_tempo->isoformat('D MMMM Y');
            $cek->tgl_jth = $jth_tempo->isoFormat('D');

            $tgl_pengajuan = Carbon::parse($cek->tgl_pengajuan);
            $cek->tgl_pengajuan = $tgl_pengajuan->isoformat('D MMMM Y');
            $cek->hari = Carbon::now()->isoFormat('dddd');
            $keputusan_komite = Carbon::parse($cek->keputusan_komite);
            $cek->keputusan_komite = $keputusan_komite->isoformat('D MMMM Y');

            //Tanggal Jatuh Tempo
            $tgl_update = $cek->update_spk;
            $carbonUpdatedAt = Carbon::parse($tgl_update);
            if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                $targetDt = Carbon::parse($cek->tgl_akhir);
            } else {
                $targetDt = Carbon::parse($cek->tgl_akhir);
            }

            $cek->tgl_jth_tmp = $targetDt->isoFormat('D MMMM Y');

            // $bln_jth_tmp = $tenMonthsLater->isoFormat('MMMM');
            // $cek->bln_jth_tmp = $bln_jth_tmp;

            $jaminan = Midle::notifikasi_general($enc);
            $cek_jaminan = (object)Midle::cek_jaminan($enc);

            if (is_null($cek->provisi)) {
                $cek->provisi = 0.00;
            }

            if (is_null($cek->administrasi)) {
                $cek->administrasi = 0.00;
            }

            $cek->b_denda = number_format((float)$cek->b_denda, 2, ',', '');


            // Cek Status Balik Nama Jaminan
            $data_balik_nama = [];
            $index = 1;
            foreach ($jaminan as $item) {
                if ($item->status_bl_nama == "YES") {
                    $data_balik_nama[] = (object)[
                        'nomor' => $index,
                        'jenis' => $item->jenis_bl_nama,
                        'atas_nama' => $item->an_bl_nama,
                    ];
                    $index++;
                }
            }

            //Khusus Reloan 
            if ($cek->kategori == 'RELOAN' && $cek->metode_rps == 'FLAT' && !is_null($cek->grace_period)) {

                $cek->jangka_pokok = $cek->jwt - $cek->grace_period;

                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                } else {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                }

                $targetDate->addMonths($cek->grace_period);
                $cek->tgl_jth_pokok = $targetDate->isoFormat('D MMMM Y');
                $cek->tgl_jth_pokok2 = $targetDate->isoFormat('D');

                $cek->jumlah_bulan = $cek->jwt / $cek->jangka_bunga;
                $cek->tgl_pokok = $cek->jwt / $cek->jangka_bunga;

                return view('cetak.perjanjian-kredit.cetak-pk-reloan', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            }

            if ($cek->produk_kode == 'KBT' && $cek->kondisi_khusus == 'PERLELEAN') {
                $tgl_update = $cek->update_spk;
                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::parse($cek->tgl_akhir);
                } else {
                    $targetDate = Carbon::parse($cek->tgl_akhir);
                }
                $cek->tgl_jth_tmp = $targetDate->isoFormat('D MMMM Y');

                $targetDate->addMonths($cek->jangka_pokok);
                $tenMonthsLater = $targetDate->copy()->addMonths($cek->jwt - $cek->jangka_pokok);
                $cek->tgl_jth = $tenMonthsLater->isoFormat('D');

                // $tenMonthsLater = $targetDate->copy()->addMonths($cek->jwt);
                $bln_jth_tmp = $tenMonthsLater->isoFormat('MMMM');
                $cek->bln_jth_tmp = $bln_jth_tmp;

                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                } else {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                }
                $jth_pokok = $targetDate->copy()->addMonths($cek->grace_period);
                $cek->tgl_jth_pokok = $jth_pokok->isoFormat('D MMMM Y');
                $cek->tgl_jth_pokok2 = $targetDate->isoFormat('D');


                $cek->banyak_bulan = ($cek->jwt - $cek->grace_period) / $cek->jangka_pokok;
                $cek->jumlah_bulan = ($cek->jwt - $cek->grace_period) / $cek->jangka_bunga;
                $cek->tgl_pokok = ($cek->jwt - $cek->grace_period) / $cek->jangka_bunga;

                return view('cetak.perjanjian-kredit.cetak-pk-kbt-lele', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            }

            if ($cek->produk_kode == 'KTA') {
                return view('cetak.perjanjian-kredit.cetak-pk-kta', [
                    'data' => $cek,
                ]);
                //Done
            } elseif ($cek->produk_kode == 'KRU' && $cek->metode_rps == 'FLAT') {
                return view('cetak.perjanjian-kredit.cetak-pk-kru-flat', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                    'data_balik_nama' => $data_balik_nama,
                ]);
            } elseif ($cek->produk_kode == 'KTO') {
                return view('cetak.perjanjian-kredit.cetak-pk-kto', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            } elseif ($cek->produk_kode == 'KPS' || $cek->produk_kode == 'KPJ' || $cek->produk_kode == 'KUP' || $cek->produk_kode == 'KKO') {

                return view('cetak.perjanjian-kredit.cetak-pk-kps-kpj', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            } elseif ($cek->produk_kode == 'KBT' && $cek->metode_rps == 'FLAT') {

                $tgl_update = $cek->update_spk;
                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::parse($cek->tgl_akhir);
                } else {
                    $targetDate = Carbon::parse($cek->tgl_akhir);
                }
                $cek->tgl_jth_tmp = $targetDate->isoFormat('D MMMM Y');

                $targetDate->addMonths($cek->jangka_pokok);
                $tenMonthsLater = $targetDate->copy()->addMonths($cek->jwt - $cek->jangka_pokok);
                $cek->tgl_jth = $tenMonthsLater->isoFormat('D');

                // $tenMonthsLater = $targetDate->copy()->addMonths($cek->jwt);
                $bln_jth_tmp = $tenMonthsLater->isoFormat('MMMM');
                $cek->bln_jth_tmp = $bln_jth_tmp;

                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                } else {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                }

                $cek->tgl_jth_pokok = $targetDate->isoFormat('D MMMM Y');
                $cek->tgl_jth_pokok2 = $targetDate->isoFormat('D');


                $cek->banyak_bulan = $cek->jwt / $cek->jangka_pokok;
                $cek->jumlah_bulan = $cek->jwt / $cek->jangka_bunga;
                $cek->tgl_pokok = $cek->jwt / $cek->jangka_bunga;

                return view('cetak.perjanjian-kredit.cetak-pk-kbt-flat', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            } elseif (($cek->metode_rps == 'EFEKTIF MUSIMAN') || $cek->produk_kode == 'KRU' && $cek->metode_rps == 'EFEKTIF MUSIMAN') {

                $tgl_update = $cek->update_spk;
                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::parse($cek->tgl_akhir);
                } else {
                    $targetDate = Carbon::parse($cek->tgl_akhir);
                }
                $cek->tgl_jth_tmp = $targetDate->isoFormat('D MMMM Y');

                $targetDate->addMonths($cek->jangka_pokok);
                $tenMonthsLater = $targetDate->copy()->addMonths($cek->jwt - $cek->jangka_pokok);
                $cek->tgl_jth = $tenMonthsLater->isoFormat('D');

                // $tenMonthsLater = $targetDate->copy()->addMonths($cek->jwt);
                $bln_jth_tmp = $tenMonthsLater->isoFormat('MMMM');
                $cek->bln_jth_tmp = $bln_jth_tmp;

                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                } else {
                    $targetDate = Carbon::parse($cek->tgl_bayar_pokok);
                }

                $cek->tgl_jth_pokok = $targetDate->isoFormat('D MMMM Y');
                $cek->tgl_jth_pokok2 = $targetDate->isoFormat('D');


                $cek->banyak_bulan = $cek->jwt / $cek->jangka_pokok;
                $cek->jumlah_bulan = $cek->jwt / $cek->jangka_bunga;
                $cek->tgl_pokok = $cek->jwt / $cek->jangka_bunga;

                return view('cetak.perjanjian-kredit.cetak-pk-kru-kbt-musiman', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            } elseif ($cek->produk_kode == 'KPM') {

                $tgl_update = $cek->update_spk;
                $carbonUpdatedAt = Carbon::parse($tgl_update);
                if ($carbonUpdatedAt->equalTo(Carbon::now())) {
                    $targetDate = Carbon::now();
                } else {
                    $targetDate = $carbonUpdatedAt;
                }

                if (is_null($cek->grace_period) || $cek->grace_period == '') {
                    $cek->grace_period = 0;
                }

                $cek->jwt = (int)$cek->jwt - (int)$cek->grace_period;

                $jth_tempo = $targetDate->copy()->addMonths($cek->grace_period + 1);

                $cek->tgl_bln_thn_tempo = $jth_tempo->isoformat('D MMMM Y');
                // dd($cek->jwt);
                return view('cetak.perjanjian-kredit.cetak-pk-pmi', [
                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            } elseif ($cek->produk_kode == 'KRU' && $cek->metode_rps == 'EFEKTIF' || $cek->produk_kode == 'KRS' && $cek->metode_rps == 'EFEKTIF') {
                // dd($cek);
                return view('cetak.perjanjian-kredit.cetak-pk-kru-efektif', [

                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            } else {
                return view('cetak.perjanjian-kredit.cetak-pk-kru-flat', [

                    'data' => $cek,
                    'jaminan' => $jaminan,
                    'agunan' => $cek_jaminan,
                ]);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_catatan_notifikasi_kredit(Request $request)
    {
        try {
            $data = [
                'keterangan' => strtoupper($request->keterangan),
                'rencana_realisasi' => strtoupper($request->rencana_realisasi),
            ];

            DB::table('data_notifikasi')->where('pengajuan_kode', $request->kode_pengajuan)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function get_catatan_notifikasi_kredit($kode)
    {
        $data = DB::table('data_notifikasi')->where('pengajuan_kode', $kode)->get();

        foreach ($data as $item) {
            $item->keterangan = strtoupper($item->keterangan);
            $item->rencana_realisasi = strtoupper($item->rencana_realisasi);
        }
        return response()->json($data);
    }
}
