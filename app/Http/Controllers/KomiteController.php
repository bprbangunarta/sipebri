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
        } elseif ($user->role_name == 'Customer Service' || $user->role_name == 'Kepala Kantor Kas') {
            $cek = Midle::persetujuan_komite_cs_kksk($usr);
        }

        //Enkripsi kode pengajuan
        $c = $cek->get();
        $count = count($c);
        $data = $cek->paginate(7);
        $usul1 = "Staff Analis";
        $usul2 = "Kasi Analis";
        $usul3 = "Kabag Analis";
        $usul4 = "Direksi";
        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
            $item->usulan1 = Midle::data_usulan($item->kode_pengajuan, $usul1) ?? null;
            $item->usulan2 = Midle::data_usulan($item->kode_pengajuan, $usul2) ?? null;
            $item->usulan3 = Midle::data_usulan($item->kode_pengajuan, $usul3) ?? null;
            $item->usulan4 = Midle::data_usulan($item->kode_pengajuan, $usul4) ?? null;
        }


        if ($user->role_name == 'Customer Service') {
            $usul1 = "Customer Service";
            foreach ($data as $item) {
                $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
                $item->usulan1 = Midle::data_usulan($item->kode_pengajuan, $usul1) ?? null;
                $item->usulan2 = Midle::data_usulan($item->kode_pengajuan, $usul2) ?? null;
                $item->usulan3 = Midle::data_usulan($item->kode_pengajuan, $usul3) ?? null;
                $item->usulan4 = Midle::data_usulan($item->kode_pengajuan, $usul4) ?? null;
            }
        } elseif ($user->role_name == 'Kepala Kantor Kas') {
            $usul1 = "Kepala Kantor Kas";
            foreach ($data as $item) {
                $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan) ?? null;
                $item->usulan1 = Midle::data_usulan($item->kode_pengajuan, $usul1) ?? null;
                $item->usulan2 = Midle::data_usulan($item->kode_pengajuan, $usul2) ?? null;
                $item->usulan3 = Midle::data_usulan($item->kode_pengajuan, $usul3) ?? null;
                $item->usulan4 = Midle::data_usulan($item->kode_pengajuan, $usul4) ?? null;
            }
        }

        $user = DB::table('v_users')->where('code_user', Auth::user()->code_user)->first('role_name');
        return view('komite.index', [
            'data' => $data,
            'user' => $user,
        ]);
    }

    public function getdata(Request $request)
    {
        $name = request('name');
        $kode = $request->input('field');
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->leftJoin('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('au_keuangan', 'data_pengajuan.kode_pengajuan', '=', 'au_keuangan.pengajuan_kode')
            ->where('data_pengajuan.kode_pengajuan', '=', $kode)

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.plafon',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.jangka_waktu',
                'data_pengajuan.metode_rps',
                'data_pengajuan.b_provisi',
                'data_pengajuan.b_admin',
                'data_pengajuan.metode_rps',
                'data_pengajuan.kategori',
                'data_nasabah.nama_nasabah',
                'a_memorandum.max_plafond',
                'au_keuangan.keuangan_perbulan',
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
            $kom = DB::table('a_komite')->where('pengajuan_kode', $request->kode_pengajuan)->first();
            $cek = Midle::analisa_usaha($request->kode_pengajuan);

            // //Data Tracking
            $trc = DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->first();
            if (!is_null($trc)) {
                $tracking = [
                    'keputusan_komite' => now(),
                ];

                DB::table('data_tracking')->where('pengajuan_kode', $request->kode_pengajuan)->update($tracking);
            }

            if ($user->role_name == 'Staff Analis' || $user->role_name == 'Customer Service' || $user->role_name == 'Kepala Kantor Kas') {
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


            if (is_null($kom)) {
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

            //Cek Data Usulan
            $du = DB::table('data_usulan')
                ->where('pengajuan_kode', $request->kode_pengajuan)
                ->where('input_user', $usr)
                ->get();

            $usulan = [
                'pengajuan_kode' => $request->kode_pengajuan,
                'role_name' => $user->role_name,
                'input_user' => Auth::user()->code_user,
                'metode_rps' => $request->metode_rps,
                'suku_bunga' => $request->suku_bunga,
                'b_provisi' => number_format($request->b_provisi, 2),
                'b_admin' => number_format($request->b_admin, 2),
                'rc' => (float) str_replace('%', '', $request->rc),
                'usulan_plafon' => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafon),
                'catatan' => $request->catatan,
                'created_at' => now(),
            ];

            if (count($du) == 0) {
                DB::table('data_usulan')->insert($usulan);
            } else {
                DB::table('data_usulan')->where('id', $du[0]->id)->update($usulan);
            }

            $capacity = ['rc' => (float) str_replace('%', '', $request->rc)];
            DB::transaction(function () use ($request, $data2, $usulan, $capacity) {
                DB::table('data_pengajuan')->where('kode_pengajuan', $request->kode_pengajuan)->update($data2);
                DB::table('a5c_capacity')->where('pengajuan_kode', $request->kode_pengajuan)->update($capacity);
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
            ->where('data_pengajuan.tracking', 'Penjadwalan')
            ->orWhere('data_pengajuan.tracking', 'Proses Survei')
            ->orWhere('data_pengajuan.tracking', 'Proses Analisa')
            ->orWhere('data_pengajuan.tracking', 'Persetujuan Komite')
            ->orWhere('data_pengajuan.tracking', 'Naik Kasi')
            ->orWhere('data_pengajuan.tracking', 'Naik Komite I')
            ->orWhere('data_pengajuan.tracking', 'Naik Komite II')
            ->where('data_pengajuan.on_current', '=', '0')
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
        // dd($data);

        foreach ($data as $item) {
            $item->kd_pengajuan = Crypt::encrypt($item->kode_pengajuan);
            $item->usulan1 = Midle::data_usulan($item->kode_pengajuan, $usul1) ?? null;
            $item->usulan2 = Midle::data_usulan($item->kode_pengajuan, $usul2) ?? null;
            $item->usulan3 = Midle::data_usulan($item->kode_pengajuan, $usul3) ?? null;
            $item->usulan4 = Midle::data_usulan($item->kode_pengajuan, $usul4) ?? null;
        }
        // for ($i = 0; $i < $count; $i++) {
        //     if ($data->isNotEmpty()) {
        //         $data[$i]->kd_pengajuan = Crypt::encrypt($data[$i]->kode_pengajuan) ?? null;
        //         $data[$i]->usulan1 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul1) ?? null;
        //         $data[$i]->usulan2 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul2) ?? null;
        //         $data[$i]->usulan3 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul3) ?? null;
        //         $data[$i]->usulan4 = Midle::data_usulan($data[$i]->kode_pengajuan, $usul4) ?? null;
        //     }
        // }

        return view('komite.survei_analisa', [
            'data' => $data,
        ]);
    }
}
