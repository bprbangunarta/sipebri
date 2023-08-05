<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        return view('pendaftaran.index');
    }

    public function store(Request $request){
        // dd($request);
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
        $last = Pendaftaran::pluck('kode_nasabah')->first();
        if ($last == 0) {
            $counter = 1;
        }else{$counter = $last + 1;}
        $length = 7;
        $kode = str_pad($counter, $length, '0', STR_PAD_LEFT);
        $ceknasabah['kode_nasabah'] = $kode;

        //Generate kode otomatis dari kanan ke kiri data pengajuan
        $lasts = Pengajuan::pluck('kode_pengajuan')->first();
        if ($lasts == 0) {
            $count = 1;
        }else{$count = $lasts + 1;}
        $lengths = 7;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
        $cekpengajuan['kode_pengajuan'] = $kodes;
        $cekpengajuan['nasabah_kode'] = $ceknasabah['kode_nasabah'];
        
        //Hapus format rupiah
        $remove = array("Rp", ".", " ");
        $cekpengajuan['plafon'] = str_replace($remove, "", $cekpengajuan['plafon']);
        try {
            DB::transaction(function () use ($ceknasabah, $cekpengajuan) {
                Pendaftaran::create($ceknasabah);
                Pengajuan::create($cekpengajuan);
            });
            return redirect()->back()->with('success', "Data berhasil ditambahkan");
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Data gagal ditambahkan");
        }
           
    }

    public function edit(Request $request)
    {
        return view('pendaftaran.edit');
    }

    public function pendamping(Request $request)
    {
        return view('pendaftaran.pendamping');
    }
}
