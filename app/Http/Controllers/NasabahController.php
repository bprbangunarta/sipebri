<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Nasabah;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
    public function edit(Request $request)
    {
        $pend = Pendidikan::all();
        $job = Pekerjaan::all();
        return view('nasabah.edit', [
            'pend' => $pend,
            'job' => $job,
        ]);
    }

    public function store(Request $request){
        
        $ceknasabah = $request->validate([
            'kode_nasabah' => '',
            'identitas' => 'required',
            'no_identitas' => 'required|unique:data_nasabah,identitas',
            'nama_nasabah' => 'required',
            'tanggal_lahir' => 'required',
        ]);

        $cekpengajuan = $request->validate([
            'plafon' => 'required',
            'jangka_waktu' => 'required',
        ]);

        //Hapus format tanggal Y-M-D menjadi YMD
        $ceknasabah['tanggal_lahir'] = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('Ymd');
        
        //Generate kode otomatis dari kanan ke kiri data nasabah
        $last = Nasabah::latest('kode_nasabah')->first();
        if (is_null($last->kode_nasabah)) {
            $counter = 1;
        }else{$counter = (int) $last->kode_nasabah + 1;}
        $length = 7;
        $kode = str_pad($counter, $length, '0', STR_PAD_LEFT);
        $ceknasabah['kode_nasabah'] = $kode;

        //Generate kode otomatis dari kanan ke kiri data pengajuan
        $lasts = Pengajuan::latest('kode_pengajuan')->first();
        if (is_null($lasts->kode_pengajuan)) {
            $count = 1;
        }else{$count = (int) $lasts->kode_pengajuan + 1;}
        $lengths = 7;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
        $cekpengajuan['kode_pengajuan'] = $kodes;
        $cekpengajuan['nasabah_kode'] = $ceknasabah['kode_nasabah'];
        
        //Hapus format rupiah
        $remove = array("Rp", ".", " ");
        $cekpengajuan['plafon'] = str_replace($remove, "", $cekpengajuan['plafon']);
        
        try {
            DB::transaction(function () use ($ceknasabah, $cekpengajuan) {
                Nasabah::create($ceknasabah);
                Pengajuan::create($cekpengajuan);
            });
            return redirect()->back()->with('success', "Data berhasil ditambahkan");
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Data gagal ditambahkan");
        }
           
    }

    public function validasi(Request $request)
    {
        return view('nasabah.validasi');
    }
}
