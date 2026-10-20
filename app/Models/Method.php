<?php

namespace App\Models;

use App\Audit\Auditable;
use App\Support\ChoiceUsage;
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

    public static function extraUsageFor(array $ids): array
    {
        return array_map(fn (int $count): array => ['pilihan metode produk' => $count], ChoiceUsage::count('allowed_method_ids', $ids));
    }
}
