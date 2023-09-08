<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'data_produk';
    protected $guarded = ['id'];
    protected $fillable = [
            'kode_produk',
            'nama_produk',
            'rate',
            'jumlah_pengajuan',
        ];

    public function getRouteKeyName()
    {
        return 'produk';
    }
}
