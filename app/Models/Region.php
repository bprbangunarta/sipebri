<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $regency
 * @property string $district
 * @property string $village
 * @property string|null $postal_code
 */
#[Fillable(['code', 'regency', 'district', 'village', 'postal_code'])]
class Region extends Model
{
    use Auditable;
}
