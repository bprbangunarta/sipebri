<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class NotifikasiController extends Controller
{
    public function data_penolakan()
    {
        $usr = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->where(function ($query) use ($usr) {
                $query->where('data_survei.surveyor_kode', '=', $usr)
                    ->where('data_pengajuan.status', '=', 'Ditolak')
                    ->where('data_pengajuan.on_current', '=', '0');
            })
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
            $cek = DB::table('data_penolakan')->where('pengajuan_kode', $enc)->first();

            if ($cek) {
                return back()->with('error', "Anda Sudah Memiliki Nomor Penolakan");
            }

            $lasts = DB::table('data_penolakan')->latest('nomor')->first();
            if (is_null($lasts)) {
                $count = 0000;
            } else {
                $count = (int) $lasts->nomor + 1;
            }
            $lengths = 4;
            $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);

            $now = Carbon::now();
            $bulan = $now->month;
            $romawi = Data::romawi($bulan);
            // 0000 / 03 / KABAG . ANALIS / PBA / XI / 2023
            $tolak = $kodes . '/' . '03' . '/' . 'KABAG.ANALIS' . '/' . $romawi . '/' . $now->year;

            $data = [
                'nomor' => $kodes,
                'no_penolakan' => $tolak,
                'pengajuan_kode' => $enc,
                'alasan' => $request->alasan,
                'keterangan' => $request->keterangan,
                'input_usr' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $data['keterangan'] = strip_tags($data['keterangan']);
            // dd($data);

            $data2['selesai'] = now();
            $data3['tracking'] = 'Selesai';
            DB::transaction(function () use ($data, $data2, $enc, $data3) {
                DB::table('data_penolakan')->insert($data);
                DB::table('data_pengajuan')->where('kode_pengajuan', $enc)->update($data3);
                DB::table('data_tracking')->where('pengajuan_kode', $enc)->update($data2);
            });

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
