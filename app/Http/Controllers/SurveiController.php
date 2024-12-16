<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\CGC;
use App\Models\User;
use App\Models\Midle;
use App\Models\Kantor;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SurveiController extends Controller
{
    public function edit(Request $request)
    {
        $req = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);
            //Data pengajuan
            $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->first();
            $cek = Nasabah::where('kode_nasabah', $pengajuan->nasabah_kode)->first();

            $cek->kode_pengajuan = $pengajuan->kode_pengajuan;

            //Data survey
            $survey = Survei::where('pengajuan_kode', $cek->kode_pengajuan)->first();
            //Data kantor
            $ktr = Kantor::where('kode_kantor', $survey->kantor_kode)->first();

            //inisialisasi variable ketika data null
            if (is_null($ktr)) {
                $survey->nama_kantor = "";
            } else {
                $survey->nama_kantor = $ktr->nama_kantor;
            }

            //Data kasi ambil
            $ks = DB::table('v_users')
                ->select('nama_user')
                ->where('code_user', $survey->kasi_kode)->first();
            if (is_null($ks)) {
                $survey->nama_kasi = null;
            } else {
                $survey->nama_kasi = $ks->nama_user;
            }

            //Data surveyor
            $st = DB::table('v_users')
                ->select('nama_user')
                ->where('code_user', $survey->surveyor_kode)->first();
            if (is_null($st)) {
                $survey->nama_surveyor = null;
            } else {
                $survey->nama_surveyor = $st->nama_user;
            }

            //Inisialisasi data        
            $survey->tabungan_cgc = $pengajuan->tabungan_cgc;

            //Data kasi
            $kasi = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Kasi Analis')->get();

            //Data Staff Analis
            $staff = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Staff Analis')->get();

            //Data KKPK
            $kantor_user = Auth::user()->kantor_kode;
            $kkpk = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Kepala Kantor Kas')
                ->where('kantor_kode', '=', $kantor_user)
                ->where('is_active', '=', 1)
                ->get();

            //validasi
            if (count($kkpk) == 0) {
                $kkpk = collect([
                    (object) ['code_user' => null, 'nama' => null]
                ]);
            }
            //Data CS
            $cs = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Customer Service')
                ->where('kantor_kode', '=', $kantor_user)
                ->get();

            // dd($cs);
            //validasi
            if (count($cs) === 0) {
                $cs = collect([
                    (object) ['code_user' => null, 'nama' => null]
                ]);
            }

            $kantor = Kantor::all();

            //Data Auth
            $us = Auth::user()->id;
            $user = DB::table('users')
                ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.code_user')
                ->where('users.id', '=', $us)->get();


            $cek->auth = $user[0]->code_user;
            $cek->kd_pengajuan = $req;
            $dt = Midle::analisa_usaha($enc);
            $cek->plafon = $dt[0]->plafon;
            $cek->jangka_waktu = $dt[0]->jangka_waktu;
            $cek->produk = $pengajuan->produk_kode;

            return view('pengajuan.data-surveyor', [
                'data' => $cek,
                'kasi' => $kasi,
                'staff' => $staff,
                'survey' => $survey,
                'kkpk' => $kkpk[0],
                'cs' => $cs,
                'kantor' => $kantor,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update(Request $request)
    {
        $cek = $request->validate([
            'kantor_kode' => 'required',
            'kasi_kode' => 'required',
            'surveyor_kode' => 'required',
            'input_user' => 'required',
        ]);

        $cek['is_entry'] = 1;
        $cek['otorisasi'] = 'N';
        // dd($cek);
        $kode_pengajuan = $request->pengajuan_kode;

        if ($cek) {
            Survei::where('pengajuan_kode', $kode_pengajuan)->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->back()->with('error', 'Data gagal diupdate');
        }
    }

    public function hasil_survei()
    {
        try {
            $keyword = request('keyword');
            $tgl1 = request('tgl1');
            $tgl2 = request('tgl2');

            if (is_null($tgl2)) {
                $tgl2 = $tgl1;
            }

            $data = DB::table('data_survei')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->leftJoin('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
                ->select(
                    'data_pengajuan.created_at as tanggal',
                    'data_pengajuan.kode_pengajuan',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.tracking',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'v_users.nama_user',
                    'data_survei.surveyor_kode',
                    'data_survei.latitude',
                    'data_survei.longitude',
                    DB::raw("DATE_FORMAT(data_survei.tgl_survei, '%d-%m-%y') as tgl_survei"),
                    'data_survei.catatan_survei',
                    DB::raw("DATE_FORMAT(data_survei.tgl_jadul_1, '%d-%m-%y') as tgl_jadul_1"),
                    'data_survei.catatan_jadul_1',
                    DB::raw("DATE_FORMAT(data_survei.tgl_jadul_2, '%d-%m-%y') as tgl_jadul_2"),
                    'data_survei.catatan_jadul_2',
                )
                ->whereNot('data_pengajuan.produk_kode', 'KTA')
                ->where('data_survei.kasi_kode', '!=', '')
                ->where(function ($query) use ($keyword) {
                    if (!empty($keyword)) {
                        $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                            ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                            ->orWhere('data_pengajuan.created_at', 'like', '%' . $keyword . '%')
                            ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                            ->orWhere('data_pengajuan.tracking', 'like', '%' . $keyword . '%')
                            ->orWhere('v_users.code_user', 'like', '%' . $keyword . '%')
                            ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                            ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%');
                    }
                });

            if (!empty($tgl1) || !empty($tgl2)) {
                $tgl2 = $tgl2 ?: $tgl1;

                $data->where(function ($query) use ($tgl1, $tgl2) {
                    $query->whereBetween(
                        'data_tracking.proses_survey',
                        [
                            Carbon::createFromFormat('Y-m-d', $tgl1)->startOfDay()->toDateTimeString(),
                            Carbon::createFromFormat('Y-m-d', $tgl2)->endOfDay()->toDateTimeString()
                        ]
                    );
                });
            } elseif (empty($tgl1) && empty($tgl2) && empty($keyword)) {
                $data->where(function ($query) use ($keyword) {
                    $query->where(function ($subQuery) {
                        $currentMonth = Carbon::now()->format('Y-m');
                        $previousMonth = Carbon::now()->subMonth()->format('Y-m');

                        $subQuery->whereRaw("DATE_FORMAT(data_tracking.proses_survey, '%Y-%m') = ?", [$currentMonth])
                            ->orWhereRaw("DATE_FORMAT(data_tracking.proses_survey, '%Y-%m') = ?", [$previousMonth]);
                    });
                });
            }

            $data->orderBy('data_survei.created_at', 'DESC');
            $datas = $data->paginate(10);
            //

            return view('analisa.hasil-survei.index', compact('datas'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditampilkan');
        }
    }

    public function pelaksanaan_survei()
    {
        try {
            $keyword = request('keyword');

            $data = DB::table('data_survei')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode',)
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
                ->select(
                    'data_pengajuan.created_at as tanggal',
                    'data_pengajuan.kode_pengajuan',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.tracking',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_survei.kantor_kode',
                    'v_users.nama_user',
                    'data_survei.surveyor_kode',
                    'data_survei.latitude',
                    'data_survei.longitude',
                    'data_survei.foto',
                    DB::raw("DATE_FORMAT(data_survei.tgl_survei, '%d-%m-%y') as tgl_survei"),
                    'data_survei.catatan_survei',
                    DB::raw("DATE_FORMAT(data_survei.tgl_jadul_1, '%d-%m-%y') as tgl_jadul_1"),
                    'data_survei.catatan_jadul_1',
                    DB::raw("DATE_FORMAT(data_survei.tgl_jadul_2, '%d-%m-%y') as tgl_jadul_2"),
                    'data_survei.catatan_jadul_2',
                )
                ->whereNot('data_pengajuan.produk_kode', 'KTA')
                ->where('data_survei.kasi_kode', '!=', '')
                ->whereNull('data_survei.foto')
                ->where(function ($query) {
                    $query->whereRaw("DATE(data_survei.tgl_survei) = ?", [Carbon::today()->toDateString()])
                        ->orWhereRaw("DATE(data_survei.tgl_jadul_1) = ?", [Carbon::today()->toDateString()])
                        ->orWhereRaw("DATE(data_survei.tgl_jadul_2) = ?", [Carbon::today()->toDateString()]);
                })
                ->where(function ($query) use ($keyword) {
                    $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                        ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                        ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $keyword . '%')
                        ->orWhere('v_users.code_user', 'like', '%' . $keyword . '%')
                        ->orWhere('v_users.nama_user', 'like', '%' . $keyword . '%')
                        ->orWhere('data_survei.kantor_kode', 'like', '%' . $keyword . '%');
                })
                ->orderBy('data_survei.created_at', 'DESC')
                ->paginate(10);

            foreach ($data as $item) {
                if (
                    $item->tracking === 'Proses Survei' &&
                    is_null($item->foto) &&
                    (
                        (Carbon::createFromFormat('d-m-y', $item->tgl_survei)->isToday() && is_null($item->catatan_survei)) ||

                        (!is_null($item->tgl_jadul_1) &&  Carbon::createFromFormat('d-m-y', $item->tgl_jadul_1)->isToday() && is_null($item->catatan_jadul_1)) ||

                        (!is_null($item->tgl_jadul_2) && Carbon::createFromFormat('d-m-y', $item->tgl_jadul_2)->isToday() && is_null($item->catatan_jadul_2))
                    )
                ) {
                    $item->ket = 'Progress';
                } elseif ($item->tracking == 'Penjadwalan' && is_null($item->foto)) {
                    $item->ket = 'Pending';
                } elseif ($item->tracking == 'Proses Analisa') {
                    $item->ket = 'Success';
                } else {
                    $item->ket = 'Next Survei';
                }

                if (Carbon::createFromFormat('d-m-y', $item->tgl_survei)->format('Y-m-d') == Carbon::today()->toDateString()) {
                    $item->catatan = $item->catatan_survei;
                } elseif (Carbon::createFromFormat('d-m-y', $item->tgl_jadul_1)->format('Y-m-d') == Carbon::today()->toDateString()) {
                    $item->catatan = $item->catatan_jadul_1;
                } elseif (Carbon::createFromFormat('d-m-y', $item->tgl_jadul_2)->format('Y-m-d') == Carbon::today()->toDateString()) {
                    $item->catatan = $item->catatan_jadul_2;
                } else {
                    $item->catatan = null;
                }
            }

            $tgl = now()->format('d F Y');

            // Mendapatkan Jumlah
            $surveyorCounts = (object) [];
            foreach ($data as $item) {
                $kode = $item->surveyor_kode;

                if (!isset($surveyorCounts->$kode)) {
                    $surveyorCounts->$kode = [
                        'count' => 0,
                        'name' => null,
                    ];
                }

                $surveyorCounts->$kode['count']++;
            }

            foreach ($surveyorCounts as $kode => &$info) {
                $info['name'] = User::where('code_user', $kode)->pluck('name')->first();
            }

            $finalResult = [];
            foreach ($surveyorCounts as $value) {
                $finalResult[] = (object) [
                    'name' => $value['name'],
                    'count' => $value['count'],
                ];
            }

            if (!empty($finalResult)) {
                $countUser = (object) $finalResult;
            } else {
                $countUser = [];
            }

            return view('analisa.pelaksanaan.index', compact('data', 'tgl', 'surveyorCounts', 'countUser'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditampilkan');
        }
    }
}
