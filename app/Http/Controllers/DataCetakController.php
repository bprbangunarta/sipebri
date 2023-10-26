<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Survei;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
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
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->where(function ($query) {
                $query->where('data_pengajuan.tracking', '=', 'Selesai')
                    ->where('data_pengajuan.status', '=', 'Disetujui');
            })
            // ->where('data_pengajuan.tracking', '=', 'Selesai')
            // ->orWhere('data_pengajuan.status', '=', 'Disetujui')
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.tracking', 'data_pengajuan.kategori', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2', 'users.name');

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
        return view('cetak.notifikasi-kredit.index', [
            'data' => $data
        ]);
    }

    public function generate_notifikasi($kode)
    {
        $data = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.nama_nasabah')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)->get();
        //
        $count = 530;
        $lengths = 4;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
        $data[0]->generate = $kodes;

        return response()->json($data[0]);
    }

    // public function perjanjian_kredit(Request $request)
    // {
    //     $cek = DB::table('data_pengajuan')
    //         ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
    //         ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
    //         ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
    //         ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
    //         ->where(function ($query) {
    //             $query->where('data_pengajuan.tracking', '=', 'Selesai')
    //                 ->where('data_pengajuan.status', '=', 'Disetujui');
    //         })
    //         // ->where('data_pengajuan.tracking', '=', 'Selesai')
    //         // ->orWhere('data_pengajuan.status', '=', 'Disetujui')
    //         ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.tracking', 'data_pengajuan.kategori', 'data_nasabah.kode_nasabah', 'data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.kelurahan', 'data_nasabah.kecamatan', 'data_pengajuan.plafon', 'data_kantor.nama_kantor', 'data_survei.surveyor_kode', 'data_survei.tgl_survei', 'data_survei.tgl_jadul_1', 'data_survei.tgl_jadul_2', 'users.name');

    //     //Enkripsi kode pengajuan
    //     $c = $cek->get();
    //     $count = count($c);
    //     $data = $cek->paginate(10);
    //     for ($i = 0; $i < $count; $i++) {
    //         if ($data->isNotEmpty()) {
    //             $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
    //         }
    //     }
    //     // dd($data);
    //     return view('cetak.notifikasi-kredit.index', [
    //         'data' => $data
    //     ]);
    // }
}
