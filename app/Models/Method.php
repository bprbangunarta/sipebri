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
 */
#[Fillable(['code', 'name'])]
class Method extends Model implements CountsExtraUsage
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
        return $this->hasMany(ProductParameter::class, 'default_method_id');
    }

    /**
     * @return HasMany<LoanApproval, $this>
     */
    public function loanApprovals(): HasMany
    {
        return $this->hasMany(LoanApproval::class);
    }

    public function extraUsage(): array
    {
        return ['pilihan metode produk' => ProductParameter::query()->whereJsonContains('allowed_method_ids', $this->id)->count()];
    }
}
