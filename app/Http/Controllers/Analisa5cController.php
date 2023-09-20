<?php

namespace App\Http\Controllers;

use App\Models\Midle;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class Analisa5cController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);

            $a5character = DB::table('a5c_character')->where('pengajuan_kode', $enc)->first();
            $a5capacity = DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->first();
            $a5collateral = DB::table('a5c_collateral')->where('pengajuan_kode', $enc)->first();
            $a5conition = DB::table('a5c_conition')->where('pengajuan_kode', $enc)->first();
            $keuangan = Keuangan::where('pengajuan_kode', $enc)->pluck('keuangan_perbulan')->first();
            $taksasi = DB::table('data_jaminan')->where('pengajuan_kode', $enc)->get();
            
            //total semua nilai taksasi
            $tak = [];
            for ($i=0; $i < count($taksasi); $i++) { 
                $tak[] = $taksasi[$i]->nilai_taksasi ?? 0;
            }
            $totaltaksasi = array_sum($tak);
 
            //Cek Taksasi sudah terisi apa belum
            if ($totaltaksasi == 0) {
                return redirect()->back()->with('error', 'Taksasi tidak boleh kosong');
            }
            
            //Cek kemampuan keuangan sudah terisi apa belum
            if (is_null($keuangan) || $keuangan == 0) {
                return redirect()->back()->with('error', 'Keuangan perbulan tidak boleh kosong');
            }
            
            //Kode user jika tidak ada di tabel a5c_karakter maka dialihkan
            if (is_null($a5character)) {
                return view('analisa.5c', [
                'data' => $cek[0],
            ]);
            }
            
            //handle data jika kosong
            if (is_null($a5character)) {
                $a5character = Midle::karakter();
            }
            if (is_null($a5capacity)) {
                $a5capacity = Midle::capacity();
            }
            if (is_null($a5collateral)) {
                $a5collateral = Midle::collateral();
            }
            if (is_null($a5conition)) {
                $a5conition = Midle::conition();
            }
                        
            //Menghitung RC
            $a = $keuangan * intval($cek[0]->jangka_waktu);
            $b = (intval($cek[0]->plafon) / $a) * 100;
            $a5capacity->RC = number_format($b, 2);  
            
            //Menghitung Taksasi Agunan
            $c = (intval($cek[0]->plafon) / $totaltaksasi) * 100;
            $a5collateral->taksasi = number_format($c, 2);
            
            //Menghitung Analisa keseluruhan
            // $totala5c = Midle::jumlah_a5c($enc);
            // dd($a5character);

            return view('analisa.5c-edit', [
                'data' => $cek[0],
                'karakter' => $a5character,
                'capacity' => $a5capacity,
                'collateral' => $a5collateral,
                'conition' => $a5conition,
            ]);

        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function store(Request $request)
    {
        $character = $request->validate([
            'gaya_hidup' => 'required',
            'konsisten' => 'required',
            'pengendalian_emosi' => 'required',
            'perbuatan_tercela' => 'required',
            'kepatuhan' => 'required',
            'hubungan_sosial' => 'required',
            'harmonis' => 'required',
            'nilai_karakter' => 'required',
        ]);

        $capacity = $request->validate([
            'kontinuitas' => 'required',
            'kondisi_slik' => 'required',
            'pengalaman_usaha' => 'required',
            'aset_diluar_usaha' => 'required',
            'aset_terkait_usaha' => 'required',
            'pertumbuhan_usaha' => 'required',
            'laporan_keuangan' => 'required',
            'rc' => 'required',
            'catatan_kredit' => 'required',
            'dsr' => 'required',
            'capital_sumber_modal' => 'required',
        ]);

        $collateral = $request->validate([            
            'agunan_utama' => 'required',
            'agunan_tambahan' => 'required',
            'legalitas_agunan' => 'required',
            'legalitas_agunan_tambahan' => 'required',
            'mudah_diuangkan' => 'required',
            'stabilitas_harga' => 'required',
            'kondisi_kendaraan' => 'required',
            'lokasi_shm' => 'required',
            'aspek_hukum' => 'required',
            'taksasi_agunan' => 'required',
        ]);

        $conition = $request->validate([
            'persaingan_usaha' => 'required',
            'kondisi_alam' => 'required',
            'regulasi_pemerintah' => 'required',
        ]);

        if ($request->nilai_karakter == "Baik") {
            $character['nilai_karakter'] = "3";
        } elseif($request->nilai_karakter == "Cukup Baik") {
            $character['nilai_karakter'] = "2";
        } elseif($request->nilai_karakter == "Kurang Baik") {
            $character['nilai_karakter'] = "1";
        }

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            
            $name = 'A5C';
            $length = 5;
            $kode = Midle::a5ckodeacak($name, $length);
            //tambah kode Analisa 5C dan kode pengajuan
            $character['kode_analisa'] = $kode;
            $character['pengajuan_kode'] = $enc;
            $capacity['kode_analisa'] = $kode;
            $capacity['pengajuan_kode'] = $enc;
            $collateral['kode_analisa'] = $kode;
            $collateral['pengajuan_kode'] = $enc;
            $conition['kode_analisa'] = $kode;
            $conition['pengajuan_kode'] = $enc;
            $capacity['rc'] = str_replace("RC : ", "", $capacity['rc']);
            $capacity['dsr'] = str_replace("DSR : ", "", $capacity['dsr']);

            DB::transaction(function () use ($character, $capacity, $collateral, $conition) {
                DB::table('a5c_character')->insert($character);
                DB::table('a5c_capacity')->insert($capacity);
                DB::table('a5c_collateral')->insert($collateral);
                DB::table('a5c_conition')->insert($conition);
            });
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal ditambahkan');
    }

    public function edit(Request $request)
    {
        //
    }

    public function update(Request $request)
    {
        
        $character = $request->validate([
            'gaya_hidup' => 'required',
            'konsisten' => 'required',
            'pengendalian_emosi' => 'required',
            'perbuatan_tercela' => 'required',
            'kepatuhan' => 'required',
            'hubungan_sosial' => 'required',
            'harmonis' => 'required',
            'nilai_karakter' => 'required',
        ]);

        $capacity = $request->validate([
            'kontinuitas' => 'required',
            'kondisi_slik' => 'required',
            'pengalaman_usaha' => 'required',
            'aset_diluar_usaha' => 'required',
            'aset_terkait_usaha' => 'required',
            'pertumbuhan_usaha' => 'required',
            'laporan_keuangan' => 'required',
            'rc' => 'required',
            'catatan_kredit' => 'required',
            'dsr' => 'required',
            'capital_sumber_modal' => 'required',
        ]);

        $collateral = $request->validate([            
            'agunan_utama' => 'required',
            'agunan_tambahan' => 'required',
            'legalitas_agunan' => 'required',
            'legalitas_agunan_tambahan' => 'required',
            'mudah_diuangkan' => 'required',
            'stabilitas_harga' => 'required',
            'kondisi_kendaraan' => 'required',
            'lokasi_shm' => 'required',
            'aspek_hukum' => 'required',
            'taksasi_agunan' => 'required',
        ]);

        $conition = $request->validate([
            'persaingan_usaha' => 'required',
            'kondisi_alam' => 'required',
            'regulasi_pemerintah' => 'required',
        ]);

         if ($request->nilai_karakter == "Baik") {
            $character['nilai_karakter'] = "3";
        } elseif($request->nilai_karakter == "Cukup Baik") {
            $character['nilai_karakter'] = "2";
        } elseif($request->nilai_karakter == "Kurang Baik") {
            $character['nilai_karakter'] = "1";
        }

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $capacity['rc'] = str_replace(["RC : ", "%"],"", $capacity['rc']);
            $capacity['dsr'] = str_replace("DSR : ", "", $capacity['dsr']);
            
            DB::transaction(function () use ($enc, $character, $capacity, $collateral, $conition) {
                DB::table('a5c_character')->where('pengajuan_kode', $enc)->update($character);
                DB::table('a5c_capacity')->where('pengajuan_kode', $enc)->update($capacity);
                DB::table('a5c_collateral')->where('pengajuan_kode', $enc)->update($collateral);
                DB::table('a5c_conition')->where('pengajuan_kode', $enc)->update($conition);
            });
            return redirect()->back()->with('success', 'Data berhasil diubah');
        } catch (DecryptException $th) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal diubah');
    }

    public function destroy(Request $request)
    {
        //
    }
}
