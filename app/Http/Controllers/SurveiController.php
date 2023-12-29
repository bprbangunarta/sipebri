<?php

namespace App\Http\Controllers;

use App\Models\CGC;
use App\Models\Midle;
use App\Models\Kantor;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class SurveiController extends Controller
{
    public function edit(Request $request)
    {
        $req = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);
            //Data pengajuan
            $pengajuan = Pengajuan::where('kode_pengajuan', $enc)->first();
            $cek = Nasabah::where('kode_nasabah', $pengajuan->nasabah_kode)->first();

            $cek->kode_pengajuan = $pengajuan->kode_pengajuan;

            //Data survey
            $survey = Survei::where('pengajuan_kode', $cek->kode_pengajuan)->first();
            //Data kantor
            $ktr = Kantor::where('kode_kantor', $survey->kantor_kode)->first();

            //inisialisasi variable ketika data null
            if (is_null($ktr)) {
                $survey->nama_kantor = "";
            } else {
                $survey->nama_kantor = $ktr->nama_kantor;
            }

            //Data kasi ambil
            $ks = DB::table('v_users')
                ->select('nama_user')
                ->where('code_user', $survey->kasi_kode)->first();
            if (is_null($ks)) {
                $survey->nama_kasi = null;
            } else {
                $survey->nama_kasi = $ks->nama_user;
            }

            //Data surveyor
            $st = DB::table('v_users')
                ->select('nama_user')
                ->where('code_user', $survey->surveyor_kode)->first();
            if (is_null($st)) {
                $survey->nama_surveyor = null;
            } else {
                $survey->nama_surveyor = $st->nama_user;
            }

            //Inisialisasi data        
            $survey->tabungan_cgc = $pengajuan->tabungan_cgc;

            //Data kasi
            $kasi = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Kasi Analis')->get();

            //Data Staff Analis
            $staff = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Staff Analis')->get();

            //Data KKPK
            $kantor_user = Auth::user()->kantor_kode;
            $kkpk = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Kepala Kantor Kas')
                ->where('kantor_kode', '=', $kantor_user)
                ->get();

            //validasi
            if (count($kkpk) == 0) {
                $kkpk = collect([
                    (object) ['code_user' => null, 'nama' => null]
                ]);
            }
            //Data CS
            $cs = DB::table('v_users')
                ->select('code_user', 'nama_user as nama')
                ->where('role_name', '=', 'Customer Service')
                ->where('kantor_kode', '=', $kantor_user)
                ->get();

            // dd($cs);
            //validasi
            if (count($cs) === 0) {
                $cs = collect([
                    (object) ['code_user' => null, 'nama' => null]
                ]);
            }

            $kantor = Kantor::all();

            //Data Auth
            $us = Auth::user()->id;
            $user = DB::table('users')
                ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.code_user')
                ->where('users.id', '=', $us)->get();


            $cek->auth = $user[0]->code_user;
            $cek->kd_pengajuan = $req;
            $dt = Midle::analisa_usaha($enc);
            $cek->plafon = $dt[0]->plafon;
            $cek->jangka_waktu = $dt[0]->jangka_waktu;
            $cek->produk = $pengajuan->produk_kode;

            return view('pengajuan.data-surveyor', [
                'data' => $cek,
                'kasi' => $kasi,
                'staff' => $staff,
                'survey' => $survey,
                'kkpk' => $kkpk[0],
                'cs' => $cs,
                'kantor' => $kantor,
            ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function update(Request $request)
    {
        $cek = $request->validate([
            'kantor_kode' => 'required',
            'kasi_kode' => 'required',
            'surveyor_kode' => 'required',
            'input_user' => 'required',
        ]);

        $cek['is_entry'] = 1;
        $cek['otorisasi'] = 'N';
        // dd($cek);
        $kode_pengajuan = $request->pengajuan_kode;

        if ($cek) {
            Survei::where('pengajuan_kode', $kode_pengajuan)->update($cek);
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } else {
            return redirect()->back()->with('error', 'Data gagal diupdate');
        }
    }
}
