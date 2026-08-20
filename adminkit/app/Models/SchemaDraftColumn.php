<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemaDraftColumn extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'sort' => 'integer',
        'is_nullable' => 'boolean',
        'is_unique' => 'boolean',
        'is_index' => 'boolean',
    ];
}
