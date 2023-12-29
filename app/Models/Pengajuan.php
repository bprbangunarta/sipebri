<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;
    protected $table = 'data_pengajuan';

    protected $fillable = [
        'kode_pengajuan',
        'nasabah_kode',
        'plafon',
        'jangka_waktu',
        'tabungan_cgc',
        'input_user',
        'kategori',
        'otorisasi',
        'is_entry',
    ];
    protected $guarded = ['id'];

    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class);
    }

    public function survei()
    {
        return $this->hasMany(Survei::class);
    }

    public function pendamping()
    {
        return $this->belongsTo(Pendamping::class);
    }
}
