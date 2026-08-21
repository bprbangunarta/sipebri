<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Baris rincian usaha. Satu tabel untuk empat kegunaan (kolom `group`):
 * GOODS (barang dagang: price=harga beli, sell_price=harga jual, qty=stok),
 * MATERIAL (bahan baku: qty × price), INCOME & EXPENSE (rincian keuangan: price=nominal).
 */
class AnalysisBusinessItem extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = ['qty' => 'float'];

    public function business(): BelongsTo
    {
        return $this->belongsTo(AnalysisBusiness::class, 'analysis_business_id');
    }
}
