<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $data = DB::table('rsc_data_pengajuan')
            ->join('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'rsc_data_pengajuan.nasabah_kode')
            ->join('data_survei', 'data_survei.pengajuan_kode', '=', 'rsc_data_pengajuan.pengajuan_kode')
            ->join('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'rsc_data_pengajuan.pengajuan_kode')
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
                'penentuan_plafon' => $total,
                'tunggakan_poko' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_pokok ?? 0),
                'tunggakan_bunga' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_bunga ?? 0),
                'tunggakan_denda' => (int)str_replace(["Rp.", " ", "."], "", $request->tunggakan_denda ?? 0),
                'total_tunggakan' => (int)str_replace(["Rp.", " ", "."], "", $request->total_tunggakan ?? 0),
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_data_pengajuan')
                ->where('pengajuan_kode', $request->query('kode'))
                ->where('kode_rsc', $request->query('rsc'))
                ->update($data);

            if ($update) {
                return redirect()->back()->with('success', 'Berhasil menambahkan data.');
            } else {
                return redirect()->back()->with('error', 'Data gagal ditambahkan.');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Informasikan ke Staff IT.');
        }
    }

    public function update_penentuan_plafon(Request $request)
    {
        try {

            $rsc = DB::table('rsc_data_pengajuan')
                ->where('pengajuan_kode', $request->kode)
                ->where('kode_rsc', $request->rsc)->first();

            if (is_null($rsc)) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            $total = (int)str_replace(["Rp.", " ", "."], "", $rsc->baki_debet ?? 0) + (int)str_replace(["Rp.", " ", "."], "", $rsc->tunggakan_bunga ?? 0);

            $data = [
                'pokok_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->pk_dibayar ?? 0),
                'bunga_dibayar' => (int)str_replace(["Rp.", " ", "."], "", $request->bg_dibayar ?? 0),
                'penentuan_plafon' => $total,
                'updated_at' => now(),
            ];

            $update = DB::table('rsc_data_pengajuan')
                ->where('kode_rsc', $request->query('rsc'))
                ->where('kode_rsc', $request->rsc)
                ->update($data);


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
