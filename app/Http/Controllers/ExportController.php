<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;

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

        $kantor = request('nama_kantor');
        $produk = request('kode_produk');

        $dataQuery = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('a_administrasi', 'a_administrasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('a_memorandum', 'a_memorandum.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->where('data_pengajuan.on_current', 1)
            ->select(
                'data_tracking.akad_kredit',
                'data_pengajuan.suku_bunga',
                'data_pengajuan.metode_rps',
                'data_spk.nomor',
                'data_spk.no_spk',
                'data_nasabah.nama_nasabah',
                'data_nasabah.no_identitas',
                'data_nasabah.no_telp',
                'data_nasabah.no_karyawan',
                'data_nasabah.alamat_ktp',
                'data_kantor.kode_kantor',
                'data_pengajuan.plafon as plafon',
                'a_administrasi.provisi',
                'a_administrasi.administrasi',
                'a_administrasi.proses_apht',
                'a_memorandum.by_fiducia',
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


        $data_array[] = array("TANGGAL PK", "NOMOR", "NO SPK", "NAMA DEBITUR", "ALAMAT", "NO KTP", "NO TELP", "NIK KARYAWAN", "PLAFON", "SUKU BUNGA", "METODE RPS", "PROVISI", "ADM", "FIDUCIA/APHT");
        foreach ($data as $item) {
            $data_array[] = array(
                'TANGGAL PK'            => \Carbon\Carbon::parse($item->akad_kredit)->format('Y-m-d'),
                'NOMOR'         => $item->nomor,
                'NO SPK'        => $item->no_spk,
                'NAMA DEBITUR'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
                'NO KTP'        => ' ' . $item->no_identitas,
                'NO TELP'       => $item->no_telp,
                'NIK KARYAWAN'  => $item->no_karyawan,
                'PLAFON'        => $item->plafon,
                'SUKU BUNGA'    => $item->suku_bunga,
                'METODE RPS'    => $item->metode_rps,
                'PROVISI'       => $item->provisi,
                'ADM'           => $item->administrasi,
                'APHT'          => $item->proses_apht,
                'FIDUCIA'       => $item->by_fiducia,
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
        $kantor = request('nama_kantor');
        $produk = request('kode_produk');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $dataQuery = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('users', 'users.code_user', '=', 'data_pengajuan.input_user')
            ->whereIn('data_pengajuan.status', ['Dibatalkan', 'Ditolak', 'Disetujui'])
            ->select(
                'data_pengajuan.created_at as created_at',
                'data_nasabah.kode_nasabah',
                'data_pendamping.nama_pendamping',
                'data_nasabah.nama_nasabah as nama_nasabah',
                'data_nasabah.no_identitas as no_identitas',
                'data_nasabah.no_karyawan as no_karyawan',
                'data_nasabah.alamat_ktp as alamat_ktp',
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.produk_kode as produk',
                'data_pengajuan.plafon as plafon',
                'data_pengajuan.suku_bunga',
                'data_nasabah.no_telp',
                'users.name'
            );

        $dataQuery->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
            return $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
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

        $data = $dataQuery->orderBy('data_pengajuan.created_at', 'asc')->get();

        $data_array[] = array("TANGGAL", "KODE PENGAJUAN", "KODE NASABAH", "NAMA NASABAH", "NO KTP", "NIK KARYAWAN", "ALAMAT", "PLAFON", "SUKU BUNGA", "PRODUK", "NO TELP", "PENDAMPING", "USER");
        foreach ($data as $item) {
            $data_array[] = array(
                'TANGGAL'       => \Carbon\Carbon::parse($item->created_at)->format('Y-m-d'),
                'KODE PENGAJUAN'  => $item->kode_pengajuan,
                'KODE NASABAH'    => $item->kode_nasabah,
                'NAMA NASABAH'    => $item->nama_nasabah,
                'NO KTP'          => " " . $item->no_identitas,
                'NIK KARYAWAN'     => $item->no_karyawan,
                'ALAMAT'          => $item->alamat_ktp,
                'PLAFON'          => number_format($item->plafon, 0, ',', '.'),
                'SUKU BUNGA'      => $item->suku_bunga . ' ' . '%',
                'PRODUK'          => $item->produk,
                'NO TELP'         => $item->no_telp,
                'PENDAMPING'      => $item->nama_pendamping,
                'USER'            => $item->name
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

        $kantor = request('nama_kantor');
        $produk = request('kode_produk');

        $dataQuery = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_pendamping', 'data_pendamping.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('v_users', 'v_users.code_user', '=', 'data_pengajuan.input_user')
            ->leftJoin('data_notifikasi', 'data_notifikasi.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')

            ->where('data_pengajuan.on_current', 0)
            ->where('data_pengajuan.status', 'Disetujui')
            // ->whereNotNull('data_notifikasi.keterangan')
            ->whereNull('data_spk.no_spk')

            ->select(
                'data_notifikasi.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_pengajuan.produk_kode',
                'v_users.nama_user',
                'data_notifikasi.keterangan',
                'data_notifikasi.rencana_realisasi',
            );

        $dataQuery->when($tgl1 && $tgl2, function ($data) use ($tgl1, $tgl2) {
            return $data->whereBetween('data_notifikasi.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
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

        $data = $dataQuery->orderBy('data_pengajuan.created_at', 'asc')->get();

        $data_array[] = array("NO", "TANGGAL", "KODE", "NAMA NASABAH", "ALAMAT", "PLAFON", "WIL", "KETERANGAN", "RENCANA");
        foreach ($data as $item) {
            $data_array[] = array(
                'NO'            => $no++,
                'TANGGAL'       => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'KODE'          => $item->kode_pengajuan,
                'NAMA_NASABAH'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
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

    public function export_filter_realisasi()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');
        $produk = request('kode_produk');
        $kantor = request('nama_kantor');
        $resort = request('resort');
        $metode = request('metode');
        $surveyor = request('surveyor');
        $cgc = request('cgc');
        // dd(request());
        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->select(
                'data_tracking.akad_kredit',
                'data_pengajuan.no_loan',
                'data_spk.no_spk',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_kantor.kode_kantor',
                'data_pengajuan.plafon as plafon',
            )
            ->where('data_pengajuan.on_current', 1);

        if ($tgl1 !== null) {
            $query->where(function ($query) use ($tgl1, $tgl2) {
                $query->whereBetween('data_pengajuan.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            });
        }

        $query->where(function ($query) use ($produk, $kantor, $metode, $surveyor, $resort, $cgc) {
            $query->where('data_produk.kode_produk', 'like', '%' . $produk . '%')
                ->where('data_survei.surveyor_kode', 'like', '%' . $surveyor . '%')
                ->where('data_pengajuan.metode_rps', 'like', '%' . $metode . '%')
                ->where('data_kantor.kode_kantor', 'like', '%' . $kantor . '%');
        })

            ->orderBy('data_tracking.akad_kredit', 'desc');
        // ->get();
        $query->paginate(10);
        //
        dd($query);
    }

    public function export_sesudah_survei($data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_sesudah_survei.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    function data_export_sesudah_survei()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');
        $no = 1;

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')

            ->whereIn('data_pengajuan.tracking', ['Proses Analisa', 'Persetujuan Komite', 'Naik Kasi', 'Naik Komite I', 'Naik Komite II'])

            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_nasabah.no_telp',
                'data_survei.tgl_survei',
                'v_users.nama_user',
                'data_pengajuan.tracking',
            )

            ->when($tgl1 && $tgl2, function ($data) use ($tgl1, $tgl2) {
                return $data->whereBetween('data_survei.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_pengajuan.created_at', 'asc')
            ->get();

        $data_array[] = array("NO", "TANGGAL", "KODE", "NAMA NASABAH", "ALAMAT", "WILAYAH", "PRODUK", "NO TELP", "TGL SURVEI", "SURVEYOR", "STATUS");
        foreach ($data as $item) {
            $data_array[] = array(
                'NO'            => $no++,
                'TANGGAL'       => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'KODE'          => $item->kode_pengajuan,
                'NAMA_NASABAH'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
                'WILAYAH'        => $item->kantor_kode,
                'PRODUK'        => $item->produk_kode,
                'NO TELP'        => $item->no_telp,
                'TGL SURVEI'           => $item->tgl_survei,
                'SURVEYOR'    => $item->nama_user,
                'STATUS'       => $item->tracking,
            );
        }

        $this->export_sesudah_survei($data_array);
    }

    public function export_sebelum_survei($data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Laporan_sebelum_survei.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    function data_export_sebelum_survei()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');
        $no = 1;

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.kasi_kode')

            ->whereIn('data_pengajuan.tracking', ['Verifikasi Data', 'Penjadwalan', 'Proses Survei'])

            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_nasabah.no_telp',
                'data_survei.tgl_survei',
                'v_users.nama_user',
                'data_pengajuan.tracking',
                'data_survei.catatan_survei',
            )

            ->when($tgl1 && $tgl2, function ($data) use ($tgl1, $tgl2) {
                return $data->whereBetween('data_survei.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            })

            ->orderBy('data_pengajuan.created_at', 'asc')
            ->get();

        $data_array[] = array("NO", "TANGGAL", "KODE", "NAMA NASABAH", "ALAMAT", "WILAYAH", "PRODUK", "PLAFON", "TGL SURVEI", "SURVEYOR", "STATUS", "CATATAN");
        foreach ($data as $item) {
            $data_array[] = array(
                'NO'            => $no++,
                'TANGGAL'       => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'KODE'          => $item->kode_pengajuan,
                'NAMA_NASABAH'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
                'WILAYAH'        => $item->kantor_kode,
                'PRODUK'        => $item->produk_kode,
                'PLAFON'        => $item->no_telp,
                'TGL SURVEI'    => $item->tgl_survei,
                'SURVEYOR'    => $item->nama_user,
                'STATUS'       => $item->tracking,
                'STATUS'       => $item->catatan_survei,
            );
        }

        $this->export_sebelum_survei($data_array);
    }

    public function data_export_tracking()
    {
        $tgl1 = request('tgl1');
        $tgl2 = request('tgl2');
        $no = 1;
        $produk = request('kode_produk');
        $kantor = request('nama_kantor');
        $resort = request('resort');
        $metode = request('metode');
        $surveyor = request('surveyor');
        $status = request('status');

        if (is_null($tgl2)) {
            $tgl2 = $tgl1;
        }

        $query = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_kantor', 'data_kantor.kode_kantor', '=', 'data_survei.kantor_kode')
            ->join('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('v_users', 'v_users.code_user', '=', 'data_survei.surveyor_kode')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')
            ->leftJoin('v_resort', 'v_resort.kode_resort', '=', 'data_pengajuan.resort_kode')

            ->select(
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kode_pengajuan',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.produk_kode',
                'data_pengajuan.plafon',
                'data_pengajuan.jangka_waktu',
                'data_pengajuan.suku_bunga',
                'v_users.nama_user',
                'data_survei.tgl_survei as tgl_survey',
                'data_survei.surveyor_kode as surveyor',
                'data_tracking.analisa_kredit as tgl_analisa',
                'data_tracking.keputusan_komite as tgl_persetujuan',
                'data_tracking.akad_kredit as tgl_realisasi',
                'v_resort.nama_resort',
                'data_pengajuan.status',
            )
            ->when($tgl1 && $tgl2, function ($query) use ($tgl1, $tgl2) {
                return $query->whereBetween('data_survei.created_at', [$tgl1 . ' 00:00:00', $tgl2 . ' 23:59:59']);
            });

        $query->where(function ($query) use ($produk, $kantor, $metode, $surveyor, $status, $resort) {
            $query->where('data_survei.surveyor_kode', 'like', '%' . $surveyor . '%')
                ->where('data_pengajuan.produk_kode', 'like', '%' . $produk . '%')
                ->where('data_pengajuan.metode_rps', 'like', '%' . $metode . '%')
                ->where('data_kantor.kode_kantor', 'like', '%' . $kantor . '%');

            if (!is_null($resort)) {
                $query->where('data_pengajuan.resort_kode', 'like', '%' . $resort . '%');
            }
            $query->where('data_pengajuan.status', 'like', '%' . $status . '%');
        })


            ->orderBy('data_pengajuan.created_at', 'desc');
        $data = $query->get();

        if (count($data) == 0) {
            return redirect()->back()->with('error', 'Data tidak ada');
        }

        $data_array[] = array(
            "NO",
            "TANGGAL",
            "KODE",
            "NAMA NASABAH",
            "ALAMAT",
            "WIL",
            "PDK",
            "PLAFON",
            "RATE",
            "RESORT",
            "SURVEYOR",
            "SURVEY",
            "ANALISA",
            "STAFF ANALIS",
            "KASI ANALIS",
            "KOMITE I",
            "KOMITE II",
            "REALISASI",
            "STATUS"
        );

        foreach ($data as $item) {

            if (is_null($item->tgl_analisa)) {
                $item->tgl_analisa;
            } else {
                $item->tgl_analisa = Carbon::parse($item->tgl_analisa ?? null)->format('d-m-Y');
            }

            if (is_null($item->tgl_persetujuan)) {
                $item->tgl_persetujuan;
            } else {
                $item->tgl_persetujuan = Carbon::parse($item->tgl_persetujuan ?? null)->format('d-m-Y');
            }

            if (is_null($item->tgl_realisasi)) {
                $item->tgl_realisasi;
            } else {
                $item->tgl_realisasi = Carbon::parse($item->tgl_realisasi ?? null)->format('d-m-Y');
            }

            if ($item->status == "Disetujui" || $item->status == "Ditolak" || $item->status == "Dibatalkan") {
                $item->status = $item->status;
            } else {
                $item->status = null;
            }

            $data_usulan = DB::table('data_usulan')->where('pengajuan_kode', $item->kode_pengajuan)->get();

            $staf_analis = '-';
            $kasi_analis = '-';
            $komiteI = '-';
            $komiteII = '-';

            if (count($data_usulan) > 0) {
                $statusLabels = ['Staf Analis', 'Kasi Analis', 'Komite I', 'Komite II'];

                foreach ($data_usulan as $key => $usulan) {
                    if ($key < count($statusLabels)) {
                        ${strtolower(str_replace(' ', '_', $statusLabels[$key]))} = $usulan->created_at ?? '-';
                    }
                }
            }


            $data_array[] = array(
                'NO'            => $no++,
                'TANGGAL'       => \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d'),
                'KODE'          => $item->kode_pengajuan,
                'NAMA_NASABAH'  => $item->nama_nasabah,
                'ALAMAT'        => $item->alamat_ktp,
                'WIL'           => $item->kantor_kode,
                'PDK'           => $item->produk_kode,
                'PLAFON'        => number_format($item->plafon, 0, ',', '.'),
                'RATE'          => $item->suku_bunga,
                'RESORT'        => $item->nama_resort,
                'SURVEYOR'      => $item->nama_user,
                'SURVEY'        => $item->tgl_survey,
                'ANALISA'       => $item->tgl_analisa,
                // 'PUTUSAN'       => $item->tgl_persetujuan,
                'STAFF ANALISA'       => $staf_analis,
                'KASI ANALISA'       => $kasi_analis,
                'KOMITEI'       => $komiteI,
                'KOMITEII'       => $komiteII,
                'REALISASI'     => $item->tgl_realisasi,
                'STATUS'        => $item->status,
            );
        }

        $this->export_data_tracking($data_array);
    }

    public function export_data_tracking($data)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');
        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($data);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Laporan_data_tracking.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }
}
