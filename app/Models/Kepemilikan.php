<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kepemilikan extends Model
{
    use HasFactory;
    protected $table = 'data_kepemilikan';
    protected $guarded = ['id'];
    protected $fillable = [
        'kode_kepemilikan',
        'pengajuan_kode',
        'rumah',
        'mobil',
        'motor',
        'televisi',
        'komputer',
        'mesin_cuci',
        'kursi_tamu',
        'lemari_panjang',
        'nama_lainnya1',
        'isi_lainnya1',
        'nama_lainnya2',
        'isi_lainnya2',
    ];

    public static function kodeacak($name, $length)
    {
        for ($i = 1; $i <= pow(10, $length) - 1; $i++) {
            $acak = $name . str_pad($i, $length, '0', STR_PAD_LEFT);

            // Cek apakah kode sudah ada dalam database
            if (!self::where('kode_kepemilikan', $acak)->exists()) {
                return $acak;
            }
        }

        return null; // Jika tidak ada kode yang unik ditemukan
    }
}
