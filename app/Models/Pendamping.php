<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendamping extends Model
{
    use HasFactory;
    protected $table = 'data_pendamping';
    protected $fillable = [
        'pengajuan_kode',
        'identitas',
        'no_identitas',
        'masa_identitas',
        'nama_pendamping',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
        'tanggungan',
        'pisah_harta',
        'photo',
        'photo_selfie',
        'input_user',
        'otorisasi',
        'is_entry',
    ];

    public function pengajuan(){
        return $this->hasMany(Pengajuan::class);
    }
}
