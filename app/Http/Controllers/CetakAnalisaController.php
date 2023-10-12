<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Carbon\Carbon;
use App\Models\Jasa;
use App\Models\Midle;
use App\Models\Keuangan;
use App\Models\Kepemilikan;
use App\Models\Survei;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CetakAnalisaController extends Controller
{
    public function analisa5c(Request $request)
    {
        $kode = $request->query('pengajuan');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.plafon', 'data_nasabah.nama_nasabah', 'data_pengajuan.produk_kode', 'data_pengajuan.metode_rps', 'data_pengajuan.jangka_bunga', 'data_pengajuan.jangka_waktu')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->first();

            $data->kd_pengajuan = $kode;

            //Hari
            $hari = Carbon::today();
            $data->hari = $hari->isoformat('D MMMM Y');

            //Format Angka
            $format_angka = "Rp. " . number_format($data->plafon, 0, ',', '.');
            $data->rp_plafon = $format_angka;

            return view('cetak.analisa.index', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function usaha_jasa(Request $request)
    {
        $kode = $request->query('pengajuan');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);

            $data = Jasa::cetak_jasa($enc);

            //Hari ini
            $hari = Carbon::today();
            $data[0][0]->hari = $hari->isoformat('D MMMM Y');

            return view('cetak.analisa.usaha_jasa', [
                'data' => $data[0][0],
                'jasa' => $data[1],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function kemampuan_keuangan(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $data = Keuangan::cetak_keuangan($enc);
            $biaya = Keuangan::data_keuangan($enc);
            $usaha = Midle::kemampuan_keuangan($enc);
            $kepemilikan = Kepemilikan::where('pengajuan_kode', $enc)->first();
            $taksasi = Midle::taksasi($enc);

            $filter = array_filter($usaha, function ($value) {
                return $value !== null ? $value : 0;
            });

            //jumlah hasil pengeluaran kemampuan keuangan
            $tbiaya = [];
            for ($i = 0; $i < count($biaya); $i++) {
                $tbiaya[] += $biaya[$i]->nominal;
            }
            $totalbiaya = array_sum($tbiaya);

            //Hasil penjumlahan analisa usaha
            $total = array_sum($filter);
            $usaha['total'] = $total;
            $kemampuanperbulan = $usaha['total'] - $totalbiaya;

            return view('cetak.analisa.kemampuan_keuangan', [
                'data' => $data[0],
                'usaha' => $usaha,
                'biaya' => $biaya,
                'totalbiaya' => $totalbiaya,
                'kemampuanperbulan' => $kemampuanperbulan,
                'kepemilikan' => $kepemilikan,
                'taksasi' => $taksasi,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetakanalisa5c(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $survei = Survei::where('pengajuan_kode', $enc)->first();
            $surveyor = DB::table('v_users')->where('code_user', $survei->surveyor_kode)->first();

            $data = DB::table('a5c_character')->where('pengajuan_kode', $enc)->first();
            $capa = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first();
            $colla = DB::table('a5c_collateral')->where('pengajuan_kode', $enc)->first();
            $con = DB::table('a5c_condition')->where('pengajuan_kode', $enc)->first();

            $character = Data::cetak_a5c_character($data);
            $capacity = Data::cetak_a5c_capacity($capa);
            $collateral = Data::cetak_a5c_collateral($colla);
            $condition = Data::cetak_a5c_condition($con);

            //Hari
            $hari = Carbon::today();
            $data->hari = $hari->isoformat('D MMMM Y');
            $data->surveyor = $surveyor->nama_user;

            return view('cetak.analisa.analisa5c', [
                'data' => $data,
                'analisa' => $character,
                'capacity' => $capacity,
                'collateral' => $collateral,
                'condition' => $condition,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function crr(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);

            return view('cetak.analisa.credit_risk_rating');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function kualitatif(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->select('data_nasabah.nama_nasabah', 'data_nasabah.kode_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.no_telp', 'data_survei.kasi_kode', 'data_survei.surveyor_kode', 'data_pengajuan.penggunaan')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->first();
            //
            $kasi = DB::table('v_users')->select('nama_user')->where('code_user', $data->kasi_kode)->first();
            $surveyor = DB::table('v_users')->select('nama_user')->where('code_user', $data->surveyor_kode)->first();
            $data->nama_kasi = $kasi->nama_user;
            $data->nama_surveyor = $surveyor->nama_user;

            //Hari
            $hari = Carbon::today();
            $data->hari = $hari->isoformat('D MMMM Y');

            $kualitatif = DB::table('au_kualitatif')->where('pengajuan_kode', $enc)->first();
            $cekkualitatif = Data::cekkualitatif($kualitatif);
            $kualitatif->bi_checking = $cekkualitatif['bi_checking'];
            $kualitatif->kewajiban_pihak_lain = $cekkualitatif['kewajiban_pihak_lain'];
            $kualitatif->pihak_berwajib = $cekkualitatif['pihak_berwajib'];
            $kualitatif->hubungan_tetangga = $cekkualitatif['hubungan_tetangga'];
            $kualitatif->pengalaman_tki = $cekkualitatif['pengalaman_tki'];
            // dd($kualitatif);
            return view('cetak.analisa.kualitatif', [
                'data' => $data,
                'kualitatif' => $kualitatif,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function tambahan(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->select('data_nasabah.nama_nasabah', 'data_nasabah.kode_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.no_telp', 'data_survei.kasi_kode', 'data_survei.surveyor_kode', 'data_pengajuan.penggunaan')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->first();
            //
            $kasi = DB::table('v_users')->select('nama_user')->where('code_user', $data->kasi_kode)->first();
            $surveyor = DB::table('v_users')->select('nama_user')->where('code_user', $data->surveyor_kode)->first();
            $data->nama_kasi = $kasi->nama_user;
            $data->nama_surveyor = $surveyor->nama_user;

            //Hari
            $hari = Carbon::today();
            $data->hari = $hari->isoformat('D MMMM Y');

            $tambahan = DB::table('au_tambahan')->where('pengajuan_kode', $enc)->first();

            return view('cetak.analisa.tambahan', [
                'data' => $data,
                'tambah' => $tambahan,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function cetak_memorandum(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($kode);
            $data = Midle::memorandum($enc);

            return view('cetak.analisa.memorandum_usulan');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
