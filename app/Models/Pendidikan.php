<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    use HasFactory;
    protected $table = 'data_pendidikan';
    protected $fillable = [
            'kode_pendidikan',
            'nama_pendidikan',
        ];

    public function getRouteKeyName()
    {
        return 'pendidikan';
    }
}
