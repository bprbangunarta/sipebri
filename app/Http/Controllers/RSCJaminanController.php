<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCJaminanController extends Controller
{
    public function index_kendaraan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = $this->data($enc_rsc);

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            //Agunan Kendaraan
            $jenis_kendaraan = DB::table('ja_kendaraan')->get();
            $dok_kendaraan = DB::table('da_kendaraan')->get();

            //Agunan Lain
            $jenis_lain = DB::table('ja_lainnya')->get();

            // Jaminan
            $jaminan = $this->jaminan($data, $enc, $enc_rsc);

            if (count($jaminan) > 0) {
                foreach ($jaminan as $item) {
                    $jenisAgunan = $jenis_kendaraan->first(function ($kendaraan) use ($item) {
                        return $item->jnsjaminan == $kendaraan->kode;
                    });
                    $item->jenis_agunan = $jenisAgunan ? $jenisAgunan->agunan : null;


                    $jenisDokumen = $dok_kendaraan->first(function ($dokumen_kendaraan) use ($item) {
                        return $item->jnsdokumen == $dokumen_kendaraan->kode;
                    });
                    $item->jenis_dokumen = $jenisDokumen ? $jenisDokumen->jenis_dokumen : null;
                }
            }
            //Data dati
            $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');

            return view('rsc.jaminan.kendaraan', [
                'data' => $data[0],
                'jenis_kendaraan' => $jenis_kendaraan,
                'data_kendaraan' => $dok_kendaraan,
                'jaminan' => $jaminan,
                'dati' => $kab,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function add_kendaraan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->pengajuan_kode);
            $rsc = Crypt::decrypt($request->kode_rsc);

            $data = [
                'pengajuan_kode' => $enc,
                'kode_rsc' => $rsc,
                'jenis_jaminan' => $request->jenis_agunan_kode,
                'jenis_dokumen' => $request->jenis_agunan_kode,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            if ($request->jenis_agunan_kode == '02') {
                $jenis_agunan = 'KENDARAAN RODA 2';
            } elseif ($request->jenis_agunan_kode == '03') {
                $jenis_agunan = 'KENDARAAN RODA 4';
            } elseif (is_null($request->jenis_agunan_kode)) {
                $jenis_agunan = null;
            }

            $data['catatan'] = 'BPKB' . ' ' . $jenis_agunan . ', ' .
                strtoupper($request->merek) . ' ' . strtoupper($request->tipe_kendaraan) .
                ', ' . strtoupper($request->tahun) . ', ' . strtoupper($request->no_rangka) .
                ', ' . strtoupper($request->no_mesin) . ', ' . strtoupper($request->no_polisi) .
                ', ' . strtoupper($request->no_dokumen) . ', ' . strtoupper($request->warna) .
                ', ' . strtoupper($request->atas_nama) . ', ' . strtoupper($request->lokasi);

            $insert = DB::table('rsc_data_jaminan')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
        }
    }

    public function get_kendaraan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->input('kode'));
            $catatan = $request->input('catatan');

            $cek = DB::table('rsc_data_jaminan')
                ->where('pengajuan_kode', $enc)
                ->where('catatan', 'like', '%' . $catatan . '%')
                ->first();

            return response()->json($cek);
        } catch (DecryptException $e) {
            return response()->json('permintaan ditolak.');
        }
    }

    public function index_tanah(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = $this->data($enc_rsc);

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            //Agunan Tanah
            $jenis_tanah = DB::table('ja_tanah')->get();
            $dok_tanah = DB::table('da_tanah')->get();

            // Jaminan
            $jaminan = $this->jaminan($data, $enc, $enc_rsc);

            if (count($jaminan) > 0) {
                foreach ($jaminan as $item) {
                    $jenisAgunan = $jenis_tanah->first(function ($tanah) use ($item) {
                        return $item->jnsjaminan == $tanah->kode;
                    });
                    $item->jenis_agunan = $jenisAgunan ? $jenisAgunan->agunan : null;


                    $jenisDokumen = $dok_tanah->first(function ($dokumen_tanah) use ($item) {
                        return $item->jnsdokumen == $dokumen_tanah->kode;
                    });
                    $item->jenis_dokumen = $jenisDokumen ? $jenisDokumen->jenis_dokumen : null;
                }
            }

            //Data dati
            $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');

            return view('rsc.jaminan.tanah', [
                'data' => $data[0],
                'jenis_tanah' => $jenis_tanah,
                'data_tanah' => $dok_tanah,
                'jaminan' => $jaminan,
                'dati' => $kab,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function add_tanah(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->pengajuan_kode);
            $rsc = Crypt::decrypt($request->kode_rsc);

            $data = [
                'pengajuan_kode' => $enc,
                'kode_rsc' => $rsc,
                'jenis_jaminan' => $request->jenis_agunan_kode,
                'jenis_dokumen' => $request->jenis_dokumen_kode,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $jaminan = DB::table('ja_tanah')->where('kode', $request->jenis_agunan_kode)->first();

            $dokumen = DB::table('da_tanah')->where('kode', $request->jenis_dokumen_kode)->first();

            $data['catatan'] = $dokumen->jenis_dokumen . ' ' . strtoupper($jaminan->jenis_agunan) . ' NO ' . strtoupper($data['jenis_dokumen']) . ', LUAS ' . strtoupper($request->luas) . ' M2, ' . 'ATAS NAMA ' . strtoupper($request->atas_nama) . ', ALAMAT ' . strtoupper($request->lokasi);

            $insert = DB::table('rsc_data_jaminan')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_lain(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = $this->data($enc_rsc);

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            //Agunan Tanah
            $jenis_lain = DB::table('ja_lainnya')->get();
            $dok_lain = DB::table('da_lainnya')->get();

            // Jaminan
            $jaminan = $this->jaminan($data, $enc, $enc_rsc);

            if (count($jaminan) > 0) {
                foreach ($jaminan as $item) {

                    $jenisAgunan = $jenis_lain->first(function ($lain) use ($item) {
                        return $item->jnsjaminan == $lain->kode;
                    });
                    $item->jenis_agunan = $jenisAgunan ? $jenisAgunan->agunan : null;


                    $jenisDokumen = $dok_lain->first(function ($dok) use ($item) {
                        return $item->jnsdokumen == $dok->kode;
                    });
                    $item->jenis_dokumen = $jenisDokumen ? $jenisDokumen->jenis_dokumen : null;
                }
            }

            //Data dati
            $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');
            return view('rsc.jaminan.lain', [
                'data' => $data[0],
                'jenis_lain' => $jenis_lain,
                'data_lain' => $dok_lain,
                'jaminan' => $jaminan,
                'dati' => $kab,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function add_lain(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->pengajuan_kode);
            $rsc = Crypt::decrypt($request->kode_rsc);

            $data = [
                'pengajuan_kode' => $enc,
                'kode_rsc' => $rsc,
                'jenis_jaminan' => $request->jenis_agunan_kode,
                'jenis_dokumen' => $request->jenis_dokumen_kode,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $jaminan = DB::table('ja_lainnya')->where('kode', $request->jenis_agunan_kode)->first();

            $data['catatan'] = strtoupper($jaminan->jenis_agunan) . ' ATAS NAMA ' . strtoupper($request->atas_nama) . ' NO ' . strtoupper($request->no_dokumen) . ' ALAMAT ' . strtoupper($request->lokasi);

            $insert = DB::table('rsc_data_jaminan')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    private function data($enc)
    {
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
                'rsc_data_pengajuan.pengajuan_kode',
                'rsc_data_pengajuan.jangka_waktu',
                'data_spk.no_spk',
                'data_spk.created_at',
                'data_spk.updated_at',
                'data_pengajuan.plafon as plafon_awal',
            )
            ->where('rsc_data_pengajuan.kode_rsc', $enc)
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
                    ->where('m_loan.noacc', $data[0]->pengajuan_kode)->first();

                if ($data_eks) {
                    $item->nama_nasabah = trim($data_eks->nama_nasabah);
                    $item->alamat_ktp = trim($data_eks->alamat_ktp);
                    $item->no_telp = trim($data_eks->no_telp);
                    $item->no_spk = trim($data_eks->no_spk);
                    $item->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $item->created_at = trim($data_eks->tgleff);
                    $item->updated_at = trim($data_eks->chgtgljam);
                    $item->plafon_awal = trim($data_eks->plafond_awal);
                }
            }
        }

        return $data;
    }

    private function jaminan($data, $enc, $enc_rsc)
    {
        if ($data[0]->status_rsc == 'EKS') {
            $jaminan = DB::connection('sqlsrv')->table('m_loan')
                ->leftJoin('m_loan_jaminan', 'm_loan_jaminan.noacc', '=', 'm_loan.noacc')
                ->leftJoin('m_detil_jaminan', 'm_detil_jaminan.noreg', '=', 'm_loan_jaminan.noreg')
                ->select(
                    'm_detil_jaminan.jnsjaminan',
                    'm_detil_jaminan.nilai_taksasi',
                    'm_detil_jaminan.jnsdokumen',
                    'm_detil_jaminan.catatan',
                )
                ->where('m_loan.noacc', $enc)->get();
        } else {
            $jaminan = DB::table('data_jaminan')
                ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.pengajuan_kode', '=', 'data_jaminan.pengajuan_kode')
                ->select(
                    'data_jaminan.jenis_agunan_kode as jnsjaminan',
                    'data_jaminan.nilai_taksasi',
                    'data_jaminan.jenis_dokumen_kode as jnsdokumen',
                    'data_jaminan.catatan',
                )
                ->where('data_jaminan.pengajuan_kode', $enc)->get();
        }

        // Jaminan RSC dari SIPEBRI
        $jaminan_sipebri = DB::table('rsc_data_jaminan')
            ->select(
                'rsc_data_jaminan.jenis_jaminan as jnsjaminan',
                'rsc_data_jaminan.nilai_taksasi',
                'rsc_data_jaminan.jenis_dokumen as jnsdokumen',
                'rsc_data_jaminan.catatan',
            )
            ->where('kode_rsc', $enc_rsc)->get();


        return $jaminan = $jaminan->merge($jaminan_sipebri);
    }
}
