<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RSC;
use App\Models\Data;
use App\Models\Jasa;
use App\Models\Lain;
use App\Models\Midle;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pertanian;
use App\Models\Perdagangan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
                'data_pengajuan.plafon',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->whereNotIn('rsc_data_pengajuan.status', ['Batal RSC'])
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

        return view('rsc.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    public function eksternal_index()
    {
        $keyword = request('keyword');

        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->whereNotIn('rsc_data_pengajuan.status', ['Batal RSC'])
            ->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%0010101%')
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);
        //

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->plafon = trim($data_eks->plafond_awal);
            } else {
                $value->nama_nasabah = null;
            }
        }

        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();
        //

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.index_eksternal', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    public function index_analisa()
    {
        $keyword = request('keyword');
        $keyword_sqlsrv = RSC::get_sqlsrv(request('keyword'));

        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc') // Perbaikan di sini
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status',
                'rsc_data_pengajuan.status_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
                'rsc_data_survei.foto'
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where(function ($query) {
                $query->whereIn('rsc_data_pengajuan.status', [
                    'Proses Analisa',
                    'Proses Survei',
                    'Proses Persetujuan',
                    'Naik Kasi',
                    'Komite I',
                    'Komite II',
                    'Notifikasi'
                ]);
            })
            ->where('rsc_data_survei.surveyor_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        $data = $data->paginate(10);

        //===Handle Data Eksternal===//
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
        //===Handle Data Eksternal===//

        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }
        // dd($data);
        return view('rsc.analisa.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    public function tambah_rsc(Request $request)
    {
        try {
            $cek_kode_pengajuan = Pengajuan::where('kode_pengajuan', $request->pengajuan_kode)->first();

            if (is_null($cek_kode_pengajuan)) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            } elseif ($cek_kode_pengajuan->on_current == "0") {
                return redirect()->back()->with('error', 'Data belum REALISASI');
            }

            $data = $request->validate([
                'pengajuan_kode' => 'required',
                'pengajuan_kode' => 'required',
                'jenis_persetujuan' => 'required',
                'surveyor_kode' => 'required',
                'kasi_kode' => 'required',
            ]);

            if (
                $request->pengajuan_kode == "" ||
                $request->pengajuan_kode == "" ||
                $request->jenis_persetujuan == "" ||
                $request->surveyor_kode == "" ||
                $request->kasi_kode == ""
            ) {
                return redirect()->back()->with('error', 'Data tidak boleh kosong!!!');
            }

            $kode_nasabah = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->where('data_pengajuan.kode_pengajuan', '=', $request->pengajuan_kode)->first();
            //
            if (is_null($kode_nasabah)) {
                return redirect()->back()->with('error', 'Nasabah tidak ada.');
            }

            $kode_rsc = $this->kodeacak('RSC');
            $data = [
                'pengajuan_kode' => $request->pengajuan_kode,
                'kode_rsc' => $kode_rsc,
                'nasabah_kode' => $kode_nasabah->nasabah_kode,
                'jenis_persetujuan' => $request->jenis_persetujuan,
                'kasi_kode' => $request->kasi_kode,
                'surveyor_kode' => $request->surveyor_kode,
                'status' => 'Penjadwalan',
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_data_pengajuan')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data.');
            } else {
                return redirect()->back()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }


    public function tambah_rsc_eksternal(Request $request)
    {
        try {
            $cek_kode_pengajuan = DB::connection('sqlsrv')->table('m_loan')
                ->where('noacc', $request->pengajuan_kode)->first();
            //

            if (is_null($cek_kode_pengajuan)) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }

            $data = $request->validate([
                'pengajuan_kode' => 'required',
                'pengajuan_kode' => 'required',
                'jenis_persetujuan' => 'required',
                'surveyor_kode' => 'required',
                'kasi_kode' => 'required',
            ]);

            if (
                $request->pengajuan_kode == "" ||
                $request->pengajuan_kode == "" ||
                $request->jenis_persetujuan == "" ||
                $request->surveyor_kode == "" ||
                $request->kasi_kode == ""
            ) {
                return redirect()->back()->with('error', 'Data tidak boleh kosong!!!');
            }

            $kode_rsc = $this->kodeacak('RSC');
            $data = [
                'pengajuan_kode' => $request->pengajuan_kode,
                'kode_rsc' => $kode_rsc,
                'nasabah_kode' => $request->pengajuan_kode,
                'jenis_persetujuan' => $request->jenis_persetujuan,
                'kasi_kode' => $request->kasi_kode,
                'surveyor_kode' => $request->surveyor_kode,
                'status' => 'Penjadwalan',
                'status_rsc' => 'EKS',
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            // dd($data);
            $insert = DB::table('rsc_data_pengajuan')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data.');
            } else {
                return redirect()->back()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }

    public function data_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_telp',
                    'rsc_data_pengajuan.penentuan_plafon as plafon',
                    'data_pengajuan.produk_kode',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_data_pengajuan.jangka_waktu',
                    'data_spk.no_spk',
                    'data_spk.created_at',
                    'data_spk.updated_at',
                    'data_pengajuan.plafon as plafon_awal',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)
                ->get();
            //

            foreach ($data as $item) {
                if ($item->status_rsc == 'EKS') {
                    $data_eks = DB::connection('sqlsrv')->table('m_loan')
                        ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                        ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                        ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                        ->select(
                            'm_loan.fnama as nama_nasabah',
                            'm_cif.alamat as alamat_ktp',
                            'm_cif.nohp as no_telp',
                            'setup_loan.ket',
                            'm_loan.no_spk',
                            'm_loan.tgleff',
                            'm_loan.chgtgljam',
                            'm_loan.plafond_awal',
                        )
                        ->where('m_loan.noacc', $enc)->first();

                    if ($data_eks) {
                        $item->nama_nasabah = trim($data_eks->nama_nasabah);
                        $item->alamat_ktp = trim($data_eks->alamat_ktp);
                        $item->no_telp = trim($data_eks->no_telp);
                        $item->no_spk = trim($data_eks->no_spk);
                        $item->created_at = trim($data_eks->tgleff);
                        $item->updated_at = trim($data_eks->chgtgljam);
                        $item->plafon_awal = trim($data_eks->plafond_awal);
                    }
                }
            }

            // if ($status_rsc == 'EKS') {
            //     $data = DB::connection('sqlsrv')->table('m_loan')
            //         ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
            //         ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
            //         ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
            //         ->select(
            //             'm_loan.fnama as nama_nasabah',
            //             'm_cif.alamat as alamat_ktp',
            //             'm_loan.plafond_awal as plafon',
            //             'm_cif.nohp as no_telp',
            //             'm_loan.jkwaktu as jangka_waktu',
            //             'setup_loan.ket',
            //             'm_loan.no_spk',
            //             'm_loan.tgleff',
            //             'm_loan.chgtgljam',
            //         )
            //         ->where('m_loan.noacc', $enc)->get();
            //     //
            //     $data[0]->nama_nasabah = trim($data[0]->nama_nasabah);
            //     $data[0]->alamat_ktp = trim($data[0]->alamat_ktp);
            //     $data[0]->plafon = trim($data[0]->plafon);
            //     $data[0]->no_telp = trim($data[0]->no_telp);
            //     $data[0]->jangka_waktu = trim($data[0]->jangka_waktu);
            //     $data[0]->produk_kode = Midle::data_produk(trim($data[0]->ket));
            //     $data[0]->created_at = date('Y-m-d H:i:s', strtotime($data[0]->tgleff));
            //     $data[0]->no_spk = trim($data[0]->no_spk);
            //     $data[0]->updated_at = date('Y-m-d H:i:s', strtotime($data[0]->chgtgljam));
            //     $data[0]->metode_rps = null;
            // } else {
            //     $data = DB::table('data_pengajuan')
            //         ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            //         ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            //         ->select(
            //             'data_nasabah.nama_nasabah',
            //             'data_nasabah.alamat_ktp',
            //             'data_nasabah.no_telp',
            //             'data_pengajuan.plafon',
            //             'data_pengajuan.produk_kode',
            //             'data_pengajuan.metode_rps',
            //             'data_pengajuan.jangka_waktu',
            //             'data_spk.no_spk',
            //             'data_spk.created_at',
            //             'data_spk.updated_at',
            //         )
            //         ->where('data_pengajuan.kode_pengajuan', $enc)
            //         ->get();
            // }
            //

            if (count($data) > 0 && $data[0]->status_rsc == 'IN') {
                $tgl_realisasi = Carbon::createFromFormat('Y-m-d H:i:s', $data[0]->created_at);
                $tgl_jth_tempo = $tgl_realisasi->addMonths($data[0]->jangka_waktu);
                $data[0]->tgl_jth_tempo = $tgl_jth_tempo->format('d-m-Y');
            } elseif (count($data) > 0 && $data[0]->status_rsc == 'EKS') {
                $tgl_realisasi = Carbon::createFromFormat('Ymd', $data[0]->created_at);
                $tgl_jth_tempo = $tgl_realisasi->addMonths($data[0]->jangka_waktu);
                $data[0]->tgl_jth_tempo = $tgl_jth_tempo->format('d-m-Y');
            } else {
                return redirect()->back()->with('error', 'Data tidak ada.');
            }


            $perdagangan = Perdagangan::where('pengajuan_kode', $enc)->get();
            $pertanian = Pertanian::where('pengajuan_kode', $enc)->get();
            $jasa = Jasa::where('pengajuan_kode', $enc)->get();
            $lain = Lain::where('pengajuan_kode', $enc)->get();

            $jenis_usaha = Collection::make()
                ->merge($perdagangan)
                ->merge($pertanian)
                ->merge($jasa)
                ->merge($lain);

            $data_rsc = DB::table('rsc_data_pengajuan')->where('pengajuan_kode', $enc)->where('kode_rsc', $enc_rsc)->first();
            $biaya_rsc = DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->first();

            $rsc_data_pengajuan = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->first();
            if (!is_null($rsc_data_pengajuan)) {
                $data[0]->jenis_persetujuan = $rsc_data_pengajuan->jenis_persetujuan;
            } else {
                $data[0]->jenis_persetujuan = null;
            }

            //Cek SPK RSC
            $spk_rsc = DB::table('rsc_spk')
                ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.pengajuan_kode', '=', 'rsc_spk.pengajuan_kode')
                ->select(
                    'rsc_spk.no_spk',
                    'rsc_spk.created_at',
                    'rsc_data_pengajuan.penentuan_plafon',
                    DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE()) + INTERVAL rsc_data_pengajuan.jangka_waktu MONTH), '%d-%m-%Y') as tgl_akhir")
                )
                ->where('rsc_spk.pengajuan_kode', $enc)->latest()->first();
            if (!is_null($spk_rsc)) {
                $data[0]->no_spk = $spk_rsc->no_spk;
                $data[0]->plafon = $spk_rsc->penentuan_plafon;
                $data[0]->tgl_jth_tempo = $spk_rsc->tgl_akhir;
            }
            //Cek SPK RSC

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $data_rsc->status_rsc;
            }

            // dd($data, $status_rsc);
            return view('rsc.data-kredit', [
                'data' => $data[0],
                'usaha' => $jenis_usaha,
                'data_rsc' => $data_rsc,
                'biaya_rsc' => $biaya_rsc,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_data_kredit(Request $request)
    {
        try {
            $td =  (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_denda ?? 0);

            $tanggal = $request->tgl_jth_tempo;

            $tanggalCarbon = Carbon::createFromFormat('d-m-Y', $tanggal);

            $tgl_jth_temp = $tanggalCarbon->format('Ymd');


            $total = (int)str_replace(["Rp.", " ", "."], "", $request->baki_debet ?? 0) + (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_bunga ?? 0);
            $data = [
                'tgl_jth_tempo' => $tgl_jth_temp,
                'klasifikasi_kredit' => Str::upper($request->klasifikasi_kredit),
                'baki_debet' => (int)str_replace(["Rp.", " ", "."], "", $request->baki_debet ?? 0),
                'plafon' => (int)str_replace(["Rp.", " ", "."], "", $request->plafon ?? 0),
                'penentuan_plafon_temp' => $total,
                'jenis_persetujuan' => Str::upper($request->jenis_persetujuan),
                'tunggakan_poko' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_pokok ?? 0),
                'tunggakan_bunga' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_bunga ?? 0),
                'tunggakan_denda' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_denda ?? 0),
                'total_tunggakan' => (int)str_replace(["Rp.", " ", "."], "", $request->total_tunggakan ?? 0),
                'jml_tgk_pokok' => (int)str_replace(["Rp.", " ", "."], "", $request->jml_tunggakan_pokok ?? 0),
                'jml_tgk_bunga' => (int)str_replace(["Rp.", " ", "."], "", $request->jml_tunggakan_bunga ?? 0),
                'pokok_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pk_dibayar ?? 0),
                'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bg_dibayar ?? 0),
                'penentuan_plafon' => (int)str_replace(["Rp.", " ", "."], "", $request->penentuan_plafon ?? 0),
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_data_pengajuan')
                ->where('pengajuan_kode', $request->query('kode'))
                ->where('kode_rsc', $request->query('rsc'))
                ->update($data);

            //==Data table rsc_biaya==//
            $total = (int)str_replace(["Rp.", " ", "."], "", $request->pk_dibayar ?? 0) + (int)str_replace(["Rp.", " ", "."], "", $request->bg_dibayar ?? 0);
            $data2 = [
                'kode_rsc' => $request->rsc,
                'poko_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pk_dibayar ?? 0),
                'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bg_dibayar ?? 0),
                'total' => $total,
            ];

            $cek_biaya = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->first();

            if (is_null($cek_biaya)) {
                $data2['created_at'] = now();
                $insert_biaya = DB::table('rsc_biaya')->insert($data2);
            } else {
                $data2['updated_at'] = now();
                $update_biaya = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->update($data2);
            }
            //==Data table rsc_biaya==//

            if ($update) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data.');
            } else {
                return redirect()->back()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }

    public function update_biaya_rsc(Request $request)
    {
        try {
            $cek = DB::table('rsc_biaya')
                ->where('kode_rsc', $request->rsc)
                ->first();
            //
            if (is_null($cek)) {
                $data = [
                    'kode_rsc' => $request->rsc,
                    'administrasi' => $request->adm,
                    'administrasi_nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->persentase ?? 0),
                    'asuransi_jiwa' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_jiwa ?? 0),
                    'asuransi_tlo' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_tlo ?? 0),
                    'poko_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pokok_dibayar ?? 0),
                    'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bunga_dibayar ?? 0),
                    'denda_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->denda_dibayar ?? 0),
                    'total' => (int)str_replace(["Rp.", " ", "."], "", $request->total_biaya ?? 0),
                    'ujroh' => (int)str_replace(["Rp.", " ", "."], "", $request->ujroh ?? 0),
                    'created_at' => now(),
                ];

                $insert = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->insert($data);
                if ($insert) {
                    return redirect()->back()->with('success', 'Berhasil menambahkan data.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal ditambahkan.');
                }
            } else {
                $data = [
                    'kode_rsc' => $request->rsc,
                    'administrasi' => $request->adm,
                    'administrasi_nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->persentase ?? 0),
                    'asuransi_jiwa' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_jiwa ?? 0),
                    'asuransi_tlo' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_tlo ?? 0),
                    'poko_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pokok_dibayar ?? 0),
                    'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bunga_dibayar ?? 0),
                    'denda_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->denda_dibayar ?? 0),
                    'total' => (int)str_replace(["Rp.", " ", "."], "", $request->total_biaya ?? 0),
                    'ujroh' => (int)str_replace(["Rp.", " ", "."], "", $request->ujroh ?? 0),
                    'updated_at' => now(),
                ];

                $insert = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->update($data);
                if ($insert) {
                    return redirect()->back()->with('success', 'Berhasil merubah data.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal dirubah.');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }

    public function delete_rsc(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->query('rsc'));
            $data_rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $rsc)->first();

            if (!is_null($data_rsc)) {
                $data = ['status' => 'Batal RSC'];
                DB::table('rsc_data_pengajuan')->where('kode_rsc', $rsc)->update($data);
                return redirect()->back()->with('success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function konfirmasi_index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_rsc($enc_rsc);

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $status_rsc;
            }

            return view('rsc.konfirmasi.analisa', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function konfirmasi_update(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $cek_pengajuan_rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->first();
            $cek_biaya_rsc = DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->first();
            $cek_kondisi_usaha = DB::table('rsc_kondisi_usaha')->where('kode_rsc', $enc_rsc)->first();
            $cek_agunan = DB::table('rsc_agunan')->where('kode_rsc', $enc_rsc)->first();

            $cek_perdagangan = DB::table('rsc_au_perdagangan')->where('kode_rsc', $enc_rsc)->get();
            $cek_pertanian = DB::table('rsc_au_pertanian')->where('kode_rsc', $enc_rsc)->get();
            $cek_jasa = DB::table('rsc_au_jasa')->where('kode_rsc', $enc_rsc)->get();
            $cek_lain = DB::table('rsc_au_lain')->where('kode_rsc', $enc_rsc)->get();

            $cek_all = count($cek_perdagangan) + count($cek_pertanian) + count($cek_jasa) + count($cek_lain);

            $cek_keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();


            if (is_null($cek_pengajuan_rsc->baki_debet)) {
                return redirect()->back()->with('error', 'Data kredit belum diisi.');
            } else if (is_null($cek_biaya_rsc)) {
                return redirect()->back()->with('error', 'Biaya RSC belum diisi.');
            } else if (is_null($cek_kondisi_usaha)) {
                return redirect()->back()->with('error', 'Faktor dipenilaian debitur belum diisi.');
            } else if (is_null($cek_agunan)) {
                return redirect()->back()->with('error', 'kondisi agunan belum diisi.');
            } else if ($cek_all <= 0) {
                return redirect()->back()->with('error', 'Data usaha harus diisi.');
            } else if (is_null($cek_keuangan)) {
                return redirect()->back()->with('error', 'Keuangan harus diisi.');
            } else if (is_null($cek_pengajuan_rsc->total_angsuran)) {
                return redirect()->back()->with('error', 'Usulan plafon harus diisi.');
            }

            $data = [
                'status' => 'Proses Persetujuan',
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data);
            if ($update) {
                return redirect()->route('rsc.index.analisa')->with('success', 'Konfirmasi analisa RSC berhasil.');
            } else {
                return redirect()->back()->with('error', 'Konfirmasi analisa RSC gagal.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }


    public function simpan_jadul(Request $request)
    {
        try {
            $enc_rsc = $request->rsc;
            $cek_survei = DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)
                ->select('catatan_survei', 'catatan_jadul_1', 'catatan_jadul_2')
                ->first();
            //
            if (is_null($request->keterangan)) {
                return redirect()->back()->with('error', 'Masukan catatan minimal 1 kata.');
            }

            if (is_null($cek_survei->catatan_survei)) {
                $data = [
                    'catatan_survei' => strtoupper($request->keterangan),
                    'updated_at' => now(),
                ];

                $data2 = [
                    'status' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                DB::transaction(function () use ($data2, $data, $enc_rsc) {
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->update($data);
                });
            } else if (is_null($cek_survei->catatan_jadul_1)) {
                $data = [
                    'catatan_jadul_1' => strtoupper($request->keterangan),
                    'updated_at' => now(),
                ];

                $data2 = [
                    'status' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                DB::transaction(function () use ($data2, $data, $enc_rsc) {
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->update($data);
                });
            } else if (is_null($cek_survei->catatan_jadul_2)) {
                $data = [
                    'catatan_jadul_2' => strtoupper($request->keterangan),
                    'updated_at' => now(),
                ];

                $data2 = [
                    'status' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                DB::transaction(function () use ($data2, $data, $enc_rsc) {
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->update($data);
                });
            } else {
                return redirect()->back()->with('error', 'Catatan sudah penuh.');
            }
            return redirect()->back()->with('success', 'Anda berhasil melakukan pembatalan survei');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_notifikasi()
    {
        $keyword = request('keyword');
        $keyword_sqlsrv = RSC::get_sqlsrv(request('keyword'));

        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'rsc_data_pengajuan.status_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where(function ($query) use ($keyword) {
                $query->where('rsc_data_pengajuan.status', 'Notifikasi')
                    ->where('rsc_data_survei.direksi_kode', Auth::user()->code_user);
            })

            ->orWhere(function ($query) use ($keyword) {
                $query->where('rsc_data_pengajuan.status', 'Notifikasi')
                    ->where('rsc_data_survei.kasi_kode', Auth::user()->code_user);
            })

            ->orWhere(function ($query) use ($keyword) {
                $query->where('rsc_data_pengajuan.status', 'Notifikasi')
                    ->where('rsc_data_survei.kabag_kode', Auth::user()->code_user);
            })

            ->orWhere(function ($query) use ($keyword) {
                $query->where('rsc_data_pengajuan.status', 'Notifikasi');
            })

            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        $data = $data->paginate(10);

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

        return view('rsc.notifikasi.index', [
            'data' => $data
        ]);
    }

    public function get_notifikasi(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->input('kode'));
            $status_rsc = $request->input('status');

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.pengajuan_kode',
                    'data_nasabah.nama_nasabah',
                    'data_pengajuan.produk_kode',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.jangka_waktu',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $rsc)->first();
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
                    // $data->plafon = $data_eks->plafond_awal;
                    $data->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }

            $lasts = DB::table('rsc_notifikasi')->latest('nomor')->first();
            if (is_null($lasts)) {
                $count = 0001;
            } else {
                $count = (int) $lasts->nomor + 1;
            }
            $lengths = 4;
            $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);


            $now = Carbon::now();
            $bulan = $now->month;
            $romawi = Data::romawi($bulan);

            $notif = $kodes . '/' . 'NK' . '/' . 'PBA' . '/' . $romawi . '/' . $now->year;

            $data->kode_notif = $notif;
            $data->nomor = $kodes;
            return response()->json($data);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_notifikasi(Request $request)
    {
        try {
            $cek = $request->validate([
                'nomor' => 'required',
                'kode_rsc' => 'required',
                'nomor' => 'required',
                'kode_notifikasi' => 'required',
            ]);

            $data = [
                'nomor' => $request->nomor,
                'kode_rsc' => $request->kode_rsc,
                'nomor' => $request->nomor,
                'input_user' => Auth::user()->code_user,
                'no_notifikasi' => $request->kode_notifikasi,
                'created_at' => now(),
            ];

            $data2 = ['status' => 'Perjanjian Kredit'];

            $cek = DB::table('rsc_notifikasi')->where('kode_rsc', $request->kode_rsc)->first();

            if (!is_null($cek)) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            }

            DB::transaction(function () use ($data, $data2, $request) {
                $insert = DB::table('rsc_notifikasi')->insert($data);
                $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $request->kode_rsc)->update($data2);
            });

            return redirect()->back()->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal disimpan.');
        }
    }

    public function pk_index()
    {
        $keyword = request('keyword');
        $keyword_sqlsrv = RSC::get_sqlsrv(request('keyword'));

        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Perjanjian Kredit')
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        $data = $data->paginate(10);

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

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.perjanjian_kredit.index', [
            'data' => $data
        ]);
    }

    public function pk_get(Request $request)
    {
        $enc = Crypt::decrypt($request->input('kode'));
        $pengajuan = $request->input('pengajuan');

        $data_rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc)->first();

        $cek_spk = DB::table('rsc_spk')->exists();
        if (!$cek_spk) {
            $count = 0001;
        } else {
            $nomor = DB::table('rsc_spk')->latest()->first();
            $count = (int) $nomor->nomor + 1;
        }

        $lengths = 4;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);

        $now = Carbon::now();
        $bulan = $now->month;
        $romawi = Data::romawi($bulan);

        $data_rsc->no_spk_rsc = $kodes . '/' . 'RST' . '/' . 'PBA' . '/' . $romawi . '/' . $now->year;
        $data_rsc->nomor = $kodes;

        $biaya = DB::table('rsc_biaya')->where('kode_rsc', $enc)->first();
        $data_rsc->tunggakan_bunga = $biaya->bunga_dibayar;
        $data_rsc->tunggakan_denda = $biaya->denda_dibayar;

        // Cek SPK RSC sebelumnya
        $cek_spk = DB::table('rsc_spk')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.pengajuan_kode', '=', 'rsc_spk.pengajuan_kode')
            ->select(
                'rsc_spk.no_spk',
                'rsc_data_pengajuan.penentuan_plafon',
                'rsc_spk.created_at',
                DB::raw("DATE_FORMAT(COALESCE(rsc_spk.created_at, CURDATE()), '%Y-%m-%d') as tgl_mulai_rsc"),
                DB::raw("DATE_FORMAT((COALESCE(rsc_spk.updated_at, CURDATE()) + INTERVAL rsc_data_pengajuan.jangka_waktu MONTH), '%Y-%m-%d') as tgl_akhir")
            )
            ->where('rsc_spk.pengajuan_kode', $pengajuan)
            ->where('rsc_spk.kode_rsc', '!=', $enc)->latest('rsc_spk.created_at')->first();

        if (is_null($cek_spk)) {
            $eks_spk = DB::table('rsc_data_spk')->where('pengajuan_kode', $pengajuan)->latest('created_at')->first();

            if (is_null($eks_spk)) {
                $data_rsc->plafon = 0;
                $data_rsc->no_spk = null;
                $data_rsc->tgL_realisasi = null;
                $data_rsc->tgl_jth_tempo = null;
            } else {
                $data_rsc->plafon = $eks_spk->plafon;
                $data_rsc->no_spk = $eks_spk->no_spk;
                $data_rsc->tgL_realisasi = $eks_spk->tgL_realisasi;
                $data_rsc->tgl_jth_tempo = $eks_spk->tgl_jth_tempo;
            }
        } else {
            $data_rsc->plafon = $cek_spk->penentuan_plafon;
            $data_rsc->no_spk = $cek_spk->no_spk;
            $data_rsc->tgL_realisasi = $cek_spk->tgl_mulai_rsc;
            $data_rsc->tgl_jth_tempo = $cek_spk->tgl_akhir;
        }


        return response()->json($data_rsc);
    }

    public function pk_simpan(Request $request)
    {
        try {
            $cek = DB::table('rsc_data_pengajuan')->where('kode_rsc', $request->kode_rsc)->first();
            $enc = $request->kode_rsc;

            $biaya = DB::table('rsc_biaya')->where('kode_rsc', $request->kode_rsc)->first();
            $total = $biaya->administrasi_nominal + $biaya->asuransi_jiwa +
                $biaya->asuransi_tlo + $biaya->poko_dibayar + $biaya->poko_dibayar +
                (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga) + (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda);


            $data1 = [
                'bunga_dibayar' => (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga),
                'denda_dibayar' =>  (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda),
                'total' =>  $total,
            ];

            $data2 = [
                'pengajuan_kode' => $cek->pengajuan_kode,
                'kode_rsc' => $request->kode_rsc,
                'nomor' => $request->nomor,
                'no_spk' => $request->pk_rsc,
                'created_at' => now(),
            ];

            $data3 = [
                'status' => 'Selesai'
            ];

            $cek_spk = DB::table('rsc_spk')->where('kode_rsc', $request->kode_rsc)->first();
            if ($cek_spk) {
                return redirect()->back()->with('error', 'Data telah memiliki kode SPK.');
            }

            DB::transaction(function () use ($data1, $data2, $data3, $enc) {
                DB::table('rsc_biaya')->where('kode_rsc', $enc)->update($data1);
                DB::table('rsc_spk')->insert($data2);
                DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc)->update($data3);
            });

            return redirect()->back()->with('success', 'Berhasil Generate PK.');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function simpan_spk_rsc(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->kode_rsc);
            $cek = $request->validate([
                'plafon_rsc' => 'required',
                'spk_rsc' => 'required',
                'tgl_realisasi' => 'required',
                'tgl_jth_temp' => 'required',
            ], [
                'plafon_rsc.required' => 'Plafon RSC harus diisi.',
                'spk_rsc.required' => 'SPK RSC harus diisi.',
                'tgl_realisasi.required' => 'Tgl Realisasi harus diisi.',
                'tgl_realisasi.date' => 'Format harus DATE.',
                'tgl_jth_temp.required' => 'Tgl Jth Temp harus diisi.',
                'tgl_jth_temp.date' => 'Format harus DATE.',
            ]);

            $data = [
                'pengajuan_kode' => $request->pengajuan_kode,
                'kode_rsc' => $enc_rsc,
                'plafon' => (int)str_replace(["Rp", " ", "."], "", $request->plafon_rsc),
                'no_spk' => $request->spk_rsc,
                'tgL_realisasi' => $request->tgl_realisasi,
                'tgl_jth_tempo' => $request->tgl_jth_temp,
            ];

            $cek_data = DB::table('rsc_data_spk')->where('pengajuan_kode', $request->pengajuan_kode)->where('kode_rsc', $enc_rsc)->first();

            if (is_null($cek_data)) {
                $data['created_at'] = now();
                $insert = DB::table('rsc_data_spk')->insert($data);

                if ($insert) {
                    return redirect()->back()->with('success', 'Data berhasil disimpan');
                } else {
                    return redirect()->back()->with('error', 'Data gagal disimpan');
                }
            } else {
                $data['updated_at'] = now();
                $update = DB::table('rsc_data_spk')->where('pengajuan_kode', $request->pengajuan_kode)->where('kode_rsc', $enc_rsc)->insert($data);
                if ($update) {
                    return redirect()->back()->with('success', 'Data berhasil disimpan');
                } else {
                    return redirect()->back()->with('error', 'Data gagal disimpan');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Hubungi IT.');
        }
    }

    public function data_jadul_eks($pengajuan)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'rsc_data_survei.*',
            )
            ->where('rsc_data_pengajuan.pengajuan_kode', $pengajuan)->first();

        $data_eks = DB::connection('sqlsrv')->table('m_loan')
            ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
            ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
            ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
            ->select(
                'm_loan.fnama',
            )
            ->where('noacc', $data->pengajuan_kode)->first();

        //
        $arr = [
            'tgl_survei' => $data->tgl_survei,
            'tgl_jadul_1' => $data->tgl_jadul_1,
            'tgl_jadul_2' => $data->tgl_jadul_2,
        ];

        //Filter data yang null
        $filteredArray = array_filter($arr, function ($value) {
            return $value !== "-" && !is_null($value);
        });

        $latestIndex = min(array_filter($filteredArray));
        $data->tgl_survei_eks = $latestIndex;
        $data->nama_nasabah = $data_eks->fnama;

        return response()->json($data);
    }

    public function penolakan_index()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_pengajuan.plafon',
                'data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . $keyword . '%');
            })

            ->whereIn('rsc_data_pengajuan.status', ['Tolak', 'Dibatalkan'])
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);
        //
        //===Handle Data Eksternal===//
        foreach ($data as $value) {
            if (strpos($value->status_rsc, 'EKS') !== false) {
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
                    $value->plafon = trim($data_eks->plafond_awal);
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
        }
        //===Handle Data Eksternal===//


        foreach ($data as $item) {
            $cek_penolakan = DB::table('rsc_penolakan')->where('kode_rsc', $item->kode_rsc)->first();
            $item->rsc_kode = Crypt::encrypt($item->kode_rsc);
            if (!is_null($cek_penolakan)) {
                $item->no_penolakan = $cek_penolakan->no_penolakan;
            } else {
                $item->no_penolakan = null;
            }
        }

        $alasan = DB::table('data_alasan_penolakan')->get();

        return view('rsc.penolakan.index', [
            'data' => $data,
            'alasan' => $alasan,
        ]);
    }

    public function get_penolakan(Request $request)
    {
        $kode_rsc = $request->query('kode');

        // $data = DB::table('rsc_penolakan')->where('kode_rsc', $kode_rsc)->first();
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select('rsc_data_pengajuan.kode_rsc', 'rsc_data_pengajuan.pengajuan_kode', 'data_nasabah.nama_nasabah')
            ->where('rsc_data_pengajuan.kode_rsc', $kode_rsc)->first();
        //

        if (is_null($data->nama_nasabah)) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->select('m_loan.fnama')
                ->where('m_loan.noacc', $data->pengajuan_kode)->first();
            //
            if ($data_eks) {
                $data->nama_nasabah = trim($data_eks->fnama);
            }
        }


        if (is_null($data)) {
            return response()->json(['error' => 'Data Tidak ada.'], 404);
        }

        $lasts = DB::table('rsc_penolakan')->latest('nomor')->first();
        if (is_null($lasts)) {
            $count = 0001;
        } else {
            $count = (int) $lasts->nomor + 1;
        }
        $lengths = 4;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);

        $now = Carbon::now();
        $bulan = $now->month;
        $romawi = Data::romawi($bulan);

        $data->no_penolakan = $kodes . '/' . 'RST' . '/' . 'PBA' . '/' . $romawi . '/' . $now->year;
        $data->nomor = $kodes;
        $data->ket = null;

        $cek_penolakan = DB::table('rsc_penolakan')->where('kode_rsc', $kode_rsc)->first();

        if ($cek_penolakan) {
            $alasan = DB::table('data_alasan_penolakan')->get();
            $data_penolakan = (object) [
                'kode_rsc' => $cek_penolakan->kode_rsc,
                'nomor' => $cek_penolakan->nomor,
                'nama_nasabah' => $data->nama_nasabah,
                'no_penolakan' => $cek_penolakan->no_penolakan,
                'alasan_id' => $cek_penolakan->alasan_id,
                'ket' => $cek_penolakan->keterangan,
            ];
            return response()->json([$data_penolakan, $alasan]);
        } else {
            return response()->json($data);
        }
    }

    public function simpan_penolakan(Request $request)
    {
        try {
            $cek = $request->validate([
                'kode_rsc' => 'required',
                'nomor' => 'required',
                'no_penolakan' => 'required',
                'nama_nasabah' => 'required',
                'alasan' => 'required',
                'keterangan' => 'required',
            ]);

            $cek_data = DB::table('rsc_penolakan')->where('kode_rsc', $request->kode_rsc)->first();

            if ($cek_data) {
                return redirect()->back()->with('error', 'Data sudah ada.');
            } else {
                $data = [
                    'kode_rsc' => $cek['kode_rsc'],
                    'nomor' => $cek['nomor'],
                    'no_penolakan' => $cek['no_penolakan'],
                    'alasan_id' => $cek['alasan'],
                    'keterangan' => str::upper($cek['keterangan']),
                    'input_user' => Auth::user()->code_user,
                    'created_at' => now(),
                ];


                $simpan = DB::table('rsc_penolakan')->insert($data);
                if ($simpan) {
                    return redirect()->back()->with('success', 'Data berhasil disimpan.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal disimpan.');
                }
            }
        } catch (\Throwable $er) {
            return redirect()->back()->with('error', 'Hubungi IT.');
        }
    }

    public function update_penolakan(Request $request)
    {
        try {
            $data = [
                'alasan_id' => $request->alasan,
                'keterangan' => Str::upper($request->keterangan),
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_penolakan')->where('kode_rsc', $request->kode_rsc)->update($data);
            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil diubah.');
            } else {
                return redirect()->back()->with('error', 'Data gagal diubah.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data error.');
        }
    }



    private function kodeacak($name)
    {
        do {
            $koderand = random_int(10000, 99999);
            $haskode = $name . $koderand;

            // Cek apakah kode sudah ada di tabel
            $existingCode = DB::table('rsc_data_pengajuan')->where('kode_rsc', $haskode)->exists();
        } while ($existingCode);

        return $haskode;
    }
}
