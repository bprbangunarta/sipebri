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
       
        //Format masa identitas
        if (!is_null($cek->masa_identitas)) {
            $carbonid = Carbon::createFromFormat('Ymd', $cek->masa_identitas);
            $cek->masa_identitas= $carbonid->format('d-m-Y');
        }
       
         //Format tanggal lahir
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

        //Ubah agama dari nomor id menjadi data string
        if ($cek->identitas == "1") {
            $cek['religi'] = 'Islam';
        }elseif ($cek->identitas == "2") {
            $cek['religi'] = 'Katolik';
        }elseif ($cek->identitas == "3"){
            $cek['religi'] = 'Kristen';
        }elseif ($cek->identitas == "4"){
            $cek['religi']= 'Hindu';
        }elseif ($cek->identitas == "5"){
            $cek['religi']= 'Budha';
        }elseif ($cek->identitas == "6"){
            $cek['religi']= 'Kong Hu Cu';
        }

        //Ubah jenis kelamin dari nomor id menjadi data string
        if ($cek->identitas == "1") {
            $cek['jk'] = 'Pria';
        }elseif ($cek->identitas == "2") {
            $cek['jk'] = 'Wanita';
        }

        //Ubah kewarganegaraan dari nomor id menjadi data string
        if ($cek->kewarganegaraan == "WNI") {
            $cek['kn'] = 'Warga Negara Indonesia';
        }elseif ($cek->kewarganegaraan == "WNA") {
            $cek['kn'] = 'Warga Negara Asing';
        }

        //Ubah pendidikan dari nomor id menjadi data string
        if (!is_null($cek->pendidikan_kode)) {
            $sc = Pendidikan::where('kode_pendidikan', $cek->pendidikan_kode)->get();
            $cek['std'] = $sc[0]->nama_pendidikan;
        }

        //Ubah pekerjaan dari nomor id menjadi data string
        if (!is_null($cek->pekerjaan_kode)) {
            $pk = Pekerjaan::where('kode_pekerjaan', $cek->pekerjaan_kode)->get();
            $cek['jo'] = $pk[0]->nama_pekerjaan;
        }
     
        //Ubah status dari nomor id menjadi data string
        if ($cek->status_pernikahan == "M") {
            $cek['st'] = 'Menikah';
        }elseif ($cek->kewarganegaraan == "L") {
            $cek['st'] = 'Lajang';
        }elseif ($cek->kewarganegaraan == "D") {
            $cek['st'] = 'Duda';
        }elseif ($cek->kewarganegaraan == "J") {
            $cek['st'] = 'Janda';
        }

        //Ubah kewarganegaraan dari nomor id menjadi data string
        if ($cek->sumber_dana == "1") {
            $cek['dana'] = 'Hibah';
        }elseif ($cek->kewarganegaraan == "2") {
            $cek['dana'] = 'Lain2';
        }elseif ($cek->kewarganegaraan == "3") {
            $cek['dana'] = 'Penghasilan';
        }elseif ($cek->kewarganegaraan == "4") {
            $cek['dana'] = 'Warisan';
        }

        //Mencari sumber dana dati berdasarkan kode_dati
       $dati = DB::table('v_dati')
            ->select('nama_dati')
            ->distinct()
            ->where('kode_dati', $cek->kode_dati)
            ->first();
        if (!is_null($dati)) {
            $cek['kode_dati'] = $dati->nama_dati;
        }

        //Ubah penghasilan utama dari nomor id menjadi data string
        if ($cek->identitas == "1") {
            $cek['hasil'] = 's/d 2,5 jt';
        }elseif ($cek->identitas == "2") {
            $cek['hasil'] = 's/d 2,5 - 5 jt';
        }elseif ($cek->identitas == "3"){
            $cek['hasil'] = 's/d 5 - 7,5 jt';
        }elseif ($cek->identitas == "4"){
            $cek['hasil']= 's/d 7,5 - 10 jt';
        }elseif ($cek->identitas == "5"){
            $cek['hasil']= '10 jt';
        }

        //Ubah penghasilan lainnya dari nomor id menjadi data string
        if ($cek->identitas == "1") {
            $cek['lain'] = 's/d 2,5 jt';
        }elseif ($cek->identitas == "2") {
            $cek['lain'] = 's/d 2,5 - 5 jt';
        }elseif ($cek->identitas == "3"){
            $cek['lain'] = 's/d 5 - 7,5 jt';
        }elseif ($cek->identitas == "4"){
            $cek['lain']= 's/d 7,5 - 10 jt';
        }elseif ($cek->identitas == "5"){
            $cek['lain']= '10 jt';
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
            'kode_nasabah' => $request->query('nasabah'),
            'identitas' => 'required',
            'no_identitas' => 'required|unique:data_nasabah,identitas',
            'masa_identitas' => 'required',
            'nama_nasabah' => 'required',
            'nama_panggilan' => 'required',
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
            'photo' => 'required',
            'photo.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photo_selfie' => 'required',
            'photo_selfie.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        //Hapus format tanggal Y-M-D menjadi YMD
        $cek['masa_identitas'] = Carbon::createFromFormat('Y-m-d', $request->masa_identitas)->format('Ymd');
        $cek['tanggal_lahir'] = Carbon::createFromFormat('m-d-Y', $request->tanggal_lahir)->format('Ymd');
       
        //cek image
        if ($request->file('photo')) {
            if ($request->oldphoto) {
                Storage::delete($request->oldphoto);
            }        
            $cek['photo'] = $request->file('photo')->store('image/photo', 'public');         
        }

        if ($request->file('photo_selfie')) {
            if ($request->oldphotoselfie) {
                Storage::delete($request->oldphotoselfie);
            }        
            $cek['photo_selfie'] = $request->file('photo_selfie')->store('image/photo_selfie', 'public');         
        }

        if ($cek) {
            $nas = Nasabah::where('kode_nasabah', $request->query('nasabah'))->get();
            Nasabah::where('id', $nas[0]->id)->update($cek);   
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        }

    }

    public function validasi(Request $request)
    {
        return view('nasabah.validasi');
    }
}
