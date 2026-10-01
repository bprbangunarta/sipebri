<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $product_id
 * @property string|null $condition
 * @property string $mechanism
 * @property bool $is_active
 * @property bool $is_default the one path that holds the default authority levels (never matched to a loan)
 * @property bool $follows_default the tiers are kept in step with the default authority levels
 * @property string|null $note
 * @property-read Product|null $product
 */
#[Fillable(['product_id', 'condition', 'mechanism', 'is_active', 'is_default', 'follows_default', 'note'])]
class CommitteePath extends Model
{
    use Auditable;

    public const MECHANISMS = [
        'plafon' => 'By amount limit',
        'hierarki' => 'Committee hierarchy',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_default' => 'boolean', 'follows_default' => 'boolean'];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return HasMany<CommitteeTier, $this>
     */
    public function tiers(): HasMany
    {
        return $this->hasMany(CommitteeTier::class)->orderBy('sort')->orderBy('id');
    }

    /** Title such as "KRU · Reloan" or "All products · Normal". */
    public function title(): string
    {
        if ($this->is_default) {
            return 'Default authority levels';
        }

        return ($this->product_id === null ? 'All products' : $this->product->alias).' · '.$this->conditionLabel();
    }

    /** Conditions are stored UPPERCASE and shown capitalised. */
    public function conditionLabel(): string
    {
        return $this->condition ? str($this->condition)->lower()->title()->value() : 'Normal';
    }

    /**
     * @return HasMany<LoanApplication, $this>
     */
    public function loanApplications(): HasMany
    {
        return $this->hasMany(LoanApplication::class);
    }
}
