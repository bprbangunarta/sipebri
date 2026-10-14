<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

/**
 * Kebutuhan dana pada memorandum. Memakai tabel alur lama (satu baris per pengajuan).
 */
class DataKebutuhanDana extends Model
{
    protected $table   = 'a_kebutuhan_dana';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(DataPengajuan::class, 'pengajuan_kode', 'kode_pengajuan');
    }
}
