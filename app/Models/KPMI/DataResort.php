<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataResort extends Model
{
    protected $table   = 'data_resort';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->hasMany(DataPengajuan::class, 'resort_kode', 'kode_resort');
    }
}
