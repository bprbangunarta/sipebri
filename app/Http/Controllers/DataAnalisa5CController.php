<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\Midle;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class DataAnalisa5CController extends Controller
{
    public function character(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $character = DB::table('a5c_character')->where('pengajuan_kode', $enc)->first();

            if (is_null($character)) {
                $data = Midle::karakter();
                return view('staff.analisa.5c.character', [
                    'data' => $cek[0],
                    'character' => $data,
                ]);
            }

            $nilai = Data::analisa5c_number($character->nilai_karakter);
            $character->nilai_karakter = $nilai;
            // dd($character);
            return view('staff.analisa.5c.character-edit', [
                'data' => $cek[0],
                'character' => $character,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpancharacter(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->nilai_karakter);
            $name = 'A5C';
            $length = 5;
            $kode = Midle::a5ckodeacak($name, $length);
            $data = [
                'kode_analisa' => $kode,
                'pengajuan_kode' => $enc,
                'gaya_hidup' => $request->gaya_hidup,
                'pengendalian_emosi' => $request->pengendalian_emosi,
                'perbuatan_tercela' => $request->perbuatan_tercela,
                'harmonis' => $request->harmonis,
                'konsisten' => $request->konsisten,
                'kepatuhan' => $request->kepatuhan,
                'hubungan_sosial' => $request->hubungan_sosial,
                'nilai_karakter' => $nilai,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            // dd($data);
            DB::table('a5c_character')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal menambahkan data');
    }

    public function updatecharacter(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->nilai_karakter);
            $data = [
                'gaya_hidup' => $request->gaya_hidup,
                'pengendalian_emosi' => $request->pengendalian_emosi,
                'perbuatan_tercela' => $request->perbuatan_tercela,
                'harmonis' => $request->harmonis,
                'konsisten' => $request->konsisten,
                'kepatuhan' => $request->kepatuhan,
                'hubungan_sosial' => $request->hubungan_sosial,
                'nilai_karakter' => $nilai,
                'input_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];

            DB::table('a5c_character')->where('pengajuan_kode', $enc)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('success', 'Gagal menambahkan data');
    }

    public function capacity(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $cek = Midle::analisa_usaha($enc);
            $keuangan = Keuangan::where('pengajuan_kode', $enc)->pluck('keuangan_perbulan')->first();

            if (is_null($keuangan)) {
                return redirect()->back()->with('error', 'Keuangan perbulan masih kosong');
            }

            //Menghitung RC
            $rc = Midle::perhitungan_rc($enc, $cek[0]->metode_rps, (int)$cek[0]->plafon, (int)$cek[0]->suku_bunga, (int)$cek[0]->jangka_waktu);

            //cek data capacity sudah ada apa belum
            $cap = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first();
            if (is_null($cap)) {
                $data = Midle::capacity();
                $data->rc = number_format($rc, 2);

                return view('staff.analisa.5c.capacity', [
                    'data' => $cek[0],
                    'capacity' => $data,
                ]);
            }

            if ($cap->rc !== $rc) {
                $data_rc = ['rc' => number_format($rc, 2)];
                DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->update($data_rc);
            }

            $cap = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first();
            $nilai = Data::analisa5c_number($cap->evaluasi_capacity);
            $cap->evaluasi_capacity = $nilai;

            return view('staff.analisa.5c.capacity-edit', [
                'data' => $cek[0],
                'capacity' => $cap,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpancapacity(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $character = DB::table('a5c_character')->where('pengajuan_kode', $enc)->first();

            if (is_null($character)) {
                return redirect()->back()->with('error', 'Lengkapi A5C CHARACTER terlebih dahulu');
            }

            $nilai = Data::analisa5c_text($request->evaluasi_capacity);
            $data = [
                'kode_analisa' => $character->kode_analisa,
                'pengajuan_kode' => $enc,
                'kontinuitas' => $request->kontinuitas,
                'pengalaman_usaha' => $request->pengalaman_usaha,
                'pertumbuhan_usaha' => $request->pertumbuhan_usaha,
                'laporan_keuangan' => $request->laporan_keuangan,
                'catatan_kredit' => $request->catatan_kredit,
                'kondisi_slik' => $request->kondisi_slik,
                'aset_diluar_usaha' => $request->aset_diluar_usaha,
                'aset_terkait_usaha' => $request->aset_terkait_usaha,
                'rc' => str_replace(array(' ', '%'), '', $request->rc),
                'evaluasi_capacity' => $nilai,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            DB::table('a5c_capacity')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('sucerrorcess', 'Gagal menambahkan data');
    }

    public function updatecapacity(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->evaluasi_capacity);
            $data = [
                'kontinuitas' => $request->kontinuitas,
                'pengalaman_usaha' => $request->pengalaman_usaha,
                'pertumbuhan_usaha' => $request->pertumbuhan_usaha,
                'laporan_keuangan' => $request->laporan_keuangan,
                'catatan_kredit' => $request->catatan_kredit,
                'kondisi_slik' => $request->kondisi_slik,
                'aset_diluar_usaha' => $request->aset_diluar_usaha,
                'aset_terkait_usaha' => $request->aset_terkait_usaha,
                'rc' => str_replace(array(' ', '%'), '', $request->rc),
                'evaluasi_capacity' => $nilai,
                'input_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];
            // dd($data);
            DB::table('a5c_capacity')->where('kode_analisa', $request->kode_analisa)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('sucerrorcess', 'Gagal menambahkan data');
    }

    public function capital(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $cap = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first();

            if (is_null($cap)) {
                return redirect()->back()->with('error', 'Lengkapi Analisa 5C Capacity terlebih dahulu');
            }

            $nilai = Data::analisa5c_number($cap->capital_evaluasi_capital);

            $cap->capital_evaluasi_capital = $nilai;
            return view('staff.analisa.5c.capital', [
                'data' => $cek[0],
                'capital' => $cap,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpancapital(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->capital_evaluasi_capital);
            $data = [
                'capital_sumber_modal' => $request->capital_sumber_modal,
                'capital_evaluasi_capital' => $nilai,
            ];
            DB::table('a5c_capacity')->where('kode_analisa', $request->kode_analisa)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function collateral(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $cap = DB::table('a5c_collateral')->where('pengajuan_kode', $enc)->first();

            //Taksasi Agunan
            $hasiltaksasi = Midle::taksasi_agunan($enc, $cek[0]->plafon);

            if (is_null($cap)) {
                $data = (object) ['taksasi_agunan' => number_format($hasiltaksasi, 2)];
                return view('staff.analisa.5c.collateral', [
                    'data' => $cek[0],
                    'collateral' => $data,
                ]);
            }
            $nilai = Data::analisa5c_number($cap->evaluasi_collateral) ?? 0;
            $cap->evaluasi_collateral = $nilai;
            $cap->taksasi_agunan = number_format($hasiltaksasi, 2);

            return view('staff.analisa.5c.collateral-edit', [
                'data' => $cek[0],
                'collateral' => $cap,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpancollateral(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->evaluasi_collateral);
            $cap = DB::table('a5c_character')->where('pengajuan_kode', $enc)->first();

            $data = [
                'kode_analisa' => $cap->kode_analisa,
                'pengajuan_kode' => $enc,
                'agunan_utama' => $request->agunan_utama,
                'legalitas_agunan' => $request->legalitas_agunan,
                'mudah_diuangkan' => $request->mudah_diuangkan,
                'kondisi_kendaraan' => $request->kondisi_kendaraan,
                'aspek_hukum' => $request->aspek_hukum,
                'agunan_tambahan' => $request->agunan_tambahan,
                'legalitas_agunan_tambahan' => $request->legalitas_agunan_tambahan,
                'stabilitas_harga' => $request->stabilitas_harga,
                'lokasi_shm' => $request->lokasi_shm,
                'taksasi_agunan' => str_replace(array(' ', '%'), '', $request->taksasi_agunan),
                'evaluasi_collateral' => $nilai,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];

            DB::table('a5c_collateral')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function updatecollateral(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->evaluasi_collateral);

            $data = [
                'agunan_utama' => $request->agunan_utama,
                'legalitas_agunan' => $request->legalitas_agunan,
                'mudah_diuangkan' => $request->mudah_diuangkan,
                'kondisi_kendaraan' => $request->kondisi_kendaraan,
                'aspek_hukum' => $request->aspek_hukum,
                'agunan_tambahan' => $request->agunan_tambahan,
                'legalitas_agunan_tambahan' => $request->legalitas_agunan_tambahan,
                'stabilitas_harga' => $request->stabilitas_harga,
                'lokasi_shm' => $request->lokasi_shm,
                'taksasi_agunan' => str_replace(array(' ', '%'), '', $request->taksasi_agunan),
                'evaluasi_collateral' => $nilai,
                'input_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];
            // dd($data, $request->all());
            DB::table('a5c_collateral')->where('kode_analisa', $request->kode_analisa)->update($data);
            return redirect()->back()->with('success', 'Berhasil melakukan perubahan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal melakukan perubahan data');
    }

    public function condition(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);
            $condition = DB::table('a5c_condition')->where('pengajuan_kode', $enc)->first();

            if (is_null($condition)) {
                return view('staff.analisa.5c.condition', [
                    'data' => $cek[0],
                ]);
            }

            $nilai = Data::analisa5c_number($condition->evaluasi_condition);
            $condition->evaluasi_condition = $nilai;

            return view('staff.analisa.5c.condition-edit', [
                'data' => $cek[0],
                'condition' => $condition,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function simpancondition(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->evaluasi_condition);
            $cek = DB::table('a5c_character')->where('pengajuan_kode', $enc)->first();
            $data = [
                'kode_analisa' => $cek->kode_analisa,
                'pengajuan_kode' => $enc,
                'persaingan_usaha' => $request->persaingan_usaha,
                'kondisi_alam' => $request->kondisi_alam,
                'regulasi_pemerintah' => $request->regulasi_pemerintah,
                'evaluasi_condition' => $nilai,
                'input_user' => Auth::user()->code_user,
                'created_at' => now(),
            ];
            DB::table('a5c_condition')->insert($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }

    public function updatecondition(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $nilai = Data::analisa5c_text($request->evaluasi_condition);
            $data = [
                'persaingan_usaha' => $request->persaingan_usaha,
                'kondisi_alam' => $request->kondisi_alam,
                'regulasi_pemerintah' => $request->regulasi_pemerintah,
                'evaluasi_condition' => $nilai,
                'input_user' => Auth::user()->code_user,
                'updated_at' => now(),
            ];
            DB::table('a5c_condition')->where('kode_analisa', $request->kode_analisa)->update($data);
            return redirect()->back()->with('success', 'Berhasil menambahkan data');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Gagal menambahkan data');
    }
}
