<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property string $code
 * @property string $alias
 * @property string $name
 * @property bool $is_active
 */
#[Fillable(['code', 'alias', 'name', 'is_active'])]
class Product extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    /**
     * @return HasOne<ProductParameter, $this>
     */
    public function parameter(): HasOne
    {
        return $this->hasOne(ProductParameter::class);
    }

    /**
     * @return HasMany<LoanApplication, $this>
     */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }

    /**
     * @return HasMany<CommitteePath, $this>
     */
    public function committeePaths(): HasMany
    {
        return $this->hasMany(CommitteePath::class);
    }
}
