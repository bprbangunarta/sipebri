<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class NotifikasiController extends Controller
{
    public function data_penolakan()
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->where('data_pengajuan.status', '=', 'Ditolak')
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.plafon',
                'data_pengajuan.updated_at',
                'data_pengajuan.kategori',
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
                'data_pengajuan.created_at as tgl_daftar',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.jangka_waktu as jk'
            );
        //

        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
            }
        }
        // dd($data);
        return view('notifikasi.penolakan.index', [
            'data' => $data,
        ]);
    }

    public function tambah_penolakan()
    {
        return view('notifikasi.penolakan.tambah');
    }

    public function edit_penolakan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            // dd($enc);
            return view('notifikasi.penolakan.edit', [
                'data' => $request->query('pengajuan'),
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_penolakan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->kd_pengajuan);
            $data = [
                'alasan' => $request->alasan,
                'keterangan' => $request->keterangan,
            ];
            $data['keterangan'] = strip_tags($data['keterangan']);
            DB::table('data_penolakan')->where('pengajuan_kode', $enc)->insert($data);
            // dd($data);
            // $data2['selesai'] = now();
            // $data3['tracking'] = 'Selesai';
            // DB::transaction(function () use ($data, $data2, $enc, $data3) {
            //     DB::table('data_tracking')->where('pengajuan_kode', $enc)->update($data2);
            //     DB::table('data_pengajuan')->where('pengajuan_kode', $enc)->update($data3);
            // });

            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }



    public function cetak_penolakan()
    {
        return view('notifikasi.penolakan.cetak');
    }
}
