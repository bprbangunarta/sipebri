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
    function data_laporan_fasilitas()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');
        $no = 1;
        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $data = DB::table('data_pengajuan')
            ->select(
                'data_tracking.akad_kredit',
                'data_pengajuan.no_loan',
                'data_spk.no_spk',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_kantor.kode_kantor',
                'data_pengajuan.plafon as plafon',
            )
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->where('data_pengajuan.on_current', 1)

            ->when(
                $tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_tracking.akad_kredit', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_tracking.akad_kredit', 'desc')
            ->get();

        $data_array[] = array("NO", "TANGGAL", "NO_LOAN", "NO_SPK", "NAMA_DEBITUR", "ALAMAT", "WIL", "PLAFON");
        foreach ($data as $item) {
            $data_array[] = array(
                'NO'            => $no++,
                'TANGGAL'       => \Carbon\Carbon::parse($item->akad_kredit)->format('Y-m-d'),
                'NO_LOAN'       => $item->no_loan,
                'NO_SPK'        => $item->no_spk,
                'NAMA_DEBITUR'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
                'WIL'           => $item->kode_kantor,
                'PLAFON'        => number_format($item->plafon, 0, ',', '.')
            );
        }
        $this->export_laporan_fasilitas($data_array);
    }
    public function export_laporan_fasilitas($data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="realisasi_kredit.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    function data_laporan_pendaftaran()
    {
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

        $data_array[] = array("TANGGAL", "KODE", "NAMA_LENGKAP", "ALAMAT", "PLAFON", "STATUS");
        foreach ($data as $item) {
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
    public function export_laporan_pendaftaran($data)
    {
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

    function data_laporan_realisasi()
    {
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

        $data_array[] = array("TANGGAL", "KODE", "NO_SPK", "NAMA_LENGKAP", "ALAMAT", "PLAFON");
        foreach ($data as $item) {
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
    public function export_laporan_realisasi($data)
    {
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

    function data_laporan_siap_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');
        $no = 1;

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $data = DB::table('data_pengajuan')
            ->select(
                'data_pengajuan.*',
                'data_nasabah.*',
                'data_notifikasi.no_notifikasi',
                'data_notifikasi.created_at as tanggal',
                'data_notifikasi.keterangan',
                'data_notifikasi.rencana_realisasi',
                'data_kantor.kode_kantor',
                'users.name as surveyor',
            )
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'users.code_user', '=', 'data_survei.surveyor_kode')
            ->where('data_pengajuan.on_current', '0')

            ->when($tgl1 && $tgl2, function ($data) use ($tgl1, $tgl2) {
                return $data->whereBetween('data_notifikasi.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_notifikasi.created_at', 'desc')
            ->get();

        $data_array[] = array("NO", "TANGGAL", "KODE", "NO_NOTIFIKASI", "NAMA_NASABAH", "PLAFON", "WIL", "RENCANA", "KETERANGAN");
        foreach ($data as $item) {
            $data_array[] = array(
                'NO'            => $no++,
                'TANGGAL'       => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'KODE'          => $item->kode_pengajuan,
                'NO_NOTIFIKASI' => $item->no_notifikasi,
                'NAMA_NASABAH'  => $item->nama_nasabah,
                'PLAFON'        => number_format($item->plafon, 0, ',', '.'),
                'WIL'           => $item->kode_kantor,
                'KETERANGAN'    => $item->keterangan,
                'RENCANA'       => $item->rencana_realisasi,
            );
        }
        $this->export_laporan_siap_realisasi($data_array);
    }
    public function export_laporan_siap_realisasi($data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="pengajuan_siap_realisasi.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
}
