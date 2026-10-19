<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Collaterals point at a region by its code, which many villages share (no foreign key).
     *
     * @return HasMany<Collateral, $this>
     */
    public function collaterals(): HasMany
    {
        return $this->hasMany(Collateral::class, 'region_code', 'code');
    }
}
