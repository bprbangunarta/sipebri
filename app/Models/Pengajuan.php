<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;
    protected $table = 'data_pengajuan';

    protected $fillable = [
        'kode_pengajuan',
        'nasabah_kode',
        'plafon',
        'jangka_waktu',
    ];
    protected $guarded = ['id'];
}
