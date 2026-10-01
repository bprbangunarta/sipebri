<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $loan_analysis_id
 * @property int $working_capital
 * @property string|null $working_capital_note
 * @property int $investment
 * @property string|null $investment_note
 * @property int $consumption
 * @property string|null $consumption_note
 * @property int $loan_settlement
 * @property string|null $loan_settlement_note
 * @property int $take_over
 * @property string|null $take_over_note
 * @property int $proposed_amount
 * @property int $term_months
 * @property float $admin_rate
 * @property float $interest_rate
 * @property float $provision_rate
 * @property float $penalty_rate
 * @property string|null $before_disbursement
 * @property string|null $additional_terms
 * @property string|null $binding
 */
#[Fillable(['loan_analysis_id', 'working_capital', 'working_capital_note', 'investment', 'investment_note', 'consumption', 'consumption_note', 'loan_settlement', 'loan_settlement_note', 'take_over', 'take_over_note', 'proposed_amount', 'term_months', 'admin_rate', 'interest_rate', 'provision_rate', 'penalty_rate', 'before_disbursement', 'additional_terms', 'binding'])]
class AnalysisMemorandum extends Model
{
    use Auditable;

    protected $table = 'analysis_memorandums';

    protected function casts(): array
    {
        return ['working_capital' => 'integer', 'investment' => 'integer', 'consumption' => 'integer', 'loan_settlement' => 'integer', 'take_over' => 'integer', 'proposed_amount' => 'integer', 'term_months' => 'integer', 'admin_rate' => 'float', 'interest_rate' => 'float', 'provision_rate' => 'float', 'penalty_rate' => 'float'];
    }

    public const NEEDS = ['working_capital', 'investment', 'consumption', 'loan_settlement', 'take_over'];

    public const RATES = ['admin_rate', 'interest_rate', 'provision_rate', 'penalty_rate'];

    public const BINDINGS = ['NOTARIIL', 'BAWAH TANGAN', 'FIDUCIA', 'APHT', 'TANPA PENGIKATAN'];

    public function auditLabel(): string
    {
        return 'Memorandum #'.$this->loan_analysis_id;
    }

    /** The funds needed, as the sum of the five purposes. */
    public function totalNeed(): int
    {
        return (int) collect(self::NEEDS)->sum(fn (string $column): int => (int) $this->getAttribute($column));
    }
}
