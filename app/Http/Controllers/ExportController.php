<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExportController extends Controller
{
    function data_laporan_pendaftaran(){
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->whereIn('data_pengajuan.status', ['Dibatalkan', 'Ditolak', 'Disetujui'])
            ->select(
                'data_pengajuan.created_at as created_at',
                'data_pengajuan.kode_pengajuan as kode_pengajuan',
                'data_nasabah.nama_nasabah as nama_nasabah',
                'data_nasabah.alamat_ktp as alamat_ktp',
                'data_pengajuan.plafon as plafon',
                'data_pengajuan.status as status'
            )

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_pengajuan.created_at', 'asc')
            ->get();

        $data_array [] = array("TANGGAL", "KODE","NAMA_LENGKAP", "ALAMAT", "PLAFON", "STATUS");
        foreach($data as $item)
        {
            $data_array[] = array(
                'TANGGAL'       => \Carbon\Carbon::parse($item->created_at)->format('Y-m-d'),
                'KODE'          => $item->kode_pengajuan,
                'NAMA_LENGKAP'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
                'PLAFON'        => number_format($item->plafon, 0, ',', '.'),
                'STATUS'        => $item->status
            );
        }
        $this->export_laporan_pendaftaran($data_array);
    }
    public function export_laporan_pendaftaran($data){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_pendaftaran.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }









    function data_laporan_realisasi(){
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_spk', 'data_pengajuan.kode_pengajuan', '=', 'data_spk.pengajuan_kode')
            ->join('data_tracking', 'data_pengajuan.kode_pengajuan', '=', 'data_tracking.pengajuan_kode')
            ->select(
                'data_tracking.pencairan_dana as pencairan_dana',
                'data_pengajuan.kode_pengajuan as kode_pengajuan',
                'data_spk.no_spk as no_spk',
                'data_nasabah.nama_nasabah as nama_nasabah',
                'data_nasabah.alamat_ktp as alamat_ktp',
                'data_pengajuan.plafon as plafon'
            )

            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_tracking.pencairan_dana', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_tracking.pencairan_dana', 'desc')
            ->get();

        $data_array [] = array("TANGGAL", "KODE", "NO_SPK", "NAMA_LENGKAP", "ALAMAT", "PLAFON");
        foreach($data as $item)
        {
            $data_array[] = array(
                'TANGGAL'       => \Carbon\Carbon::parse($item->pencairan_dana)->format('Y-m-d'),
                'KODE'          => $item->kode_pengajuan,
                'NO_SPK'        => $item->no_spk,
                'NAMA_LENGKAP'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
                'PLAFON'        => number_format($item->plafon, 0, ',', '.')
            );
        }
        $this->export_laporan_realisasi($data_array);
    }
    public function export_laporan_realisasi($data){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_realisasi.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
}
