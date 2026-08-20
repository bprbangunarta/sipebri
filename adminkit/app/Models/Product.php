<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = ['code', 'alias', 'name', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Parameter SK Direksi (batas plafon, tenor, bunga, ambang RC, dll). */
    public function parameter(): HasOne
    {
        return $this->hasOne(ProductParameter::class);
    }
}
