<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class CheckListKendaraanController extends Controller
{
    public function index()
    {
        return view('cetak-berkas.check-list-kelengkapan.index');
    }

    public function report_kih()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KIH')->get();

        return view('cetak-berkas.cek-kendaraan.report_kih', [
            'data' => $data
        ]);
    }

    public function report_kko()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KKO')->get();
        return view('cetak-berkas.cek-kendaraan.report_kko', [
            'data' => $data
        ]);
    }

    public function report_kpj()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KPJ')->get();
        return view('cetak-berkas.cek-kendaraan.report_kpj', [
            'data' => $data
        ]);
    }

    public function report_kpn()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KPN')->get();
        return view('cetak-berkas.cek-kendaraan.report_kpn', [
            'data' => $data
        ]);
    }

    public function report_kps()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KPS')->get();

        return view('cetak-berkas.cek-kendaraan.report_kps', [
            'data' => $data
        ]);
    }

    public function report_krs_bpkb()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KRS')->where('jenis_agunan', 'kendaraan')->get();

        return view('cetak-berkas.cek-kendaraan.report_krs_bpkb', [
            'data' => $data
        ]);
    }

    public function report_krs_bpkb_shm()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KRS')->where('jenis_agunan', 'tanah dan kendaraan')->get();

        return view('cetak-berkas.cek-kendaraan.report_krs_bpkb_shm', [
            'data' => $data
        ]);
    }

    public function report_krs_shm()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KRS')->where('jenis_agunan', 'tanah')->get();

        return view('cetak-berkas.cek-kendaraan.report_krs_shm', [
            'data' => $data
        ]);
    }

    public function report_kru_bpkb()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KRU')->where('jenis_agunan', 'kendaraan')->get();

        return view('cetak-berkas.cek-kendaraan.report_kru_bpkb', [
            'data' => $data
        ]);
    }

    public function report_kru_bpkb_shm()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KRU')->where('jenis_agunan', 'tanah dan kendaraan')->get();

        return view('cetak-berkas.cek-kendaraan.report_kru_bpkb_shm', [
            'data' => $data
        ]);
    }

    public function report_kru_shm()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KRU')->where('jenis_agunan', 'tanah')->get();

        return view('cetak-berkas.cek-kendaraan.report_kru_shm', [
            'data' => $data
        ]);
    }

    public function report_kta()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KTA')->get();

        return view('cetak-berkas.cek-kendaraan.report_kta', [
            'data' => $data
        ]);
    }

    public function report_kup()
    {
        $data = DB::table('data_dokumen_ceklis')->where('produk_kode', 'KUP')->get();

        return view('cetak-berkas.cek-kendaraan.report_kup', [
            'data' => $data
        ]);
    }
}
