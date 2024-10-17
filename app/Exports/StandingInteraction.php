<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StandingInteraction implements FromView
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

        $query = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('v_resort', 'v_resort.kode_resort', '=', 'data_pengajuan.resort_kode')
            ->where('data_pengajuan.on_current', '=', '1')

            ->select(
                'data_nasabah.no_cif',
                'data_nasabah.nama_nasabah',
                'data_nasabah.no_telp',
                'data_nasabah.no_rekening',
                'data_nasabah.no_identitas',
                'v_resort.nama_resort',
                'data_pengajuan.plafon',
                'data_pengajuan.jangka_waktu',
            )

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            });

        $query->where(function ($query) use ($produk, $kantor) {
            if (empty($produk)) {
                $query->whereIn('data_pengajuan.resort_kode', ['058', '057', '055', '129', '141', '095', '090', '130', '131', '141'])
                    ->where('data_kantor.kode_kantor', 'like', '%' . $kantor . '%');
            } else {
                $query->where('data_pengajuan.produk_kode', 'like', '%' . $produk . '%')
                    ->where('data_kantor.kode_kantor', 'like', '%' . $kantor . '%');
            }
        })

            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->get();

        foreach ($data as $value) {
            $data_eks = DB::connection('sqlsrv')->table('m_loan')
                ->select(
                    'm_loan.noacc',
                )
                ->where('nocif', $value->no_cif)->first();
            $value->no_loan = $data_eks ? trim($data_eks->noacc) : null;
            $value->nama_resort = trim($value->nama_resort);
            $value->nama_resort = trim($value->nama_resort);
        }

        return view('cetak.exports.si_subang', [
            'data' => $data
        ]);
    }
}
