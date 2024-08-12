<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RSC extends Model
{
    use HasFactory;

    protected static function get_data_rsc($enc_rsc)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.jangka_waktu',
            )
            // ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            // ->paginate(10);
            ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->get();
        //

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
                    'm_loan.plafond_awal',
                    'm_cif.alamat',
                    'm_loan.jkwaktu',
                    'setup_loan.ket',
                    'wilayah.ket as wil',
                )
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                $value->jangka_waktu = $data_eks->jkwaktu;
                $value->metode_rps = null;
                $value->plafon = $data_eks->plafond_awal;
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

        return $data;
    }

    protected static function get_data_pertanian_all_rsc($enc_rsc)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.jangka_waktu',
            )
            ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)->get();
        //

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
                    'm_loan.plafond_awal',
                    'm_cif.alamat',
                    'm_loan.jkwaktu',
                    'setup_loan.ket',
                    'wilayah.ket as wil',
                )
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                $value->jangka_waktu = $data_eks->jkwaktu;
                $value->metode_rps = null;
                $value->plafon = $data_eks->plafond_awal;
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }
        return $data;
    }

    protected static function get_data_pertanian_rsc($data)
    {
        $data =
            DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.jangka_waktu',
            )
            ->where('rsc_data_pengajuan.pengajuan_kode', $data)
            ->get();
        //
        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
                    'm_loan.plafond_awal',
                    'm_cif.alamat',
                    'm_loan.jkwaktu',
                    'setup_loan.ket',
                    'wilayah.ket as wil',
                )
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                $value->jangka_waktu = $data_eks->jkwaktu;
                $value->metode_rps = null;
                $value->plafon = $data_eks->plafond_awal;
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }
        return $data;
    }

    protected static function get_data_jasa_all_rsc($enc_rsc)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.jangka_waktu',
            )
            ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)
            ->get();

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
                    'm_loan.plafond_awal',
                    'm_cif.alamat',
                    'm_loan.jkwaktu',
                    'setup_loan.ket',
                    'wilayah.ket as wil',
                )
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                $value->jangka_waktu = $data_eks->jkwaktu;
                $value->metode_rps = null;
                $value->plafon = $data_eks->plafond_awal;
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }
        return $data;
    }

    protected static function get_data_persetujuan($enc_rsc)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_agunan', 'rsc_agunan.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('v_users as kasi', 'kasi.code_user', '=', 'rsc_data_pengajuan.kasi_kode')
            ->leftJoin('v_users as surveyor', 'surveyor.code_user', '=', 'rsc_data_pengajuan.surveyor_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.jangka_waktu',
                'rsc_data_pengajuan.tunggakan_poko',
                'rsc_data_pengajuan.tunggakan_bunga',
                'rsc_data_pengajuan.tunggakan_denda',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.total_tunggakan',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'kasi.nama_user as kasi',
                'surveyor.nama_user as surveyor',
                'rsc_agunan.posisi_agunan',
                'rsc_agunan.kondisi_agunan',
                'rsc_agunan.nilai_taksasi',
            )
            ->where('rsc_data_pengajuan.kode_rsc', $enc_rsc)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->get();
        //
        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                ->select(
                    'm_loan.fnama',
                    'm_loan.plafond_awal',
                    'm_cif.alamat',
                    'm_loan.jkwaktu',
                    'setup_loan.ket',
                    'wilayah.ket as wil',
                )
                ->where('noacc', $value->kode_pengajuan)->first();
            //
            if ($data_eks) {
                $value->nama_nasabah = trim($data_eks->fnama);
                $value->alamat_ktp = trim($data_eks->alamat);
                $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
            }
        }

        return $data;
    }

    protected static function persetujuan_rsc_staff($keyword, $keyword_sqlsrv)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
                'data_pengajuan.plafon',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Proses Persetujuan')
            ->where('rsc_data_survei.surveyor_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');
        //

        return $data;
    }

    protected static function persetujuan_rsc_kasi($keyword, $keyword_sqlsrv)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Naik Kasi')
            ->where('rsc_data_survei.kasi_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        return $data;
    }

    protected static function persetujuan_rsc_kabag_analis($keyword, $keyword_sqlsrv)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Komite I')
            ->where('rsc_data_survei.kabag_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        return $data;
    }

    protected static function persetujuan_rsc_direktur_bisnis($keyword, $keyword_sqlsrv)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Komite II')
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        return $data;
    }

    protected static function persetujuan_rsc_direksi($keyword, $keyword_sqlsrv)
    {
        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
            )

            ->where(function ($query) use ($keyword, $keyword_sqlsrv) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere(function ($subquery) use ($keyword_sqlsrv) {
                        if ($keyword_sqlsrv) {
                            $subquery->where('rsc_data_pengajuan.pengajuan_kode', 'like', '%' . trim($keyword_sqlsrv->noacc) . '%');
                        }
                    })
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Komite III')
            ->where('rsc_data_survei.direksi_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        return $data;
    }

    protected static function catatan($data)
    {
        $roles = [
            'Staff Analis',
            'Kasi Analis',
            'Kabag Analis',
            'Direktur Bisnis',
            'Direksi'
        ];

        $catatan = [];

        foreach ($roles as $role) {
            $item = null;

            foreach ($data as $datum) {
                if ($datum->role_name === $role) {
                    $item = $datum;
                    break;
                }
            }

            if ($item) {
                $catatan[$role] = "\n" . 'METODE : ' . $item->metode_rps . ' - ' . ' SUKU BUNGA : ' .
                    $item->suku_bunga . '%' . ' - ' . 'JANGKA WAKTU : ' . $item->jangka_waktu . ' - '
                    . 'JANGKA POKOK : ' . $item->jangka_pokok . ' - ' . 'JANGKA BUNGA : ' . $item->jangka_bunga .
                    "\n" . 'RC : ' . $item->rc . '%' . ' - ' . 'ANGSURAN POKOK : ' . 'Rp. ' . number_format($item->angsuran_pokok, 0, ',', '.')  .
                    ' - ' . 'ANGSURAN BUNGA : ' . 'Rp. ' . number_format($item->angsuran_bunga, 0, ',', '.') . ' - ' . 'TOTAL ANGSURAN : ' . 'Rp. ' . number_format($item->total_angsuran, 0, ',', '.') .
                    "\n" . 'CATATAN : ' . ($item->catatan ?? "TIDAK ADA CATATAN");
            } else {
                $catatan[$role] = "\n" . 'TIDAK ADA CATATAN';
            }
        }

        return $catatan;
    }


    // protected static function catatan($data)
    // {
    //     if (isset($data[0])) {
    //         $catatan1 = [
    //             'catatan_staff_analisa' => "\n" . 'METODE : ' . $data[0]->metode_rps . ' - ' . ' SUKU BUNGA : ' .
    //                 $data[0]->suku_bunga . '%' . ' - ' . 'JANGKA WAKTU : ' . $data[0]->jangka_waktu . ' - '
    //                 . 'JANGKA POKOK : ' . $data[0]->jangka_pokok . ' - ' . 'JANGKA BUNGA : ' . $data[0]->jangka_bunga .
    //                 "\n" . 'RC : ' . $data[0]->rc . '%' . ' - ' . 'ANGSURAN POKOK : ' . 'Rp. ' . number_format($data[0]->angsuran_pokok, '0', ',', '.')  .
    //                 ' - ' . 'ANGSURAN BUNGA : ' . 'Rp. ' . number_format($data[0]->angsuran_bunga, '0', ',', '.') . ' - ' . 'TOTAL ANGSURAN : ' . 'Rp. ' . number_format($data[0]->total_angsuran, '0', ',', '.') .
    //                 "\n" . 'CATATAN : ' . $data[0]->catatan ?? "TIDAK ADA CATATAN",
    //         ];
    //     } else {
    //         $catatan1 = ['catatan_staff_analisa' => "\n" . 'TIDAK ADA CATATAN'];
    //     }

    //     if (isset($data[1])) {
    //         $catatan2 = [
    //             'catatan_kasi_analisa' => "\n" . 'METODE : ' . $data[1]->metode_rps . ' - ' . ' SUKU BUNGA : ' .
    //                 $data[1]->suku_bunga . '%' . ' - ' . 'JANGKA WAKTU : ' . $data[1]->jangka_waktu . ' - '
    //                 . 'JANGKA POKOK : ' . $data[1]->jangka_pokok . ' - ' . 'JANGKA BUNGA : ' . $data[1]->jangka_bunga .
    //                 "\n" . 'RC : ' . $data[1]->rc . '%' . ' - ' . 'ANGSURAN POKOK : ' . 'Rp. ' . number_format($data[1]->angsuran_pokok, '0', ',', '.')  .
    //                 ' - ' . 'ANGSURAN BUNGA : ' . 'Rp. ' . number_format($data[1]->angsuran_bunga, '0', ',', '.') . ' - ' . 'TOTAL ANGSURAN : ' . 'Rp. ' . number_format($data[1]->total_angsuran, '0', ',', '.') .
    //                 "\n" . 'CATATAN : ' . $data[1]->catatan ?? "TIDAK ADA CATATAN",
    //         ];
    //     } else {
    //         $catatan2 = ['catatan_kasi_analisa' => "\n" . 'TIDAK ADA CATATAN'];
    //     }

    //     if (isset($data[2])) {
    //         $catatan3 = [
    //             'catatan_kabag_analisa' => "\n" . 'METODE : ' . $data[2]->metode_rps . ' - ' . ' SUKU BUNGA : ' .
    //                 $data[2]->suku_bunga . '%' . ' - ' . 'JANGKA WAKTU : ' . $data[2]->jangka_waktu . ' - '
    //                 . 'JANGKA POKOK : ' . $data[2]->jangka_pokok . ' - ' . 'JANGKA BUNGA : ' . $data[2]->jangka_bunga .
    //                 "\n" . 'RC : ' . $data[2]->rc . '%' . ' - ' . 'ANGSURAN POKOK : ' . 'Rp. ' . number_format($data[2]->angsuran_pokok, '0', ',', '.')  .
    //                 ' - ' . 'ANGSURAN BUNGA : ' . 'Rp. ' . number_format($data[2]->angsuran_bunga, '0', ',', '.') . ' - ' . 'TOTAL ANGSURAN : ' . 'Rp. ' . number_format($data[2]->total_angsuran, '0', ',', '.') .
    //                 "\n" . 'CATATAN : ' . $data[2]->catatan ?? "TIDAK ADA CATATAN",
    //         ];
    //     } else {
    //         $catatan3 = ['catatan_kabag_analisa' => "\n" . 'TIDAK ADA CATATAN'];
    //     }

    //     if (isset($data[3])) {
    //         $catatan4 = [
    //             'catatan_direktur_bisnis' => "\n" . 'METODE : ' . $data[3]->metode_rps . ' - ' . ' SUKU BUNGA : ' .
    //                 $data[3]->suku_bunga . '%' . ' - ' . 'JANGKA WAKTU : ' . $data[3]->jangka_waktu . ' - '
    //                 . 'JANGKA POKOK : ' . $data[3]->jangka_pokok . ' - ' . 'JANGKA BUNGA : ' . $data[3]->jangka_bunga .
    //                 "\n" . 'RC : ' . $data[3]->rc . '%' . ' - ' . 'ANGSURAN POKOK : ' . 'Rp. ' . number_format($data[3]->angsuran_pokok, '0', ',', '.')  .
    //                 ' - ' . 'ANGSURAN BUNGA : ' . 'Rp. ' . number_format($data[3]->angsuran_bunga, '0', ',', '.') . ' - ' . 'TOTAL ANGSURAN : ' . 'Rp. ' . number_format($data[3]->total_angsuran, '0', ',', '.') .
    //                 "\n" . 'CATATAN : ' . $data[3]->catatan ?? "TIDAK ADA CATATAN",
    //         ];
    //     } else {
    //         $catatan4 = ['catatan_direksi' => "\n" . 'TIDAK ADA CATATAN'];
    //     }

    //     if (isset($data[4])) {
    //         $catatan5 = [
    //             'catatan_direksi' => "\n" . 'METODE : ' . $data[3]->metode_rps . ' - ' . ' SUKU BUNGA : ' .
    //                 $data[3]->suku_bunga . '%' . ' - ' . 'JANGKA WAKTU : ' . $data[3]->jangka_waktu . ' - '
    //                 . 'JANGKA POKOK : ' . $data[3]->jangka_pokok . ' - ' . 'JANGKA BUNGA : ' . $data[3]->jangka_bunga .
    //                 "\n" . 'RC : ' . $data[3]->rc . '%' . ' - ' . 'ANGSURAN POKOK : ' . 'Rp. ' . number_format($data[3]->angsuran_pokok, '0', ',', '.')  .
    //                 ' - ' . 'ANGSURAN BUNGA : ' . 'Rp. ' . number_format($data[3]->angsuran_bunga, '0', ',', '.') . ' - ' . 'TOTAL ANGSURAN : ' . 'Rp. ' . number_format($data[3]->total_angsuran, '0', ',', '.') .
    //                 "\n" . 'CATATAN : ' . $data[3]->catatan ?? "TIDAK ADA CATATAN",
    //         ];
    //     } else {
    //         $catatan5 = ['catatan_direksi' => "\n" . 'TIDAK ADA CATATAN'];
    //     }

    //     $catatan = [
    //         'catatan1' => $catatan1,
    //         'catatan2' => $catatan2,
    //         'catatan3' => $catatan3,
    //         'catatan4' => $catatan4,
    //         'catatan5' => $catatan5,
    //     ];

    //     return $catatan;
    // }



    // Analisa Usaha
    protected static function perdagangan_rsc($data, $status_rsc)
    {
        $data = DB::table('rsc_au_perdagangan')
            ->leftJoin('rsc_bu_perdagangan', 'rsc_bu_perdagangan.usaha_kode', '=', 'rsc_au_perdagangan.kode_usaha')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_au_perdagangan.kode_rsc')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'data_nasabah.nama_nasabah',
                'rsc_au_perdagangan.*',
                'rsc_bu_perdagangan.*',
            )
            ->where('rsc_au_perdagangan.kode_rsc', $data)->get();
        //
        if ($status_rsc == "EKS") {
            foreach ($data as $value) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_loan.jkwaktu',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $value->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $value->nama_nasabah = trim($data_eks->fnama);
                    $value->alamat_ktp = trim($data_eks->alamat);
                    $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $value->jangka_waktu = $data_eks->jkwaktu;
                    $value->metode_rps = null;
                    $value->plafon = $data_eks->plafond_awal;
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
        }
        return $data;
    }

    protected static function pertanian_rsc($data, $status_rsc)
    {
        $data = DB::table('rsc_au_pertanian')
            ->leftJoin('rsc_bu_pertanian', 'rsc_bu_pertanian.usaha_kode', '=', 'rsc_au_pertanian.kode_usaha')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_au_pertanian.kode_rsc')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'data_nasabah.nama_nasabah',
                'rsc_au_pertanian.*',
                'rsc_bu_pertanian.*',
            )
            ->where('rsc_au_pertanian.kode_rsc', $data)->get();
        //
        if ($status_rsc == "EKS") {
            foreach ($data as $value) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_loan.jkwaktu',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $value->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $value->nama_nasabah = trim($data_eks->fnama);
                    $value->alamat_ktp = trim($data_eks->alamat);
                    $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $value->jangka_waktu = $data_eks->jkwaktu;
                    $value->metode_rps = null;
                    $value->plafon = $data_eks->plafond_awal;
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
        }
        return $data;
    }

    protected static function jasa_rsc($datas, $status_rsc)
    {
        $data = DB::table('rsc_au_jasa')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_au_jasa.kode_rsc')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'data_nasabah.nama_nasabah',
                'rsc_au_jasa.*',
            )
            ->where('rsc_au_jasa.kode_rsc', $datas)->get();
        //
        if ($status_rsc == "EKS") {
            foreach ($data as $value) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_loan.jkwaktu',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $value->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $value->nama_nasabah = trim($data_eks->fnama);
                    $value->alamat_ktp = trim($data_eks->alamat);
                    $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $value->jangka_waktu = $data_eks->jkwaktu;
                    $value->metode_rps = null;
                    $value->plafon = $data_eks->plafond_awal;
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
        }
        return $data;
    }

    protected static function lain_rsc($datas, $status_rsc)
    {
        $data = DB::table('rsc_au_lain')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_au_lain.kode_rsc')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'data_nasabah.nama_nasabah',
                'rsc_au_lain.*',
            )
            ->where('rsc_au_lain.kode_rsc', $datas)->get();
        //
        if ($status_rsc == "EKS") {
            foreach ($data as $value) {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.plafond_awal',
                        'm_cif.alamat',
                        'm_loan.jkwaktu',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $value->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $value->nama_nasabah = trim($data_eks->fnama);
                    $value->alamat_ktp = trim($data_eks->alamat);
                    $value->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $value->jangka_waktu = $data_eks->jkwaktu;
                    $value->metode_rps = null;
                    $value->plafon = $data_eks->plafond_awal;
                    $value->kantor_kode = Midle::data_kantor(trim($data_eks->wil));
                }
            }
        }
        return $data;
    }
    // Anlisa Usaha


    protected static function get_sqlsrv($keyword)
    {
        if (!empty($keyword)) {
            $keyword_sqlsrv = DB::connection('sqlsrv')
                ->table('m_Loan')
                // ->where('fnama', 'like', '%' . $keyword . '%')
                ->where('fnama', $keyword)
                ->where('plafond', '>',  0)
                ->select('fnama', 'noacc')->first();
        } else {
            $keyword_sqlsrv = null;
        }

        return $keyword_sqlsrv;
    }
}
