<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnershipStatus extends Model
{
    protected $fillable = ['collateral_type_code', 'code', 'name'];
}
