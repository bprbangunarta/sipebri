<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $loan_analysis_id
 * @property int $cost_staple
 * @property int $cost_education
 * @property int $cost_children
 * @property int $cost_cigarette
 * @property int $cost_health
 * @property int $cost_gatel
 * @property int $cost_social
 * @property string|null $asset_house
 * @property string|null $asset_car
 * @property string|null $asset_motorcycle
 * @property string|null $asset_computer
 * @property string|null $asset_washer
 * @property string|null $asset_tv
 * @property string|null $asset_chair
 * @property string|null $asset_cabinet
 * @property-read LoanAnalysis $loanAnalysis
 * @property-read Collection<int, AnalysisFinanceItem> $items
 */
#[Fillable(['loan_analysis_id', 'cost_staple', 'cost_education', 'cost_children', 'cost_cigarette', 'cost_health', 'cost_gatel', 'cost_social', 'asset_house', 'asset_car', 'asset_motorcycle', 'asset_computer', 'asset_washer', 'asset_tv', 'asset_chair', 'asset_cabinet'])]
class AnalysisFinance extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['cost_staple' => 'integer', 'cost_education' => 'integer', 'cost_children' => 'integer', 'cost_cigarette' => 'integer', 'cost_health' => 'integer', 'cost_gatel' => 'integer', 'cost_social' => 'integer'];
    }

    public const HOUSEHOLD = ['cost_staple', 'cost_education', 'cost_children', 'cost_cigarette', 'cost_health', 'cost_gatel', 'cost_social'];

    public const HOUSE_OPTIONS = ['PERMANEN', 'SEDERHANA', 'SEMI PERMANEN'];

    public const UNIT_OPTIONS = ['TIDAK ADA', '1 UNIT', '2 UNIT', '3 UNIT', '4 UNIT', '5 UNIT'];

    public const EXIST_OPTIONS = ['ADA', 'TIDAK ADA'];

    public const TV_OPTIONS = ['LCD', 'LED', 'CRT FLAT', 'CRT CEMBUNG', 'TIDAK ADA'];

    /** Each owned item with the choices it can have. */
    public const ASSETS = [
        'asset_house' => self::HOUSE_OPTIONS,
        'asset_car' => self::UNIT_OPTIONS,
        'asset_motorcycle' => self::UNIT_OPTIONS,
        'asset_computer' => self::EXIST_OPTIONS,
        'asset_washer' => self::EXIST_OPTIONS,
        'asset_tv' => self::TV_OPTIONS,
        'asset_chair' => self::EXIST_OPTIONS,
        'asset_cabinet' => self::EXIST_OPTIONS,
    ];

    /**
     * @return BelongsTo<LoanAnalysis, $this>
     */
    public function loanAnalysis(): BelongsTo
    {
        return $this->belongsTo(LoanAnalysis::class);
    }

    /**
     * @return HasMany<AnalysisFinanceItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(AnalysisFinanceItem::class)->orderBy('sort')->orderBy('id');
    }

    public function auditLabel(): string
    {
        return 'Analisa keuangan #'.$this->loan_analysis_id;
    }

    /**
     * Income of the businesses by type, the household and obligation costs, and what is left each month.
     *
     * @return array<string, int>
     */
    public function metrics(): array
    {
        $byType = AnalysisBusiness::query()
            ->where('loan_analysis_id', $this->loan_analysis_id)
            ->selectRaw('type, SUM(monthly_income) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $trade = (int) ($byType['trade'] ?? 0);
        $farm = (int) ($byType['farm'] ?? 0);
        $service = (int) ($byType['service'] ?? 0);
        $other = (int) ($byType['other'] ?? 0);

        $household = (int) collect(self::HOUSEHOLD)->sum(fn (string $column): int => (int) $this->getAttribute($column));
        $obligation = (int) $this->items->where('group', 'obligation')->sum('amount');
        $income = $trade + $farm + $service + $other;

        return [
            'trade_income' => $trade,
            'farm_income' => $farm,
            'service_income' => $service,
            'other_income' => $other,
            'business_income' => $income,
            'household_cost' => $household,
            'obligation_cost' => $obligation,
            'monthly_balance' => $income - $household - $obligation,
        ];
    }
}
