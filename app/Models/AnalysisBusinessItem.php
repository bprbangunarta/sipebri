<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $analysis_business_id
 * @property string $group
 * @property string $name
 * @property float $qty
 * @property int $price
 * @property int $sell_price
 * @property int $sort
 * @property-read AnalysisBusiness $business
 */
#[Fillable(['analysis_business_id', 'group', 'name', 'qty', 'price', 'sell_price', 'sort'])]
class AnalysisBusinessItem extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['qty' => 'float', 'price' => 'integer', 'sell_price' => 'integer', 'sort' => 'integer'];
    }

    /**
     * @return BelongsTo<AnalysisBusiness, $this>
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(AnalysisBusiness::class, 'analysis_business_id');
    }

    public function auditLabel(): string
    {
        return "{$this->group}: {$this->name}";
    }
}
