<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $table = 'data_survei';
    protected $fillable = [
        'pengajuan_kode',
        'kantor_kode',
        'direksi_kode',
        'kabag_kode',
        'kasi_kode',
        'surveyor_kode',
        'kolektor_kode',
        'tgl_survei',
        'catatan_survei',
        'tgl_jadul_1',
        'catatan_jadul_1',
        'tgl_jadul_2',
        'catatan_jadul_2',
        'tgl_resurvei',
        'catatan_resurvei',
        'tgl_resurvei_1',
        'catatan_resurvei_1',
        'tgl_resurvei_2',
        'catatan_resurvei_2',
        'lokasi',
        'latitude',
        'longitude',
        'foto',
        'otorisasi',
        'input_user',
        'auth_user',
        'is_entry',
    ];
}
