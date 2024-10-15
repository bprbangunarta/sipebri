<?php

namespace App\Models;

use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Midle extends Model
{
    use HasFactory;

    protected static function cifedit($data)
    {

        //Cek data Current CIF
        $query = Tabungan::where('noid', $data['no_identitas'])
            // ->where('jttempoid', $data['tanggal_lahir'])
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
        // $query->masa_identitas = $query->tgllahir;
        $query->masa_identitas = '2099-12-29';

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
        if (is_null($j)) {
            $query->jo = null;
            $query->pekerjaan_kode = null;
        } else {
            $query->jo = $j->nama_pekerjaan;
            $query->pekerjaan_kode = $query->pekerjaan;
        }

        //Pendidikan dari CIF
        $p = Pendidikan::where('kode_pendidikan', $query->pendidikan)->first();
        if (is_null($p)) {
            $query->std = null;
        } else {
            $query->std = $p->nama_pendidikan;
        }
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
            $cek->masa_identitas = $carbonid->format('Y-m-d');
        }

        //Format tanggal lahir
        $carbonDate = Carbon::createFromFormat('Ymd', $cek->tanggal_lahir);
        $cek->tanggal_lahir = $carbonDate->format('Y-m-d');

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
            if (count($sc) > 0) {
                $cek['std'] = $sc[0]->nama_pendidikan;
            } else {
                $cek['std'] = null;
            }
        }

        //Ubah pekerjaan dari nomor id menjadi data string
        if (!is_null($cek->pekerjaan_kode)) {
            $pk = Pekerjaan::where('kode_pekerjaan', $cek->pekerjaan_kode)->get();
            if (count($pk) > 0) {
                $cek['jo'] = $pk[0]->nama_pekerjaan;
            } else {
                $cek['jo'] = null;
            }
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
            ->select('data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_pengajuan.*')->get();
        $cek[0]->kd_pengajuan = Crypt::encrypt($data);
        $produk = DB::table('data_produk')->where('kode_produk', $cek[0]->produk_kode)->first('admin');
        $cek[0]->admin = $produk->admin ?? null;
        return $cek;
    }

    protected static function perdagangan_detail($data)
    {
        $enc = Crypt::decrypt($data);
        $cek = DB::table('au_perdagangan')
            ->leftJoin('bu_perdagangan', 'au_perdagangan.kode_usaha', '=', 'bu_perdagangan.usaha_kode')
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
            ->select('au_pertanian.*', 'bu_pertanian.*', 'du_pertanian.*')
            ->where('au_pertanian.kode_usaha', '=', $data)->get();

        return $data;
    }

    protected static function kemampuan_keuangan($data)
    {

        $perdagangan = Perdagangan::where('pengajuan_kode', $data)->get();
        $jasa = Jasa::where('pengajuan_kode', $data)->get();
        $pertanian = Pertanian::where('pengajuan_kode', $data)->get();
        $lain = Lain::where('pengajuan_kode', $data)->get();

        $tani = [];
        for ($i = 0; $i < count($pertanian); $i++) {
            $tani[] = $pertanian[$i]->laba_perbulan ?? 0;
        }
        //Hasil penjumlahan analisa usaha pertanian
        $totalpertanian = array_sum($tani);

        $dagang = [];
        for ($j = 0; $j < count($perdagangan); $j++) {
            $dagang[] = $perdagangan[$j]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha perdagangan
        $totalperdagangan = array_sum($dagang);

        $js = [];
        for ($k = 0; $k < count($jasa); $k++) {
            $js[] = $jasa[$k]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha jasa
        $totaljasa = array_sum($js);

        $la = [];
        for ($l = 0; $l < count($lain); $l++) {
            $la[] = $lain[$l]->laba_bersih ?? 0;
        }
        //Hasil penjumlahan analisa usaha jasa
        $totallain = array_sum($la);

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

    public static function a5ckodeacak($name, $length)
    {

        // for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
        //     $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

        //     if (!DB::table('a5c_character')->where('kode_analisa', $acak)->exists()) {
        //         return $acak;
        //     }
        // }

        // return null;

        do {
            $lastCode = DB::table('a5c_character')
                ->whereNotNull('kode_analisa')
                ->orderBy('kode_analisa', 'desc')
                ->value('kode_analisa');

            if (!$lastCode) {
                $prefix = $name;
                $newNumber = 1;
            } else {

                $prefix = substr($lastCode, 0, 3);
                $numberPart = substr($lastCode, 3);

                $newNumber = (int) $numberPart + 1;
            }

            $acak = $prefix . str_pad($newNumber, $length, '0', STR_PAD_LEFT);
        } while (DB::table('a5c_character')->where('kode_analisa', $acak)->exists());
        return $acak;
    }

    public static function karakter()
    {
        $data = (object) [
            'gaya_hidup' => null,
            'pengendalian_emosi' => null,
            'perbuatan_tercela' => null,
            'harmonis' => null,
            'konsisten' => null,
            'kepatuhan' => null,
            'hubungan_sosial' => null,
            'nilai_karakter' => null,
        ];
        return $data;
    }

    public static function capacity()
    {
        $data = (object) [
            'kontinuitas' => null,
            'pengalaman_usaha' => null,
            'pertumbuhan_usaha' => null,
            'laporan_keuangan' => null,
            'catatan_kredit' => null,
            'kondisi_slik' => null,
            'aset_diluar_usaha' => null,
            'aset_terkait_usaha' => null,
            'capital_sumber_modal' => null,
            'rc' => null,
            'capital_evaluasi_capital' => null,
            'evaluasi_capacity' => null,
        ];
        return $data;
    }

    public static function collateral()
    {
        $data = (object) [
            'agunan_utama' => null,
            'agunan_tambahan' => null,
            'legalitas_agunan_tambahan' => null,
            'legalitas_agunan' => null,
            'mudah_diuangkan' => null,
            'stabilitas_harga' => null,
            'lokasi_shm' => null,
            'kondisi_kendaraan' => null,
            'aspek_hukum' => null,
            'taksasi_agunan' => null,
        ];
        return $data;
    }

    public static function conition()
    {
        $data = (object) [
            'persaingan_usaha' => null,
            'kondisi_alam' => null,
            'regulasi_pemerintah' => null,
        ];
        return $data;
    }

    public static function memorandum($data)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('au_perdagangan', 'data_pengajuan.kode_pengajuan', '=', 'au_perdagangan.pengajuan_kode')
            ->leftJoin('au_pertanian', 'data_pengajuan.kode_pengajuan', '=', 'au_pertanian.pengajuan_kode')
            ->select('data_nasabah.nama_nasabah', 'data_nasabah.alamat_ktp', 'data_nasabah.no_telp', 'au_perdagangan.lokasi_usaha as dg_lokasi', 'au_pertanian.lokasi_usaha as pt_lokasi')
            ->where('data_pengajuan.kode_pengajuan', '=', $data)->get();
    }

    public static function kodeacak_memorandum($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('a_memorandum')->where('kode_analisa', $acak)->exists()) {
                return $acak;
            }
        }

        return null; // Jika tidak ada kode yang unik ditemukan
    }

    public static function kodeacak_adm($name, $length)
    {
        // for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
        //     $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

        //     if (!DB::table('a_administrasi')->where('kode_analisa', $acak)->exists()) {
        //         return $acak;
        //     }
        // }

        do {
            $lastCode = DB::table('a_administrasi')
                ->whereNotNull('kode_analisa')
                ->orderBy('kode_analisa', 'desc')
                ->value('kode_analisa');

            if (!$lastCode) {
                $lastCode = $name . str_pad(1, $length, '0', STR_PAD_LEFT);
            } else {
                $prefix = substr($lastCode, 0, 3);
                $numberPart = substr($lastCode, 3);

                $newNumber = (int) $numberPart + 1;
            }

            $newCode = $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        } while (DB::table('a_administrasi')->where('kode_analisa', $newCode)->exists());

        return $newCode;
    }

    public static function sandibi($enc)
    {
        // $sifat = DB::table('bi_sifat')->where('sandi', $data->bi_sifat_kode)->get();
        // $bi_penggunaan = DB::table('bi_penggunaan_debitur')->where('sandi', $data->bi_penggunaan_kode)->get();
        // $bi_gol_penjamin = DB::table('bi_golongan_penjamin')->where('sandi', $data->bi_gol_penjamin_kode)->get();
        // $sumber_pelunasan = DB::table('bi_sumber_dana_pelunasan')->where('sandi', $data->bi_sumber_pelunasan_kode)->get();
        // $jenis_usaha = DB::table('bi_jenis_usaha')->where('sandi', $data->bi_jenis_usaha_kode)->get();
        // $sek_ekonomi = DB::table('bi_sektor_ekonomi')->where('sandi', $data->bi_sek_ekonomi_kode)->get();
        // $sek_ekonomi_slik = DB::table('bi_sektor_ekonomi_slik')->where('sandi', $data->bi_sek_ekonomi_slik)->get();
        // $bi_gol_debitur = DB::table('bi_golongan_debitur')->where('sandi', $data->bi_gol_debitur_kode)->get();
        // $bi_gol_debitur_slik = DB::table('bi_golongan_debitur_slik')->where('sandi', $data->bi_gol_debitur_slik)->get();

        // if (count($sifat) == 0) {
        //     $sandi = null;
        //     $keterangan = null;
        // } else {
        //     $sandi = $sifat[0]->sandi;
        //     $keterangan = $sifat[0]->keterangan;
        // }
        // $hasil = (object) [
        //     'sifat_kode' => $sandi,
        //     'sifat_nama' => $keterangan,
        //     'bi_penggunaan_debitur_kode' => $bi_penggunaan[0]->sandi,
        //     'bi_penggunaan_debitur_nama' => $bi_penggunaan[0]->keterangan,
        //     'bi_gol_penjamin_kode' => $bi_gol_penjamin[0]->sandi,
        //     'bi_gol_penjamin_nama' => $bi_gol_penjamin[0]->keterangan,
        //     'fiducia' => $data->by_fiducia,
        //     'bagian_dijaminkan' => $data->bagian_dijaminkan,
        //     'sumber_pelunasan_kode' => $sumber_pelunasan[0]->sandi,
        //     'sumber_pelunasan_nama' => $sumber_pelunasan[0]->keterangan,
        //     'jenis_usaha_kode' => $jenis_usaha[0]->sandi,
        //     'jenis_usaha_nama' => $jenis_usaha[0]->keterangan,
        //     'sek_ekonomi_kode' => $sek_ekonomi[0]->sandi,
        //     'sek_ekonomi_nama' => $sek_ekonomi[0]->keterangan,
        //     'sek_ekonomi_slik_kode' => $sek_ekonomi_slik[0]->sandi,
        //     'sek_ekonomi_slik_nama' => $sek_ekonomi_slik[0]->keterangan,
        //     'bi_gol_debitur_kode' => $bi_gol_debitur[0]->sandi,
        //     'bi_gol_debitur_nama' => $bi_gol_debitur[0]->keterangan,
        //     'bi_gol_debitur_slik_kode' => $bi_gol_debitur_slik[0]->sandi,
        //     'bi_gol_debitur_slik_slik_nama' => $bi_gol_debitur_slik[0]->keterangan,
        // ];

        // return $hasil;

        $data = DB::table('data_pengajuan')
            ->leftJoin('a_memorandum', 'data_pengajuan.kode_pengajuan', '=', 'a_memorandum.pengajuan_kode')
            ->leftJoin('bi_sifat', 'a_memorandum.bi_sifat_kode', '=', 'bi_sifat.sandi')
            ->leftJoin('bi_penggunaan_debitur', 'a_memorandum.bi_penggunaan_kode', '=', 'bi_penggunaan_debitur.sandi')
            ->leftJoin('bi_golongan_penjamin', 'a_memorandum.bi_gol_penjamin_kode', '=', 'bi_golongan_penjamin.sandi')
            ->leftJoin('bi_sumber_dana_pelunasan', 'a_memorandum.bi_sumber_pelunasan_kode', '=', 'bi_sumber_dana_pelunasan.sandi')
            ->leftJoin('bi_jenis_usaha', 'a_memorandum.bi_jenis_usaha_kode', '=', 'bi_jenis_usaha.sandi')
            ->leftJoin('bi_sektor_ekonomi', 'a_memorandum.bi_sek_ekonomi_kode', '=', 'bi_sektor_ekonomi.sandi')
            ->leftJoin('bi_sektor_ekonomi_slik', 'a_memorandum.bi_sek_ekonomi_slik', '=', 'bi_sektor_ekonomi_slik.sandi')
            ->leftJoin('bi_golongan_debitur', 'a_memorandum.bi_gol_debitur_kode', '=', 'bi_golongan_debitur.sandi')
            ->leftJoin('bi_golongan_debitur_slik', 'a_memorandum.bi_gol_debitur_slik', '=', 'bi_golongan_debitur_slik.sandi')
            ->where('data_pengajuan.kode_pengajuan', $enc)
            ->select(
                'a_memorandum.*',
                'bi_sifat.keterangan as bi_sifat',
                'bi_penggunaan_debitur.keterangan as bi_penggunaan_debitur',
                'bi_golongan_penjamin.keterangan as bi_golongan_penjamin',
                'bi_sumber_dana_pelunasan.keterangan as bi_sumber_dana_pelunasan',
                'bi_jenis_usaha.keterangan as bi_jenis_usaha',
                'bi_sektor_ekonomi.keterangan as bi_sek_ekonomi',
                'bi_sektor_ekonomi_slik.keterangan as bi_sektor_ekonomi_slik',
                'bi_golongan_debitur.keterangan as bi_golongan_debitur',
                'bi_golongan_debitur_slik.keterangan as bi_golongan_debitur_slik',
            )->get();
        //

        return $data;
    }

    public static function kode_komite($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('a_komite')->where('kode_analisa', $acak)->exists()) {
                return $acak;
            }
        }

        return null; // Jika tidak ada kode yang unik ditemukan
    }

    public static function data_usulan($data, $role)
    {
        $cek = DB::table('data_usulan')
            ->where('pengajuan_kode', $data)
            ->where('role_name', $role)
            ->first();
        //
        if (is_null($cek)) {
            $cek = (object) ['usulan_plafon' => null];
        }

        return $cek->usulan_plafon;
    }

    public static function persetujuan_komite_cs_kksk($user, $name)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')

            ->where(function ($query) use ($user) {
                $query->where('data_survei.surveyor_kode', '=', $user)
                    ->where('data_pengajuan.status', '=', 'Sudah Otorisasi')
                    ->where('data_pengajuan.tracking', '=', 'Persetujuan Komite')
                    ->where('data_pengajuan.on_current', '=', '0');
            })
            // ->orWhere(function ($query) use ($user) {
            //     $query->where('data_survei.surveyor_kode', '=', $user)
            //         ->where('data_pengajuan.status', '=', 'Disetujui')
            //         ->where('data_pengajuan.tracking', '=', 'Selesai')
            //         ->whereNull('data_notifikasi.pengajuan_kode');
            // })

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('users.code_user', 'like', '%' . $name . '%')
                    ->orWhere('users.name', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_pengajuan.plafon',
                'data_pengajuan.created_at as tanggal',
                'data_pengajuan.kategori',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.status_pengembalian',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name as surveyor',
                'data_pengajuan.jangka_waktu as jk',
                'data_produk.*'
            )
            ->orderByRaw("CASE WHEN status_pengembalian = 'YA' THEN 1 WHEN status_pengembalian = 'TIDAK' THEN 2 ELSE 3 END")
            ->orderBy('data_tracking.analisa_kredit', 'desc');

        return $cek;
    }

    public static function persetujuan_komite_staff($user, $role, $name)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')

            ->where(function ($query) use ($user, $role) {
                $query->where('data_survei.surveyor_kode', '=', $user)
                    ->where('data_pengajuan.tracking', '=', $role);
            })
            // ->orWhere(function ($query) use ($user) {
            //     $query->where('data_survei.surveyor_kode', '=', $user)
            //         ->where('data_pengajuan.status', '=', 'Disetujui')
            //         ->where('data_pengajuan.tracking', '=', 'Selesai')
            //         ->whereNull('data_notifikasi.pengajuan_kode');
            // })

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('users.code_user', 'like', '%' . $name . '%')
                    ->orWhere('users.name', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_pengajuan.plafon',
                'data_tracking.analisa_kredit as tanggal',
                'data_pengajuan.kategori',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name as surveyor',
                'data_pengajuan.jangka_waktu as jk',
                'data_produk.*'
            )
            ->orderBy('data_tracking.analisa_kredit', 'desc');

        return $cek;
    }

    public static function persetujuan_komite_kasi($user, $role, $name)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')

            ->where(function ($query) use ($user, $role) {
                $query->where('data_survei.kasi_kode', '=', $user)
                    ->where('data_pengajuan.tracking', '=', $role);
            })

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('users.code_user', 'like', '%' . $name . '%')
                    ->orWhere('users.name', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_pengajuan.plafon',
                'data_tracking.analisa_kredit as tanggal',
                'data_pengajuan.kategori',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.status_pengembalian',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name as surveyor',
                'data_pengajuan.jangka_waktu as jk',
                'data_produk.*'
            )->orderByRaw("CASE WHEN status_pengembalian = 'YA' THEN 1 WHEN status_pengembalian = 'TIDAK' THEN 2 ELSE 3 END")
            ->orderBy('data_tracking.analisa_kredit', 'desc');
        return $cek;
    }

    public static function persetujuan_komite_kabag($role, $name)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')

            // ->where('data_pengajuan.tracking', '=', $role)
            ->where('data_pengajuan.tracking', 'Naik Komite I')

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('users.code_user', 'like', '%' . $name . '%')
                    ->orWhere('users.name', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_pengajuan.plafon',
                'data_tracking.analisa_kredit as tanggal',
                'data_pengajuan.kategori',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.status_pengembalian',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name as surveyor',
                'data_pengajuan.jangka_waktu as jk',
                'data_produk.*'
            )
            ->orderByRaw("CASE WHEN status_pengembalian = 'YA' THEN 1 WHEN status_pengembalian = 'TIDAK' THEN 2 ELSE 3 END")
            ->orderBy('data_tracking.analisa_kredit', 'desc');

        return $cek;
    }

    public static function persetujuan_komite_direk_bisnis($role, $name)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')

            ->where('data_pengajuan.tracking', '=', $role)

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('users.code_user', 'like', '%' . $name . '%')
                    ->orWhere('users.name', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_pengajuan.plafon',
                'data_tracking.analisa_kredit as tanggal',
                'data_pengajuan.kategori',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.status_pengembalian',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name as surveyor',
                'data_pengajuan.jangka_waktu as jk',
                'data_produk.*'
            )
            ->orderByRaw("CASE WHEN status_pengembalian = 'YA' THEN 1 WHEN status_pengembalian = 'TIDAK' THEN 2 ELSE 3 END")
            ->orderBy('data_tracking.analisa_kredit', 'desc');

        return $cek;
    }

    public static function persetujuan_komite_direksi($role, $name)
    {
        $cek = DB::table('data_pengajuan')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->leftJoin('users', 'data_survei.surveyor_kode', '=', 'users.code_user')
            ->leftJoin('data_tracking', 'data_tracking.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->join('data_produk', 'data_produk.kode_produk', '=', 'data_pengajuan.produk_kode')

            ->where('data_pengajuan.tracking', '=', $role)

            ->where(function ($query) use ($name) {
                $query->where('data_nasabah.nama_nasabah', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.kode_pengajuan', 'like', '%' . $name . '%')
                    ->orWhere('data_pengajuan.produk_kode', 'like', '%' . $name . '%')
                    ->orWhere('users.code_user', 'like', '%' . $name . '%')
                    ->orWhere('users.name', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.kode_kantor', 'like', '%' . $name . '%')
                    ->orWhere('data_kantor.nama_kantor', 'like', '%' . $name . '%');
            })
            ->select(
                'data_pengajuan.kode_pengajuan',
                'data_pengajuan.tracking',
                'data_pengajuan.status',
                'data_pengajuan.plafon',
                'data_tracking.analisa_kredit as tanggal',
                'data_pengajuan.kategori',
                'data_pengajuan.produk_kode',
                'data_pengajuan.metode_rps',
                'data_pengajuan.status_pengembalian',
                'data_nasabah.kode_nasabah',
                'data_nasabah.nama_nasabah',
                'data_nasabah.alamat_ktp',
                'data_nasabah.kelurahan',
                'data_nasabah.kecamatan',
                'data_pengajuan.plafon',
                'data_kantor.kode_kantor',
                'data_kantor.nama_kantor',
                'data_survei.surveyor_kode',
                'data_survei.tgl_survei',
                'data_survei.tgl_jadul_1',
                'data_survei.tgl_jadul_2',
                'users.name as surveyor',
                'data_pengajuan.jangka_waktu as jk',
                'data_produk.*'
            )
            ->orderByRaw("CASE WHEN status_pengembalian = 'YA' THEN 1 WHEN status_pengembalian = 'TIDAK' THEN 2 ELSE 3 END")
            ->orderBy('data_tracking.analisa_kredit', 'desc');

        return $cek;
    }

    public static function kode_tracking($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!DB::table('data_tracking')->where('kode_tracking', $acak)->exists()) {
                return $acak;
            }
        }

        return null; // Jika tidak ada kode yang unik ditemukan
    }

    public static function taksasi_agunan($data, $plafon)
    {
        //Taksasi
        $taksasi = DB::table('data_jaminan')->where('pengajuan_kode', $data)->get();
        //total semua nilai taksasi
        $tak = [];
        for ($i = 0; $i < count($taksasi); $i++) {
            $tak[] = $taksasi[$i]->nilai_taksasi ?? 0;
        }

        $totaltaksasi = array_sum($tak);

        if (count($taksasi) != 0 && $totaltaksasi != 0) {
            $hasiltaksasi = (intval($plafon) / $totaltaksasi) * 100;
        } else {
            $hasiltaksasi = 0;
        }
        return $hasiltaksasi;
    }

    public static function perhitungan_rc($kode, $metode, $plafon, $suku_bunga, $jangka_waktu, $grace_periode)
    {
        $keuangan = Keuangan::where('pengajuan_kode', $kode)->pluck('keuangan_perbulan')->first();

        if ($metode == 'EFEKTIF MUSIMAN' || $metode == 'EFEKTIF') {

            $sb = $suku_bunga / 100;
            $plafon_permusim = ($plafon * 70) / 100;
            $bg = ((($plafon * $suku_bunga) / 100) * 30) / 365;
            $rc = ($bg / $keuangan) * 100;
        } else if ($metode == 'EFEKTIF ANUITAS') {

            $ssb = $suku_bunga / 100;
            $sb = $ssb / 12;
            $anuitas = ($plafon * $sb) / (1 - 1 / pow(1 + $sb, $jangka_waktu));
            $rc = ($anuitas / $keuangan) * 100;
        } elseif ($metode == 'FLAT' && !is_null($grace_periode)) {

            $bunga_10_bulan = (($plafon * $suku_bunga) / 100) / 12;
            $pokok_bulanan = $plafon / ($jangka_waktu - $grace_periode);
            $angsuran = $bunga_10_bulan + $pokok_bulanan;
            $rc = ($angsuran / $keuangan) * 100;
        } else {

            $bunga = (($plafon * $suku_bunga) / 100) / 12;
            $pokok = $plafon / $jangka_waktu;
            $angsuran = $bunga + $pokok;
            $rc = ($angsuran / $keuangan) * 100;
        }

        return number_format($rc, 2);
    }

    public static function perhitungan_apht_fiducia($data)
    {
        $memo = DB::table('data_pengajuan')
            ->leftJoin('a_memorandum', 'data_pengajuan.kode_pengajuan', '=', 'a_memorandum.pengajuan_kode')
            ->where('pengajuan_kode', $data)
            ->select(
                'data_pengajuan.*',
                'a_memorandum.*',
            )->first();

        if (!is_null($memo)) {
            if ($memo->pengikatan == '1') {
                $adm = ($memo->plafon * $memo->b_admin) / 100;
                $has = (int)$adm;
                $hasil = (object) [
                    'apht' => 0,
                    'pengikatan' => $memo->pengikatan,
                    'fiducia' => 0,
                    'adm' => $has,
                ];
            } elseif ($memo->pengikatan == '2') {
                $jml = ($memo->plafon * 1.5) / 100;
                $adm = ($memo->plafon * $memo->b_admin) / 100;
                // $has = (int)$adm - (int)$jml;
                $hasil = (object) [
                    'apht' => 0,
                    'pengikatan' => $memo->pengikatan,
                    'fiducia' => 0,
                    'adm' => (int)$adm,
                ];
            } else  if ($memo->pengikatan == '3') {
                $jml = ($memo->plafon * 1.5) / 100;
                $adm = ($memo->plafon * $memo->b_admin) / 100;
                // $has = (int)$adm - (int)$jml;
                $hasil = (object) [
                    'apht' => 0,
                    'pengikatan' => $memo->pengikatan,
                    'fiducia' => 0,
                    'adm' => (int)$adm,
                ];
            } else  if ($memo->pengikatan == '4') {
                $jml = ($memo->plafon * 0.75) / 100;
                $adm = ($memo->plafon * $memo->b_admin) / 100;
                // $has = (int)$adm - ((int)$jml * 2);
                $hasil = (object) [
                    'apht' => 0,
                    'pengikatan' => $memo->pengikatan,
                    'fiducia' => 0,
                    'adm' => (int)$adm,
                ];
            }
        } else {
            return null;
        }
        // dd($hasil);
        return $hasil;
    }

    public static function cetak_dokumen_analisa($kode)
    {
        $data = DB::table('data_pengajuan')
            ->join('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->join('data_pendamping', 'data_pengajuan.kode_pengajuan', '=', 'data_pendamping.pengajuan_kode')
            ->join('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->join('v_users', 'data_survei.surveyor_kode', '=', 'v_users.code_user')
            ->join('data_kantor', 'data_survei.kantor_kode', '=', 'data_kantor.kode_kantor')
            ->join('data_tracking', 'data_pengajuan.kode_pengajuan', '=', 'data_tracking.pengajuan_kode')
            ->select(
                'data_pengajuan.*',
                'data_pengajuan.created_at as tgl_pengajuan',
                'data_pengajuan.input_user as input_user_pengajuan',
                'data_nasabah.*',
                'data_nasabah.created_at as tgl_nasabah',
                'data_nasabah.photo as photo_nasabah',
                'data_nasabah.input_user as input_user_nasabah',
                'data_pendamping.no_identitas as no_identitas_pendamping',
                'data_pendamping.nama_pendamping',
                'v_users.nama_user as nama_surveyor',
                'data_survei.*',
                'data_survei.created_at as tgl_survei',
                'data_survei.surveyor_kode as input_user_survei',
                'data_kantor.nama_kantor',
                'data_tracking.*',
            )
            ->where('data_pengajuan.kode_pengajuan', $kode)
            ->get();
        //

        //Kasi
        $kasi = DB::table('v_users')->where('code_user', $data[0]->kasi_kode)->first();
        $data[0]->nama_kasi = $kasi->nama_user;
        //Input User Pengajuan
        $up = DB::table('v_users')->where('code_user', $data[0]->input_user_pengajuan)->first();
        $data[0]->nama_cs = $up->nama_user;
        //Input User Nasabah
        $un = DB::table('v_users')->where('code_user', $data[0]->input_user_nasabah)->first();
        $data[0]->nama_input_nasabah = $un->nama_user;
        //Input User Survei
        $ks = DB::table('v_users')->where('code_user', $data[0]->input_user_survei)->first();
        $data[0]->nama_input_survei = $ks->nama_user;

        return $data;
    }

    public static function cetak_dokumen_analisa_usaha_perdagangan($kode)
    {
        $data = DB::table('au_perdagangan')
            ->leftJoin('bu_perdagangan', 'au_perdagangan.kode_usaha', '=', 'bu_perdagangan.usaha_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'au_perdagangan.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->select('au_perdagangan.*', 'bu_perdagangan.*', 'data_nasabah.nama_nasabah')
            ->where('au_perdagangan.pengajuan_kode', $kode)->get();
        return $data;
    }

    public static function cetak_dokumen_analisa_usaha_pertanian($kode)
    {
        $data = DB::table('au_pertanian')
            ->leftJoin('bu_pertanian', 'au_pertanian.kode_usaha', '=', 'bu_pertanian.usaha_kode')
            ->leftJoin('du_pertanian', 'au_pertanian.kode_usaha', '=', 'du_pertanian.usaha_kode')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'au_pertanian.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->select('au_pertanian.*', 'bu_pertanian.*', 'du_pertanian.*', 'data_nasabah.nama_nasabah', 'data_pengajuan.jangka_waktu', 'data_pengajuan.plafon')
            ->where('au_pertanian.pengajuan_kode', $kode)->get();
        return $data;
    }

    public static function cetak_dokumen_analisa_usaha_jasa($kode)
    {
        $data = DB::table('au_jasa')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'au_jasa.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->where('au_jasa.pengajuan_kode', $kode)->get();

        //
        // if (count($data) != 0) {
        //     $pendapatan = [];
        //     $biaya_pajak = [];
        //     $pengeluaran = [];
        //     foreach ($data as $item) {
        //         $pendapatan[] = $item->laba_bersih ?? 0;
        //         $biaya_pajak[] = $item->b_pajak ?? 0;
        //         $pengeluaran[] = $item->pengeluaran ?? 0;
        //     }
        //     $total_pendapatan = array_sum($pendapatan);
        //     $total_biaya_pajak = array_sum($biaya_pajak);
        //     $total_pengeluaran = array_sum($pengeluaran);
        //     $total = $total_pendapatan - ($total_pengeluaran + $total_biaya_pajak) ?? 0;

        //     $jasa = (object) [
        //         'pendapatan' => $total_pendapatan,
        //         'biaya_pajak' => $total_biaya_pajak,
        //         'pengeluaran' => $total_pengeluaran,
        //         'total_usaha_jasa' => $total,
        //     ];
        // };

        return $data;
    }
    public static function cetak_dokumen_analisa_usaha_lain($kode)
    {
        $data = DB::table('au_lainnya')
            ->leftJoin('data_pengajuan', 'data_pengajuan.kode_pengajuan', '=', 'au_lainnya.pengajuan_kode')
            ->leftJoin('data_nasabah', 'data_nasabah.kode_nasabah', '=', 'data_pengajuan.nasabah_kode')
            ->where('au_lainnya.pengajuan_kode', $kode)->get();
        return $data;
    }
    public static function cetak_dokumen_analisa_keuangan($kode)
    {
        $data = DB::table('au_keuangan')
            ->leftjoin('data_kepemilikan', 'au_keuangan.pengajuan_kode', '=', 'data_kepemilikan.pengajuan_kode')
            ->select(
                'au_keuangan.*',
                'data_kepemilikan.*',
            )
            ->where('au_keuangan.pengajuan_kode', $kode)->get();
        return $data;
    }

    public static function cetak_dokumen_analisa_usaha_total($kode)
    {
        $perdagangan = DB::table('au_perdagangan')->where('pengajuan_kode', $kode)->get();
        if (count($perdagangan) != 0) {
            $tperdagangan = [];
            foreach ($perdagangan as $item) {
                $tperdagangan[] = $item->laba_bersih;
            }
            $tp = array_sum($tperdagangan) ?? 0;
        }

        $pertanian = DB::table('au_pertanian')->where('pengajuan_kode', $kode)->get();
        if (count($pertanian) != 0) {
            $tpertanian = [];
            foreach ($pertanian as $item) {
                $tpertanian[] = $item->laba_perbulan;
            }
            $tpt = array_sum($tpertanian) ?? 0;
        }

        $jasa = DB::table('au_jasa')->where('pengajuan_kode', $kode)->get();
        if (count($jasa) != 0) {
            $tjasa = [];
            foreach ($jasa as $item) {
                $tjasa[] = $item->laba_bersih;
            }
            $tj = array_sum($tjasa) ?? 0;
        }

        $lainnya = DB::table('au_lainnya')->where('pengajuan_kode', $kode)->get();
        if (count($lainnya) != 0) {
            $tlainnya = [];
            foreach ($lainnya as $item) {
                $tlainnya[] = $item->laba_bersih;
            }
            $tln = array_sum($tlainnya) ?? 0;
        }

        $total_all = ($tp ?? 0) + ($tpt ?? 0) + ($tj ?? 0) + ($tln ?? 0);

        $data = (object)[
            'laba_bersih_perdagangan' => $tp ?? 0,
            'laba_bersih_pertanian' => $tpt ?? 0,
            'laba_bersih_jasa' => $tj ?? 0,
            'laba_bersih_lainnya' => $tln ?? 0,
            'total_laba_usaha' => $total_all ?? 0,
        ];
        return $data;
    }

    public static function cetak_dokumen_jaminan_analisa_keuangan($data)
    {
        $jaminan = DB::table('data_jaminan')
            ->leftjoin('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
            ->leftjoin('data_jenis_agunan', 'data_jaminan.jenis_agunan_kode', '=', 'data_jenis_agunan.kode')
            ->select(
                'data_jaminan.*' ?? null,
                'data_jaminan.catatan as catatan_jaminan' ?? null,
                'data_jenis_dokumen.jenis_dokumen as nama_jenis_dokumen' ?? null,
                'data_jenis_agunan.jenis_agunan' ?? null,
            )
            ->where('pengajuan_kode', $data)->get();

        return $jaminan;
    }

    public static function cetak_data_analisa5C_character($data)
    {
        try {
            $character = DB::table('a5c_character')->where('pengajuan_kode', $data)->first();
            $analisa5C = (object) Data::cetak_a5c_character($character);

            return $analisa5C;
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Analisa 5C CHARACTER Tidak ada.');
        }
    }

    public static function cetak_data_analisa5C_capacity($data)
    {
        try {
            $capacity = DB::table('a5c_capacity')->where('pengajuan_kode', $data)->first();
            $analisa5C = (object)Data::a5c_capacity($capacity);
            return $analisa5C;
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Analisa 5C CAPACITY Tidak ada.');
        }
    }

    public static function cetak_data_analisa5C_collateral($data)
    {
        try {
            $collateral = DB::table('a5c_collateral')->where('pengajuan_kode', $data)->first();

            if (!is_null($collateral)) {
                $analisa5C = (object)Data::a5c_collateral($collateral);
            } else {
                $analisa5C = (object)[
                    'agunan_utama' => null,
                    'agunan_tambahan' => null,
                    'legalitas_agunan' => null,
                    'legalitas_agunan_tambahan' => null,
                    'mudah_diuangkan' => null,
                    'stabilitas_harga' => null,
                    'lokasi_shm' => null,
                    'kondisi_kendaraan' => null,
                    'aspek_hukum' => null,
                    'taksasi_agunan' => null,
                    'evaluasi_collateral' => null,
                ];
            }
            return $analisa5C;
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Analisa 5C COLLATERAL Tidak ada.');
        }
    }

    public static function cetak_data_analisa5C_condition($data)
    {
        try {
            $condition = DB::table('a5c_condition')->where('pengajuan_kode', $data)->first();
            if ($condition->persaingan_usaha == 1) {
                $persaingan_usaha = 'Persaingan Usaha Ketat';
            } elseif ($condition->persaingan_usaha == 2) {
                $persaingan_usaha = 'Persaingan Usaha Kurang Ketat';
            } elseif ($condition->persaingan_usaha == 3) {
                $persaingan_usaha = 'Persaingan Usaha Tidak Ketat';
            }

            if ($condition->kondisi_alam == 1) {
                $kondisi_alam = 'Resiko Sangat Tinggi';
            } elseif ($condition->kondisi_alam == 2) {
                $kondisi_alam = 'Resiko Tinggi';
            } elseif ($condition->kondisi_alam == 3) {
                $kondisi_alam = 'Resiko Sedang';
            } elseif ($condition->kondisi_alam == 4) {
                $kondisi_alam = 'Resiko Rendah';
            } elseif ($condition->kondisi_alam == 5) {
                $kondisi_alam = 'Resiko Sangat Rendah';
            }

            if ($condition->regulasi_pemerintah == 1) {
                $regulasi_pemerintah = 'TIDAK MENDUKUNG';
            } elseif ($condition->regulasi_pemerintah == 2) {
                $regulasi_pemerintah = 'KURANG MENDUKUNG';
            } elseif ($condition->regulasi_pemerintah == 3) {
                $regulasi_pemerintah = 'MENDUKUNG';
            } elseif ($condition->regulasi_pemerintah == 4) {
                $regulasi_pemerintah = 'MENDUKUNG';
            }

            $hasil = (object) [
                'persaingan_usaha' => $persaingan_usaha,
                'kondisi_alam' => $kondisi_alam,
                'regulasi_pemerintah' => $regulasi_pemerintah,
                'evaluasi_condition' => Data::analisa5c_number($condition->evaluasi_condition),
            ];

            return $hasil;
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data Analisa 5C CONDITION Tidak ada.');
        }
    }

    public static function cetak_data_kualitatif($data)
    {
        $kualitatif = DB::table('au_kualitatif')->where('pengajuan_kode', $data)->first();
        if (!is_null($kualitatif)) {

            if ($kualitatif->bi_checking == 1) {
                $kualitatif->bi_checking = 'Macet';
            } elseif ($kualitatif->bi_checking == 2) {
                $kualitatif->bi_checking = 'Diragukan';
            } elseif ($kualitatif->bi_checking == 3) {
                $kualitatif->bi_checking = 'Kurang Lancar';
            } elseif ($kualitatif->bi_checking == 4) {
                $kualitatif->bi_checking = 'Lancar';
            }

            if ($kualitatif->pihak_berwajib == 1) {
                $kualitatif->pihak_berwajib = 'Tidak Pernah';
            } elseif ($kualitatif->pihak_berwajib == 2) {
                $kualitatif->pihak_berwajib = 'Pernah';
            }
        } else {
            $kualitatif = (object)[
                'bi_checking' => null,
                'kewajiban1' => null,
                'ket_kewajiban1' => null,
                'kewajiban2' => null,
                'ket_kewajiban2' => null,
                'kewajiban3' => null,
                'ket_kewajiban3' => null,
                'pihak_berwajib' => null,
                'hubungan_tetangga' => null,
                'pengalaman_tki' => null,
                'ket_pengalaman' => null,
                'pemohon_ada' => null,
                'pemohon_ada' => null,
                'pendamping_ada' => null,
                'info_masyarakat' => null,
                'bahan_baku' => null,
                'proses_olah' => null,
                'target_market' => null,
                'pembayaran' => null,
                'pendukung_usaha' => null,
                'pengurang_usaha' => null,
                'trade_checking' => null,
            ];
        }
        return $kualitatif;
    }

    public static function cetak_data_memorandum($data)
    {
        $memorandum = DB::table('a_memorandum')
            ->leftJoin('data_pengajuan', 'a_memorandum.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftJoin('bi_penggunaan_debitur', 'bi_penggunaan_debitur.sandi', '=', 'a_memorandum.bi_penggunaan_kode')
            ->leftJoin('data_nasabah', 'data_pengajuan.nasabah_kode', '=', 'data_nasabah.kode_nasabah')
            ->leftJoin('data_survei', 'data_pengajuan.kode_pengajuan', '=', 'data_survei.pengajuan_kode')
            ->leftJoin('v_users', 'data_survei.surveyor_kode', '=', 'v_users.code_user')
            ->leftJoin('a5c_capacity', 'data_pengajuan.kode_pengajuan', '=', 'a5c_capacity.pengajuan_kode')
            ->leftJoin('a5c_character', 'data_pengajuan.kode_pengajuan', '=', 'a5c_character.pengajuan_kode')
            ->leftJoin('a5c_collateral', 'data_pengajuan.kode_pengajuan', '=', 'a5c_collateral.pengajuan_kode')
            ->leftJoin('a5c_condition', 'data_pengajuan.kode_pengajuan', '=', 'a5c_condition.pengajuan_kode')
            ->leftJoin('data_produk', 'data_pengajuan.produk_kode', '=', 'data_produk.kode_produk')
            ->leftJoin('bi_sektor_ekonomi', 'a_memorandum.bi_sek_ekonomi_kode', '=', 'bi_sektor_ekonomi.sandi')
            ->leftJoin('bi_sumber_dana_pelunasan', 'a_memorandum.bi_sumber_pelunasan_kode', '=', 'bi_sumber_dana_pelunasan.sandi')
            ->select(
                'a_memorandum.*',
                'data_pengajuan.*',
                'bi_penggunaan_debitur.keterangan as penggunaan_debitur',
                'data_pengajuan.jangka_waktu as jk',
                'data_nasabah.*',
                'a5c_capacity.*',
                'a_memorandum.*',
                'data_produk.nama_produk',
                'bi_sumber_dana_pelunasan.keterangan as sumber_dana_pelunasan',
                'bi_sektor_ekonomi.keterangan as ket_sektor_ekonomi',
                'a5c_character.nilai_karakter',
                'a5c_collateral.evaluasi_collateral as nilai_collateral',
                'a5c_collateral.lokasi_shm',
                'a5c_condition.evaluasi_condition as nilai_condition',
                'v_users.nama_user as nama_surveyor',
            )
            ->where('a_memorandum.pengajuan_kode', $data)->first();
        //

        if (is_null($memorandum)) {
            return null;
        }
        $usulan = DB::table('data_usulan')->where('pengajuan_kode', $data)->get();

        if (count($usulan) != 0) {
            $dasu = [];
            for ($i = 0; $i < count($usulan); $i++) {
                $dasu[] = $usulan[$i]->usulan_plafon;
            }
            $memorandum->plafon_usulan = end($dasu);
        } else {
            $memorandum = (object) ['plafon_usulan' => 0];
        }



        $memorandum->capital_evaluasi_capital = Data::analisa5c_number($memorandum->capital_evaluasi_capital) ?? null;
        $memorandum->evaluasi_capacity = Data::analisa5c_number($memorandum->evaluasi_capacity) ?? null;
        $memorandum->nilai_karakter = Data::analisa5c_number($memorandum->nilai_karakter) ?? null;
        $memorandum->nilai_collateral = Data::analisa5c_number($memorandum->nilai_collateral) ?? null;
        $memorandum->nilai_condition = Data::analisa5c_number($memorandum->nilai_condition) ?? null;
        $memorandum->pengalaman_usaha = Data::a5c_capacity_pengalaman_usaha($memorandum->pengalaman_usaha) ?? null;
        $memorandum->pertumbuhan_usaha = Data::a5c_capacity_pertumbuhan_usaha($memorandum->pertumbuhan_usaha) ?? null;
        $memorandum->catatan_kredit = Data::a5c_capacity_catatan_kredit($memorandum->catatan_kredit) ?? null;
        $memorandum->lokasi_shm = Data::a5c_capacity_lokasi_shm($memorandum->lokasi_shm) ?? null;

        //Hari
        $hari = Carbon::today();
        $memorandum->hari = $hari->isoformat('D MMMM Y');

        return $memorandum;
    }

    public static function cetak_data_swot($data)
    {
        $swot = DB::table('a_swot')->where('pengajuan_kode', $data)->first();
        if (!is_null($swot)) {
            return $swot;
        } else {
            $swot = (object) [
                'kekuatan' => null,
                'kelemahan' => null,
                'peluang' => null,
                'ancaman' => null,
            ];
        }
        return $swot;
    }

    public static function notifikasi_general($data)
    {
        $cek = DB::table('data_jaminan')
            ->join('data_pengajuan', 'data_jaminan.pengajuan_kode', '=', 'data_pengajuan.kode_pengajuan')
            ->leftjoin('data_jenis_agunan', 'data_jenis_agunan.kode', '=', 'data_jaminan.jenis_agunan_kode')
            ->leftjoin('data_jenis_dokumen', 'data_jaminan.jenis_dokumen_kode', '=', 'data_jenis_dokumen.kode')
            ->select(
                'data_jaminan.*',
                'data_pengajuan.*',
                'data_jenis_agunan.jenis_agunan',
                'data_jenis_dokumen.jenis_dokumen as nama_jenis_dokumen' ?? null,
            )
            ->where('data_pengajuan.kode_pengajuan', $data)
            ->get();

        //
        return $cek;
    }

    public static function cek_jaminan($data)
    {
        $cek = DB::table('data_jaminan')
            ->where('data_jaminan.pengajuan_kode', $data)
            ->groupBy('data_jaminan.jenis_jaminan')
            ->get();
        //

        if (count($cek) != 0) {
            $jaminan = $cek;
        } else {
            $jaminan = collect([
                (object)['jenis_jaminan' => null],
                (object)['jenis_jaminan' => null],
                (object)['jenis_jaminan' => null],
            ]);
        }
        //
        return $jaminan;
    }

    public static function get_qrcode($data, $text, $user)
    {

        $carbon = Carbon::now();
        $tgl = $carbon->format('d-m-y');

        // Path untuk menyimpan QR Code
        $strpath = storage_path('app/public/image/qr_code');
        $imgname = $text . '_' . $data . '_' . $user . '_' . $tgl . '.png';
        $imgpath = $strpath . '/' . $imgname;

        $data_url = $text . '_' . $data . '_' . $user;

        // URL dan QR Code dari Google Chart API
        $url = 'http://sipebri.bprbangunarta.co.id/verifikasi?qrcode=' . $data_url;
        // $uri = urlencode($url);
        // $chartUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . $uri;
        // Simpan QR Code dari Google Chart API
        // file_put_contents($imgpath, file_get_contents($chartUrl));

        $logoPath = public_path('assets/img/favicon2.png');
        QrCode::size(300)
            ->format('png')
            ->errorCorrection('H')
            ->merge($logoPath, 0.3, true)
            ->generate($url, $imgpath);

        // QrCode::size(300)
        //     ->style('dot')
        //     ->eye('circle')
        //     ->format('png')
        //     ->errorCorrection('H')
        //     ->merge($logoPath, 0.3, true)
        //     ->generate($url, $imgpath);

        $simpan = [
            'pengajuan_kode' => $data,
            'keterangan' => $imgname,
            'user' => $user,
            'created_at' => now(),
        ];

        if ($simpan) {
            $cek = DB::table('log_persetujuan')
                ->select('log_persetujuan.*')
                ->where('pengajuan_kode', $data)
                ->where('keterangan', $imgname)
                ->where('user', $user)->get();
            //
            if ($cek->isEmpty()) {
                DB::table('log_persetujuan')->insert($simpan);
            } else {
                DB::table('log_persetujuan')
                    ->where('pengajuan_kode', $data)
                    ->where('keterangan', $imgname)
                    ->where('user', $user)
                    ->update($simpan);
            }
        }

        return $imgname;
    }

    public static function get_qrcode_denah($data, $text, $user, $lat, $long)
    {
        // $url = "https://www.google.com/maps/place/' . $lat . ',' . $long . '/@' . $lat . ',' . $long . ',18z";
        $url = "https://www.google.com/maps/place/{$lat},{$long}/@{$lat},{$long},18z";

        // $url = "https://www.google.com/maps/place/";

        $logoPath = public_path('assets/img/favicon2.png');
        $qrCode = QrCode::size(300)
            ->format('png')
            ->errorCorrection('H')
            ->merge($logoPath, 0.3, true)
            ->generate(strval($url));

        $qrCodeBase64 = base64_encode($qrCode);

        return $qrCodeBase64;
    }

    public static function data_kantor($data)
    {
        if ($data == 'KANTOR PUSAT PAMANUKAN') {
            return 'PMK';
        } elseif ($data == 'KANTOR KAS PUSAKAJAYA') {
            return 'PSK';
        } elseif ($data == 'KANTOR KAS SUKAMANDI') {
            return 'SKM';
        } elseif ($data == 'KANTOR KAS KALIJATI') {
            return 'KJT';
        } elseif ($data == 'KANTOR KAS SUBANG') {
            return 'SBG';
        } elseif ($data == 'KANTOR KAS JALANCAGAK') {
            return 'CGK';
        } elseif ($data == 'KANTOR KAS PAGADEN') {
            return 'PGD';
        }
    }

    public static function data_produk($data)
    {
        if ($data == 'KREDIT UMUM') {
            return 'KRU';
        } elseif ($data == 'KREDIT PEGAWAI') {
            return 'KUP';
        } elseif ($data == 'KREDIT MOTOR') {
            return 'KRM';
        } elseif ($data == 'KREDIT REKENING KORAN') {
            return 'PRK';
        } elseif ($data == 'KREDIT TAKE OVER') {
            return 'KTO';
        } elseif ($data == 'KREDIT BUDIDAYA PERTANIAN') {
            return 'KBT';
        } elseif ($data == 'KREDIT PEGAWAI SWASTA') {
            return 'KPS';
        } elseif ($data == 'KREDIT KENDARAAN OPERASIONAL') {
            return 'KKO';
        } elseif ($data == 'KREDIT IBADAH HAJI') {
            return 'KIH';
        } elseif ($data == 'KREDIT PEGAWAI SWASTA NON MOU') {
            return 'KPJ';
        } elseif ($data == 'KREDIT RESEPSI') {
            return 'KRS';
        } elseif ($data == 'KREDIT PEGAWAI NEGERI') {
            return 'KPN';
        } elseif ($data == 'KREDIT IBADAH UMROH') {
            return 'KIU';
        } elseif ($data == 'KREDIT TANPA AGUNAN') {
            return 'KTA';
        } elseif ($data == 'KREDIT PEKERJA MIGRAN INDONESIA') {
            return 'KPM';
        }
    }
}
