<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DataSI implements FromView
{
    public function view(): View
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2') ?: $tgl1;

        if (empty($tgl1)) {
            abort(400, 'Tanggal tidak boleh kosong.');
        }

        $produk = request('kode_produk');
        $kantor = request('nama_kantor');

        $dataQuery = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_realisasi', 'data_realisasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('v_resort', 'v_resort.kode_resort', '=', 'data_pengajuan.resort_kode')
            ->where('data_pengajuan.on_current', 1)
            ->select(
                'data_nasabah.no_cif',
                'data_nasabah.nama_nasabah',
                'data_nasabah.no_identitas',
                'data_nasabah.no_telp',
                'data_nasabah.no_karyawan',
                'data_nasabah.alamat_ktp',
                'data_nasabah.no_rekening',
                'data_kantor.kode_kantor',
                'data_pengajuan.plafon as plafon',
                'data_pengajuan.jangka_waktu',
                'v_resort.nama_resort',
                'data_spk.no_spk',
                DB::raw("DATE_FORMAT(data_spk.created_at, '%d-%m-%Y') as tgl_realisasi"),
                DB::raw("DATE_FORMAT(data_realisasi.created_at_si, '%d-%m-%Y') as tgl_si"),
            );

        $dataQuery->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
            return $query->whereBetween('data_tracking.akad_kredit', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
        });

        // Hanya tambahkan klausa where jika $kantor tidak kosong
        if ($kantor !== null && $produk !== null) {

            $dataQuery->where('data_survei.kantor_kode', $kantor)
                ->where('data_pengajuan.produk_kode', $produk);
        } elseif ($kantor !== null && $produk === null) {

            $dataQuery->where('data_survei.kantor_kode', $kantor);
        } elseif ($kantor === null && $produk !== null) {

            $dataQuery->where('data_pengajuan.produk_kode', $produk);
        }

        $data = $dataQuery->orderBy('data_tracking.akad_kredit', 'desc')->get();

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->join('m_loan_tagihan', 'm_loan_tagihan.noacc', '=', 'm_loan.noacc')
                ->select(
                    'm_loan.noacdroping',
                    'm_loan.noacc',
                    'm_loan_tagihan.tag_pokok',
                    'm_loan_tagihan.tag_bunga'
                )
                ->where('m_loan.nocif', $value->no_cif)
                ->where('m_loan.no_spk', $value->no_spk)
                ->first();
            $value->no_loan = $data_eks ? trim($data_eks->noacc) : null;
            $value->norek = $data_eks ? trim($data_eks->noacdroping) : null;
            $value->nominal_si = $data_eks ? trim((int) $data_eks->tag_pokok + (int) $data_eks->tag_bunga) : null;
            $value->nama_resort = trim($value->nama_resort);

            if (!empty($value->tgl_realisasi)) {
                $tglRealisasi = Carbon::parse($value->tgl_realisasi);

                $deviasiSi = $tglRealisasi->startOfDay()->diffInDays(now()->startOfDay());

                $value->deviasiSi = $deviasiSi;
            } else {
                $value->deviasiSi = 0;
            }
        }

        return view('cetak.exports.data_si', [
            'data' => $data
        ]);
    }
}
