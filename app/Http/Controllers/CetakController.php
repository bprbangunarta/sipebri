<?php

namespace App\Http\Controllers;

use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

class CetakController extends Controller
{
    public function pengajuan(Request $request){
        $kode = $request->query('pengajuan');
        
        return view('cetak.pengajuan',[
            'data' => $kode
        ]);
    }

    public function nik(Request $request){
        $kode = $request->query('cetak');
        $data = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->select('data_nasabah.no_identitas', 'data_nasabah.nama_nasabah')
                ->where('data_pengajuan.kode_pengajuan', '=', $kode)->get();

        //Hari ini
        $hari = Carbon::today();
        $data[0]->hari = $hari->isoformat('D MMMM Y');
        
        return view('cetak.layouts.nik',[
            'data' => $data[0]
        ]);
    }

    public function pendamping(Request $request){
        $kode = $request->query('cetak');
        $data = DB::table('data_pengajuan')
                ->leftJoin('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->where('data_pengajuan.kode_pengajuan', '=', $kode)
                ->select('data_pendamping.no_identitas', 'data_pendamping.nama_pendamping', 'data_pendamping.tempat_lahir', 'data_pendamping.tanggal_lahir', 'data_nasabah.no_identitas as iden', 'data_nasabah.nama_nasabah', 'data_nasabah.tempat_lahir as tempat', 'data_nasabah.tanggal_lahir as ttl', 'data_nasabah.pendidikan_kode', 'data_nasabah.alamat_ktp')->get();
                

        //Rubah tanggal
        $carbonDate = Carbon::createFromFormat('Ymd', $data[0]->tanggal_lahir);
        $data[0]->tanggal_lahir = $carbonDate->isoformat('D MMMM Y');
        $data[0]->tempat_lahir = ucfirst(strtolower($data[0]->tempat_lahir));

        //data pekerjaan
        $job = Pendidikan::where('kode_pendidikan', $data[0]->pendidikan_kode)->get();
        $data[0]->pendidikan_kode = $job[0]->nama_pendidikan;
        
        //Hari ini
        $hari = Carbon::today();
        $data[0]->hari = $hari->isoformat('dddd D MMMM YYYY');

        return view('cetak.layouts.pendamping', [
            'data' => $data[0]
        ]);
    }
}
