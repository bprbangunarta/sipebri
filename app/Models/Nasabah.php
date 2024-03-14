<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nasabah extends Model
{
    use HasFactory;
    protected $table = 'data_nasabah';
    protected $fillable = [
        'kode_nasabah',
        'no_cif',
        'identitas',
        'no_identitas',
        'masa_identitas',
        'nama_nasabah',
        'nama_panggilan',
        'tempat_lahir',
        'tanggal_lahir',
        'kode_dati',
        'kecamatan',
        'kelurahan',
        'kota',
        'alamat_ktp',
        'alamat_sekarang',
        'agama',
        'jenis_kelamin',
        'kewarganegaraan',
        'pendidikan_kode',
        'status_pernikahan',
        'perkerjaan_kode',
        'nama_ibu_kandung',
        'no_rekening',
        'no_npwp',
        'no_telp',
        'email',
        'sumber_dana',
        'penghasilan_utama',
        'penghasilan_lainnya',
        'tempat_kerja',
        'no_telp_kantor',
        'no_karyawan',
        'input_user',
        'is_entry',
        'photo',
        'photo_selfie',
        'photo_ktp',
        'photo_kk',
        'otorisasi',
    ];
    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class);
    }

    public function getRouteKeyName()
    {
        return 'nasabah';
    }

    public static function kode_nasabah($lasts)
    {
        if (is_null($lasts)) {
            $count = 339931;
        } else {

            $count = (int) $lasts->kode_pengajuan + 1;
            $lengths = 8;
            $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
            $isUsed = Pengajuan::where('kode_pengajuan', $kodes)->exists() ||
                Pendamping::where('pengajuan_kode', $kodes)->exists() ||
                Survei::where('pengajuan_kode', $kodes)->exists();

            while ($isUsed) {
                // Tambahkan 1 ke nilai $count untuk membuat kode baru
                $count += 1;
                $kodes = str_pad($count, $lengths, '0', STR_PAD_LEFT);
                // Periksa kembali apakah nilai yang baru sudah digunakan dalam tabel
                $isUsed = Pengajuan::where('kode_pengajuan', $kodes)->exists() ||
                    Pendamping::where('pengajuan_kode', $kodes)->exists() ||
                    Survei::where('pengajuan_kode', $kodes)->exists();
            }
        }
        return $kodes;
    }

    public static function nasabahkode()
    {
        do {
            $koderand = random_int(100000, 999999);
            $date = Carbon::now();
            $haskode = $date->format('my') . $koderand;

            // Cek apakah kode sudah ada di tabel
            $existingCode = self::where('kode_nasabah', $haskode)->exists();
        } while ($existingCode);

        // Di sini $haskode akan menjadi kode unik baru yang belum ada di tabel
        return $haskode;
    }
}
