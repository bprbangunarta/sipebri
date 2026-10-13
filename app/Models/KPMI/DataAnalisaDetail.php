<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataAnalisaDetail extends Model
{
    protected $table   = 'data_analisas_kpmi_detail_amaterasu';
    protected $guarded = ['id'];

    protected $casts = [
        'skor'  => 'float',
        'wajib' => 'boolean',
    ];

    public function analisa()
    {
        return $this->belongsTo(DataAnalisa::class, 'analisa_id');
    }
}
