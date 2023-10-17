<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Midle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Auth;

class KualitatifController extends Controller
{
    public function index(Request $request)
    {
        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));
            $cek = Midle::analisa_usaha($enc);

            //Cek data ada atau tidak
            $table = DB::table('au_kualitatif')->where('pengajuan_kode', $enc)->first();

            if (is_null($table)) {
                return view('analisa.analisa-kualitatif', [
                    'data' => $cek[0],
                ]);
            }

            return view('analisa.analisa-kualitatif-edit', [
                'data' => $cek[0],
                'kualitatif' => $table,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function store(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $req = $request->validate([
                'bi_checking' => 'required',
                'kewajiban_pihak_lain' => 'required',
                'pihak_berwajib' => 'required',
                'hubungan_tetangga' => 'required',
                'pengalaman_tki' => 'required',
                'sumber_bahan' => '',
                'aktivitas_usaha' => '',
                'wilayah_operasional' => '',
                'pembayaran' => '',
                'pendukung_usaha' => '',
                'pengurang_usaha' => '',
                'trade_checking' => '',
                'trade_checking' => '',
            ]);

            $req['pengajuan_kode'] = $enc;
            $req['input_user'] = Auth::user()->code_user;
            $req['created_at'] = now();

            DB::table('au_kualitatif')->insert($req);
            return redirect()->back()->with('success', 'Data berhasil disimpan');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal disimpan');
    }

    public function update(Request $request)
    {

        try {
            $enc = Crypt::decrypt($request->query('pengajuan'));

            $req = $request->validate([
                'bi_checking' => 'required',
                'kewajiban_pihak_lain' => 'required',
                'pihak_berwajib' => 'required',
                'hubungan_tetangga' => 'required',
                'pengalaman_tki' => 'required',
                'sumber_bahan' => '',
                'aktivitas_usaha' => '',
                'wilayah_operasional' => '',
                'pembayaran' => '',
                'pendukung_usaha' => '',
                'pengurang_usaha' => '',
                'trade_checking' => '',
            ]);
            $req['input_user'] = Auth::user()->code_user;
            $req['updated_at'] = now();
            $table = DB::table('au_kualitatif')->where('pengajuan_kode', $enc)->first();
            DB::table('au_kualitatif')->where('id', $table->id)->update($req);
            return redirect()->back()->with('success', 'Data berhasil diubah');
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        return redirect()->back()->with('error', 'Data gagal diubah');
    }

    public function karakter (Request $request)
    {
        return view('staff.analisa.kualitatif.karakter');
    }

    public function usaha (Request $request)
    {
        return view('staff.analisa.kualitatif.usaha');
    }
}
