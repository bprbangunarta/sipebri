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
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Cast\String_;
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
            $cek->masa_identitas= $carbonid->format('m-d-Y');
        }
       
         //Format tanggal lahir
        $carbonDate = Carbon::createFromFormat('Ymd', $cek->tanggal_lahir);
        $cek->tanggal_lahir= $carbonDate->format('m-d-Y');
        
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
        }elseif ($cek->status_pernikahan == "L") {
            $cek['st'] = 'Lajang';
        }elseif ($cek->status_pernikahan == "D") {
            $cek['st'] = 'Duda';
        }elseif ($cek->status_pernikahan == "J") {
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

        //Mencari sumber data dati berdasarkan kode_dati
       $dati = DB::table('v_dati')
            ->select('nama_dati')
            ->distinct()
            ->where('kode_dati', $cek->kode_dati)
            ->first();
        if (!is_null($dati)) {
            $cek['nm_dati'] = $dati->nama_dati;
        }

        //Ubah penghasilan utama dari nomor id menjadi data string
        if ($cek->penghasilan_utama == "1") {
            $cek['hasil'] = 's/d 2,5 jt';
        }elseif ($cek->penghasilan_utama == "2") {
            $cek['hasil'] = 's/d 2,5 - 5 jt';
        }elseif ($cek->penghasilan_utama == "3"){
            $cek['hasil'] = 's/d 5 - 7,5 jt';
        }elseif ($cek->penghasilan_utama == "4"){
            $cek['hasil']= 's/d 7,5 - 10 jt';
        }elseif ($cek->penghasilan_utama == "5"){
            $cek['hasil']= '10 jt';
        }

        //Ubah penghasilan lainnya dari nomor id menjadi data string
        if ($cek->penghasilan_lainnya == "1") {
            $cek['lain'] = 's/d 2,5 jt';
        }elseif ($cek->penghasilan_lainnya == "2") {
            $cek['lain'] = 's/d 2,5 - 5 jt';
        }elseif ($cek->penghasilan_lainnya == "3"){
            $cek['lain'] = 's/d 5 - 7,5 jt';
        }elseif ($cek->penghasilan_lainnya == "4"){
            $cek['lain']= 's/d 7,5 - 10 jt';
        }elseif ($cek->penghasilan_lainnya == "5"){
            $cek['lain']= '10 jt';
        }
        
        //Data Wilayah / Dati
        // $kab = DB::table('v_dati')
        //         ->select('kode_dati', 'nama_dati')
        //         ->distinct()
        //         ->take(10)
        //         ->get(); 
        // select distinct kode_dati, nama_dati from v_dati LIMIT 10
        $kab = DB::select('select distinct kode_dati, nama_dati from v_dati'); 
        // $kec = DB::select('select distinct kecamatan from v_dati'); 
        // $kel = DB::select('select distinct kelurahan from v_dati'); 
        $pend = Pendidikan::all();
        $job = Pekerjaan::all();
        return view('nasabah.edit', [
            'pend' => $pend,
            'job' => $job,
            'nasabah' => $cek,
            'kab' => $kab,
            // 'kec' => $kec,
            // 'kel' =>$kel,
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

        //Membuat kode Nasabah otomatis
        $date = Carbon::now();
        $koderand = random_int(100000, 999999);
        $haskode = $date->format('m').$date->format('y').$koderand;
        $ceknasabah['kode_nasabah'] = $haskode;

        $cekpengajuan = $request->validate([
            'plafon' => 'required',
            'jangka_waktu' => 'required',
        ]);

        //Hapus format tanggal Y-M-D menjadi YMD
        $ceknasabah['tanggal_lahir'] = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('Ymd');
        

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

        //huruf kapital nama nasabah
        $ceknasabah['nama_nasabah'] = strtoupper($ceknasabah['nama_nasabah']);
        
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
            'pekerjaan_kode' => 'required',
            'nama_ibu_kandung' => 'required',
            'no_rekening' => 'required',
            'no_npwp' => 'required',
            'no_telp' => 'required',
            'no_telp_darurat' => 'required',
            'email' => 'required',
            'sumber_dana' => 'required',
            'penghasilan_utama' => 'required',
            'penghasilan_lainnya' => 'required',
            'tempat_kerja' => 'required',
            'no_telp_kantor' => 'required',
            'no_karyawan' => 'required',
            'photo' => '',
            'photo.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photo_selfie' => '',
            'photo_selfie.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        // Pengecekan format "m-d-Y"
        $tg = explode('-', $request->masa_identitas);
        if (strlen($tg[0]) == 2) {
            $cek['masa_identitas'] = Carbon::createFromFormat('m-d-Y', $request->masa_identitas)->format('Ymd');
        }else if (strlen($tg[0]) == 4){
            $cek['masa_identitas'] = Carbon::createFromFormat('Y-m-d', $request->masa_identitas)->format('Ymd');
        }
       
        //Hapus format tanggal Y-M-D menjadi YMD
        $cek['tanggal_lahir'] = Carbon::createFromFormat('m-d-Y', $request->tanggal_lahir)->format('Ymd');
       
        //Cek Photo
        if ($request->file('photo')) {
            if ($request->oldphoto) {
                Storage::delete('public/image/photo/'.$request->oldphoto);
            }        
            $filename = $cek['photo']->getClientOriginalName(); 
            $cek['photo'] = $request->file('photo')->storeAs('image/photo', $request->nama_nasabah.$filename, 'public'); 
            $cek['photo'] = $request->nama_nasabah.$filename;     
        }else{
            $cek['photo'] = $request->oldphoto;
        }
        
         //Cek Photo Selfie
        if ($request->file('photo_selfie')) {
            if ($request->oldphotoselfie) {
                Storage::delete('public/image/photo_selfie/'.$request->oldphotoselfie);
            }
            $files = $cek['photo_selfie']->getClientOriginalName();         
            $cek['photo_selfie'] = $request->file('photo_selfie')->storeAs('image/photo_selfie', $request->nama_nasabah.$files, 'public');         
            $cek['photo_selfie'] = $request->nama_nasabah.$files;
        }else{
            $cek['photo_selfie'] = $request->oldphotoselfie;
        }
        
        //Huruf kapital
        $cek['nama_nasabah'] = strtoupper($cek['nama_nasabah']);
        $cek['nama_panggilan'] = strtoupper($cek['nama_panggilan']);
        $cek['tempat_lahir'] = strtoupper($cek['tempat_lahir']);
        $cek['kecamatan'] = strtoupper($cek['kecamatan']);
        $cek['kelurahan'] = strtoupper($cek['kelurahan']);
        $cek['kota'] = strtoupper($cek['kota']);
        $cek['alamat_ktp'] = strtoupper($cek['alamat_ktp']);
        $cek['alamat_sekarang'] = strtoupper($cek['alamat_sekarang']);
        $cek['nama_ibu_kandung'] = strtoupper($cek['nama_ibu_kandung']);
        $cek['tempat_kerja'] = strtoupper($cek['tempat_kerja']);

        
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
