<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $collateral_type_code
 * @property string $code
 * @property string $name
 */
#[Fillable(['collateral_type_code', 'code', 'name'])]
class OwnershipStatus extends Model
{
    use Auditable;
}
