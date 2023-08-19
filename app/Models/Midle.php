<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Midle extends Model
{
    use HasFactory;

    protected static function cifedit($data)
    {

        //Cek data Current CIF
        $query = Tabungan::where('noid', $data['no_identitas'])
            ->where('jttempoid', $data['tanggal_lahir'])
            ->first();
        // dd($query);

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

        //perubahan field tabel untuk view
        $query->no_identitas = $query->noid;
        $query->nama_nasabah = $query->fname;
        $query->tempat_lahir = $query->tempatlahir;
        $query->alamat_ktp = $query->alamat;

        // $query->no_npwp = (int) $query->npwp;
        // $query->no_telp = (int) $query->nohp;

        $query->no_npwp = $query->npwp;
        $query->no_telp = $query->nohp;
        $query->nama_ibu_kandung = $query->nmibukandung;

        //Data dati
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

        $cek = Nasabah::where('kode_nasabah', $data)->first();
        // dd($cek);
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
                    ->select('users.name as nama', 'roles.name')
                    ->where('users.id', '=', $us)->get();
        // dd($user);

        $pend = Pendidikan::all();
        $job = Pekerjaan::all();
        return [
            'pend' => $pend,
            'job' => $job,
            'nasabah' => $cek,
            'kab' => $kab,
        ];
    }
}