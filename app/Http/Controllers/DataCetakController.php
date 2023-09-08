<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Survei;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class DataCetakController extends Controller
{
    public function slik(Request $request){
        $kode = $request->query('cetak');
        
        //====Try Enkripsi Request====//
        try {
                $enc = Crypt::decrypt($kode);
                $data = DB::table('data_pengajuan')
                        ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                        ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                        ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah', 'data_nasabah.tempat_lahir', 'data_nasabah.tanggal_lahir', 'data_nasabah.no_telp', 'data_nasabah.alamat_ktp', 'data_survei.kasi_kode', 'data_survei.surveyor_kode')
                        ->where('data_pengajuan.kode_pengajuan', '=', $enc)->get();
                
                //Surveyor dan Kasi
                $kasi = DB::table('v_users')
                            ->where('code_user', $data[0]->kasi_kode)                   
                            ->select('nama_user')->get();

                $surveyor = DB::table('v_users')    
                            ->where('code_user', $data[0]->surveyor_kode)
                            ->select('nama_user')->get();

                //Rubah tanggal
                $carbonDate = Carbon::createFromFormat('Ymd', $data[0]->tanggal_lahir);
                $data[0]->tanggal_lahir = $carbonDate->isoformat('D MMMM Y');

                //Ubah format String
                $data[0]->tempat_lahir = ucfirst(strtolower($data[0]->tempat_lahir));

                $data[0]->kasi_kode = $kasi[0]->nama_user;
                $data[0]->surveyor_kode = $surveyor[0]->nama_user;

                $hari = Carbon::today();
                $data[0]->hari = $hari->isoformat('D MMMM Y');

                return view('cetak.layouts.slik', [
                    'data' =>$data[0]
                ]);
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
        
    }
}
