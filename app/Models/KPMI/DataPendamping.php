<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataPendamping extends Model
{
    protected $table   = 'data_pendamping';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->hasMany(DataPengajuan::class, 'kode_pengajuan', 'pengajuan_kode');
    }
}
