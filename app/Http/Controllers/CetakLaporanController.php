<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class CetakLaporanController extends Controller
{
    public function laporan_fasilitas()
    {
        $name = request('name');
        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where('data_pengajuan.status', 'Disetujui')
            ->where(function ($query) use ($name) {
                $query->orWhere('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*'
            )->orderBy('data_pengajuan.created_at', 'asc');

        $data = $query->paginate(7);

        return view('laporan.fasilitas', [
            'data' => $data,
        ]);
    }

    public function laporan_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->join('data_tracking', 'data_pengajuan.kode_pengajuan', '=', 'data_tracking.pengajuan_kode')
            // ->where(function ($query) {
            //     $query->where('data_pengajuan.status', '=', 'Disetujui')
            //         ->where('data_spk.pengajuan_kode', '!=', null);
            // })

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_tracking.pencairan_dana', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_tracking.*',
                'data_spk.*',
            )
            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);

        return view('laporan.realisasi', [
            'data' => $data,
        ]);
    }
    public function post_laporan_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->join('data_tracking', 'data_pengajuan.kode_pengajuan', '=', 'data_tracking.pengajuan_kode')
            // ->where(function ($query) {
            //     $query->where('data_pengajuan.status', '=', 'Disetujui')
            //         ->where('data_spk.pengajuan_kode', '!=', null);
            // })

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_tracking.pencairan_dana', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_tracking.*',
            )
            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);

        return view('laporan.realisasi', [
            'data' => $data,
        ]);
    }

    public function laporan_penolakan()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Dibatalkan')
                    ->orWhere('data_pengajuan.status', '=', 'Ditolak');
            })

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )->orderBy('data_pengajuan.created_at', 'asc');
        $data = $query->paginate(7);

        return view('laporan.penolakan', [
            'data' => $data,
        ]);
    }

    public function siap_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')
            ->where('data_pengajuan.on_current', '0')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_notifikasi.*',
                'data_survei.kantor_kode as wilayah',
                'users.name as surveyor',
            )

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);
        // dd($data);
        return view('laporan.siap-realisasi', [
            'data' => $data,
        ]);
    }
    public function post_siap_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->where('data_pengajuan.on_current', '0')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_notifikasi.*',
                'data_survei.kantor_kode as wilayah',
            )

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);
        // dd($data);
        return view('laporan.siap-realisasi', [
            'data' => $data,
        ]);
    }

    public function laporan_pendaftaran()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            // ->whereIn('data_pengajuan.status', ['Dibatalkan', 'Ditolak', 'Disetujui'])
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);
        // dd($data);
        return view('laporan.pendaftaran', [
            'data' => $data,
        ]);
    }
    public function post_laporan_pendaftaran(Request $request)
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            // ->whereIn('data_pengajuan.status', ['Dibatalkan', 'Ditolak', 'Disetujui'])
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->paginate(10);
        // dd($data);
        return view('laporan.pendaftaran', [
            'data' => $data,
        ]);
    }

    public function laporan_survey_analisa(Request $request)
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.tracking', [
                'Penjadwalan',
                'Proses Survei',
                'Proses Analisa',
                'Naik Kasi',
                'Naik Komite I',
                'Naik Komite II'
            ])

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )
            ->orderBy('data_pengajuan.created_at', 'asc');
        $data = $query->paginate(7);

        return view('laporan.survey-analisa', [
            'data' => $data,
        ]);
    }

    public function post_laporan_survey(Request $request)
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.tracking', [
                'Penjadwalan',
                'Proses Survei',
                'Proses Analisa',
                'Naik Kasi',
                'Naik Komite I',
                'Naik Komite II'
            ])

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )
            ->orderBy('data_pengajuan.created_at', 'asc');
        $data = $query->paginate(7);

        return view('laporan.survey-analisa', [
            'data' => $data,
        ]);
    }

    public function post_laporan_penolakan(Request $request)
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->where(function ($query) {
                $query->where('data_pengajuan.status', '=', 'Dibatalkan')
                    ->orWhere('data_pengajuan.status', '=', 'Ditolak');
            })

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
            )->orderBy('data_pengajuan.created_at', 'asc');
        $data = $query->paginate(7);

        return view('laporan.penolakan', [
            'data' => $data,
        ]);
    }


    public function laporan_notifikasi(Request $request)
    {
        $name = request('name');

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_survei.kantor_kode', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })

            ->select(
                'data_notifikasi.created_at as tgl_notifikasi',
                'data_pengajuan.kode_pengajuan',
                'data_notifikasi.no_notifikasi',
                'data_nasabah.nama_nasabah',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'users.name as surveyor',
            )
            ->orderBy('data_pengajuan.created_at', 'desc');

        $data = $query->paginate(10);
        return view('laporan.rekap-notifikasi', [
            'data' => $data,
        ]);
    }
}
