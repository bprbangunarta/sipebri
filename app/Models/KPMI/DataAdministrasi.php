<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

/**
 * Biaya administrasi kredit. Memakai tabel administrasi pada alur lama (satu baris per pengajuan),
 * karena persetujuan Komite sampai pencairan masih membacanya.
 */
class DataAdministrasi extends Model
{
    protected $table   = 'a_administrasi';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(DataPengajuan::class, 'pengajuan_kode', 'kode_pengajuan');
    }
}
