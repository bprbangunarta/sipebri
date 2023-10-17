<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Pengajuan;
use App\Models\Tambahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class AnalisaMemorandumController extends Controller
{
    public function kebutuhan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $data = DB::table('a_kebutuhan_dana')->where('pengajuan_kode', $enc)->first();

            if (is_null($data)) {
                return view('staff.analisa.memorandum.kebutuhan', [
                    'data' => $cek[0],
                ]);
            }
            // dd($data);
            return view('staff.analisa.memorandum.kebutuhan-edit', [
                'data' => $cek[0],
                'kebutuhan' => $data,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpankebutuhan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $name = 'AUT';
            $length = 5;
            $kode = Tambahan::kodeacak($name, $length);

            $data = [
                'kode_analisa' => $kode,
                'pengajuan_kode' => $enc,
                'modal_kerja' => (int)str_replace(["Rp", " ", "."], "", $request->modal_kerja),
                'investasi' => (int)str_replace(["Rp", " ", "."], "", $request->investasi),
                'konsumtif' => (int)str_replace(["Rp", " ", "."], "", $request->konsumtif),
                'pelunasan_kredit' => (int)str_replace(["Rp", " ", "."], "", $request->pelunasan_kredit),
                'take_over' => (int)str_replace(["Rp", " ", "."], "", $request->take_over),
                'kebutuhan_dana' => (int)str_replace(["Rp", " ", "."], "", $request->kebutuhan_dana),
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            DB::table('a_kebutuhan_dana')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal menambahkan data');
    }

    public function updatekebutuhan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = [
                'modal_kerja' => (int)str_replace(["Rp", " ", "."], "", $request->modal_kerja),
                'investasi' => (int)str_replace(["Rp", " ", "."], "", $request->investasi),
                'konsumtif' => (int)str_replace(["Rp", " ", "."], "", $request->konsumtif),
                'pelunasan_kredit' => (int)str_replace(["Rp", " ", "."], "", $request->pelunasan_kredit),
                'take_over' => (int)str_replace(["Rp", " ", "."], "", $request->take_over),
                'kebutuhan_dana' => (int)str_replace(["Rp", " ", "."], "", $request->kebutuhan_dana),
                'input_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];
            $a = DB::table('a_kebutuhan_dana')->where('pengajuan_kode', $enc)->first();
            DB::table('a_kebutuhan_dana')->where('id', $a->id)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal menambahkan data');
    }

    public function sandi(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            //Penggunaan Debitur
            $debitur = DB::table('bi_penggunaan_debitur')->get();
            //Sifat
            $sifat = DB::table('bi_sifat')->get();
            //gogolngan penjamin
            $golongan = DB::table('bi_golongan_penjamin')->get();
            //Sumber dana pelunasa
            $sumber = DB::table('bi_sumber_dana_pelunasan')->get();
            //Jenis usaha
            $jenis = DB::table('bi_jenis_usaha')->get();
            //sektor ekonomi
            $sektor = DB::table('bi_sektor_ekonomi')->get();
            //sektor ekonomi slik
            $slik = DB::table('bi_sektor_ekonomi_slik')->get();
            //Golongan Debitur
            $golongandebitur = DB::table('bi_golongan_debitur')->get();
            //Golongan Debitur Slik
            $golongandebiturslik = DB::table('bi_golongan_debitur_slik')->get();

            //Cek Data Sandi BI
            $sandibi = DB::table('a_memorandum')->where('pengajuan_kode', $enc)->first();

            if (is_null($sandibi)) {
                return view('staff.analisa.memorandum.sandi-bi', [
                    'data' => $cek[0],
                    'debitur' => $debitur,
                    'sifat' => $sifat,
                    'golongan' => $golongan,
                    'sumber' => $sumber,
                    'jenis' => $jenis,
                    'sektor' => $sektor,
                    'slik' => $slik,
                    'golongandebitur' => $golongandebitur,
                    'golongandebiturslik' => $golongandebiturslik,
                ]);
            }

            $sandidata = Midle::sandibi($sandibi);

            return view('staff.analisa.memorandum.sandi-bi-edit', [
                'data' => $cek[0],
                'debitur' => $debitur,
                'sifat' => $sifat,
                'golongan' => $golongan,
                'sumber' => $sumber,
                'jenis' => $jenis,
                'sektor' => $sektor,
                'slik' => $slik,
                'golongandebitur' => $golongandebitur,
                'golongandebiturslik' => $golongandebiturslik,
                'sandibi' => $sandidata,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpansandi(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $name = 'AMO';
            $length = 5;
            $kode = Midle::kodeacak_memorandum($name, $length);
            $data = [
                'kode_analisa' => $kode,
                'pengajuan_kode' => $enc,
                'bi_sifat_kode' => $request->bi_sifat_kode,
                'bi_penggunaan_kode' => $request->bi_penggunaan_kode,
                'bi_gol_penjamin_kode' => $request->bi_gol_penjamin_kode,
                'by_fiducia' => $request->ket_kewajiban1,
                'bagian_dijaminkan' => $request->ket_kewajiban2,
                'bi_sumber_pelunasan_kode' => $request->bi_sumber_pelunasan_kode,
                'bi_jenis_usaha_kode' => $request->bi_jenis_usaha_kode,
                'bi_sek_ekonomi_kode' => $request->bi_sek_ekonomi_kode,
                'bi_sek_ekonomi_slik' => $request->bi_sek_ekonomi_slik,
                'bi_gol_debitur_kode' => $request->bi_gol_debitur_kode,
                'bi_gol_debitur_slik' => $request->bi_gol_debitur_slik,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            DB::table('a_memorandum')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function updatesandi(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = [
                'bi_sifat_kode' => $request->bi_sifat_kode,
                'bi_penggunaan_kode' => $request->bi_penggunaan_kode,
                'bi_gol_penjamin_kode' => $request->bi_gol_penjamin_kode,
                'by_fiducia' => $request->ket_kewajiban1,
                'bagian_dijaminkan' => $request->ket_kewajiban2,
                'bi_sumber_pelunasan_kode' => $request->bi_sumber_pelunasan_kode,
                'bi_jenis_usaha_kode' => $request->bi_jenis_usaha_kode,
                'bi_sek_ekonomi_kode' => $request->bi_sek_ekonomi_kode,
                'bi_sek_ekonomi_slik' => $request->bi_sek_ekonomi_slik,
                'bi_gol_debitur_kode' => $request->bi_gol_debitur_kode,
                'bi_gol_debitur_slik' => $request->bi_gol_debitur_slik,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            DB::table('a_memorandum')->where('pengajuan_kode', $enc)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function usulan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $taksasi = DB::table('data_jaminan')->where('pengajuan_kode', $enc)->get();
            $rc = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first('rc');
            //total semua nilai taksasi
            $tak = [];
            for ($i = 0; $i < count($taksasi); $i++) {
                $tak[] = $taksasi[$i]->nilai_taksasi ?? 0;
            }
            $totaltaksasi = array_sum($tak);
            //Menghitung Max Plafon
            $cek[0]->maxplafon = ($totaltaksasi * 70) / 100;
            $cek[0]->rc = $rc->rc;

            //Menghitung Taksasi Agunan
            $taksasiagunan = (intval($cek[0]->plafon) / $totaltaksasi) * 100;
            $cek[0]->taksasiagunan = number_format($taksasiagunan, 2);
            // dd($cek[0]);

            return view('staff.analisa.memorandum.usulan', [
                'data' => $cek[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function updateusulan(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);

            $data = [
                'max_plafond' => (int)str_replace(["Rp", " ", "."], "", $request->max_plafond),
                'usulan_plafond' => $cek[0]->plafon,
                'jangka_waktu' => $request->jangka_waktu,
                'sebelum_realisasi' => $request->sebelum_realisasi,
                'syarat_tambahan' => $request->syarat_tambahan,
                'syarat_lainnya' => $request->syarat_lainnya,
                'updated_at' => now(),
            ];

            $data2 = [
                'plafon' => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafond),
                'jangka_waktu' => $request->jangka_waktu,
                'updated_at' => now(),
            ];

            DB::transaction(function () use ($data, $data2, $enc) {
                Pengajuan::where('kode_pengajuan', $enc)->update($data2);
                DB::table('a_memorandum')->where('pengajuan_kode', $enc)->update($data);
            });
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }
}
