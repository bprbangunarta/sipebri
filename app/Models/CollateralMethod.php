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
class CollateralMethod extends Model
{
    use Auditable;

    /**
     * Collaterals keep the code of their valuation class in `ppap_code`.
     *
     * @return HasMany<Collateral, $this>
     */
    public function collaterals(): HasMany
    {
        return $this->hasMany(Collateral::class, 'ppap_code', 'code');
    }
}
