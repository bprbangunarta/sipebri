<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCCetakController extends Controller
{
    public function cetakanalisa_index()
    {
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('rsc_kondisi_usaha', 'rsc_kondisi_usaha.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc') // Perbaikan di sini
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon'
            )
            ->where(function ($query) {
                $query->whereNotIn('rsc_data_pengajuan.status', ['Proses Analisa', 'Proses Survei', 'Penjadwalan', 'Batal RSC']);
            })
            ->where('rsc_data_survei.surveyor_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);


        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.cetak_analisa.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    public function cetakanalisa_kredit_detail(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            $data = DB::table('rsc_data_pengajuan')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', 'data_pengajuan.nasabah_kode')
                ->join('data_jaminan', 'data_jaminan.pengajuan_kode', 'data_pengajuan.kode_pengajuan')
                ->join('a_memorandum', 'a_memorandum.pengajuan_kode', 'data_pengajuan.kode_pengajuan')
                ->join('bi_penggunaan_debitur', 'bi_penggunaan_debitur.sandi', 'a_memorandum.bi_penggunaan_kode')
                ->join('data_spk', 'data_spk.pengajuan_kode', 'data_pengajuan.kode_pengajuan')
                ->join('rsc_data_survei', 'rsc_data_survei.kode_rsc', 'rsc_data_pengajuan.kode_rsc')
                ->join('users', 'users.code_user', 'rsc_data_survei.surveyor_kode')
                ->select(
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.baki_debet',
                    'rsc_data_pengajuan.klasifikasi_kredit',
                    'rsc_data_pengajuan.jml_tgk_pokok',
                    'rsc_data_pengajuan.jml_tgk_bunga',
                    'rsc_data_pengajuan.tunggakan_poko',
                    'rsc_data_pengajuan.tunggakan_bunga',
                    'rsc_data_pengajuan.tunggakan_denda',
                    'rsc_data_pengajuan.total_tunggakan',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.angsuran_pokok',
                    'rsc_data_pengajuan.angsuran_bunga',
                    'rsc_data_pengajuan.total_angsuran',
                    'rsc_data_pengajuan.rc',
                    'rsc_data_pengajuan.updated_at as update_pengajuan',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_identitas',
                    'data_nasabah.no_telp',
                    'data_pengajuan.plafon',
                    'data_jaminan.catatan',
                    'bi_penggunaan_debitur.keterangan',
                    'data_spk.no_spk',
                    'data_spk.updated_at as update_spk',
                    'users.name',
                    DB::raw("DATE_FORMAT((COALESCE(data_spk.updated_at, CURDATE()) + INTERVAL data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir")
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->first();

            //
            $targetDt = Carbon::parse($data->tgl_akhir);
            $data->tgl_jth_tmp = $targetDt->isoFormat('D MMMM Y');

            $up = Carbon::parse($data->update_pengajuan);
            $data->update_pengajuan = $up->isoFormat('D MMMM Y');

            $data->total_angsuran = $data->angsuran_bunga + $data->angsuran_pokok;

            //Kondisi Usaha
            $kondisi_usaha = DB::table('rsc_kondisi_usaha')->where('kode_rsc', $enc_rsc)->first();
            if (is_null($kondisi_usaha)) {
                return redirect()->back()->with('error', 'Kondisi Usaha Masih Kosong.');
            }
            //Kondisi Usaha

            //Kondisi Agunan
            $kondisi_agunan = DB::table('rsc_agunan')->where('kode_rsc', $enc_rsc)->first();
            if (is_null($kondisi_agunan)) {
                return redirect()->back()->with('error', 'Kondisi Agunan Masih Kosong.');
            }
            //Kondisi Agunan

            $usaha = $this->rsc_usaha($enc_rsc);

            //Biaya
            $biaya = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();
            if (is_null($biaya)) {
                return redirect()->back()->with('error', 'Rincian Biaya Masih Kosong.');
            }
            $biaya->total = $biaya->konsumsi + $biaya->kesehatan + $biaya->pendidikan +
                $biaya->gatel + $biaya->jajan_anak + $biaya->sumbangan + $biaya->roko;


            $biaya->total_kewajiban = $biaya->nominal_kewajiban1 + $biaya->nominal_kewajiban2 + $biaya->nominal_kewajiban3;

            $likuidasi = $usaha->total_usaha - ($biaya->total + $biaya->total_kewajiban);
            $biaya->likuidasi = (int) ceil($likuidasi * 70 / 100);
            //Biaya

            //QRCode
            $surveyor = DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->first();
            $qr = $this->get_qrcode($enc_rsc, 'RSC_ANALISA', $surveyor->surveyor_kode);

            return view('rsc.cetak_analisa.cetak_analisa', [
                'data' => $data,
                'kondisi' => $kondisi_usaha,
                'agunan' => $kondisi_agunan,
                'usaha' => $usaha,
                'biaya' => $biaya,
                'qr' => $qr,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetaknotifikasi_index(Request $request)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('rsc_kondisi_usaha', 'rsc_kondisi_usaha.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->join('rsc_notifikasi', 'rsc_notifikasi.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc') // Perbaikan di sini
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
                'rsc_notifikasi.no_notifikasi',
            )
            ->where(function ($query) {
                $query->whereNotIn('rsc_data_pengajuan.status', ['Proses Analisa', 'Proses Survei', 'Penjadwalan', 'Batal RSC']);
            })
            ->where('rsc_data_survei.surveyor_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);


        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.cetak_notifikasi.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    public function cetaknotifikasi_detail(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('rsc_notifikasi')
                ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_notifikasi.kode_rsc')
                ->join('rsc_biaya', 'rsc_biaya.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
                ->join('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->join('bi_penggunaan_debitur', 'bi_penggunaan_debitur.sandi', '=', 'a_memorandum.bi_penggunaan_kode')
                ->join('bi_sektor_ekonomi', 'bi_sektor_ekonomi.sandi', '=', 'a_memorandum.bi_sek_ekonomi_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.status',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.jangka_waktu as jwt',
                    'rsc_data_pengajuan.jangka_bunga',
                    'rsc_data_pengajuan.jangka_pokok',
                    'rsc_data_pengajuan.suku_bunga',
                    'rsc_biaya.administrasi_nominal as administrasi',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.b_denda',
                    'data_produk.nama_produk',
                    'rsc_notifikasi.no_notifikasi',
                    'rsc_notifikasi.input_user',
                    'bi_penggunaan_debitur.keterangan as penggunaan_debitur',
                    'bi_sektor_ekonomi.sandi as sandi_sektor_ekonomi',
                    'bi_sektor_ekonomi.keterangan as keterangan_sektor_ekonomi',
                    'rsc_notifikasi.created_at as created_notif',
                    'rsc_notifikasi.updated_at as updated_notif',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->first();
            //

            if (!is_null($data->updated_notif)) {
                $tgl = $data->updated_notif;
            } else {
                $tgl = $data->created_notif;
            }
            $targetDt = Carbon::parse($tgl);
            $data->tgl_notif = $targetDt->isoFormat('D MMMM Y');

            // Jaminan
            $agunan = Midle::notifikasi_general($data->kode_pengajuan);
            $cek_jaminan = (object)Midle::cek_jaminan($data->kode_pengajuan);
            $data->count_jaminan = count($agunan);

            //Narasi Angsuran Graceperiode
            $data->jw = $data->jwt / $data->jangka_pokok;
            $data->awal_angsuran = $data->jangka_pokok;

            // Biaya Kredit
            if ($data->penentuan_plafon <= 10000000) {
                $biaya_kredit = 2;
            } elseif ($data->penentuan_plafon > 10000000 && $data->penentuan_plafon <= 20000000) {
                $biaya_kredit = 1.5;
            } elseif ($data->penentuan_plafon > 20000000) {
                $biaya_kredit = 1;
            }
            $data->biaya_kredit = $biaya_kredit;

            $qr = $this->get_qrcode($enc_rsc, 'RSC_NOTIFIKASI', $data->input_user);

            return view('rsc.cetak_notifikasi.cetak_notifikasi', [
                'data' => $data,
                'jaminan' => $cek_jaminan,
                'agunan' => $agunan,
                'qr' => $qr,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function persetujuan_index(Request $request)
    {
        $data = DB::table('rsc_notifikasi')
            ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_notifikasi.kode_rsc')
            ->join('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_notifikasi.kode_rsc')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_survei.kantor_kode',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_pengajuan.plafon',
                'rsc_notifikasi.no_notifikasi',
            )
            ->where(function ($query) {
                $query->whereIn('rsc_data_pengajuan.status', ['Proses Persetujuan', 'Notifikasi', 'Perjanjian Kredit', 'Selesai']);
            })
            ->orderBy('rsc_notifikasi.created_at', 'desc')
            ->paginate(10);

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.cetak_persetujuan.index',);
    }



    //Function Protected
    protected function rsc_usaha($kode_rsc)
    {
        $perdagangan = DB::table('rsc_au_perdagangan')->where('kode_rsc', $kode_rsc)->get();
        $pertanian = DB::table('rsc_au_pertanian')->where('kode_rsc', $kode_rsc)->get();
        $jasa = DB::table('rsc_au_jasa')->where('kode_rsc', $kode_rsc)->get();
        $lain = DB::table('rsc_au_lain')->where('kode_rsc', $kode_rsc)->get();

        //ekstraksi usaha
        //Perdagangan
        if (count($perdagangan) > 0) {
            foreach ($perdagangan as $item) {
                $total_perdagangan[] = $item->laba_bersih;
            }
            $ttpg = array_sum($total_perdagangan);
        } else {
            $ttpg = 0;
        }

        //Pertanian
        if (count($pertanian) > 0) {
            foreach ($pertanian as $item) {
                $total_pertanian[] = $item->laba_perbulan;
            }
            $ttp = array_sum($total_pertanian);
        } else {
            $ttp = 0;
        }

        //Jasa
        if (count($jasa) > 0) {
            foreach ($jasa as $item) {
                $total_jasa[] = $item->laba_bersih;
            }
            $ttj = array_sum($total_jasa);
        } else {
            $ttj = 0;
        }

        //Lain
        if (count($lain) > 0) {
            foreach ($lain as $item) {
                $total_lain[] = $item->laba_bersih;
            }
            $ttl = array_sum($total_lain);
        } else {
            $ttl = 0;
        }

        //ekstraksi usaha


        //Total Semua Usaha
        $total_usaha = $ttpg + $ttp + $ttj + $ttl;

        $data = (object) [
            'perdagangan' => $ttpg ?? 0,
            'pertanian' => $ttp ?? 0,
            'jasa' => $ttj ?? 0,
            'lain' => $ttl ?? 0,
            'total_usaha' => $total_usaha ?? 0,
        ];

        return $data;
    }

    protected function get_qrcode($data, $text, $user)
    {

        $carbon = Carbon::now();
        $tgl = $carbon->format('d-m-y');

        // Path untuk menyimpan QR Code
        $strpath = storage_path('app/public/image/qr_code');
        $imgname = $text . '_' . $data . '_' . $user . '_' . $tgl . '.png';
        $imgpath = $strpath . '/' . $imgname;

        $data_url = $text . '_' . $data . '_' . $user;

        // URL dan QR Code dari Google Chart API
        $url = 'http://sipebri.bprbangunarta.co.id/verifikasi?qrcode=' . $data_url;


        $logoPath = public_path('assets/img/favicon2.png');
        QrCode::size(300)
            ->format('png')
            ->errorCorrection('H')
            ->merge($logoPath, 0.3, true)
            ->generate($url, $imgpath);

        return $imgname;
    }
    //Function Protected
}
