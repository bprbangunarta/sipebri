<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survei extends Model
{
    use HasFactory;
    protected $table = 'data_survei';
    protected $fillable = [
        'pengajuan_kode',
        'kasi_kode',
        'surveyor_kode',        
        'input_user',      
        'otorisasi',  
        'is_entry',        
    ];

    public function pengajuan(){
        return $this->belongsTo(Survei::class);
    }
}
