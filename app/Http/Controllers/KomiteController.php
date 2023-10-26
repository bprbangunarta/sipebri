<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Midle;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class KomiteController extends Controller
{
    public function index()
    {
        $usr = Auth::user()->code_user;
        $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();

        if ($user->role_name == 'Staff Analis') {
            $role = "Persetujuan Komite";
            $cek = Midle::persetujuan_komite_staff($usr, $role);
        } elseif ($user->role_name == 'Kasi Analis') {
            $role = "Naik Kasi";
            $cek = Midle::persetujuan_komite_kasi($usr, $role);
        } elseif ($user->role_name == 'Kabag Analis') {
            $role = "Komite 1";
            $cek = Midle::persetujuan_komite_kabag($role);
        } elseif ($user->role_name == 'Direksi') {
            $role = "Komite 2";
            $cek = Midle::persetujuan_komite_direksi($role);
        }

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
        return view('komite.index', [
            'data' => $data,
        ]);
    }

    public function getdata(Request $request)
    {
        $kode = $request->input('field');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)
            ->select('data_pengajuan.kode_pengajuan', 'data_pengajuan.plafon', 'data_nasabah.nama_nasabah')->get();

        //User
        $usr = Auth::user()->code_user;
        $user = DB::table('v_users')->where('code_user', $usr)->get();
        $cek[0]->role_user = $user[0]->role_name;
        return response()->json($cek);
    }

    public function simpan(Request $request)
    {

        try {
            $usr = Auth::user()->code_user;
            $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();
            $cek = DB::table('a_komite')->where('pengajuan_kode', $request->kode_pengajuan)->first();

            //Data Tracking
            $trc = DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->first();
            if (!is_null($trc)) {
                $tracking = [
                    'keputusan_komite' => now(),
                ];

                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($tracking);
            }

            if (!is_null($cek)) {
                return self::update($request);
            }

            if ($user->role_name == 'Staff Analis') {
                $komite = 'komite1';
                $catatan = 'catatan1';
                $waktu = 'waktu1';
            } elseif ($user->role_name == 'Kasi Analis') {
                $komite = 'komite2';
                $catatan = 'catatan2';
                $waktu = 'waktu2';
            } elseif ($user->role_name == 'Kabag Analis') {
                $komite = 'komite3';
                $catatan = 'catatan3';
                $waktu = 'waktu3';
            } elseif ($user->role_name == 'Direksi') {
                $komite = 'komite4';
                $catatan = 'catatan4';
                $waktu = 'waktu4';
            }

            $name = 'KMT';
            $length = 5;
            $kode = Midle::kode_komite($name, $length);
            $data = [
                'kode_analisa' => $kode,
                'pengajuan_kode' => $request->kode_pengajuan,
                $komite => $usr,
                $catatan => $request->catatan,
                $waktu => now(),
            ];

            // dd($request);

            if ($request->putusan_komite == 'Naik Kasi' || $request->putusan_komite == 'Naik Komite 1' || $request->putusan_komite == 'Naik Komite 2') {
                $data2 = [
                    'tracking' => ucwords($request->putusan_komite),
                    'updated_at' => now(),
                ];
            } elseif ($request->putusan_komite == 'Ditolak' || $request->putusan_komite == 'Disetujui' || $request->putusan_komite == 'Dibatalkan') {
                $data2 = [
                    'status' => ucwords($request->putusan_komite),
                    'tracking' => "Selesai",
                    'updated_at' => now(),
                ];
            } else {
                $data2 = [
                    'status' => ucwords($request->putusan_komite),
                    'updated_at' => now(),
                ];
            }

            DB::transaction(function () use ($data, $request, $data2) {
                $a = Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->get();
                Pengajuan::where('id', $a[0]->id)->update($data2);
                DB::table('a_komite')->insert($data);
            });
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (Throwable $th) {
            return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function update($request)
    {
        try {
            $usr = Auth::user()->code_user;
            $user = DB::table('v_users')->where('code_user', $usr)->select('role_name')->first();

            //Cek role user
            if ($user->role_name == 'Staff Analis') {
                $komite = 'komite1';
                $catatan = 'catatan1';
                $waktu = 'waktu1';
            } elseif ($user->role_name == 'Kasi Analis') {
                $komite = 'komite2';
                $catatan = 'catatan2';
                $waktu = 'waktu2';
            } elseif ($user->role_name == 'Kabag Analis') {
                $komite = 'komite3';
                $catatan = 'catatan3';
                $waktu = 'waktu3';
            } elseif ($user->role_name == 'Direksi') {
                $komite = 'komite4';
                $catatan = 'catatan4';
                $waktu = 'waktu4';
            }

            $data = [
                $komite => $usr,
                $catatan => $request->catatan,
                $waktu => now(),
            ];

            if ($request->putusan_komite == 'Naik Kasi' || $request->putusan_komite == 'Naik Komite 1' || $request->putusan_komite == 'Naik Komite 2') {
                $data2 = [
                    'tracking' => ucwords($request->putusan_komite),
                    'updated_at' => now(),
                ];
            } else {
                $data2 = [
                    'status' => ucwords($request->putusan_komite),
                    'updated_at' => now(),
                ];
            }
            // dd($data2);
            DB::transaction(function () use ($data, $request, $data2) {
                Pengajuan::where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
                $b = DB::table('a_komite')->where('pengajuan_kode', $request->kode_pengajuan)->get();
                DB::table('a_komite')->where('id', $b[0]->id)->update($data);
            });
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (Throwable $th) {
            // return redirect()->back()->with('error', 'Gagal menambahkan data');
        }
    }

    public function catatan($pengajuan)
    {
        $data = DB::table('a_komite')->where('pengajuan_kode', $pengajuan)->first();
        return response()->json($data);
    }
}
