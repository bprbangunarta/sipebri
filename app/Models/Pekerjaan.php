<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    use HasFactory;
    protected $table = 'data_pekerjaan';
    protected $fillable = [
        'kode_pekerjaan',
        'nama_pekerjaan',
    ];

    public function getRouteKeyName()
    {
        return 'job';
    }
}
