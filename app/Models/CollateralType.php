<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 */
#[Fillable(['code', 'name'])]
class CollateralType extends Model
{
    use Auditable;

    /**
     * Records that refer to this one by its code (there is no database foreign key on a code).
     *
     * @return HasMany<Collateral, $this>
     */
    public function collaterals(): HasMany
    {
        return $this->hasMany(Collateral::class, 'collateral_type_code', 'code');
    }

    /**
     * Records that refer to this one by its code (there is no database foreign key on a code).
     *
     * @return HasMany<OwnershipStatus, $this>
     */
    public function ownershipStatuses(): HasMany
    {
        return $this->hasMany(OwnershipStatus::class, 'collateral_type_code', 'code');
    }
}
