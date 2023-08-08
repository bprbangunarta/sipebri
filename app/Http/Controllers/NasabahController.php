<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Pekerjaan;
use App\Models\Pengajuan;
use App\Models\Pendamping;
use App\Models\Pendidikan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class NasabahController extends Controller
{
    public function edit(Request $request)
    {

        $req = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $req)->first();
       
        //Format tanggal
        $carbonDate = Carbon::createFromFormat('Ymd', $cek->tanggal_lahir);
        $cek->tanggal_lahir= $carbonDate->format('d-m-Y');
        
        //Ubah identitas dari nomor id menjadi data string
        if ($cek->identitas == "1") {
            $cek['iden'] = 'KTP';
        }elseif ($cek->identitas == "2") {
            $cek['iden'] = 'SIM';
        }elseif ($cek->identitas == "3"){
            $cek['iden'] = 'Passport';
        }elseif ($cek->identitas == "9"){
            $cek['iden']= 'Lainnya';
        }

        //Data Wilayah / Dati
        $kab = DB::select('select distinct kode_dati, nama_dati from v_dati'); 
        $kec = DB::select('select distinct kecamatan from v_dati'); 
        $kel = DB::select('select distinct kelurahan from v_dati'); 
        $pend = Pendidikan::all();
        $job = Pekerjaan::all();
        return view('nasabah.edit', [
            'pend' => $pend,
            'job' => $job,
            'nasabah' => $cek,
            'kab' => $kab,
            'kec' => $kec,
            'kel' =>$kel,
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
        
        //Membuat kode Nasabah otomatis
        $date = Carbon::now();
        $koderand = random_int(100000, 999999);
        $haskode = $date->format('m').$date->format('y').$koderand;
        $ceknasabah['kode_nasabah'] = $haskode;

        //Generate kode otomatis dari kanan ke kiri data pengajuan
        $lasts = Pengajuan::latest('kode_pengajuan')->first();
        if (is_null($lasts)) {
            $count = 339931;
        }else{
            $count = (int) $lasts->kode_pengajuan + 1;
        }
        $lengths = 8;
        $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
        $cekpengajuan['kode_pengajuan'] = $kodes;
        $cekpengajuan['nasabah_kode'] = $ceknasabah['kode_nasabah'];
               
        //Hapus format rupiah
        $remove = array("Rp", ".", " ");
        $cekpengajuan['plafon'] = str_replace($remove, "", $cekpengajuan['plafon']);
        $kdpengajuan['pengajuan_kode'] = $cekpengajuan['kode_pengajuan'];
        
        try {
            DB::transaction(function () use ($ceknasabah, $cekpengajuan, $kdpengajuan) {
                Nasabah::create($ceknasabah);
                Pengajuan::create($cekpengajuan);
                Pendamping::create($kdpengajuan);
                Survei::create($kdpengajuan);
            });
            return redirect()->back()->with('success', "Data berhasil ditambahkan");
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Data gagal ditambahkan");
        }
           
    }

    public function update(Request $request){
        $cek = $request->validate([
            'identitas' => 'required',
            'no_identitas' => 'required|unique:data_nasabah,identitas',
            'masa_identitas' => 'required',
            'nama_panggilan' => 'required',
            'nama_nasabah' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required',
            'kode_dati' => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'kota' => 'required',
            'alamat_ktp' => 'required',
            'alamat_sekarang' => 'required',
            'agama' => 'required',
            'jenis_kelamin' => 'required',
            'kewarganegaraan' => 'required',
            'pendidikan_kode' => 'required',
            'status_pernikahan' => 'required',
            'perkerjaan_kode' => 'required',
            'nama_ibu_kandung' => 'required',
            'no_rekening' => 'required',
            'no_npwp' => 'required',
            'no_telp' => 'required',
            'email' => 'required',
            'sumber_dana' => 'required',
            'penghasilan_utama' => 'required',
            'penghasilan_lainnya' => 'required',
            'tempat_kerja' => 'required',
            'no_telp_kantor' => 'required',
            'no_karyawan' => 'required',
            'photo' => 'required|',
            'photo.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photo_selfie' => 'required',
            'photo_selfie.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
       
        //cek image
        if ($request->file('photo')) {
            if ($request->oldphoto) {
                Storage::delete($request->oldphoto);
            }        
            $cek['photo'] = $request->file('photo')->store('image/photo');         
        }

        if ($request->file('photo_selfie')) {
            if ($request->oldphotoselfie) {
                Storage::delete($request->oldphotoselfie);
            }        
            $cek['photo_selfie'] = $request->file('photo_selfie')->store('image/photo_selfie');         
        }

        if ($cek) {
            $nas = Nasabah::where('kode_nasabah', $request->query('nasabah'))
                    ->select('id')
                    ->first();
            Nasabah::where('id', $nas->id)->update($cek);   
                return redirect()->back()->with('success', 'Data berhasil diupdate');
        }

    }

    public function validasi(Request $request)
    {
        return view('nasabah.validasi');
    }
}
