<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCPengusulanController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

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

            // foreach ($data as $item) {
            //     $item->kode = $request->query('kode');
            //     $item->rsc = $request->query('rsc');
            //     $item->status_rsc = $status_rsc;
            // }

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'rsc_data_pengajuan.penentuan_plafon as plafon',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_data_pengajuan.status_rsc',
                    'rsc_data_pengajuan.jangka_waktu',
                    'data_pengajuan.produk_kode',
                )
                ->where('rsc_data_pengajuan.pengajuan_kode', $enc)
                ->first();


            if ($status_rsc == 'EKS') {

                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama as nama_nasabah',
                        'setup_loan.ket',
                    )
                    ->where('m_loan.noacc', $data->pengajuan_kode)->first();
                //

                $data->nama_nasabah = trim($data_eks->nama_nasabah);
                $data->produk_kode = Midle::data_produk(trim($data_eks->ket));
            }

            $data->kode = $request->query('kode');
            $data->rsc = $request->query('rsc');
            $data->status_rsc = $status_rsc;

            $rsc = DB::table('rsc_data_pengajuan')->where('pengajuan_kode', $enc)->where('kode_rsc', $enc_rsc)->first();
            $keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();

            $syarat_tambahan = DB::table('rsc_syarat_tambahan')->where('kode_rsc', $enc_rsc)->first();
            if (is_null($syarat_tambahan)) {
                $syarat_tambahan = (object) [
                    'sebelum_realisasi' => null,
                    'syarat_tambahan' => null,
                    'syarat_lain' => null,
                ];
            }

            return view('rsc.pengusulan.index', [
                'data' => $data,
                'pengusulan' => $rsc,
                'keuangan' => $keuangan,
                'syarat' => $syarat_tambahan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_pengusulan(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));

            $cek = $request->validate([
                'jangka_waktu' => 'required',
                'jangka_pokok' => 'required',
                'jangka_bunga' => 'required',
                'suku_bunga' => 'required',
                'metode_rps' => 'required',
                'angsuran_pokok' => 'required',
                'angsuran_bunga' => 'required',
                'total_angsuran' => 'required',
                'angsuran_pokok' => 'required',
                'rc' => 'required',
            ]);

            $data = [
                'jangka_waktu' => $request->jangka_waktu,
                'jangka_pokok' => $request->jangka_pokok,
                'jangka_bunga' => $request->jangka_bunga,
                'suku_bunga' => $request->suku_bunga,
                'metode_rps' => $request->metode_rps,
                'angsuran_pokok' => (int)str_replace(["Rp", " ", "."], "", $request->angsuran_pokok),
                'angsuran_bunga' => (int)str_replace(["Rp", " ", "."], "", $request->angsuran_bunga),
                'total_angsuran' => (int)str_replace(["Rp", " ", "."], "", $request->total_angsuran),
                'rc' => str_replace('%', '', $request->rc),
                'updated_at' => now(),
            ];

            $data_syarat = [
                'kode_rsc' => $enc_rsc,
                'sebelum_realisasi' => Str::upper($request->sebelum_realisasi),
                'syarat_tambahan' => Str::upper($request->syarat_tambahan),
                'syarat_lain' => Str::upper($request->syarat_lain),
            ];

            // Syarat Tambahan
            $cek_syarat = DB::table('rsc_syarat_tambahan')->where('kode_rsc', $enc_rsc)->first();
            if (is_null($cek_syarat)) {
                $data_syarat['created_at'] = now();
                DB::table('rsc_syarat_tambahan')->insert($data_syarat);
            } else {
                $data_syarat['updated_at'] = now();
                DB::table('rsc_syarat_tambahan')->where('kode_rsc', $enc_rsc)->update($data_syarat);
            }
            // Syarat Tambahan

            $cek = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->first();

            if (is_null($cek)) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data);
            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('error', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
