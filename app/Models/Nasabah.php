<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;
    protected $table = 'data_nasabah';
    protected $fillable = [
            'kode_nasabah',
            'no_cif',
            'identitas',
            'no_identitas',
            'masa_identitas',
            'nama_nasabah',
            'nama_panggilan',
            'tempat_lahir',
            'tanggal_lahir',
            'kode_dati',
            'kecamatan',
            'kelurahan',
            'kota',
            'alamat_ktp',
            'alamat_sekarang',
            'agama',
            'jenis_kelamin',
            'kewarganegaraan',
            'pendidikan_kode',
            'status_pernikahan',
            'perkerjaan_kode',
            'nama_ibu_kandung',
            'no_rekening',
            'no_npwp',
            'no_telp',
            'email',
            'sumber_dana',
            'penghasilan_utama',
            'penghasilan_lainnya',
            'tempat_kerja',
            'no_telp_kantor',
            'no_karyawan',
            'input_user',
            'is_entry',
            'photo',
            'photo_selfie',
            'photo_ktp',
            'photo_kk',
        ];
    public function pengajuan(){
        return $this->hasMany(Pengajuan::class);
    }

    public function getRouteKeyName()
    {
        return 'nasabah';
    }
}
