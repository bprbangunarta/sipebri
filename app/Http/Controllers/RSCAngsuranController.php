<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCAngsuranController extends Controller
{
    public function index()
    {
        $data = DB::table('rsc_spk')
            ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_spk.kode_rsc')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->select(
                'rsc_data_pengajuan.pengajuan_kode',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.suku_bunga',
                'rsc_data_pengajuan.jangka_waktu',
                'rsc_data_pengajuan.penentuan_plafon',
                'rsc_data_pengajuan.metode_rps',
                'rsc_data_pengajuan.status_rsc',
                'data_pengajuan.produk_kode',
                'rsc_spk.created_at as tgl_realisasi',
                'rsc_spk.no_spk',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.no_cif',
                DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE())), '%d-%m-%Y') as tgl_realisasi")
            )
            ->orderBy('rsc_spk.created_at', 'desc')->paginate(10);
        //

        foreach ($data as $item) {
            if ($item->status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                        'm_cif.alamat',
                        'm_cif.nocif',
                        'setup_loan.ket',
                    )
                    ->where('noacc', $item->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $item->nama_nasabah = trim($data_eks->fnama);
                    $item->alamat_ktp = trim($data_eks->alamat);
                    $item->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $item->no_cif = trim($data_eks->nocif);
                    $item->no_tab = trim($data_eks->noacdroping);
                    $item->no_kredit = trim($data_eks->noacc);
                }
            } else {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->select(
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                    )
                    ->where('nocif', $item->no_cif)->first();
                //
                if ($data_eks) {
                    $item->no_tab = trim($data_eks->noacdroping);
                    $item->no_kredit = trim($data_eks->noacc);
                }
            }
        }

        foreach ($data as $item) {
            $item->kd_rsc = Crypt::encrypt($item);
        }

        return view('rsc.angsuran.index', [
            'data' => $data
        ]);
    }

    public function detail_angsuran(Request $request)
    {
        try {
            $kode_rsc = Crypt::decrypt($request->query('kode_rsc'));

            $data = DB::table('rsc_spk')
                ->leftJoin('rsc_data_pengajuan', 'rsc_data_pengajuan.kode_rsc', '=', 'rsc_spk.kode_rsc')
                ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->select(
                    'rsc_data_pengajuan.pengajuan_kode',
                    'rsc_data_pengajuan.kode_rsc',
                    'rsc_data_pengajuan.suku_bunga',
                    'rsc_data_pengajuan.jangka_waktu',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.metode_rps',
                    'rsc_data_pengajuan.status_rsc',
                    'data_pengajuan.produk_kode',
                    'rsc_spk.created_at as tgl_real',
                    'rsc_spk.no_spk',
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_cif',
                    DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE())), '%d-%m-%Y') as tgl_realisasi")
                )
                ->where('rsc_data_pengajuan.kode_rsc', $kode_rsc->kode_rsc)->first();
            //

            if ($data->status_rsc == 'EKS') {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->join('m_cif', 'm_cif.nocif', '=', 'm_loan.nocif')
                    ->join('setup_loan', 'setup_loan.kodeprd', '=', 'm_loan.kdprd')
                    ->select(
                        'm_loan.fnama',
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                        'm_cif.alamat',
                        'm_cif.nocif',
                        'setup_loan.ket',
                    )
                    ->where('noacc', $data->pengajuan_kode)->first();
                //
                if ($data_eks) {
                    $data->nama_nasabah = trim($data_eks->fnama);
                    $data->alamat_ktp = trim($data_eks->alamat);
                    $data->produk_kode = Midle::data_produk(trim($data_eks->ket));
                    $data->no_cif = trim($data_eks->nocif);
                    $data->no_tab = trim($data_eks->noacdroping);
                    $data->no_kredit = trim($data_eks->noacc);
                }
            } else {
                $data_eks = DB::connection('sqlsrv')->table('m_loan')
                    ->select(
                        'm_loan.noacc',
                        'm_loan.noacdroping',
                    )
                    ->where('nocif', $data->no_cif)->first();
                //
                if ($data_eks) {
                    $data->no_tab = trim($data_eks->noacdroping);
                    $data->no_kredit = trim($data_eks->noacc);
                }
            }
            $data->tgl_realisasi = Carbon::parse($data->tgl_realisasi)->translatedFormat('d F Y');

            if ($data->metode_rps == "FLAT") {
                $angsuran = $this->flat($data->suku_bunga, $data->jangka_waktu, $data->penentuan_plafon, $data->tgl_real);
            } elseif ($data->metode_rps == "EFEKTIF ANUITAS") {
                $angsuran = $this->efektif_anuitas($data->suku_bunga, $data->jangka_waktu, $data->penentuan_plafon, $data->tgl_real);
            } elseif ($data->metode_rps == "EFEKTIF MUSIMAN") {
                $angsuran = $this->efektif_musiman($data->suku_bunga, $data->jangka_waktu, $data->penentuan_plafon, $data->tgl_real);
            }

            return view('rsc.angsuran.detail', [
                'data' => $data,
                'angsuran' => $angsuran,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    private function flat($suku_bunga, $jangka_waktu, $plafon, $tgl_real)
    {
        try {
            $pokok = $plafon / $jangka_waktu;
            $bunga = ($plafon * $suku_bunga) / 100 / 12;
            $jml_setoran = $bunga + $pokok;

            $bulan_array = range(1, $jangka_waktu);
            $rincian = [];

            foreach ($bulan_array as $bulan_ke) {
                // Hitung tanggal setoran untuk setiap bulan
                $tanggal_setoran = date('d/m/Y', strtotime("+$bulan_ke month", strtotime($tgl_real)));

                $plafon -= $pokok;
                // Simpan rincian per bulan
                $rincian[] = [
                    'bulan_ke' => $bulan_ke,
                    'tanggal_setoran' => $tanggal_setoran,
                    'setoran_pokok' => $pokok,
                    'setoran_bunga' => $bunga,
                    'jumlah_setoran' => $jml_setoran,
                    'sisa_plafon' => $plafon
                ];
            }

            return $rincian;
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function efektif_anuitas($suku_bunga, $jangka_waktu, $plafon, $tgl_real)
    {
        try {
            $ssb = $suku_bunga / 100;
            $sb = $ssb / 12;

            $bulan_array = range(1, $jangka_waktu);
            $rincian = [];
            $total_pokok_dibayar = 0;

            foreach ($bulan_array as $bulan_ke) {
                $tanggal_setoran = date('d/m/Y', strtotime("+$bulan_ke month", strtotime($tgl_real)));

                $per = $bulan_ke;
                $angsuran = ($plafon * $sb) / (1 - 1 / pow(1 + $sb, $jangka_waktu));
                $pokok = ($plafon * $sb * pow(1 + $sb, $per - 1)) / (pow(1 + $sb, $jangka_waktu) - 1);
                $bunga = round($angsuran) - round($pokok);

                $total_pokok_dibayar += $pokok;
                $sisa_plafon = round($plafon) - round($total_pokok_dibayar);

                $rincian[] = [
                    'bulan_ke' => $bulan_ke,
                    'tanggal_setoran' => $tanggal_setoran,
                    'setoran_pokok' => round($pokok),
                    'setoran_bunga' => round($bunga),
                    'jumlah_setoran' => round($angsuran),
                    'sisa_plafon' => round($sisa_plafon)
                ];
            }

            return $rincian;
        } catch (\Throwable $th) {
            return null;
        }
    }

    private function efektif_musiman($suku_bunga, $jangka_waktu, $plafon, $tgl_real)
    {
        $plafon = 20000000;
        $suku_bunga = 31;
        $jangka_waktu = 24;
        $hpokok = $plafon / ($jangka_waktu / 6);

        $hariini = Carbon::now();
        $bulansekarang = $hariini->month;
        $tahunsekarang = $hariini->year;
        $tglskrng = $hariini->day;

        $bulan_array = range(1, $jangka_waktu);
        $rincian = [];

        foreach ($bulan_array as $i) {
            $bulanberikut = ($bulansekarang + $i) % 12 ?: 12;
            $tahunsekarang += ($bulansekarang + $i) > 12 ? 1 : 0;
            $jmlhari = Carbon::createFromDate($tahunsekarang, $bulanberikut, 1)->daysInMonth;

            $tanggal_setoran = date('d/m/Y', strtotime("+$i month", strtotime($tgl_real)));
            $hbunga = (($plafon * $suku_bunga) / 100) * $jmlhari / 365;

            // Angsuran pokok hanya setiap 6 bulan
            $pokok = (($i) % 6 == 0) ? $hpokok : 0;
            $angsuran = $hbunga + $pokok;

            $sisa_plafon = max($plafon - $pokok, 0);

            $rincian[] = [
                'bulan_ke' => $i,
                'tanggal_setoran' => $tanggal_setoran,
                'setoran_bunga' => $hbunga,
                'setoran_pokok' => $pokok,
                'jumlah_setoran' => $angsuran,
                'sisa_plafon' => $sisa_plafon,
            ];

            if ($pokok > 0) {
                $plafon -= $hpokok;
            }
        }
        return $rincian;
    }
}
