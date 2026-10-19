<?php

namespace App\Models;

use App\Audit\Auditable;
use App\Support\CountsExtraUsage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $period_months
 */
#[Fillable(['code', 'name', 'period_months'])]
class Installment extends Model implements CountsExtraUsage
{
    use Auditable;

    /**
     * @return HasMany<LoanApplication, $this>
     */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    /**
     * @return HasMany<ProductParameter, $this>
     */
    public function defaultForProducts(): HasMany
    {
        return $this->hasMany(ProductParameter::class, 'default_installment_id');
    }

    public function extraUsage(): array
    {
        return ['pilihan angsuran produk' => ProductParameter::query()->whereJsonContains('allowed_installment_ids', $this->id)->count()];
    }
}
