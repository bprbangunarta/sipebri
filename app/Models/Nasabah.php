<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;
    protected $table = 'data_nasabah';
    protected $fillable = [
            'identitas',
            'no_identitas',
            'nama_nasabah',
            'tanggal_lahir',
            'kode_nasabah',
        ];
    public function pengajuan(){
        return $this->hasMany(Pengajuan::class);
    }
}
