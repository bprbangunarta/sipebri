<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Keuangan;
use App\Models\Tambahan;
use App\Models\Pengajuan;
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
                return redirect()->back()->with('error', 'Lengkapi usulan kredit terlebih dahulu');
            }

            if (is_null($sandibi->bi_sifat_kode)) {
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

            $sandidata = Midle::sandibi($enc);
            // dd($sandidata[0]);
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
                'sandibi' => $sandidata[0],
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpansandi(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $data = [
                'bi_sifat_kode' => $request->bi_sifat_kode,
                'bi_penggunaan_kode' => $request->bi_penggunaan_kode,
                'bi_gol_penjamin_kode' => $request->bi_gol_penjamin_kode,
                // 'by_fiducia' => $request->ket_kewajiban1,
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

    public function updatesandi(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $data = [
                'bi_sifat_kode' => $request->bi_sifat_kode,
                'bi_penggunaan_kode' => $request->bi_penggunaan_kode,
                'bi_gol_penjamin_kode' => $request->bi_gol_penjamin_kode,
                // 'by_fiducia' => $request->ket_kewajiban1,
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
            $keuangan = Keuangan::where('pengajuan_kode', $enc)->pluck('keuangan_perbulan')->first();
            $RC = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first('rc');
            // $tk_agunan = DB::table('a5c_collateral')->where('pengajuan_kode', $enc)->first();

            //Cek kemampuan keuangan sudah terisi apa belum
            if (is_null($keuangan) || $keuangan == 0) {
                return redirect()->back()->with('error', 'Keuangan perbulan tidak boleh kosong');
            }

            //Taksasi
            $taksasi = DB::table('data_jaminan')->where('pengajuan_kode', $enc)->get();
            //total semua nilai taksasi
            $tak = [];
            for ($i = 0; $i < count($taksasi); $i++) {
                $tak[] = $taksasi[$i]->nilai_taksasi ?? 0;
            }
            $totaltaksasi = array_sum($tak);

            //kebutuhan dana
            $dana = DB::table('a_kebutuhan_dana')->where('pengajuan_kode', $enc)->first();
            if (is_null($dana)) {
                $kdana = 0;
            } else {
                $kdana = $dana->kebutuhan_dana;
            }

            //Cek data usulan
            $usulan = DB::table('a_memorandum')->where('pengajuan_kode', $enc)->first();

            if (is_null($usulan)) {
                $usulan = (object) [
                    'kebutuhan_dana' => $kdana,
                    'usulan_plafond' => null,
                    'sebelum_realisasi' => null,
                    'syarat_tambahan' => null,
                    'syarat_lainnya' => null,
                    'pengikatan' => null,
                ];
            } else {
                $usulan->kebutuhan_dana = $kdana;
            }

            //Menghitung RC
            if ($cek[0]->produk_kode == "KBT" && $cek[0]->metode_rps == 'EFEKTIF MUSIMAN') {
                $bunga = (((int)$cek[0]->plafon * $cek[0]->suku_bunga) / 100) / 12;
                $pokok_bulanan = (int)$cek[0]->plafon / $cek[0]->jangka_waktu;
                $angsuran = $bunga + $pokok_bulanan;
                $rc = ($angsuran / $keuangan) * 100;

                //Max Plafon
                $mp_sb = (int)$cek[0]->suku_bunga / 100;
                $cek[0]->maxplafon = ((int)$keuangan * (int)$cek[0]->jangka_waktu) / (1 + ((int)$cek[0]->jangka_waktu * $mp_sb / 12));
            } else if ($cek[0]->produk_kode == "KBT" && $cek[0]->metode_rps == 'FLAT') {

                $bunga = (((int)$cek[0]->plafon * $cek[0]->suku_bunga) / 100) / 12;
                $pokok_bulanan = (int)$cek[0]->plafon / $cek[0]->jangka_waktu;
                $angsuran = $bunga + $pokok_bulanan;
                $rc = ($bunga / $keuangan) * 100;

                $tani = DB::table('au_pertanian')->where('pengajuan_kode', $enc)->get();
                //total semua nilai tani
                $tn = [];

                for ($i = 0; $i < count($tani); $i++) {
                    $tn[] = $tani[$i]->laba_bersih ?? 0;
                }
                $totaltn = array_sum($tn);
                $saving = $totaltn;


                $detail_pertanian = DB::table('du_pertanian')->where('usaha_kode', $tani[0]->kode_usaha)->get();
                $tdu = [];
                foreach ($detail_pertanian as $item) {
                    $tdu[] = $item->luas_sendiri + $item->luas_sewa + $item->luas_gadai;
                }

                $total_luas = array_sum($tdu);

                //Max Plafond Musiman
                $cek[0]->maxplafon = ($total_luas / 10000) * 15000000;

                $cek[0]->laba_usaha_pertanian = $saving;
            } else if ($cek[0]->metode_rps == 'EFEKTIF MUSIMAN' || $cek[0]->metode_rps == 'EFEKTIF') {

                $rc = Midle::perhitungan_rc($enc, $cek[0]->metode_rps, (int)$cek[0]->plafon, (int)$cek[0]->suku_bunga, (int)$cek[0]->jangka_waktu, (int)$cek[0]->grace_period);

                $tani = DB::table('au_pertanian')->where('pengajuan_kode', $enc)->get();
                //total semua nilai tani
                $tn = [];

                for ($i = 0; $i < count($tani); $i++) {
                    $tn[] = $tani[$i]->laba_bersih ?? 0;
                }

                $totaltn = array_sum($tn);

                $saving = ($totaltn * 70) / 100;

                //Max Plafond Musiman
                $cek[0]->maxplafon = $saving * ((int)$cek[0]->jangka_waktu / 6);

                $cek[0]->laba_usaha_pertanian = $saving;
            } else if ($cek[0]->metode_rps == 'EFEKTIF ANUITAS') {
                $ssb = $cek[0]->suku_bunga / 100;

                $sb = $ssb / 12;

                $rc = Midle::perhitungan_rc($enc, $cek[0]->metode_rps, (int)$cek[0]->plafon, (int)$cek[0]->suku_bunga, (int)$cek[0]->jangka_waktu, (int)$cek[0]->grace_period);

                //Max Plafon
                $cek[0]->maxplafon = $keuangan / ($sb * (pow(1 + $sb, $cek[0]->jangka_waktu) / (pow(1 + $sb, $cek[0]->jangka_waktu) - 1)));
                //
            } else if ($cek[0]->kategori == 'RELOAN' && $cek[0]->metode_rps == 'FLAT' && !is_null($cek[0]->grace_period)) {

                $rc = Midle::perhitungan_rc($enc, $cek[0]->metode_rps, (int)$cek[0]->plafon, (int)$cek[0]->suku_bunga, (int)$cek[0]->jangka_waktu, (int)$cek[0]->grace_period);
                //Max Plafon
                $mp_sb = (int)$cek[0]->suku_bunga / 100;

                $cek[0]->maxplafon = ((int)$keuangan * (int)$cek[0]->jangka_waktu) / (1 + ((int)$cek[0]->jangka_waktu * $mp_sb / 12));
            } elseif ($cek[0]->metode_rps == 'FLAT') {
                $rc = Midle::perhitungan_rc($enc, $cek[0]->metode_rps, (int)$cek[0]->plafon, (int)$cek[0]->suku_bunga, (int)$cek[0]->jangka_waktu, (int)$cek[0]->grace_period);
                //Max Plafon
                $mp_sb = (int)$cek[0]->suku_bunga / 100;

                $cek[0]->maxplafon = ((int)$keuangan * (int)$cek[0]->jangka_waktu) / (1 + ((int)$cek[0]->jangka_waktu * $mp_sb / 12));
            } else {
                $rc = Midle::perhitungan_rc($enc, $cek[0]->metode_rps, (int)$cek[0]->plafon, (int)$cek[0]->suku_bunga, (int)$cek[0]->jangka_waktu, (int)$cek[0]->grace_period);
                //Max Plafon
                $mp_sb = (int)$cek[0]->suku_bunga / 100;

                $cek[0]->maxplafon = ((int)$keuangan * (int)$cek[0]->jangka_waktu) / (1 + ((int)$cek[0]->jangka_waktu * $mp_sb / 12));
            }

            //validasi RC jika kosong
            if (is_null($RC)) {
                return redirect()->back()->with('error', 'Lengkapi A5C CAPACITY terlebih dahulu');
            }

            //cek RC jika ada perubahan analisa usaha
            if ($RC->rc !== number_format($rc, 2)) {
                $data = ['rc' => number_format($rc, 2)];
                DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->update($data);
            }
            $cek[0]->rc = number_format($rc, 2);
            $cek[0]->keuangan = $keuangan;
            $cek[0]->laba_usaha_pertanian = $saving ?? null;

            // $hasiltaksasi = Midle::taksasi_agunan($enc, (int)$cek[0]->plafon);
            //Taksasi
            $taksasi = DB::table('data_jaminan')->where('pengajuan_kode', $enc)->get();
            //total semua nilai taksasi
            $tak = [];
            for ($i = 0; $i < count($taksasi); $i++) {
                $tak[] = $taksasi[$i]->nilai_taksasi ?? 0;
            }

            $totaltaksasi = array_sum($tak);

            if (count($taksasi) != 0 && $totaltaksasi != 0) {
                $hasiltaksasi = (intval((int)$cek[0]->plafon) / $totaltaksasi) * 100;
            } else {
                $hasiltaksasi = 0;
            }

            $cek[0]->taksasiagunan = number_format($hasiltaksasi, 2) ?? 0;
            // dd($cek[0]);
            return view('staff.analisa.memorandum.usulan', [
                'data' => $cek[0],
                'usulan' => $usulan,
                'total_taksasi' => $totaltaksasi,
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
            $usul = str_replace(["Rp", " ", "."], "", $request->usulan_plafond);
            if ($usul < 500000) {
                return redirect()->back()->with('error', 'Usulan plafon tidak boleh 500.000 dan sesuaikan dengan kebutuhan');
            }

            $data = [
                'max_plafond' => (int)str_replace(["Rp", " ", "."], "", $request->max_plafond),
                'usulan_plafond' => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafond),
                'jangka_waktu' => $request->jangka_waktu,
                'pengikatan' => $request->pengikatan,
                'sebelum_realisasi' => $request->sebelum_realisasi,
                'syarat_tambahan' => $request->syarat_tambahan,
                'syarat_lainnya' => $request->syarat_lainnya,
                'updated_at' => now(),
            ];

            $data2 = [
                'plafon' => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafond),
                'jangka_waktu' => $request->jangka_waktu,
                'temp_plafon' => (int)$cek[0]->plafon,
                'suku_bunga' => $request->s_bunga,
                'b_admin' => number_format($request->b_admin, 2),
                'b_provisi' => number_format($request->b_provisi, 2),
                'b_penalti' => number_format($request->b_penalti, 2),
                'updated_at' => now(),
            ];
            // dd($request->all());
            //cek data memorandum sudah ada apa belum
            $memorandum = DB::table('a_memorandum')->where('pengajuan_kode', $enc)->first();
            // dd($data2);
            if (is_null($memorandum)) {
                $name = 'AMO';
                $length = 5;
                $kode = Midle::kodeacak_memorandum($name, $length);
                $data = [
                    'kode_analisa' => $kode,
                    'pengajuan_kode' => $enc,
                    'max_plafond' => (int)str_replace(["Rp", " ", "."], "", $request->max_plafond),
                    'usulan_plafond' => (int)str_replace(["Rp", " ", "."], "", $request->usulan_plafond),
                    'jangka_waktu' => $request->jangka_waktu,
                    'pengikatan' => $request->pengikatan,
                    'sebelum_realisasi' => $request->sebelum_realisasi,
                    'syarat_tambahan' => $request->syarat_tambahan,
                    'syarat_lainnya' => $request->syarat_lainnya,
                    'updated_at' => now(),
                ];
                DB::transaction(function () use ($data, $data2, $enc) {
                    Pengajuan::where('kode_pengajuan', $enc)->update($data2);
                    DB::table('a_memorandum')->where('pengajuan_kode', $enc)->insert($data);
                });
            }

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
