<?php

namespace App\Models\KPMI;

use Illuminate\Database\Eloquent\Model;

class DataPengajuan extends Model
{
    protected $table   = 'data_pengajuan';
    protected $guarded = ['id'];

    static function getPengajuan(string $kode_pengajuan)
    {
        return self::with('nasabah', 'produk', 'resort', 'pendamping')
            ->where('kode_pengajuan', $kode_pengajuan)
            ->first();
    }

    public function nasabah()
    {
        return $this->belongsTo(DataNasabah::class, 'nasabah_kode', 'kode_nasabah');
    }

    public function produk()
    {
        return $this->belongsTo(DataProduk::class, 'produk_kode', 'kode_produk');
    }

    public function resort()
    {
        return $this->belongsTo(DataResort::class, 'resort_kode', 'kode_resort');
    }

    public function analisa()
    {
        return $this->hasOne(DataAnalisa::class, 'pengajuan_kode', 'kode_pengajuan');
    }

    public function administrasi()
    {
        return $this->hasOne(DataAdministrasi::class, 'pengajuan_kode', 'kode_pengajuan');
    }

    public function dokumen()
    {
        return $this->hasMany(DataDokumen::class, 'pengajuan_kode', 'kode_pengajuan');
    }

    public function pendamping()
    {
        return $this->belongsTo(DataPendamping::class, 'kode_pengajuan', 'pengajuan_kode');
    }
}
