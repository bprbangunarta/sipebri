<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Rancangan satu tabel (alat developer, bukan data operasional). */
class SchemaDraft extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'with_id' => 'boolean',
        'with_timestamps' => 'boolean',
        'with_soft_deletes' => 'boolean',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(SchemaDraftColumn::class)->orderBy('sort');
    }
}
