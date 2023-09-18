<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Midle extends Model
{
    use HasFactory;

    protected static function cifedit($data)
    {
    
        //Cek data Current CIF
        $query = Tabungan::where('noid', $data['no_identitas'])
            ->where('jttempoid', $data['tanggal_lahir'])
            ->first();

        //Ubah identitas dari nomor id menjadi data string
        $iden = Data::identitas($query->kodeid);
        $query->identitas = $query->kodeid;
        $query->iden = $iden;

        //Ubah tanggal lahir
        $carbonDate = Carbon::createFromFormat('Ymd', $query->jttempoid);
        $query->jttempoid = $carbonDate->format('Y-m-d');
        $query->tanggal_lahir = $query->jttempoid;

        //Ubah masa identitas
        $carbonMasa = Carbon::createFromFormat('Ymd', $query->tgllahir);
        $query->tgllahir = $carbonMasa->format('Y-m-d');
        $query->masa_identitas = $query->tgllahir;

        //Ubah agama dari nomor id menjadi data string
        $agama = Data::agama($query->agama);
        $query->religi = $agama;

        //Ubah jenis kelamin dari nomor id menjadi data string
        $jk = Data::jk($query->sex);
        $query->jenis_kelamin = $query->sex;
        $query->jk = $jk;

        //Ubah status dari nomor id menjadi data string
        $stat = Data::status($query->stsrt);
        $query->status_pernikahan = $query->stsrt;
        $query->st = $stat;

        //Pekerjaan dari CIF
        $j = Pekerjaan::where('kode_pekerjaan', $query->pekerjaan)->first();
        $query->jo = $j->nama_pekerjaan;
        $query->pekerjaan_kode = $query->pekerjaan;

        //Pendidikan dari CIF
        $p = Pendidikan::where('kode_pendidikan', $query->pendidikan)->first();
        $query->std = $p->nama_pendidikan;
        $query->pendidikan_kode = $query->pendidikan;

        //perubahan field tabel untuk view
        $query->no_identitas = $query->noid;
        $query->nama_nasabah = $query->fname;
        $query->tempat_lahir = $query->tempatlahir;
        $query->alamat_ktp = $query->alamat;
        $query->no_npwp = trim($query->npwp);
        $query->no_telp = trim($query->nohp);
        $query->nama_ibu_kandung = $query->nmibukandung;

        //Auth user
        $us = Auth::user()->id;
        $user = DB::table('users')
                    ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.code_user')
                    ->where('users.id', '=', $us)->get();
        $query->kode_user = $user[0]->code_user;
        $query->tempat_kerja = $query->tempat_bekerja;
        
        $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');
        $pend = Pendidikan::all();
        $job = Pekerjaan::all();
        return [
            'pend' => $pend,
            'job' => $job,
            'nasabah' => $query,
            'kab' => $kab,
        ];
    }

    protected static function nasabahedit($data)
    {
        $enc = Crypt::decrypt($data);
        $kd_pengajuan = Pengajuan::where('kode_pengajuan', $enc)->get();
        
        $cek = Nasabah::where('kode_nasabah', $kd_pengajuan[0]->nasabah_kode)->first();
                
        //Format masa identitas
        if (!is_null($cek->masa_identitas)) {
            $carbonid = Carbon::createFromFormat('Ymd', $cek->masa_identitas);
            $cek->masa_identitas = $carbonid->format('m-d-Y');
        }

        //Format tanggal lahir
        $carbonDate = Carbon::createFromFormat('Ymd', $cek->tanggal_lahir);
        $cek->tanggal_lahir = $carbonDate->format('m-d-Y');

        //Ubah identitas dari nomor id menjadi data string
        $iden = Data::identitas($cek->identitas);
        $cek['iden'] = $iden;

        //Ubah agama dari nomor id menjadi data string
        $agama = Data::agama($cek->agama);
        $cek['religi'] = $agama;

        //Ubah jenis kelamin dari nomor id menjadi data string
        $jk = Data::jk($cek->jenis_kelamin);
        $cek['jk'] = $jk;

        //Ubah kewarganegaraan dari nomor id menjadi data string
        $warga = Data::warganegara($cek->kewarganegaraan);
        $cek['kn'] = $warga;

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
        $sts = Data::status($cek->status_pernikahan);
        $cek['st'] = $sts;

        //Ubah sumber dana dari nomor id menjadi data string
        $dn = Data::dana($cek->sumber_dana);
        $cek['dana'] = $dn;

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
        $utama = Data::penghasilanutama($cek->penghasilan_utama);
        $cek['hasil'] = $utama;

        //Ubah penghasilan lainnya dari nomor id menjadi data string
        $lain = Data::penghasilanlain($cek->penghasilan_lainnya);
        $cek['lain'] = $lain;

        //Data dati
        $kab = DB::select('select distinct kode_dati, nama_dati from v_dati');

        //Auth user
        $us = Auth::user()->id;
        $user = DB::table('users')
                    ->leftjoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->select('users.code_user')
                    ->where('users.id', '=', $us)->get();
        $cek['kode_user'] = $user[0]->code_user;
        $cek['sname'] = $cek['nama_panggilan'];
        $cek['kd_pengajuan'] = $kd_pengajuan[0]->kode_pengajuan;
        
        $pend = Pendidikan::all();
        $job = Pekerjaan::all();
        return [
            'pend' => $pend,
            'job' => $job,
            'nasabah' => $cek,
            'kab' => $kab,
        ];
    }

    protected static function analisa_usaha($data)
    {
        $cek = DB::table('data_pengajuan')
                ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
                ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
                ->where('data_pengajuan.kode_pengajuan', '=', $data)
                ->select('data_nasabah.nama_nasabah', 'data_pengajuan.kode_pengajuan', 'data_pengajuan.plafon', 'data_pengajuan.jangka_waktu')->get();
        $cek[0]->kd_pengajuan = Crypt::encrypt($data);
        return $cek;
    }

    protected static function perdagangan_detail($data)
    {
        $enc = Crypt::decrypt($data);
        $cek = DB::table('au_perdagangan')
                ->leftJoin('bu_perdagangan','au_perdagangan.kode_usaha', '=', 'bu_perdagangan.usaha_kode')
                ->select('au_perdagangan.*', 'bu_perdagangan.*')
                ->where('au_perdagangan.kode_usaha', $enc)->get();
        
        $dusaha = DB::table('du_perdagangan')
                ->where('usaha_kode', $enc)->get();
        
        return [$cek, $dusaha];
    }
    protected static function jasa_detail($data)
    {
        $cek = Jasa::where('kode_usaha', $data)->get();
        return $cek;
    }

    protected static function pertanian_detail($data)
    {
        $data = DB::table('au_pertanian')
                ->leftJoin('bu_pertanian', 'au_pertanian.kode_usaha', '=', 'bu_pertanian.usaha_kode')
                ->leftJoin('du_pertanian', 'au_pertanian.kode_usaha', '=', 'du_pertanian.usaha_kode')
                ->select('au_pertanian.*','bu_pertanian.*', 'du_pertanian.*')
                ->where('au_pertanian.kode_usaha', '=', $data)->get();
        
        return $data;
    }    

    protected static function kemampuan_keuangan($data)
    {
        
        $perdagangan = Perdagangan::where('pengajuan_kode', $data)->get();
        $jasa = Jasa::where('pengajuan_kode', $data)->get();
        $pertanian = Pertanian::where('pengajuan_kode', $data)->get();
        $lain = Lain::where('pengajuan_kode', $data)->get();
        $keuangan = Keuangan::where('pengajuan_kode', $data)->get();

        $tani = [];
        for ($i=0; $i < count($pertanian); $i++) { 
            $tani[] = $pertanian[$i]->laba_perbulan ?? 0;
        }
        //Hasil penjumlahan analisa usaha pertanian
        $totalpertanian = array_sum($tani);
        
        $dagang = [];
        for ($j=0; $j < count($perdagangan); $j++) { 
            $dagang[] = $perdagangan[$j]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha perdagangan
        $totalperdagangan = array_sum($dagang);

        $js = [];
        for ($k=0; $k < count($jasa); $k++) { 
            $js[] = $jasa[$k]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha jasa
        $totaljasa = array_sum($js);

        $la = [];
        for ($l=0; $l < count($lain); $l++) { 
            $la[] = $lain[$l]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha jasa
        $totallain = array_sum($la);

        $uang = [];
        for ($m=0; $m < count($keuangan); $m++) { 
            $uang['rumah'] = $keuangan[$m]->b_rumah_tangga ?? 0;
            $uang['kewajiban'] = $keuangan[$m]->b_kewajiban_lainya ?? 0;
            $uang['perbulan'] = $keuangan[$m]->keuangan_perbulan ?? 0;
        }
        // dd($uang);
        $hasil = [
            'perdagangan' => $totalperdagangan,
            'jasa' => $totaljasa,
            'pertanian' => $totalpertanian,
            'lain' => $totallain,
        ];
    
        return $hasil;
    }

    protected static function taksasi_jaminan($data)
    {
        $jaminan = DB::table('data_pengajuan')
                    ->join('data_jaminan', 'data_pengajuan.kode_pengajuan', '=', 'data_jaminan.pengajuan_kode')
                    ->join('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
                    ->join('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
                    ->select('data_pengajuan.kode_pengajuan', 'data_jaminan.id', 'data_jaminan.no_dokumen', 'data_jaminan.atas_nama', 'data_jaminan.otorisasi', 'data_jaminan.nilai_taksasi', 'data_jenis_agunan.jenis_agunan', 'data_jenis_dokumen.jenis_dokumen')
                    ->where('data_pengajuan.kode_pengajuan', '=', $data)
                    ->get();

        //
        return $jaminan;
    }
}
