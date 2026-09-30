<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Parameters from the board decree (SK Direksi) that limit what a loan of this product may request.
 * Amounts are in whole rupiah; rates are percentages of the loan amount.
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $min_amount
 * @property int|null $max_amount
 * @property int|null $min_tenor
 * @property int|null $max_tenor
 * @property string|null $interest_rate
 * @property string|null $provision_rate
 * @property string|null $admin_rate
 * @property string|null $rc_threshold
 * @property int|null $default_method_id
 * @property int|null $default_installment_id
 * @property list<int>|null $allowed_method_ids
 * @property list<int>|null $allowed_installment_ids
 * @property bool $collateral_required
 * @property string|null $decree
 * @property string|null $note
 */
#[Fillable([
    'product_id', 'min_amount', 'max_amount', 'min_tenor', 'max_tenor', 'interest_rate', 'provision_rate', 'admin_rate',
    'rc_threshold', 'default_method_id', 'default_installment_id', 'allowed_method_ids', 'allowed_installment_ids',
    'collateral_required', 'decree', 'note',
])]
class ProductParameter extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return [
            'allowed_method_ids' => 'array',
            'allowed_installment_ids' => 'array',
            'collateral_required' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
