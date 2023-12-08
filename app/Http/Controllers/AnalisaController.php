<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Survei;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaController extends Controller
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

            ->where(function ($query) use ($user) {
                $query->where('data_survei.surveyor_kode', $user)
                    ->where(function ($subquery) {
                        $subquery->where('data_pengajuan.tracking', 'Proses Survei')
                            ->orWhere('data_pengajuan.tracking', 'Proses Analisa')
                            ->orWhere('data_pengajuan.tracking', 'Persetujuan Komite')
                            ->orWhere('data_pengajuan.tracking', 'Naik Kasi')
                            ->orWhere('data_pengajuan.tracking', 'Naik Komite I')
                            ->orWhere('data_pengajuan.tracking', 'Naik Komite II')
                            ->orWhere('data_pengajuan.status', 'Disetujui');
                    });
            })

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
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
        // dd($data);
        return view('staff.analisa.index', [
            'data' => $data
        ]);
    }

    public function data_jadul($pengajuan)
    {
        $data = DB::table('data_survei')
            ->leftJoin('data_pengajuan', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->select('data_pengajuan.kode_pengajuan', 'data_nasabah.nama_nasabah', 'data_survei.*')
            ->where('data_survei.pengajuan_kode', '=', $pengajuan)->get();
        //
        $arr = [
            'tgl_survei' => $data[0]->tgl_survei,
            'tgl_jadul_1' => $data[0]->tgl_jadul_1,
            'tgl_jadul_2' => $data[0]->tgl_jadul_2,
            'tgl_resurvei' => $data[0]->tgl_resurvei,
            'tgl_resurvei_1' => $data[0]->tgl_resurvei_1,
            'tgl_resurvei_2' => $data[0]->tgl_resurvei_2,
        ];

        //Filter data yang null
        $filteredArray = array_filter($arr, function ($value) {
            return $value !== "-" && !is_null($value);
        });

        //Amibl yang terbaru dengan cara mengambil dari indexnya
        $latestIndex = min(array_filter($filteredArray));
        $data[0]->tgl_survei = $latestIndex;
        return response()->json($data[0]);
    }

    public function simpanjadul(Request $request)
    {

        try {
            $survei = Survei::where('id', $request->id)->first();

            //Cek tanggal 
            $data = [
                'catatan' => $request->keterangan,
                'updated_at' => now(),
            ];
            if ($request->tgl_survei === $survei->tgl_survei) {
                $data = [
                    'catatan_survei' => $request->keterangan,
                    'updated_at' => now(),
                ];

                $data2 = [
                    'tracking' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                Survei::where('id', $request->id)->update($data);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
            } elseif ($request->tgl_survei === $survei->tgl_jadul_1) {
                $data = [
                    'catatan_jadul_1' => $request->keterangan,
                    'updated_at' => now(),
                ];
                $data2 = [
                    'tracking' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                Survei::where('id', $request->id)->update($data);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
            } elseif ($request->tgl_survei === $survei->tgl_jadul_2) {
                $data = [
                    'catatan_jadul_2' => $request->keterangan,
                    'updated_at' => now(),
                ];
                $data2 = [
                    'tracking' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                Survei::where('id', $request->id)->update($data);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
            } elseif ($request->tgl_survei === $survei->tgl_resurvei) {
                $data = [
                    'catatan_resurvei' => $request->keterangan,
                    'updated_at' => now(),
                ];
                $data2 = [
                    'tracking' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                Survei::where('id', $request->id)->update($data);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
            } elseif ($request->tgl_survei === $survei->tgl_resurvei_1) {
                $data = [
                    'catatan_resurvei_1' => $request->keterangan,
                    'updated_at' => now(),
                ];
                $data2 = [
                    'tracking' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                Survei::where('id', $request->id)->update($data);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
            } elseif ($request->tgl_survei === $survei->tgl_resurvei_2) {
                $data = [
                    'catatan_resurvei_2' => $request->keterangan,
                    'updated_at' => now(),
                ];
                $data2 = [
                    'tracking' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                Survei::where('id', $request->id)->update($data);
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
            }
            return redirect()->back()->with('success', 'Anda berhasil melakukan pembatalan survei');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Anda gagal melakukan pembatalan survei');
        }
    }

    public function get_data_penolakan($kode)
    {
        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
            )
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)
            ->get();
        return response()->json($data);
    }

    public function simpan_penolakan(Request $request)
    {
        try {
            $user = Auth::user()->code_user;
            $data = [
                'status' => $request->status,
            ];
            // dd($request, $data, $user);
            Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data);
            return redirect()->back()->with('success', 'Anda berhasil melakukan perubahan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Gagal melakukan perubahan');
        }
    }
}
