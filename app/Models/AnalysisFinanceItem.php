<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $analysis_finance_id
 * @property string $group
 * @property string $name
 * @property int $amount
 * @property int $sort
 */
#[Fillable(['analysis_finance_id', 'group', 'name', 'amount', 'sort'])]
class AnalysisFinanceItem extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['amount' => 'integer', 'sort' => 'integer'];
    }

    public function auditLabel(): string
    {
        return "{$this->group}: {$this->name}";
    }
}
