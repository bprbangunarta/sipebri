<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Data;
use App\Models\Handle;
use App\Models\Midle;
use App\Models\Survei;
use App\Models\Nasabah;
use App\Models\Tabungan;
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

        //Data nasabah sipebri
        $req = $request->query('nasabah');
        $cek = Nasabah::where('kode_nasabah', $req)->first();
        
        //Validasi data pertama kali berdasarkan data alamat yang null
        if (is_null($cek->alamat_ktp)) {

            $query = Tabungan::where('noid', $cek->no_identitas)
                        ->where('jttempoid', $cek->tanggal_lahir)
                        ->first();

                        if (is_null($query)) {
                                //Jika alamat kosong dan data CIF kosong
                                $kosong = Midle::nasabahedit($req);
                                return view('nasabah.edit', [
                                    'pend' => $kosong['pend'],
                                    'job' => $kosong['job'],
                                    'nasabah' => $kosong['nasabah'],
                                    'kab' => $kosong['kab'],
                                ]);
                        }else {
                            //jika ada data CIF
                            $data = ['no_identitas' => $cek->no_identitas,
                                    'tanggal_lahir' => $cek->tanggal_lahir,];

                            $cif = Midle::cifedit($data);
                            $cif['nasabah']->kode_nasabah = $cek->kode_nasabah;
                            // dd($cif);
                            return view('nasabah.edit', [
                                'pend' => $cif['pend'],
                                'job' => $cif['job'],
                                'nasabah' => $cif['nasabah'],
                                'kab' => $cif['kab'],
                            ]);
                        }

            
        }else{
            //Jika alamat ada
            $data = Midle::nasabahedit($req);  
            return view('nasabah.edit', [
                        'pend' => $data['pend'],
                        'job' => $data['job'],
                        'nasabah' => $data['nasabah'],
                        'kab' => $data['kab'],
                    ]);            
        }
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
            'photo_ktp' => '',
            'photo_ktp.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'photo_kk' => '',
            'photo_kk.*' => 'image|mimes:jpeg,png,jpg|max:2048',
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
