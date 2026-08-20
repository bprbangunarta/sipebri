<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductParameter extends Model
{
    protected $fillable = [
        'product_id', 'min_amount', 'max_amount', 'min_tenor', 'max_tenor',
        'interest_rate', 'provision_rate', 'admin_rate', 'rc_threshold',
        'default_method_id', 'default_installment_id',
        'allowed_method_ids', 'allowed_installment_ids',
        'collateral_required', 'decree', 'note',
    ];

    protected $casts = [
        'allowed_method_ids' => 'array',
        'allowed_installment_ids' => 'array',
        'collateral_required' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
