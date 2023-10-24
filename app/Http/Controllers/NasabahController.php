<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Data;
use App\Models\Midle;
use App\Models\Handle;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Expr\Cast\String_;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;

class NasabahController extends Controller
{
    public function edit(Request $request)
    {
        //Data nasabah sipebri
        $req = $request->query('nasabah');

        //====Try Enkripsi Request====//
        try {
            $enc = Crypt::decrypt($req);
            $kd_pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();

            $cek = Nasabah::where('kode_nasabah', $kd_pengajuan[0]->nasabah_kode)->first();

            //Validasi data pertama kali berdasarkan data alamat yang null
            if (is_null($cek->alamat_ktp)) {

                $query = Tabungan::where('noid', $cek->no_identitas)
                    ->where('jttempoid', $cek->tanggal_lahir)
                    ->first();

                if (is_null($query)) {
                    //Jika alamat kosong dan data CIF kosong
                    $kosong = Midle::nasabahedit($req);

                    $kosong['nasabah']->kd_nasabah = Crypt::encrypt($kosong['nasabah']->kode_nasabah);
                    $kosong['nasabah']->kd_pengajuan = Crypt::encrypt($kosong['nasabah']->kd_pengajuan);
                    $cek = Midle::analisa_usaha($enc);

                    return view('nasabah.edit', [
                        'data' => $cek[0],
                        'pend' => $kosong['pend'],
                        'job' => $kosong['job'],
                        'nasabah' => $kosong['nasabah'],
                        'kab' => $kosong['kab'],
                    ]);
                } else {
                    //jika ada data CIF
                    $data = [
                        'no_identitas' => $cek->no_identitas,
                        'tanggal_lahir' => $cek->tanggal_lahir,
                    ];

                    $cif = Midle::cifedit($data);
                    $cif['nasabah']->kode_nasabah = $cek->kode_nasabah;
                    $peng = Pengajuan::where('nasabah_kode', $cek->kode_nasabah)->first();

                    $cif['nasabah']->kd_nasabah = Crypt::encrypt($cif['nasabah']->kode_nasabah);
                    $cif['nasabah']->kd_pengajuan = Crypt::encrypt($peng->kode_pengajuan);

                    $cek = Midle::analisa_usaha($enc);

                    return view('nasabah.edit', [
                        'data' => $cek[0],
                        'pend' => $cif['pend'],
                        'job' => $cif['job'],
                        'nasabah' => $cif['nasabah'],
                        'kab' => $cif['kab'],
                    ]);
                }
            } else {
                //Jika alamat ada
                $data = Midle::nasabahedit($req);
                $data['nasabah']->kd_nasabah = Crypt::encrypt($data['nasabah']->kode_nasabah);
                $data['nasabah']->nocif = $data['nasabah']->no_cif;
                $data['nasabah']->kd_pengajuan = Crypt::encrypt($data['nasabah']->kd_pengajuan);
                //tambah data  
                $cek = Midle::analisa_usaha($enc);

                return view('pengajuan.data-nasabah', [
                    'data' => $cek[0],
                    'pend' => $data['pend'],
                    'job' => $data['job'],
                    'nasabah' => $data['nasabah'],
                    'kab' => $data['kab'],
                ]);
            }
        } catch (DecryptException $e) {
            return abort(403, 'Permintaan anda di Tolak.');
        }
    }

    public function store(Request $request)
    {
        $ceknasabah = $request->validate([
            'kode_nasabah' => '',
            'identitas' => 'required',
            'no_identitas' => 'required',
            'nama_nasabah' => 'required',
            'tanggal_lahir' => 'required',
            'input_user' => 'required',
            'kategori' => 'required',
        ]);

        //Hapus format tanggal Y-M-D menjadi YMD
        $tanggal = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('Ymd');

        //Cek Nik ke database sipebri
        $nik = Nasabah::where('no_identitas', $request->no_identitas)
            ->where('tanggal_lahir', $tanggal)->first();

        if (is_null($nik)) {
            //===Jika NIK tidak ada di data si pebri===//
            //Membuat kode Nasabah otomatis
            $date = Carbon::now();
            $koderand = random_int(100000, 999999);
            $haskode = $date->format('m') . $date->format('y') . $koderand;
            $ceknasabah['kode_nasabah'] = $haskode;

            $cekpengajuan = $request->validate([
                'plafon' => 'required',
                'jangka_waktu' => 'required',
            ]);

            //Hapus format tanggal Y-M-D menjadi YMD
            $ceknasabah['tanggal_lahir'] = $tanggal;

            //Generate kode otomatis dari kanan ke kiri data pengajuan
            $lasts = Pengajuan::latest('kode_pengajuan')->first();
            if (is_null($lasts)) {
                $count = 339931;
            } else {
                $count = (int) $lasts->kode_pengajuan + 1;
            }
            $lengths = 8;
            $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
            $cekpengajuan['kode_pengajuan'] = $kodes;
            $cekpengajuan['nasabah_kode'] = $ceknasabah['kode_nasabah'];

            //Auth
            $usr = Auth::user()->code_user;

            //Hapus format rupiah
            $remove = array("Rp", ".", " ");
            $cekpengajuan['plafon'] = str_replace($remove, "", $cekpengajuan['plafon']);
            $cekpengajuan['kategori'] = $request->kategori;
            $cekpengajuan['input_user'] = $usr;

            //masuk ke pendamping dan survei
            $kdpengajuan['pengajuan_kode'] = $cekpengajuan['kode_pengajuan'];
            $kdpengajuan['input_user'] = $usr;

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
        } else {
            //Generate kode otomatis dari kanan ke kiri data pengajuan
            $cekpengajuan = $request->validate([
                'plafon' => 'required',
                'jangka_waktu' => 'required',
            ]);

            $lasts = Pengajuan::latest('kode_pengajuan')->first();
            if (is_null($lasts)) {
                $count = 339931;
            } else {
                $count = (int) $lasts->kode_pengajuan + 1;
            }
            $lengths = 8;
            $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
            $cekpengajuan['kode_pengajuan'] = $kodes;
            $cekpengajuan['nasabah_kode'] = $nik->kode_nasabah;

            //Auth
            $usr = Auth::user()->code_user;

            //Hapus format rupiah
            $remove = array("Rp", ".", " ");
            $cekpengajuan['plafon'] = str_replace($remove, "", $cekpengajuan['plafon']);
            $cekpengajuan['kategori'] = $request->kategori;
            $cekpengajuan['input_user'] = $usr;


            //masuk ke pendamping dan survei
            $kdpengajuan['pengajuan_kode'] = $cekpengajuan['kode_pengajuan'];
            $kdpengajuan['input_user'] = $usr;

            try {
                DB::transaction(function () use ($cekpengajuan, $kdpengajuan) {
                    Pengajuan::create($cekpengajuan);
                    Pendamping::create($kdpengajuan);
                    Survei::create($kdpengajuan);
                });
                return redirect()->back()->with('success', "Data berhasil ditambahkan");
            } catch (Exception $e) {
                return redirect()->back()->with('error', "Data gagal ditambahkan");
            }
        }
    }

    public function update(Request $request)
    {

        $cek = $request->validate([
            'kode_nasabah' => $request->query('nasabah'),
            'no_cif' => '',
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
            'no_rekening' => '',
            'no_npwp' => '',
            'no_telp' => 'required',
            'no_telp_darurat' => 'required',
            'email' => '',
            'sumber_dana' => 'required',
            'penghasilan_utama' => 'required',
            'penghasilan_lainnya' => 'required',
            'tempat_kerja' => 'required',
            'no_telp_kantor' => 'required',
            'no_karyawan' => 'required',
            'input_user' => 'required',
            'photo' => '',
            'photo.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'photo_selfie' => '',
            'photo_selfie.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'photo_ktp' => '',
            'photo_ktp.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'photo_kk' => '',
            'photo_kk.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);
        $cek['is_entry'] = 1;

        // Pengecekan format "m-d-Y"
        $tg = explode('-', $request->masa_identitas);
        if (strlen($tg[0]) == 2) {
            $cek['masa_identitas'] = Carbon::createFromFormat('m-d-Y', $request->masa_identitas)->format('Ymd');
        } else if (strlen($tg[0]) == 4) {
            $cek['masa_identitas'] = Carbon::createFromFormat('Y-m-d', $request->masa_identitas)->format('Ymd');
        }

        //Hapus format tanggal Y-M-D menjadi YMD
        $cek['tanggal_lahir'] = Carbon::createFromFormat('Y-m-d', $request->tanggal_lahir)->format('Ymd');

        //Cek Photo
        if ($request->file('photo')) {
            if ($request->oldphoto) {
                Storage::delete('public/image/photo/' . $request->oldphoto);
            }
            $ekstensi = $cek['photo']->getClientOriginalExtension();
            $new = $request->no_identitas . '_' . $request->nama_nasabah . '.' . $ekstensi;
            $cek['photo'] = $request->file('photo')->storeAs('image/photo', $new, 'public');
            $cek['photo'] = $new;
        } else {
            $cek['photo'] = $request->oldphoto;
        }

        //Cek Photo Selfie
        if ($request->file('photo_selfie')) {
            if ($request->oldphotoselfie) {
                Storage::delete('public/image/photo_selfie/' . $request->oldphotoselfie);
            }
            $files = $cek['photo_selfie']->getClientOriginalExtension();
            $new = $request->no_identitas . '_' . $request->nama_nasabah . '.' . $files;
            $cek['photo_selfie'] = $request->file('photo_selfie')->storeAs('image/photo_selfie', $new, 'public');
            $cek['photo_selfie'] = $new;
        } else {
            $cek['photo_selfie'] = $request->oldphotoselfie;
        }

        //Cek Photo KTP
        if ($request->file('photo_ktp')) {
            if ($request->oldphotoktp) {
                Storage::delete('public/image/photo_ktp/' . $request->oldphotoktp);
            }
            $files = $cek['photo_ktp']->getClientOriginalExtension();
            $new = $request->no_identitas . '_' . $request->nama_nasabah . '.' . $files;
            $cek['photo_ktp'] = $request->file('photo_ktp')->storeAs('image/photo_ktp', $new, 'public');
            $cek['photo_ktp'] = $new;
        } else {
            $cek['photo_ktp'] = $request->oldphotoktp;
        }

        //Cek Photo KK
        if ($request->file('photo_kk')) {
            if ($request->oldphotokk) {
                Storage::delete('public/image/photo_kk/' . $request->oldphotokk);
            }
            $files = $cek['photo_kk']->getClientOriginalExtension();
            $new = $request->no_identitas . '_' . $request->nama_nasabah . '.' . $files;
            $cek['photo_kk'] = $request->file('photo_kk')->storeAs('image/photo_kk', $new, 'public');
            $cek['photo_kk'] = $new;
        } else {
            $cek['photo_kk'] = $request->oldphotokk;
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

        $cek['is_entry'] = 1;
        $cek['otorisasi'] = 'N';

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
