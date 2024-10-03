<?php

namespace App\Http\Controllers\Administratif;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Midle;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class DenahLokasiController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $user = Auth::user()->code_user;
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')

            ->where('data_pengajuan.on_current', '0')
            ->whereNotNull('data_survei.foto')

            ->where(function ($query) use ($user) {
                $query->where('data_survei.surveyor_kode', $user)
                    ->orWhere('data_survei.kasi_kode', $user)
                    ->where(function ($subquery) {
                        $subquery->where('data_pengajuan.tracking', 'Proses Analisa')
                            ->orWhere('data_pengajuan.tracking', 'Persetujuan Komite')
                            ->orWhere('data_pengajuan.tracking', 'Naik Kasi')
                            ->orWhere('data_pengajuan.tracking', 'Naik Komite I')
                            ->orWhere('data_pengajuan.tracking', 'Naik Komite II')
                            ->orWhere('data_pengajuan.tracking', 'Realisasi')
                            ->orWhere('data_pengajuan.status', 'Disetujui');
                    });
            })

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_survei.pengajuan_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                    ->orWhere('users.code_user', 'like', '%' . $keyword . '%')
                    ->orWhere('users.name', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $keyword . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $keyword . '%');
            })

            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
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
                'users.name',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.jangka_waktu as jk',
                'data_pengajuan.created_at as tgl_daftar'
            )
            ->orderBy('data_tracking.proses_survey', 'desc');;

        //Enkripsi kode pengajuan
        $data = $cek->paginate(10);
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan);
        }

        return view('administratif.denah-lokasi.index', [
            'data' => $data
        ]);
    }

    public function data_lokasi(Request $request, $kode)
    {
        try {
            $enc = Crypt::decrypt($kode);
            $user = Auth::user()->code_user;
            $data = DB::table('data_survei')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', 'data_survei.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', 'data_pengajuan.nasabah_kode')
                ->leftJoin('v_users', 'v_users.code_user', 'data_survei.surveyor_kode')

                ->where(function ($query) use ($user) {
                    $query->where('data_survei.surveyor_kode', $user)
                        ->where(function ($subquery) {
                            $subquery->orWhere('data_pengajuan.status', 'Disetujui')
                                ->orWhere('data_pengajuan.tracking', 'Persetujuan Komite')
                                ->orWhere('data_pengajuan.tracking', 'Proses Analisa')
                                ->orWhere('data_pengajuan.tracking', 'Naik Kasi')
                                ->orWhere('data_pengajuan.tracking', 'Naik Komite I')
                                ->orWhere('data_pengajuan.tracking', 'Naik Komite II')
                                ->orWhere('data_pengajuan.tracking', 'Realisasi');
                        });
                })

                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.no_identitas',
                    'data_nasabah.alamat_ktp',
                    'data_survei.*',
                    'v_users.nama_user',
                )
                ->where('data_survei.pengajuan_kode', $enc)
                ->get();

            $usaha_pertanian = DB::table('au_pertanian')->where('pengajuan_kode', $enc)->get();
            $usaha_perdagangan = DB::table('au_perdagangan')->where('pengajuan_kode', $enc)->get();
            $usaha_jasa = DB::table('au_jasa')->where('pengajuan_kode', $enc)->get();
            $usaha_lain = DB::table('au_lainnya')->where('pengajuan_kode', $enc)->get();

            if (count($data) != 0) {
                if (!is_null($data[0]->latitude) || !is_null($data[0]->longitude)) {
                    // $qr_lokasi_rumah = Midle::get_qrcode_denah('Lokasi_Rumah', $data[0]->no_identitas, $data[0]->nama_nasabah, $data[0]->latitude, $data[0]->longitude);
                } else {
                    $qr_lokasi_rumah = (object) [
                        'latitude' => null,
                        'longitude' => null,
                    ];
                }

                $lokasi_usaha = [];
                //Usaha Pertanian
                foreach ($usaha_pertanian as $pertanian) {
                    $lokasi_usaha[] = $pertanian;
                }
                //Usaha Perdagangan
                foreach ($usaha_perdagangan as $perdagangan) {
                    $lokasi_usaha[] = $perdagangan;
                }
                //Usaha Jasa
                foreach ($usaha_jasa as $jasa) {
                    $lokasi_usaha[] = $jasa;
                }
                //Usaha Lain
                foreach ($usaha_lain as $lain) {
                    $lokasi_usaha[] = $lain;
                }
            } else {
                return redirect()->back()->with('error', 'Data Tidak Ditemukan');
            }
            // dd($lokasi_usaha);
            return view('administratif.denah-lokasi.lokasi', [
                'data' => $data[0],
                // 'qr_lokasi_rumah' => $qr_lokasi_rumah,
                // 'lokasi_usaha' => $lokasi_usaha,
            ]);
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function qrcode(Request $request) {}
}
