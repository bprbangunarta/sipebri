<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataNasabah extends Model
{
    protected $table   = 'data_nasabah';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->hasMany(DataPengajuan::class, 'nasabah_kode', 'kode_nasabah');
    }
}
