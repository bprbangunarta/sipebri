<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataProduk extends Model
{
    protected $table   = 'data_produk';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->hasMany(DataPengajuan::class, 'produk_kode', 'kode_produk');
    }
}
