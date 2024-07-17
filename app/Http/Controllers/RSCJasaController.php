<?php

namespace App\Http\Controllers;

use App\Models\RSC;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCJasaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = RSC::get_data_jasa_all_rsc();

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->status_rsc = $status_rsc;
            }

            $jasa = DB::table('rsc_au_jasa')->where('kode_rsc', $enc_rsc)->get();
            foreach ($jasa as $item) {
                $item->kd_usaha = Crypt::encrypt($item->kode_usaha);
            }

            return view('rsc.usaha_jasa.index', [
                'data' => $data[0],
                'jasa' => $jasa,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_jasa(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $kode_usaha = $this->kodeacak('AUJ', 6);

            $data = [
                'kode_rsc' => $enc_rsc,
                'kode_usaha' => $kode_usaha,
                'nama_usaha' => $request->nama_usaha,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_au_jasa')->insert($data);
            if ($insert) {
                return redirect()->back()->with('success', 'Data berhasil disimpan.');
            } else {
                return redirect()->back()->with('success', 'Data gagal disimpan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function rsc_jasa_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $kode = Crypt::decrypt($request->query('kode'));
            $rsc = Crypt::decrypt($request->query('rsc'));
            $status_rsc = $request->query('status_rsc');

            $data = DB::table('rsc_data_pengajuan')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.id',
                    'rsc_data_pengajuan.created_at as tanggal_rsc',
                    'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                )
                ->where('rsc_data_pengajuan.pengajuan_kode', $kode)
                ->orderBy('rsc_data_pengajuan.created_at', 'desc')
                ->get();

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

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
                $item->kode_usaha = $request->query('kode_usaha');
                $item->status_rsc = $status_rsc;
            }

            $jasa = DB::table('rsc_au_jasa')->where('kode_usaha', $kode_usaha)->get();

            return view('rsc.usaha_jasa.keuangan', [
                'data' => $data[0],
                'jasa' => $jasa[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_rsc_jasa_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $cek = $request->validate([
                'kode_usaha' => 'required',
                'nama_usaha' => 'required',
                'lokasi_usaha' => 'required',
                'lama_usaha' => 'required',
                'pendapatan' => '',
                'b_pajak' => '',
                'b_lainnya' => '',
                'pengeluaran' => '',
                'laba_bersih' => '',
            ]);

            $cek['kode_usaha'] = $kode_usaha;
            $cek['nama_usaha'] = ucwords($request->nama_usaha);
            $cek['lokasi_usaha'] = ucwords($request->lokasi_usaha);
            $cek['lama_usaha'] = ucwords($request->lama_usaha);
            $cek['pendapatan'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('pendapatan'));
            $cek['b_pajak'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('b_pajak'));
            $cek['b_lainnya'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('b_lainnya'));
            $cek['pengeluaran'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('pengeluaran'));
            $cek['laba_bersih'] = (int)str_replace(["Rp.", " ", "."], "", $request->input('laba_bersih'));
            $cek['input_user'] = Auth::user()->code_user;
            $cek['updated_at'] = now();

            $update = DB::table('rsc_au_jasa')->where('kode_usaha', $kode_usaha)->update($cek);
            if ($update) {
                return redirect()->back()->with('success', 'Data berhasil diupdate');
            } else {
                return redirect()->back()->with('error', 'Data gagal diupdate');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function delete_rsc_jasa_keuangan(Request $request)
    {
        try {
            $kode_usaha = Crypt::decrypt($request->query('kode_usaha'));
            $jasa = DB::table('rsc_au_jasa')->where('kode_usaha', $kode_usaha)->get();

            if (count($jasa) > 0) {
                DB::table('rsc_au_jasa')->where('kode_usaha', $kode_usaha)->delete();
                return redirect()->back()->with('success', 'Data berhasil dihapus');
            } else {
                return redirect()->back()->with('error', 'Data gagal dihapus');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }



    private function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('rsc_au_jasa')->where('kode_usaha', $acak)->exists()) {
                return $acak;
            }
        }
        return null;
    }
}
