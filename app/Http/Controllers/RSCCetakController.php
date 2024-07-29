<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RSC;
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
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_kondisi_usaha', 'rsc_kondisi_usaha.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc') // Perbaikan di sini
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'rsc_data_pengajuan.status',
                'rsc_data_pengajuan.status_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where(function ($query) {
                $query->whereNotIn('rsc_data_pengajuan.status', ['Proses Analisa', 'Proses Survei', 'Penjadwalan', 'Batal RSC']);
            })

            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);
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
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

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
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', 'data_pengajuan.nasabah_kode')
                ->leftJoin('a_memorandum', 'a_memorandum.pengajuan_kode', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('bi_penggunaan_debitur', 'bi_penggunaan_debitur.sandi', 'a_memorandum.bi_penggunaan_kode')
                ->leftJoin('data_spk', 'data_spk.pengajuan_kode', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', 'rsc_data_pengajuan.kode_rsc')
                ->leftJoin('users', 'users.code_user', 'rsc_data_survei.surveyor_kode')
                ->select(
                    'rsc_data_pengajuan.pengajuan_kode',
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
                    'rsc_data_pengajuan.jenis_persetujuan',
                    'rsc_data_pengajuan.rc',
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.jangka_waktu',
                    'rsc_data_pengajuan.updated_at as update_pengajuan',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_identitas',
                    'data_nasabah.no_telp',
                    'data_pengajuan.plafon',
                    'bi_penggunaan_debitur.keterangan',
                    'data_spk.no_spk',
                    'data_spk.updated_at as update_spk',
                    'users.name',
                    DB::raw("DATE_FORMAT((COALESCE(data_spk.updated_at, CURDATE()) + INTERVAL data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir")
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->first();

            //
            if ($status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_cif.noid',
                        'm_cif.nohp',
                        'm_loan.jkwaktu',
                        'm_loan.no_spk',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $data->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $data->nama_nasabah = trim($data_eks->fnama);
                    $data->alamat_ktp = trim($data_eks->alamat);
                    $data->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $data->jangka_waktu = $data_eks->jkwaktu;
                    $data->metode_rps = null;
                    $data->no_identitas = $data_eks->noid;
                    $data->no_telp = $data_eks->nohp;
                    $data->no_spk = trim($data_eks->no_spk);
                    $data->plafon = $data_eks->plafond_awal;
                    $data->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
            // dd($data);
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

            //Syarat Tambahan
            $syarat = DB::table('rsc_syarat_tambahan')->where('kode_rsc', $enc_rsc)->first();
            if (is_null($syarat)) {
                $syarat = (object) [
                    'sebelum_realisasi' => null,
                    'syarat_tambahan' => null,
                    'syarat_lain' => null,
                ];
            }

            // Data Analisa Usaha
            $perdagangan = RSC::perdagangan_rsc($enc_rsc, $data->status_rsc);
            $biaya_perdagangan = [];
            if (count($perdagangan) > 0) {
                foreach ($perdagangan as $item) {
                    $biaya_perdagangan = DB::table('rsc_du_perdagangan')->where('usaha_kode', $item->kode_usaha)->get();
                }
            } else {
                $biaya_perdagangan = null;
            }

            $pertanian = RSC::pertanian_rsc($enc_rsc, $data->status_rsc);
            if (count($pertanian) != 0) {
                for ($i = 0; $i < count($pertanian); $i++) {
                    $jml = ((int)$pertanian[$i]->laba_bersih * 70) / 100;
                    $pertanian[$i]->saving = $jml;
                }
            }

            $jasa = RSC::jasa_rsc($enc_rsc, $data->status_rsc);
            $lain = RSC::lain_rsc($enc_rsc, $data->status_rsc);
            if (count($lain) != 0) {
                foreach ($lain as $key => $item) {
                    $pendapatanlain = DB::table('rsc_pendapatan_lain')->where('usaha_kode', $item->kode_usaha)->get();
                    $pengeluaranlain = DB::table('rsc_pengeluaran_lain')->where('usaha_kode', $item->kode_usaha)->get();
                    $bahanbaku = DB::table('rsc_bahan_baku_lain')->where('usaha_kode', $item->kode_usaha)->get();

                    $total_bahan = [];
                    foreach ($bahanbaku as $items) {
                        $total_bahan[] = $items->total;
                    }
                    $item->total_bahan = array_sum($total_bahan);

                    $total_pengeluaran = [];
                    foreach ($pengeluaranlain as $items) {
                        $total_pengeluaran[] = $items->nominal;
                    }
                    $item->total_pengeluaran = $item->pengeluaran + $item->total_bahan;
                }
                // dd($bahanbaku);
            }

            // Kemampuan keuangan
            $keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();
            $keuangan->total_pengeluaran = $keuangan->b_rumah_tangga + $keuangan->b_kewajiban_lainnya;
            if (is_null($keuangan)) {
                return redirect()->back()->with('error', 'Analisa Keuangan belum diisi.');
            }

            // Cek Jaminan
            if ($data->status_rsc == 'IN') {
                $jaminan = DB::table('rsc_data_pengajuan')
                    ->leftJoin('data_jaminan', 'data_jaminan.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                    ->select('data_jaminan.catatan')
                    ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->get();
            } elseif ($data->status_rsc == 'EKS') {
                $jaminan = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('m_detil_jaminan', 'm_detil_jaminan.nocif', '=', 'm_cif.nocif')
                    ->select(
                        'm_detil_jaminan.catatan',
                        'm_cif.nocif',
                    )
                    ->where('noacc', $data->pengajuan_kode)->get();
                //

                if ($jaminan) {
                    foreach ($jaminan as $item) {
                        $item->catatan = trim($item->catatan);
                    }
                }
            } else {
                $jaminan = [];
            }

            return view('rsc.cetak_analisa.cetak_analisa', [
                'data' => $data,
                'kondisi' => $kondisi_usaha,
                'agunan' => $kondisi_agunan,
                'usaha' => $usaha,
                'perdagangan' => $perdagangan ?? null,
                'pertanian' => $pertanian,
                'jasa' => $jasa  ?? null,
                'lain' => $lain ?? null,
                'jaminan' => $jaminan,
                'biayaperdagangan' => $biaya_perdagangan,
                'pendapatanlain' => $pendapatanlain ?? null,
                'pengeluaranlain' => $pengeluaranlain  ?? null,
                'bahan_baku' => $bahanbaku ?? null,
                'biaya' => $biaya,
                'keuangan' => $keuangan,
                'qr' => $qr,
                'syarat' => $syarat,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetaknotifikasi_index(Request $request)
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_kondisi_usaha', 'rsc_kondisi_usaha.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('rsc_notifikasi', 'rsc_notifikasi.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc') // Perbaikan di sini
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'rsc_notifikasi.no_notifikasi',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where(function ($query) {
                $query->whereNotIn('rsc_data_pengajuan.status', ['Proses Analisa', 'Proses Survei', 'Penjadwalan', 'Batal RSC']);
            })

            ->where(function ($query) use ($keyword) {
                $query->orWhere('rsc_data_survei.direksi_kode', Auth::user()->code_user)
                    ->orWhere('rsc_data_survei.kabag_kode', Auth::user()->code_user)
                    ->orWhere('rsc_data_survei.kasi_kode', Auth::user()->code_user)
                    ->orWhere('rsc_data_survei.surveyor_kode', Auth::user()->code_user);
            })

            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);
        //

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
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
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

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
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_notifikasi')
                ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_notifikasi.kode_rsc')
                ->leftJoin('rsc_biaya', 'rsc_biaya.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
                ->leftJoin('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('bi_penggunaan_debitur', 'bi_penggunaan_debitur.sandi', '=', 'a_memorandum.bi_penggunaan_kode')
                ->leftJoin('bi_sektor_ekonomi', 'bi_sektor_ekonomi.sandi', '=', 'a_memorandum.bi_sek_ekonomi_kode')
                ->leftJoin('v_users', 'v_users.code_user', '=', 'rsc_notifikasi.input_user')
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
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_biaya.administrasi_nominal as administrasi',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.b_denda',
                    'data_produk.nama_produk',
                    'rsc_notifikasi.no_notifikasi',
                    'rsc_notifikasi.input_user',
                    'v_users.nama_user',
                    'bi_penggunaan_debitur.keterangan as penggunaan_debitur',
                    'bi_sektor_ekonomi.sandi as sandi_sektor_ekonomi',
                    'bi_sektor_ekonomi.keterangan as keterangan_sektor_ekonomi',
                    'rsc_notifikasi.created_at as created_notif',
                    'rsc_notifikasi.updated_at as updated_notif',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->first();
            //

            if ($status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_cif.noid',
                        'm_cif.nohp',
                        'm_cif.nocif',
                        'm_loan.jkwaktu',
                        'm_loan.no_spk',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $data->kode_pengajuan)->first();
                //
                if ($data_eks) {
                    $data->nama_nasabah = trim($data_eks->fnama);
                    $data->alamat_ktp = trim($data_eks->alamat);
                    $data->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $data->jangka_waktu = $data_eks->jkwaktu;
                    $data->metode_rps = null;
                    $data->nocif = trim($data_eks->nocif);
                    $data->no_identitas = $data_eks->noid;
                    $data->no_telp = $data_eks->nohp;
                    $data->no_spk = trim($data_eks->no_spk);
                    $data->plafon = $data_eks->plafond_awal;
                    $data->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }

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

            // Jaminan Eksternal
            if ($status_rsc == 'EKS') {
                $cek_jaminan = DB::connection('sqlsrv')->table('m_loan_jaminan')
                    ->where('nocif', $data->nocif)
                    ->where('noacc', $data->kode_pengajuan)
                    ->get();

                $ag = [];
                foreach ($cek_jaminan as $value) {
                    if ($value->jnsjaminan == '02' || $value->jnsjaminan == '03') {
                        $jenis = DB::table('ja_kendaraan')->where('kode', $value->jnsjaminan)->first();
                        $value->jenis_jaminan = ucwords($jenis->agunan);
                    } elseif (in_array($value->jnsjaminan, ['04', '05', '06', '07', '14'])) {
                        $jenis = DB::table('ja_tanah')->where('kode', $value->jnsjaminan)->first();
                        $value->jenis_jaminan = ucwords($jenis->agunan);
                    } elseif (in_array($value->jnsjaminan, ['10', '12', '09', '18', '99', '08', '13', '15', '17', '11', '01', '16'])) {
                        $jenis = DB::table('ja_lainnya')->where('kode', $value->jnsjaminan)->first();
                        $value->jenis_jaminan = ucwords($jenis->agunan);
                    } else {
                        $value->jenis_jaminan = null;
                    }

                    $cek_agunan = DB::connection('sqlsrv')->table('m_detil_jaminan')
                        ->where('noreg', $value->noreg)->get();
                    foreach ($cek_agunan as $items) {
                        $ag[] = $items;
                    }
                }
                $agunan = array_merge($ag);

                $col = collect($cek_jaminan);
                $cek_jaminan = $col->unique('jenis_jaminan')->values()->all();
                $data->count_jaminan = count($agunan);
            }

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

    public function cetakpersetujuan_index(Request $request)
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_notifikasi')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_notifikasi.kode_rsc')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_notifikasi.kode_rsc')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'rsc_data_survei.kantor_kode',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_notifikasi.no_notifikasi',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })
            ->where(function ($query) {
                $query->whereIn('rsc_data_pengajuan.status', ['Notifikasi', 'Perjanjian Kredit', 'Selesai']);
            })
            ->orderBy('rsc_notifikasi.created_at', 'desc')
            ->paginate(10);
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
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.cetak_persetujuan.index', [
            'data' => $data
        ]);
    }

    public function cetakpersetujuan_detail(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_jaminan', 'data_jaminan.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->leftJoin('rsc_biaya', 'rsc_biaya.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
                ->select(
                    'rsc_data_pengajuan.jenis_persetujuan',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_data_pengajuan.baki_debet',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.penggunaan',
                    'data_pengajuan.plafon',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_jaminan.nilai_taksasi',
                    'rsc_data_pengajuan.suku_bunga',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.jangka_waktu',
                    'rsc_data_pengajuan.jangka_pokok',
                    'rsc_data_pengajuan.jangka_bunga',
                    'rsc_data_pengajuan.angsuran_pokok',
                    'rsc_data_pengajuan.angsuran_bunga',
                    'rsc_data_pengajuan.tunggakan_poko',
                    'rsc_data_pengajuan.tunggakan_bunga',
                    'rsc_data_pengajuan.tunggakan_denda',
                    'rsc_data_pengajuan.rc',
                    'rsc_biaya.denda_dibayar',
                    'rsc_biaya.administrasi',
                    'rsc_biaya.administrasi_nominal',
                    'rsc_biaya.asuransi_jiwa',
                    'rsc_biaya.asuransi_tlo',
                    'rsc_biaya.poko_dibayar',
                    'rsc_biaya.bunga_dibayar',
                    'rsc_biaya.total',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->first();
            //

            if ($status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->leftJoin('m_loan_jaminan', 'm_loan_jaminan.noacc', '=', 'm_loan.noacc')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.noacc',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_loan_jaminan.nilai_jaminan',
                    )
                    ->where('m_loan.noacc', $data->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $data->nama_nasabah = trim($data_eks->fnama);
                    $data->alamat_ktp = trim($data_eks->alamat);
                    $data->nilai_taksasi = trim($data_eks->nilai_jaminan);
                    $data->penggunaan = null;
                    $data->rc = null;
                    $data->plafon = trim($data_eks->plafond_awal);
                }

                // Jaminan Eks
                $jaminan = DB::connection('sqlsrv')->table('m_loan_jaminan')->where('noacc', $data_eks->noacc)->get();
                if (count($jaminan) > 0) {
                    $jaminan_eks = [];
                    foreach ($jaminan as $item) {
                        $jaminan_eks[] = $item->nilai_jaminan;
                    }
                    $data->total_taksasi = array_sum($jaminan_eks);
                } else {
                    $data->total_taksasi = null;
                }
                // Jaminan Eks
            } else {
                $jaminan = DB::table('data_jaminan')->where('pengajuan_kode', $data->pengajuan_kode)->get();
                if (count($jaminan) > 0) {
                    $jaminan_in = [];
                    foreach ($jaminan as $item) {
                        $jaminan_in[] = $item->nilai_taksasi;
                    }
                    $data->total_taksasi = array_sum($jaminan_in);
                }
            }

            $data_usulan = DB::table('rsc_data_usulan')->where('kode_rsc', $enc_rsc)->get();
            if (count($data_usulan) > 0) {
                $data_petugas = [];
                foreach ($data_usulan as $item) {
                    $user = DB::table('v_users')->where('code_user', $item->input_user)->pluck('nama_user')->first();
                    $data_petugas[$item->role_name] = $user;
                }
            } else {
                $data_petugas = [
                    'Staff Analis' => null,
                    'Kasi Analis' => null,
                    'Kabag Analis' => null,
                    'Direksi' => null,
                ];
            }


            //Data Usulan
            $usulan = DB::table('rsc_data_usulan')->where('kode_rsc', $enc_rsc)->latest()->first();
            $tgl = Carbon::parse($usulan->created_at);
            $data->tgl_usulan = $tgl->isoFormat('D MMMM Y');

            //Angsuran
            if (($data->produk_kode == 'KRU' && $data->metode_rps == 'EFEKTIF MUSIMAN') || ($data->produk_kode == 'KBT' && $data->metode_rps == 'FLAT')) {
                $data->jangka_musim = $data->jangka_waktu / $data->jangka_pokok;
            } else {
                $data->jangka_musim = null;
            }

            //QR
            $data_usulan_qr = DB::table('rsc_data_usulan')
                ->leftJoin('v_users', 'v_users.code_user', '=', 'rsc_data_usulan.input_user')
                ->select(
                    'rsc_data_usulan.*',
                    'v_users.nama_user',
                )
                ->where('kode_rsc', $enc_rsc)->get();
            if (count($data_usulan_qr) > 0) {

                foreach ($data_usulan_qr as $item) {
                    $user = DB::table('v_users')->where('code_user', $item->input_user)->pluck('code_user')->first();

                    $item->data_qr_usulan = $this->get_qrcode($enc_rsc, 'RSC_PERSETUJUAN', $user);
                }
            } else {
                $data_qr_usulan = [
                    'Staff Analis' => null,
                    'Kasi Analis' => null,
                    'Kabag Analis' => null,
                    'Direksi' => null,
                ];
            }

            return view('rsc.cetak_persetujuan.cetak_persetujuan', [
                'data' => $data,
                'petugas' => $data_petugas,
                'qr' => $data_usulan_qr,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetakpk_index()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_spk')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_spk.kode_rsc')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_spk.kode_rsc')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'rsc_data_survei.kantor_kode',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_spk.no_spk',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where(function ($query) {
                $query->whereIn('rsc_data_pengajuan.status', ['Selesai']);
            })
            ->orderBy('rsc_spk.created_at', 'desc')
            ->paginate(10);
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
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.cetak_pk.index', [
            'data' => $data
        ]);
    }

    public function cetakpk_index_detail(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_spk')
                ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_spk.kode_rsc')
                ->leftJoin('rsc_biaya', 'rsc_biaya.kode_rsc', '=', 'rsc_spk.kode_rsc')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_pekerjaan', 'data_pekerjaan.kode_pekerjaan', '=', 'data_nasabah.pekerjaan_kode')
                ->select(
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.tunggakan_poko',
                    'rsc_data_pengajuan.tunggakan_bunga',
                    'rsc_data_pengajuan.tunggakan_denda',
                    'rsc_data_pengajuan.jangka_waktu as jw_rsc',
                    'rsc_data_pengajuan.jangka_pokok as jp_rsc',
                    'rsc_data_pengajuan.jangka_bunga as jb_rsc',
                    'rsc_data_pengajuan.suku_bunga as suku_bunga_rsc',
                    'rsc_data_pengajuan.metode_rps as metode_rps_rsc',
                    'rsc_data_pengajuan.angsuran_pokok',
                    'rsc_data_pengajuan.angsuran_bunga',
                    'rsc_data_pengajuan.baki_debet',
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_spk.no_spk as spk_rsc',
                    'rsc_biaya.bunga_dibayar',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.no_identitas',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.tempat_kerja',
                    'data_pendamping.nama_pendamping',
                    'data_pekerjaan.nama_pekerjaan',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.jangka_waktu as jw_pk',
                    'data_spk.no_spk',
                    'data_spk.updated_at as tgl_create_pk',
                    DB::raw("DATE_FORMAT(COALESCE(rsc_spk.created_at, CURDATE()), '%Y%m%d') as tgl_mulai_rsc"),
                    DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE()) + INTERVAL rsc_data_pengajuan.jangka_bunga MONTH), '%Y%m%d') as tgl_bayar_rsc"),
                    DB::raw("DATE_FORMAT((COALESCE(data_spk.updated_at, CURDATE()) + INTERVAL data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir"),
                    DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE()) + INTERVAL rsc_data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir_rsc")
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->first();
            //
            // dd($data);
            if ($status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->join('REF_PEKERJAAN', 'REF_PEKERJAAN.DESC1', '=', 'm_cif.pekerjaan')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_cif.noid',
                        'm_cif.tempat_bekerja',
                        'm_cif.slik_pasangan',
                        'm_loan.jkwaktu',
                        'm_loan.no_spk',
                        'm_loan.tgleff',
                        'm_loan.plafond_awal',
                        'm_loan.tgljtempo',
                        'REF_PEKERJAAN.DESC2',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $data->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $data->nama_nasabah = trim($data_eks->fnama);
                    $data->alamat_ktp = trim($data_eks->alamat);
                    $data->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $data->jangka_waktu = $data_eks->jkwaktu;
                    $data->metode_rps = null;
                    $data->tempat_kerja = trim($data_eks->tempat_bekerja);
                    $data->no_identitas = trim($data_eks->noid);
                    $data->jw_pk = trim($data_eks->jkwaktu);
                    $data->plafon = trim($data_eks->plafond_awal);
                    $data->no_spk = trim($data_eks->no_spk);
                    $data->nama_pekerjaan = trim($data_eks->DESC2);
                    $data->nama_pendamping = trim($data_eks->slik_pasangan);
                    $data->kantor_kode = Midle::data_kantor(trim($data_eks->wil));

                    // Tanggal Pembuatan PK
                    $tgl = Carbon::parse(trim($data_eks->tgleff));
                    $data->tgl_create_pk = $tgl->isoFormat('D MMMM Y');

                    // Tanggal Jatuh Tempo
                    $tgl_tempo = Carbon::parse(trim($data_eks->tgljtempo));
                    $data->tgl_akhir = $tgl_tempo->isoFormat('D MMMM Y');
                }
            }

            $targetDt = Carbon::parse($data->tgl_create_pk);
            $data->tgl_create_pk = $targetDt->isoFormat('D MMMM Y');

            $tgL_end = Carbon::parse($data->tgl_akhir);
            $data->tgl_akhir_pk = $tgL_end->isoFormat('D MMMM Y');

            $tgl_mulai_rsc = Carbon::parse($data->tgl_mulai_rsc);
            $data->tgl_mulai_rsc = $tgl_mulai_rsc->isoFormat('D MMMM Y');
            $data->hari_mulai_rsc = $tgl_mulai_rsc->isoFormat('dddd');

            $tgl_bayar_rsc = Carbon::parse($data->tgl_bayar_rsc);
            $data->tgl_bayar_rsc = $tgl_bayar_rsc->isoFormat('D MMMM Y');

            $tgl_akhir_rsc = Carbon::parse($data->tgl_akhir_rsc);
            $data->tgl_akhir_rsc = $tgl_akhir_rsc->isoFormat('D MMMM Y');

            //Pengkondisian PK RSC jika lebih dari 1
            $cek_spk = DB::table('rsc_spk')
                ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_spk.kode_rsc')
                ->select(
                    'rsc_spk.no_spk',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.metode_rps',
                    DB::raw("DATE_FORMAT(COALESCE(rsc_spk.created_at, CURDATE()), '%Y%m%d') as tgl_mulai_rsc"),
                    DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE()) + INTERVAL rsc_data_pengajuan.jangka_waktu MONTH), '%Y%m%d') as tgl_akhir_rsc")
                )
                ->where('rsc_spk.kode_rsc', $enc_rsc)->orderBy('rsc_spk.created_at', 'desc')->get();
            //

            if (count($cek_spk) > 1) {
                $data->no_spk = $cek_spk[1]->no_spk;

                $data->plafon = $cek_spk[1]->penentuan_plafon;

                $tgl_mulai_rsc = Carbon::parse($cek_spk[1]->tgl_mulai_rsc);
                $data->tgl_mulai_rsc = $tgl_mulai_rsc->isoFormat('D MMMM Y');

                $tgl_akhir_rsc = Carbon::parse($cek_spk[1]->tgl_mulai_rsc);
                $data->tgl_akhir_rsc = $tgl_akhir_rsc->isoFormat('D MMMM Y');
            }

            //Bunga dibayar
            $data->tunggakan_bunga = $data->tunggakan_bunga - $data->bunga_dibayar;

            //Validasi Musiman
            if ($data->produk_kode == 'KRU' && $data->metode_rps_rsc == 'EFEKTIF MUSIMAN') {
                $data->jw_rsc_musiman = $data->jw_rsc / $data->jp_rsc;
            } else {

                $data->jw_rsc_musiman = null;
            }

            if ($data->metode_rps_rsc == "FLAT" && $data->produk_kode == 'KPJ' && $data->tempat_kerja == 'PT HANDSOME') {
                return view('rsc.cetak_pk.kpj_flat_handsome', [
                    'data' => $data
                ]);
            } elseif ($data->metode_rps_rsc == "EFEKTIF ANUITAS" && $data->produk_kode == 'KPS' && $data->tempat_kerja == 'PT HANDSOME') {
                return view('rsc.cetak_pk.kps_anuitas_handsome', [
                    'data' => $data
                ]);
            }

            if ($data->metode_rps_rsc == "EFEKTIF MUSIMAN") {
                return view('rsc.cetak_pk.efektif_musiman', [
                    'data' => $data
                ]);
            } elseif ($data->metode_rps_rsc == "EFEKTIF") {
                return view('rsc.cetak_pk.efektif', [
                    'data' => $data
                ]);
            } elseif ($data->metode_rps_rsc == "FLAT") {
                return view('rsc.cetak_pk.flat', [
                    'data' => $data
                ]);
            } elseif ($data->metode_rps_rsc == "EFEKTIF ANUITAS") {

                return view('rsc.cetak_pk.anuitas', [
                    'data' => $data
                ]);
            } else {
                return view('rsc.cetak_pk.first', [
                    'data' => $data
                ]);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetak_penolakan(Request $request)
    {
        try {
            $kode_rsc = Crypt::decrypt($request->query('kode_rsc'));
            $data = DB::table('rsc_penolakan')
                ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_penolakan.kode_rsc')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->select(
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'rsc_penolakan.no_penolakan',
                    'rsc_penolakan.keterangan as alasan',
                    'rsc_penolakan.input_user',
                )
                ->where('rsc_penolakan.kode_rsc', $kode_rsc)->first();
            //
            if (is_null($data->nama_nasabah)) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->select(
                        'm_loan.fnama',
                        'm_cif.alamat',
                        'm_cif.nohp',
                    )
                    ->where('m_loan.noacc', $data->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $data->nama_nasabah = trim($data_eks->fnama);
                    $data->alamat_ktp = trim($data_eks->alamat);
                    $data->no_telp = trim($data_eks->nohp);
                }
            }

            $komite = DB::table('rsc_data_usulan')->where('kode_rsc', $kode_rsc)->latest()->first();
            if (!is_null($komite)) {
                $conv = Carbon::parse($komite->created_at);
                $data->tgl_usulan = $conv->isoFormat('D MMMM Y');
            } else {
                $conv = now();
                $data->tgl_usulan = $conv->locale('id')->isoFormat('D MMMM Y');
            }

            $qr = $this->get_qrcode($kode_rsc, 'PENOLAKAN_RSC', $data->input_user);
            //

            return view('rsc.penolakan.cetak_penolakan', [
                'data' => $data,
                'qr' => $qr,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
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
