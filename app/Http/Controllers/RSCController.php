<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\RSC;
use App\Models\Data;
use App\Models\Jasa;
use App\Models\Lain;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use App\Models\Pertanian;
use App\Models\Perdagangan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class RSCController extends Controller
{
    public function index()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
                'data_pengajuan.plafon',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->whereNotIn('rsc_data_pengajuan.status', ['Batal RSC'])
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);

        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    public function index_analisa()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->leftJoin('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc') // Perbaikan di sini
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'rsc_data_pengajuan.status',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'rsc_data_survei.kantor_kode',
                'data_pengajuan.plafon'
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where(function ($query) {
                $query->where('rsc_data_pengajuan.status', 'Proses Analisa')
                    ->orWhere('rsc_data_pengajuan.status', 'Proses Survei');
            })
            ->where('rsc_data_survei.surveyor_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc')
            ->paginate(10);


        $kasi = DB::table('v_users')->where('role_name', 'Kasi Analis')->get();
        $surveyor = DB::table('v_users')
            ->where('role_name', 'Staff Analis')
            ->where('is_active', 1)
            ->get();

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }
        // dd($data);
        return view('rsc.analisa.index', [
            'kasi' => $kasi,
            'surveyor' => $surveyor,
            'data' => $data,
        ]);
    }

    // public function tambah_rsc(Request $request)
    // {
    //     try {
    //         $cek_kode_pengajuan = Pengajuan::where('kode_pengajuan', $request->pengajuan_kode)->first();

    //         if (is_null($cek_kode_pengajuan)) {
    //             return redirect()->back()->with('error', 'Data tidak ditemukan');
    //         } elseif ($cek_kode_pengajuan->on_current == "0") {
    //             return redirect()->back()->with('error', 'Data belum REALISASI');
    //         }

    //         $data = $request->validate([
    //             'pengajuan_kode' => 'required',
    //             'pengajuan_kode' => 'required',
    //             'jenis_persetujuan' => 'required',
    //             'surveyor_kode' => 'required',
    //             'kasi_kode' => 'required',
    //         ]);

    //         if (
    //             $request->pengajuan_kode == "" ||
    //             $request->pengajuan_kode == "" ||
    //             $request->jenis_persetujuan == "" ||
    //             $request->surveyor_kode == "" ||
    //             $request->kasi_kode == ""
    //         ) {
    //             return redirect()->back()->with('error', 'Data tidak boleh kosong!!!');
    //         }

    //         $kode_nasabah = DB::table('data_pengajuan')
    //             ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
    //             ->where('data_pengajuan.kode_pengajuan', '=', $request->pengajuan_kode)->first();
    //         //
    //         if (is_null($kode_nasabah)) {
    //             return redirect()->back()->with('error', 'Nasabah tidak ada.');
    //         }

    //         $kode_rsc = $this->kodeacak('RSC');
    //         $data = [
    //             'pengajuan_kode' => $request->pengajuan_kode,
    //             'kode_rsc' => $kode_rsc,
    //             'nasabah_kode' => $kode_nasabah->nasabah_kode,
    //             'jenis_persetujuan' => $request->jenis_persetujuan,
    //             'kasi_kode' => $request->kasi_kode,
    //             'surveyor_kode' => $request->surveyor_kode,
    //             'status' => 'Proses Analisa',
    //             'input_user' => Auth::user()->code_user,
    //             'created_at' => now(),
    //         ];

    //         $insert = DB::table('rsc_data_pengajuan')->insert($data);

    //         if ($insert) {
    //             return redirect()->back()->with('success', 'Berhasil menambahkan data.');
    //         } else {
    //             return redirect()->back()->with('error', 'Data gagal ditambahkan.');
    //         }
    //     } catch (\Throwable $th) {
    //         return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
    //     }
    // }

    public function tambah_rsc(Request $request)
    {
        try {
            $cek_kode_pengajuan = Pengajuan::where('kode_pengajuan', $request->pengajuan_kode)->first();

            if (is_null($cek_kode_pengajuan)) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            } elseif ($cek_kode_pengajuan->on_current == "0") {
                return redirect()->back()->with('error', 'Data belum REALISASI');
            }

            $data = $request->validate([
                'pengajuan_kode' => 'required',
                'pengajuan_kode' => 'required',
                'jenis_persetujuan' => 'required',
                'surveyor_kode' => 'required',
                'kasi_kode' => 'required',
            ]);

            if (
                $request->pengajuan_kode == "" ||
                $request->pengajuan_kode == "" ||
                $request->jenis_persetujuan == "" ||
                $request->surveyor_kode == "" ||
                $request->kasi_kode == ""
            ) {
                return redirect()->back()->with('error', 'Data tidak boleh kosong!!!');
            }

            $kode_nasabah = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->where('data_pengajuan.kode_pengajuan', '=', $request->pengajuan_kode)->first();
            //
            if (is_null($kode_nasabah)) {
                return redirect()->back()->with('error', 'Nasabah tidak ada.');
            }

            $kode_rsc = $this->kodeacak('RSC');
            $data = [
                'pengajuan_kode' => $request->pengajuan_kode,
                'kode_rsc' => $kode_rsc,
                'nasabah_kode' => $kode_nasabah->nasabah_kode,
                'jenis_persetujuan' => $request->jenis_persetujuan,
                'kasi_kode' => $request->kasi_kode,
                'surveyor_kode' => $request->surveyor_kode,
                'status' => 'Penjadwalan',
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            $insert = DB::table('rsc_data_pengajuan')->insert($data);

            if ($insert) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data.');
            } else {
                return redirect()->back()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }

    public function data_kredit(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = DB::table('data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
                ->join('data_spk', 'data_spk.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
                ->select(
                    'data_nasabah.nama_nasabah',
                    'data_nasabah.alamat_ktp',
                    'data_nasabah.no_telp',
                    'data_pengajuan.plafon',
                    'data_pengajuan.produk_kode',
                    'data_pengajuan.metode_rps',
                    'data_pengajuan.jangka_waktu',
                    // 'data_pengajuan.jenis_persetujuan',
                    'data_spk.no_spk',
                    'data_spk.created_at',
                    'data_spk.updated_at',
                )
                ->where('data_pengajuan.kode_pengajuan', $enc)
                ->get();
            //
            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            if (count($data) > 0) {
                $tgl_realisasi = Carbon::createFromFormat('Y-m-d H:i:s', $data[0]->created_at);
                $tgl_jth_tempo = $tgl_realisasi->addMonths($data[0]->jangka_waktu);
                $data[0]->tgl_jth_tempo = $tgl_jth_tempo->format('d-m-Y');
            } else {
                return redirect()->back()->with('error', 'Data tidak ada.');
            }

            $perdagangan = Perdagangan::where('pengajuan_kode', $enc)->get();
            $pertanian = Pertanian::where('pengajuan_kode', $enc)->get();
            $jasa = Jasa::where('pengajuan_kode', $enc)->get();
            $lain = Lain::where('pengajuan_kode', $enc)->get();

            $jenis_usaha = Collection::make()
                ->merge($perdagangan)
                ->merge($pertanian)
                ->merge($jasa)
                ->merge($lain);

            $data_rsc = DB::table('rsc_data_pengajuan')->where('pengajuan_kode', $enc)->where('kode_rsc', $enc_rsc)->first();
            $biaya_rsc = DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->first();

            $rsc_data_pengajuan = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->first();
            if (!is_null($rsc_data_pengajuan)) {
                $data[0]->jenis_persetujuan = $rsc_data_pengajuan->jenis_persetujuan;
            } else {
                $data[0]->jenis_persetujuan = null;
            }

            //Cek SPK RSC
            $spk_rsc = DB::table('rsc_spk')
                ->join('rsc_data_pengajuan', 'rsc_data_pengajuan.pengajuan_kode', '=', 'rsc_spk.pengajuan_kode')
                ->select(
                    'rsc_spk.no_spk',
                    'rsc_spk.created_at',
                    'rsc_data_pengajuan.penentuan_plafon',
                    DB::raw("DATE_FORMAT((COALESCE(rsc_spk.created_at, CURDATE()) + INTERVAL rsc_data_pengajuan.jangka_waktu MONTH), '%d-%m-%Y') as tgl_akhir")
                )
                ->where('rsc_spk.pengajuan_kode', $enc)->latest()->first();
            if (!is_null($spk_rsc)) {
                $data[0]->no_spk = $spk_rsc->no_spk;
                $data[0]->plafon = $spk_rsc->penentuan_plafon;
                $data[0]->tgl_jth_tempo = $spk_rsc->tgl_akhir;
            }
            //Cek SPK RSC

            return view('rsc.data-kredit', [
                'data' => $data[0],
                'usaha' => $jenis_usaha,
                'data_rsc' => $data_rsc,
                'biaya_rsc' => $biaya_rsc,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update_data_kredit(Request $request)
    {
        try {
            $td =  (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_denda ?? 0);

            $tanggal = $request->tgl_jth_tempo;

            $tanggalCarbon = Carbon::createFromFormat('d-m-Y', $tanggal);

            $tgl_jth_temp = $tanggalCarbon->format('Ymd');


            $total = (int)str_replace(["Rp.", " ", "."], "", $request->baki_debet ?? 0) + (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_bunga ?? 0);
            $data = [
                'tgl_jth_tempo' => $tgl_jth_temp,
                'klasifikasi_kredit' => Str::upper($request->klasifikasi_kredit),
                'baki_debet' => (int)str_replace(["Rp.", " ", "."], "", $request->baki_debet ?? 0),
                'plafon' => (int)str_replace(["Rp.", " ", "."], "", $request->plafon ?? 0),
                'penentuan_plafon_temp' => $total,
                'jenis_persetujuan' => Str::upper($request->jenis_persetujuan),
                'tunggakan_poko' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_pokok ?? 0),
                'tunggakan_bunga' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_bunga ?? 0),
                'tunggakan_denda' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_denda ?? 0),
                'total_tunggakan' => (int)str_replace(["Rp.", " ", "."], "", $request->total_tunggakan ?? 0),
                'jml_tgk_pokok' => (int)str_replace(["Rp.", " ", "."], "", $request->jml_tunggakan_pokok ?? 0),
                'jml_tgk_bunga' => (int)str_replace(["Rp.", " ", "."], "", $request->jml_tunggakan_bunga ?? 0),
                'pokok_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pk_dibayar ?? 0),
                'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bg_dibayar ?? 0),
                'penentuan_plafon' => (int)str_replace(["Rp.", " ", "."], "", $request->penentuan_plafon ?? 0),
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_data_pengajuan')
                ->where('pengajuan_kode', $request->query('kode'))
                ->where('kode_rsc', $request->query('rsc'))
                ->update($data);

            //==Data table rsc_biaya==//
            $total = (int)str_replace(["Rp.", " ", "."], "", $request->pk_dibayar ?? 0) + (int)str_replace(["Rp.", " ", "."], "", $request->bg_dibayar ?? 0);
            $data2 = [
                'kode_rsc' => $request->rsc,
                'poko_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pk_dibayar ?? 0),
                'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bg_dibayar ?? 0),
                'total' => $total,
            ];

            $cek_biaya = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->first();

            if (is_null($cek_biaya)) {
                $data2['created_at'] = now();
                $insert_biaya = DB::table('rsc_biaya')->insert($data2);
            } else {
                $data2['updated_at'] = now();
                $update_biaya = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->update($data2);
            }
            //==Data table rsc_biaya==//

            if ($update) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data.');
            } else {
                return redirect()->back()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }

    public function update_biaya_rsc(Request $request)
    {
        try {
            $cek = DB::table('rsc_biaya')
                ->where('kode_rsc', $request->rsc)
                ->first();
            //
            if (is_null($cek)) {
                $data = [
                    'kode_rsc' => $request->rsc,
                    'administrasi' => $request->adm,
                    'administrasi_nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->persentase ?? 0),
                    'asuransi_jiwa' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_jiwa ?? 0),
                    'asuransi_tlo' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_tlo ?? 0),
                    'poko_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pokok_dibayar ?? 0),
                    'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bunga_dibayar ?? 0),
                    'denda_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->denda_dibayar ?? 0),
                    'total' => (int)str_replace(["Rp.", " ", "."], "", $request->total_biaya ?? 0),
                    'ujroh' => (int)str_replace(["Rp.", " ", "."], "", $request->ujroh ?? 0),
                    'created_at' => now(),
                ];

                $insert = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->insert($data);
                if ($insert) {
                    return redirect()->back()->with('success', 'Berhasil menambahkan data.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal ditambahkan.');
                }
            } else {
                $data = [
                    'kode_rsc' => $request->rsc,
                    'administrasi' => $request->adm,
                    'administrasi_nominal' => (int)str_replace(["Rp.", " ", "."], "", $request->persentase ?? 0),
                    'asuransi_jiwa' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_jiwa ?? 0),
                    'asuransi_tlo' => (int)str_replace(["Rp.", " ", "."], "", $request->asuransi_tlo ?? 0),
                    'poko_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pokok_dibayar ?? 0),
                    'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bunga_dibayar ?? 0),
                    'denda_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->denda_dibayar ?? 0),
                    'total' => (int)str_replace(["Rp.", " ", "."], "", $request->total_biaya ?? 0),
                    'ujroh' => (int)str_replace(["Rp.", " ", "."], "", $request->ujroh ?? 0),
                    'updated_at' => now(),
                ];

                $insert = DB::table('rsc_biaya')->where('kode_rsc', $request->rsc)->update($data);
                if ($insert) {
                    return redirect()->back()->with('success', 'Berhasil merubah data.');
                } else {
                    return redirect()->back()->with('error', 'Data gagal dirubah.');
                }
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }

    public function delete_rsc(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->query('rsc'));
            $data_rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $rsc)->first();

            if (!is_null($data_rsc)) {
                $data = ['status' => 'Batal RSC'];
                DB::table('rsc_data_pengajuan')->where('kode_rsc', $rsc)->update($data);
                return redirect()->back()->with('success', 'Data berhasil dihapus.');
            } else {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function konfirmasi_index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('kode'));
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $data = RSC::get_data_rsc();

            foreach ($data as $item) {
                $item->kode = $request->query('kode');
                $item->rsc = $request->query('rsc');
            }

            return view('rsc.konfirmasi.analisa', [
                'data' => $data[0]
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function konfirmasi_update(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->query('rsc'));
            $cek_pengajuan_rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->first();
            $cek_biaya_rsc = DB::table('rsc_biaya')->where('kode_rsc', $enc_rsc)->first();
            $cek_kondisi_usaha = DB::table('rsc_kondisi_usaha')->where('kode_rsc', $enc_rsc)->first();
            $cek_agunan = DB::table('rsc_agunan')->where('kode_rsc', $enc_rsc)->first();

            $cek_perdagangan = DB::table('rsc_au_perdagangan')->where('kode_rsc', $enc_rsc)->get();
            $cek_pertanian = DB::table('rsc_au_pertanian')->where('kode_rsc', $enc_rsc)->get();
            $cek_jasa = DB::table('rsc_au_jasa')->where('kode_rsc', $enc_rsc)->get();
            $cek_lain = DB::table('rsc_au_lain')->where('kode_rsc', $enc_rsc)->get();

            $cek_all = count($cek_perdagangan) + count($cek_pertanian) + count($cek_jasa) + count($cek_lain);

            $cek_keuangan = DB::table('rsc_analisa_keuangan')->where('kode_rsc', $enc_rsc)->first();


            if (is_null($cek_pengajuan_rsc->baki_debet)) {
                return redirect()->back()->with('error', 'Data kredit belum diisi.');
            } else if (is_null($cek_biaya_rsc)) {
                return redirect()->back()->with('error', 'Biaya RSC belum diisi.');
            } else if (is_null($cek_kondisi_usaha)) {
                return redirect()->back()->with('error', 'Faktor dipenilaian debitur belum diisi.');
            } else if (is_null($cek_agunan)) {
                return redirect()->back()->with('error', 'kondisi agunan belum diisi.');
            } else if ($cek_all <= 0) {
                return redirect()->back()->with('error', 'Data usaha harus diisi.');
            } else if (is_null($cek_keuangan)) {
                return redirect()->back()->with('error', 'Keuangan harus diisi.');
            } else if (is_null($cek_pengajuan_rsc->total_angsuran)) {
                return redirect()->back()->with('error', 'Usulan plafon harus diisi.');
            }

            $data = [
                'status' => 'Proses Persetujuan',
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data);
            if ($update) {
                return redirect()->back()->with('success', 'Konfirmasi analisa RSC berhasil.');
            } else {
                return redirect()->back()->with('error', 'Konfirmasi analisa RSC gagal.');
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }


    public function simpan_jadul(Request $request)
    {
        try {
            $enc_rsc = Crypt::decrypt($request->input('rsc'));
            $cek_survei = DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)
                ->select('catatan_survei', 'catatan_jadul_1', 'catatan_jadul_2')
                ->first();
            //
            if (is_null($request->keterangan)) {
                return redirect()->back()->with('error', 'Masukan catatan minimal 1 kata.');
            }

            if (is_null($cek_survei->catatan_survei)) {
                $data = [
                    'catatan_survei' => strtoupper($request->keterangan),
                    'updated_at' => now(),
                ];

                $data2 = [
                    'status' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                DB::transaction(function () use ($data2, $data, $enc_rsc) {
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->update($data);
                });
            } else if (is_null($cek_survei->catatan_jadul_1)) {
                $data = [
                    'catatan_jadul_1' => strtoupper($request->keterangan),
                    'updated_at' => now(),
                ];

                $data2 = [
                    'status' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                DB::transaction(function () use ($data2, $data, $enc_rsc) {
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->update($data);
                });
            } else if (is_null($cek_survei->catatan_jadul_2)) {
                $data = [
                    'catatan_jadul_2' => strtoupper($request->keterangan),
                    'updated_at' => now(),
                ];

                $data2 = [
                    'status' => 'Penjadwalan',
                    'updated_at' => now(),
                ];

                DB::transaction(function () use ($data2, $data, $enc_rsc) {
                    DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc_rsc)->update($data2);
                    DB::table('rsc_data_survei')->where('kode_rsc', $enc_rsc)->update($data);
                });
            } else {
                return redirect()->back()->with('error', 'Catatan sudah penuh.');
            }
            return redirect()->back()->with('success', 'Anda berhasil melakukan pembatalan survei');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function index_notifikasi()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Notifikasi')
            ->orWhere('rsc_data_survei.direksi_kode', Auth::user()->code_user)
            ->orWhere('rsc_data_survei.kasi_kode', Auth::user()->code_user)
            ->orWhere('rsc_data_survei.kabag_kode', Auth::user()->code_user)
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        $data = $data->paginate(10);

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.notifikasi.index', [
            'data' => $data
        ]);
    }

    public function get_notifikasi(Request $request)
    {
        try {
            $rsc = Crypt::decrypt($request->input('kode'));
            $data = DB::table('rsc_data_pengajuan')
                ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
                ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
                ->select(
                    'rsc_data_pengajuan.kode_rsc',
                    'data_nasabah.nama_nasabah',
                    'data_pengajuan.produk_kode',
                    'rsc_data_pengajuan.penentuan_plafon',
                    'rsc_data_pengajuan.jangka_waktu',
                )
                ->where('rsc_data_pengajuan.kode_rsc', $rsc)->first();

            $lasts = DB::table('rsc_notifikasi')->latest('nomor')->first();
            if (is_null($lasts)) {
                $count = 0001;
            } else {
                $count = (int) $lasts->nomor + 1;
            }
            $lengths = 4;
            $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);


            $now = Carbon::now();
            $bulan = $now->month;
            $romawi = Data::romawi($bulan);

            $notif = $kodes . '/' . 'NK' . '/' . 'PBA' . '/' . $romawi . '/' . $now->year;

            $data->kode_notif = $notif;
            $data->nomor = $kodes;
            return response()->json($data);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpan_notifikasi(Request $request)
    {
        try {
            $cek = $request->validate([
                'nomor' => 'required',
                'kode_rsc' => 'required',
                'nomor' => 'required',
                'kode_notifikasi' => 'required',
            ]);

            $data = [
                'nomor' => $request->nomor,
                'kode_rsc' => $request->kode_rsc,
                'nomor' => $request->nomor,
                'input_user' => Auth::user()->code_user,
                'no_notifikasi' => $request->kode_notifikasi,
                'created_at' => now(),
            ];

            $data2 = ['status' => 'Perjanjian Kredit'];

            DB::transaction(function () use ($data, $data2, $request) {
                $insert = DB::table('rsc_notifikasi')->insert($data);
                $update = DB::table('rsc_data_pengajuan')->where('kode_rsc', $request->kode_rsc)->update($data2);
            });

            return redirect()->back()->with('success', 'Data berhasil disimpan.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal disimpan.');
        }
    }

    public function pk_index()
    {
        $keyword = request('keyword');
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('rsc_data_survei', 'rsc_data_survei.kode_rsc', '=', 'rsc_data_pengajuan.kode_rsc')
            ->select(
                'rsc_data_pengajuan.id',
                'rsc_data_pengajuan.created_at as tanggal_rsc',
                'rsc_data_pengajuan.pengajuan_kode as kode_pengajuan',
                'rsc_data_pengajuan.kode_rsc',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_survei.kantor_kode',
                'data_pengajuan.plafon',
            )

            ->where(function ($query) use ($keyword) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_pengajuan.kode_rsc', 'like', '%' . $keyword . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $keyword . '%')
                    ->orWhere('rsc_data_survei.kantor_kode', 'like', '%' . $keyword . '%');
            })

            ->where('rsc_data_pengajuan.status', 'Perjanjian Kredit')
            ->orderBy('rsc_data_pengajuan.created_at', 'desc');

        $data = $data->paginate(10);

        foreach ($data as $item) {
            $item->kode = Crypt::encrypt($item->kode_pengajuan);
            $item->rsc = Crypt::encrypt($item->kode_rsc);
        }

        return view('rsc.perjanjian_kredit.index', [
            'data' => $data
        ]);
    }

    public function pk_get(Request $request)
    {
        $enc = Crypt::decrypt($request->input('kode'));
        $data_rsc = DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc)->first();

        $cek_spk = DB::table('rsc_spk')->exists();
        if (!$cek_spk) {
            $count = 000001;
        } else {
            $nomor = DB::table('rsc_spk')->latest()->first();
            $count = (int) $nomor->nomor + 1;
        }

        $lengths = 6;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);

        $now = Carbon::now();
        $bulan = $now->month;
        $romawi = Data::romawi($bulan);

        $data_rsc->no_spk_rsc = $kodes . '/' . 'RST' . '/' . 'PBA' . '/' . $romawi . '/' . $now->year;
        $data_rsc->nomor = $kodes;

        $biaya = DB::table('rsc_biaya')->where('kode_rsc', $enc)->first();
        $data_rsc->tunggakan_bunga = $biaya->bunga_dibayar;
        $data_rsc->tunggakan_denda = $biaya->denda_dibayar;

        return response()->json($data_rsc);
    }

    public function pk_simpan(Request $request)
    {
        try {
            $cek = DB::table('rsc_data_pengajuan')->where('kode_rsc', $request->kode_rsc)->first();
            $enc = $request->kode_rsc;

            $biaya = DB::table('rsc_biaya')->where('kode_rsc', $request->kode_rsc)->first();
            $total = $biaya->administrasi_nominal + $biaya->asuransi_jiwa +
                $biaya->asuransi_tlo + $biaya->poko_dibayar + $biaya->poko_dibayar +
                (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga) + (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda);


            $data1 = [
                'bunga_dibayar' => (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_bunga),
                'denda_dibayar' =>  (int)str_replace(["Rp", " ", "."], "", $request->tunggakan_denda),
                'total' =>  $total,
            ];

            $data2 = [
                'pengajuan_kode' => $cek->pengajuan_kode,
                'kode_rsc' => $request->kode_rsc,
                'nomor' => $request->nomor,
                'no_spk' => $request->pk_rsc,
                'created_at' => now(),
            ];

            $data3 = [
                'status' => 'Selesai'
            ];

            $cek_spk = DB::table('rsc_spk')->where('kode_rsc', $request->kode_rsc)->first();
            if ($cek_spk) {
                return redirect()->back()->with('error', 'Data telah memiliki kode SPK.');
            }

            DB::transaction(function () use ($data1, $data2, $data3, $enc) {
                DB::table('rsc_biaya')->where('kode_rsc', $enc)->update($data1);
                DB::table('rsc_spk')->insert($data2);
                DB::table('rsc_data_pengajuan')->where('kode_rsc', $enc)->update($data3);
            });

            return redirect()->back()->with('success', 'Berhasil Generate PK.');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }



    private function kodeacak($name)
    {
        do {
            $koderand = random_int(10000, 99999);
            $haskode = $name . $koderand;

            // Cek apakah kode sudah ada di tabel
            $existingCode = DB::table('rsc_data_pengajuan')->where('kode_rsc', $haskode)->exists();
        } while ($existingCode);

        return $haskode;
    }
}
