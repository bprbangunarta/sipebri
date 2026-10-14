<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

/**
 * Usulan kredit dan sandi lapbul pada memorandum. Memakai tabel alur lama (satu baris per pengajuan).
 */
class DataMemorandum extends Model
{
    protected $table   = 'a_memorandum';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(DataPengajuan::class, 'pengajuan_kode', 'kode_pengajuan');
    }
}
