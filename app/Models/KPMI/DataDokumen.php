<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataDokumen extends Model
{
    protected $table   = 'data_analisas_kpmi_dokumen';
    protected $guarded = ['id'];

    protected $casts = [
        'ukuran' => 'integer',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(DataPengajuan::class, 'pengajuan_kode', 'kode_pengajuan');
    }
}
