<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $code
 * @property string|null $alias
 * @property string $name
 */
#[Fillable(['code', 'alias', 'name'])]
class Office extends Model
{
    use Auditable;

    /** Column sizes in `offices`; what Codex sends is checked against them before it is stored. */
    public const CODE_MAX = 8;

    public const ALIAS_MAX = 8;

    public const NAME_MAX = 100;

    /** "ALIAS : Name" for a picker; an office Codex sent without an alias that was free shows its name alone. */
    public function label(): string
    {
        return $this->alias !== null && $this->alias !== '' ? "{$this->alias} : {$this->name}" : $this->name;
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<LoanApplication, $this>
     */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }
}
