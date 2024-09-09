<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanRealisasi implements FromView
{
    public function view(): View
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2') ?: $tgl1;

        if (empty($tgl1)) {
            abort(400, 'Tanggal tidak boleh kosong.');
        }

        $data = DB::table('rsc_data_pengajuan')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->leftJoin('data_pekerjaan', 'data_pekerjaan.kode_pekerjaan', '=', 'data_nasabah.pekerjaan_kode')
            ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_spk', 'rsc_spk.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->leftJoin('rsc_biaya', 'rsc_biaya.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'data_nasabah.no_cif',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.no_identitas',
                'data_spk.no_spk as spk',
                'data_pekerjaan.nama_pekerjaan',
                'data_pendamping.nama_pendamping',
                'data_pengajuan.jangka_waktu',
                'data_pengajuan.plafon',
                'rsc_data_pengajuan.pengajuan_kode',
                'rsc_data_pengajuan.status_rsc',
                'rsc_data_pengajuan.penentuan_plafon as plafon_rsc',
                'rsc_data_pengajuan.jangka_waktu as jw_rsc',
                'rsc_data_pengajuan.suku_bunga as rate_rsc',
                'rsc_data_pengajuan.metode_rps as rps_rsc',
                'rsc_data_pengajuan.jenis_persetujuan as jenis_rsc',
                'rsc_data_pengajuan.tunggakan_bunga',
                'rsc_data_pengajuan.tunggakan_denda',
                'rsc_data_pengajuan.angsuran_bunga',
                'rsc_data_pengajuan.angsuran_pokok',
                'rsc_data_pengajuan.tunggakan_denda',
                'rsc_biaya.administrasi_nominal',
                'rsc_biaya.ujroh',
                'rsc_biaya.denda_dibayar',
                'rsc_biaya.asuransi_tlo',
                'rsc_biaya.asuransi_jiwa',
                'rsc_spk.no_spk as spk_rsc',
                'rsc_spk.nomor as nomor',
                DB::raw("DATE_FORMAT(COALESCE(data_spk.created_at), '%d-%m-%Y') as tgl_eff"),
                DB::raw("DATE_FORMAT(COALESCE(rsc_spk.created_at, CURDATE()), '%d-%m-%Y') as tgl_eff_rsc"),
                DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at) + INTERVAL rsc_data_pengajuan.jangka_bunga MONTH), '%d-%m-%Y') as rsc_bayar_bunga"),
                DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at) + INTERVAL rsc_data_pengajuan.jangka_pokok MONTH), '%d-%m-%Y') as rsc_bayar_pokok"),
            )
            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('rsc_spk.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })
            ->get();

        foreach ($data as $value) {
            if ($value->status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->join('wilayah', 'wilayah.kodewil', '=', 'm_loan.kdwil')
                    ->join('REF_PEKERJAAN', 'REF_PEKERJAAN.DESC1', '=', 'm_cif.pekerjaan')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.noacc',
                        'm_loan.plafond_awal',
                        'm_loan.no_spk',
                        'm_loan.os',
                        'm_loan.tgleff',
                        'm_loan.noacdroping',
                        'm_cif.alamat',
                        'm_cif.slik_pasangan',
                        'm_cif.nocif',
                        'm_cif.noid',
                        'REF_PEKERJAAN.DESC2',
                        'm_loan.jkwaktu',
                        'setup_loan.ket',
                        'wilayah.ket as wil',
                    )
                    ->where('noacc', $value->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $date = Carbon::createFromFormat('Ymd', trim($data_eks->tgleff));

                    $value->nama_nasabah = trim($data_eks->fnama);
                    $value->no_loan = trim($data_eks->noacc);
                    $value->no_acc_simapan = trim($data_eks->noacdroping);
                    $value->nama_pendamping = trim($data_eks->slik_pasangan);
                    $value->no_cif = trim($data_eks->nocif);
                    $value->spk = trim($data_eks->no_spk);
                    $value->tgl_eff = $date->format('d-m-Y');
                    $value->sisa_os = (int) trim($data_eks->os);
                    $value->no_identitas = trim($data_eks->noid);
                    $value->plafon = (int) trim($data_eks->plafond_awal);
                    $value->alamat_ktp = trim($data_eks->alamat);
                    $value->nama_pekerjaan = trim($data_eks->DESC2);
                    $value->jangka_waktu = $data_eks->jkwaktu;
                }
            } else {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->select(
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                        'm_loan.os',
                    )
                    ->where('m_loan.plafond', '<>', 0.00)
                    ->where('m_loan.nocif', $value->no_cif)->first();

                if (!empty($data_eks)) {
                    $value->no_loan = $data_eks->noacc;
                    $value->sisa_os = (int) trim($data_eks->os);
                    $value->no_acc_simapan = trim($data_eks->noacdroping);
                }
            }
        }

        return view('rsc.exports.laporan_realisasi', [
            'data' => $data
        ]);
    }
}
