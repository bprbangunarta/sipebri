<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pekerjaan;
use App\Models\Pengajuan;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Return_;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CetakController extends Controller
{
    public function pengajuan(Request $request)
    {
        $kode = $request->query('pengajuan');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.plafon', 'data_nasabah.nama_nasabah', 'data_pengajuan.produk_kode', 'data_pengajuan.metode_rps', 'data_pengajuan.jangka_bunga', 'data_pengajuan.jangka_waktu')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            $data[0]->kd_pengajuan = $kode;

            //Hari
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');

            //Format Angka
            $format_angka = "Rp. " . number_format($data[0]->plafon, 0, ',', '.');
            $data[0]->rp_plafon = $format_angka;

            return view('cetak.pengajuan', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function nik(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            //Hari ini
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('D MMMM Y');

            return view('cetak.layouts.nik', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function pendamping(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->where('data_pengajuan.kode_pengajuan', '=', $enc)
                ->select('data_pendamping.no_identitas', 'data_pendamping.nama_pendamping', 'data_pendamping.tempat_lahir', 'data_pendamping.tanggal_lahir', 'data_nasabah.no_identitas as iden', 'data_nasabah.nama_nasabah', 'data_nasabah.tempat_lahir as tempat', 'data_nasabah.tanggal_lahir as ttl', 'data_nasabah.pendidikan_kode', 'data_nasabah.alamat_ktp')->get();


            //Rubah tanggal
            $carbonDate = Carbon::createFromFormat('Ymd', $data[0]->tanggal_lahir);
            $data[0]->tanggal_lahir = $carbonDate->isoformat('D MMMM Y');
            $data[0]->tempat_lahir = ucfirst(strtolower($data[0]->tempat_lahir));

            //data pekerjaan
            $job = Pendidikan::where('kode_pendidikan', $data[0]->pendidikan_kode)->get();
            $data[0]->pendidikan_kode = $job[0]->nama_pendidikan;

            //Hari ini
            $hari = Carbon::today();
            $data[0]->hari = $hari->isoformat('dddd D MMMM YYYY');

            return view('cetak.layouts.pendamping', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function motor(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->leftJoin('ja_kendaraan', 'data_jaminan.jenis_agunan_kode', '=', 'ja_kendaraan.kode')
                ->select(
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_survei.surveyor_kode',
                    'data_jaminan.*',
                )
                ->where(function ($query) use ($enc) {
                    $query->where('data_pengajuan.kode_pengajuan', '=', $enc)
                        ->where('ja_kendaraan.jenis_agunan', '=', 'Kendaraan Bermotor Roda 2');
                })->get();

            if (count($data) == 0) {
                return redirect()->back()->with('error', 'Jaminan kendaraan tidak ada');
            }

            for ($i = 0; $i < count($data); $i++) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $data[$i]->surveyor_kode)
                    ->select('nama_user', 'role_name')->get();

                $thn = Carbon::now()->year;
                $data[$i]->thn = $thn;
                $data[$i]->nama_user = $surveyor[$i]->nama_user;
                $data[$i]->role_name = $surveyor[$i]->role_name;
            }

            // $surveyor = DB::table('v_users')
            //     ->where('code_user', $data[0]->surveyor_kode)
            //     ->select('nama_user', 'role_name')->get();

            // //Tahun
            // $thn = Carbon::now()->year;
            // $data[0]->thn = $thn;

            // $data[0]->nama_user = $surveyor[0]->nama_user;
            // $data[0]->role_name = $surveyor[0]->role_name;

            return view('cetak.layouts.motor', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function tanah(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);

            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->select(
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_survei.surveyor_kode',
                    'data_jaminan.*',
                )
                ->where(function ($query) use ($enc) {
                    $query->where('data_pengajuan.kode_pengajuan', '=', $enc)
                        ->where('data_jaminan.jenis_jaminan', '=', 'Tanah');
                })->get();
            // ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();

            for ($i = 0; $i < count($data); $i++) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $data[$i]->surveyor_kode)
                    ->select('nama_user', 'role_name')->get();

                $thn = Carbon::now()->year;
                $data[$i]->thn = $thn;
                $data[$i]->nama_user = $surveyor[$i]->nama_user;
                $data[$i]->role_name = $surveyor[$i]->role_name;
            }

            // //Surveyor
            // $surveyor = DB::table('v_users')
            //     ->where('code_user', $data[0]->surveyor_kode)
            //     ->select('nama_user', 'role_name')->get();

            // $data[0]->nama_user = $surveyor[0]->nama_user;
            // $data[0]->role_name = $surveyor[0]->role_name;

            // //Tahun
            // $thn = Carbon::now()->year;
            // $data[0]->thn = $thn;

            return view('cetak.layouts.tanah', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function mobil(Request $request)
    {
        $kode = $request->query('cetak');

        //====Try Enkripsi Request====//
        try {

            $enc = Crypt::decrypt($kode);
            $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                ->leftJoin('ja_kendaraan', 'data_jaminan.jenis_agunan_kode', '=', 'ja_kendaraan.kode')
                ->select(
                    'data_nasabah.no_identitas',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.tempat_lahir',
                    'data_nasabah.tanggal_lahir',
                    'data_nasabah.no_telp',
                    'data_nasabah.alamat_ktp',
                    'data_survei.surveyor_kode',
                    'data_jaminan.*',
                )
                ->where(function ($query) use ($enc) {
                    $query->where('data_pengajuan.kode_pengajuan', '=', '00339933')
                        ->where('ja_kendaraan.jenis_agunan', '=', 'Kendaraan Bermotor Roda 4');
                })->get();

            if (count($data) == 0) {
                return redirect()->back()->with('error', 'Jaminan kendaraan tidak ada');
            }

            for ($i = 0; $i < count($data); $i++) {
                $surveyor = DB::table('v_users')
                    ->where('code_user', $data[$i]->surveyor_kode)
                    ->select('nama_user', 'role_name')->get();

                $thn = Carbon::now()->year;
                $data[$i]->thn = $thn;
                $data[$i]->nama_user = $surveyor[$i]->nama_user;
                $data[$i]->role_name = $surveyor[$i]->role_name;
            }

            // dd($data);
            return view('cetak.layouts.mobil', [
                'data' => $data
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }
}
