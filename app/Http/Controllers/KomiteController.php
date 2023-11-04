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
            $role = "Naik Komite I";
            $cek = Midle::persetujuan_komite_kabag($role);
        } elseif ($user->role_name == 'Direksi') {
            $role = "Naik Komite II";
            $cek = Midle::persetujuan_komite_direksi($role);
        }

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        $usul1 = "Staff Analis";
        $usul2 = "Kasi Analis";
        $usul3 = "Kabag Analis";
        $usul4 = "Direksi";
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
                $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
                $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
                $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
                $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
            }
        }

        $user = DB::table('v_users')->where('code_user', Auth::user()->code_user)->first('role_name');
        // dd($data);
        return view('komite.index', [
            'data' => $data,
            'user' => $user,
        ]);
    }

    public function getdata(Request $request)
    {
        $kode = $request->input('field');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            // ->leftJoin('data_usulan', 'data_pengajuan.kode_pengajuan', '=', 'data_usulan.pengajuan_kode')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.plafon',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.metode_rps',
                'data_pengajuan.b_provisi',
                'data_pengajuan.b_admin',
                'data_pengajuan.metode_rps',
                'data_nasabah.nama_nasabah',
                'a_memorandum.max_plafond',
            )->get();

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

            // //Data Tracking
            $trc = DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->first();
            if (!is_null($trc)) {
                $tracking = [
                    'keputusan_komite' => now(),
                ];

                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($tracking);
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


            if (is_null($cek)) {
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
                DB::table('a_komite')->insert($data);
            } else {
                $data = [
                    $komite => $usr,
                    $catatan => $request->catatan,
                    $waktu => now(),
                ];
                DB::table('a_komite')->where('pengajuan_kode', $request->kode_pengajuan)->update($data);
            }


            if ($request->putusan_komite == 'Naik Kasi' || $request->putusan_komite == 'Naik Komite I' || $request->putusan_komite == 'Naik Komite II') {
                $data2 = [
                    'plafon'  => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafon),
                    'tracking' => ucwords($request->putusan_komite),
                    'b_provisi' => number_format($request->b_provisi, 2),
                    'suku_bunga' => $request->suku_bunga,
                    'metode_rps' => $request->metode_rps,
                    'b_admin' => number_format($request->b_admin, 2),
                    'b_provisi' => number_format($request->b_provisi, 2),
                    'updated_at' => now(),
                ];
            } elseif ($request->putusan_komite == 'Ditolak' || $request->putusan_komite == 'Disetujui' || $request->putusan_komite == 'Dibatalkan') {
                $data2 = [
                    'plafon'  => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafon),
                    'tracking' => "Selesai",
                    'b_provisi' => number_format($request->b_provisi, 2),
                    'suku_bunga' => $request->suku_bunga,
                    'metode_rps' => $request->metode_rps,
                    'b_admin' => number_format($request->b_admin, 2),
                    'b_provisi' => number_format($request->b_provisi, 2),
                    'status' => ucwords($request->putusan_komite),
                    'updated_at' => now(),
                ];
            } else {
                $data2 = [
                    'b_provisi' => number_format($request->b_provisi, 2),
                    'suku_bunga' => $request->suku_bunga,
                    'metode_rps' => $request->metode_rps,
                    'b_admin' => number_format($request->b_admin, 2),
                    'b_provisi' => number_format($request->b_provisi, 2),
                    'plafon'  => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafon),
                    'tracking' => ucwords($request->putusan_komite),
                    'updated_at' => now(),
                ];
            }


            $usulan = [
                'pengajuan_kode' => $request->kode_pengajuan,
                'role_name' => $user->role_name,
                'input_user' => Auth::user()->code_user,
                'metode_rps' => $request->metode_rps,
                'suku_bunga' => $request->suku_bunga,
                'b_provisi' => number_format($request->b_provisi, 2),
                'b_admin' => number_format($request->b_admin, 2),
                'usulan_plafon' => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafon),
                'catatan' => $request->catatan,
                'created_at' => now(),
            ];

            DB::transaction(function () use ($request, $data2, $usulan) {
                DB::table('data_pengajuan')->where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
                DB::table('data_usulan')->insert($usulan);
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
        $data = DB::table('data_usulan')->where('pengajuan_kode', $pengajuan)->get();
        return response()->json($data);
    }

    public function survei_analisa()
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->where('data_pengajuan.tracking', '=', "Persetujuan Komite")
            ->orWhere('data_pengajuan.status', '=', "Disetujui")
            ->orWhere('data_pengajuan.tracking', '=', "Naik Kasi")
            ->orWhere('data_pengajuan.tracking', '=', "Naik Komite I")
            ->orWhere('data_pengajuan.tracking', '=', "Naik Komite II")
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.metode_rps',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_pengajuan.jangka_waktu as jk',
                'data_pengajuan.created_at',
                'data_pengajuan.kategori',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name'
            );
        //
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(10);
        $usul1 = "Staff Analis";
        $usul2 = "Kasi Analis";
        $usul3 = "Kabag Analis";
        $usul4 = "Direksi";
        for ($i = 0; $i < $count; $i++) {
            if ($data->isNotEmpty()) {
                $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan);
                $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1);
                $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2);
                $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3);
                $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4);
            }
        }

        return view('komite.survei_analisa', [
            'data' => $data,
        ]);
    }
}
