<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataAnalisa extends Model
{
    protected $table   = 'data_analisas_kpmi_amaterasu';
    protected $guarded = ['id'];

    protected $casts = [
        'skor_lendana'     => 'float',
        'dsr'              => 'float',
        'skor_total'       => 'float',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(DataPengajuan::class, 'pengajuan_kode', 'kode_pengajuan');
    }

    public function details()
    {
        return $this->hasMany(DataAnalisaDetail::class, 'analisa_id');
    }
}
