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
 * @property string $type
 * @property string $code
 * @property string $name
 * @property string|null $business_length
 * @property string|null $address
 * @property int $daily_purchase
 * @property int $cost_of_goods
 * @property int $transport_cost
 * @property int $employee_cost
 * @property int $retribution_cost
 * @property int $unload_cost
 * @property int $gatel_cost
 * @property int $rent_cost
 * @property string|null $economy_sector
 * @property string|null $plant_type
 * @property float $harvest_quintals
 * @property int $cost_land
 * @property int $cost_seed
 * @property int $cost_fertilizer
 * @property int $cost_pesticide
 * @property int $cost_labor
 * @property int $cost_irrigation
 * @property int $cost_harvest
 * @property int $cost_sharecropper
 * @property int $cost_tax
 * @property int $cost_village
 * @property int $cost_amortization
 * @property int $cost_other_bank
 * @property int $area_own
 * @property int $area_rent
 * @property int $area_pawn
 * @property int $price_per_quintal
 * @property int $addition_result
 * @property int $other_bank_loan
 * @property int $principal_installment
 * @property int $service_income
 * @property int $vehicle_tax
 * @property int $other_expense
 * @property string|null $business_kind
 * @property int $projection_addition
 * @property int $revenue
 * @property int $expense
 * @property int $net_profit
 * @property int $monthly_income
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read LoanAnalysis $loanAnalysis
 * @property-read Collection<int, AnalysisBusinessItem> $items
 */
#[Fillable(['loan_analysis_id', 'type', 'code', 'name', 'business_length', 'address', 'daily_purchase', 'cost_of_goods', 'transport_cost', 'employee_cost', 'retribution_cost', 'unload_cost', 'gatel_cost', 'rent_cost', 'economy_sector', 'plant_type', 'harvest_quintals', 'cost_land', 'cost_seed', 'cost_fertilizer', 'cost_pesticide', 'cost_labor', 'cost_irrigation', 'cost_harvest', 'cost_sharecropper', 'cost_tax', 'cost_village', 'cost_amortization', 'cost_other_bank', 'area_own', 'area_rent', 'area_pawn', 'price_per_quintal', 'addition_result', 'other_bank_loan', 'principal_installment', 'service_income', 'vehicle_tax', 'other_expense', 'business_kind', 'projection_addition', 'created_by', 'updated_by'])]
class AnalysisBusiness extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['harvest_quintals' => 'float', 'daily_purchase' => 'integer', 'cost_of_goods' => 'integer', 'transport_cost' => 'integer', 'employee_cost' => 'integer', 'retribution_cost' => 'integer', 'unload_cost' => 'integer', 'gatel_cost' => 'integer', 'rent_cost' => 'integer', 'area_own' => 'integer', 'area_rent' => 'integer', 'area_pawn' => 'integer', 'price_per_quintal' => 'integer', 'cost_land' => 'integer', 'cost_seed' => 'integer', 'cost_fertilizer' => 'integer', 'cost_pesticide' => 'integer', 'cost_labor' => 'integer', 'cost_irrigation' => 'integer', 'cost_harvest' => 'integer', 'cost_sharecropper' => 'integer', 'cost_tax' => 'integer', 'cost_village' => 'integer', 'cost_amortization' => 'integer', 'cost_other_bank' => 'integer', 'addition_result' => 'integer', 'other_bank_loan' => 'integer', 'principal_installment' => 'integer', 'service_income' => 'integer', 'vehicle_tax' => 'integer', 'other_expense' => 'integer', 'projection_addition' => 'integer', 'revenue' => 'integer', 'expense' => 'integer', 'net_profit' => 'integer', 'monthly_income' => 'integer'];
    }

    /** Business types, with the prefix of their code (AUPG00001, AUP00001, ...). */
    public const TYPES = ['trade', 'farm', 'service', 'other'];

    public const PREFIX = ['trade' => 'AUPG', 'farm' => 'AUP', 'service' => 'AUJ', 'other' => 'AUL'];

    public const LABELS = ['trade' => 'Usaha Perdagangan', 'farm' => 'Usaha Pertanian', 'service' => 'Usaha Jasa', 'other' => 'Usaha Lainnya'];

    /** Months of one harvest, used when the installment system says nothing about the period. */
    public const HARVEST_MONTHS = 6;

    public const LENGTHS = ['1 TAHUN', '2 TAHUN', '3 TAHUN', '4 TAHUN', '>5 TAHUN'];

    public const SECTORS = ['PERTANIAN', 'PERKEBUNAN'];

    public const PLANTS = ['PADI KETAN', 'PADI INPARI', 'PADI CIHERANG', 'PADI 42', 'PADI IR64', 'PADI MUNCUL', 'PADI PANDAN WANGI', 'LAINNYA'];

    public const KINDS = ['MAKANAN', 'MINUMAN', 'KELONTONG', 'KERAJINAN', 'PETERNAKAN', 'PERIKANAN', 'BENGKEL', 'KONVEKSI', 'LAINNYA'];

    public const GROUPS = ['goods', 'material', 'income', 'expense'];

    public const TRADE_COSTS = ['transport_cost', 'employee_cost', 'retribution_cost', 'unload_cost', 'gatel_cost', 'rent_cost'];

    public const FARM_COSTS = ['cost_land', 'cost_seed', 'cost_fertilizer', 'cost_pesticide', 'cost_labor', 'cost_irrigation', 'cost_harvest', 'cost_sharecropper', 'cost_tax', 'cost_village', 'cost_amortization', 'cost_other_bank'];

    /**
     * @return BelongsTo<LoanAnalysis, $this>
     */
    public function loanAnalysis(): BelongsTo
    {
        return $this->belongsTo(LoanAnalysis::class);
    }

    /**
     * @return HasMany<AnalysisBusinessItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(AnalysisBusinessItem::class)->orderBy('sort')->orderBy('id');
    }

    /** The next free code for a type, e.g. AUPG00012. */
    public static function nextCode(string $type): string
    {
        $prefix = self::PREFIX[$type];

        $last = self::query()
            ->where('code', 'like', $prefix.'%')
            ->orderByRaw('LENGTH(code) desc')
            ->orderBy('code', 'desc')
            ->value('code');

        return $prefix.str_pad((string) ((int) substr((string) $last, strlen($prefix)) + 1), 5, '0', STR_PAD_LEFT);
    }

    public function auditLabel(): string
    {
        return "{$this->code} {$this->name}";
    }

    /**
     * The worked-out figures of this business. The formulas are those of the credit analysis worksheet that was in use
     * before this system; the figures of both must agree for the same file.
     *
     * @return array<string, int|float>
     */
    public function metrics(): array
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return match ($this->type) {
            'trade' => $this->tradeMetrics($items),
            'farm' => $this->farmMetrics(),
            'service' => $this->serviceMetrics(),
            default => $this->otherMetrics($items),
        };
    }

    /** Store the results of `metrics()` on the row. */
    public function recalculate(): void
    {
        $m = $this->metrics();

        $this->revenue = max(0, (int) $m['revenue']);
        $this->expense = max(0, (int) $m['expense']);
        $this->net_profit = (int) $m['net_profit'];
        $this->monthly_income = (int) ($m['monthly_income'] ?? $m['net_profit']);
    }

    /**
     * @param  Collection<int, AnalysisBusinessItem>  $items
     * @return array<string, int|float>
     */
    private function tradeMetrics(Collection $items): array
    {
        $goods = $items->where('group', 'goods');
        $totalBuy = (int) $goods->sum('price');
        $totalSell = (int) $goods->sum('sell_price');
        $totalProfit = $totalSell - $totalBuy;
        $margin = $totalBuy > 0 ? round($totalProfit / $totalBuy * 100, 2) : 0.0;

        $dailyRevenue = (int) round($this->daily_purchase * (1 + $margin / 100));
        $dailyProfit = $dailyRevenue - $this->cost_of_goods;
        $dailyCost = (int) collect(self::TRADE_COSTS)->sum(fn (string $column): int => (int) $this->getAttribute($column));

        $monthlyProfit = $dailyProfit * 30;
        $monthlyCost = $dailyCost * 30;

        return [
            'total_buy' => $totalBuy,
            'total_sell' => $totalSell,
            'total_profit' => $totalProfit,
            'total_stock' => (float) $goods->sum('qty'),
            'margin_percent' => $margin,
            'daily_revenue' => $dailyRevenue,
            'daily_profit' => $dailyProfit,
            'daily_cost' => $dailyCost,
            'monthly_profit' => $monthlyProfit,
            'monthly_cost' => $monthlyCost,
            'revenue' => $monthlyProfit,
            'expense' => $monthlyCost,
            'net_profit' => $monthlyProfit - $monthlyCost + $this->projection_addition,
        ];
    }

    /**
     * @return array<string, int|float>
     */
    private function farmMetrics(): array
    {
        $harvestIncome = (int) round($this->harvest_quintals * $this->price_per_quintal);
        $totalCost = (int) collect(self::FARM_COSTS)->sum(fn (string $column): int => (int) $this->getAttribute($column));
        $net = $harvestIncome - $totalCost;

        $application = $this->loanAnalysis->loanApplication;
        $tenor = $application->requested_tenor;
        $period = $this->installmentPeriod($tenor);
        $terms = $period > 0 ? $tenor / $period : 0;

        $principal = $terms > 0 ? (int) round($application->requested_amount / $terms) : 0;
        $afterPrincipal = $net - $principal;
        $monthly = $period > 0 ? (int) floor($afterPrincipal / $period) + $this->addition_result : 0;

        return [
            'total_area' => $this->area_own + $this->area_rent + $this->area_pawn,
            'harvest_income' => $harvestIncome,
            'total_cost' => $totalCost,
            'installment_period' => $period,
            'principal_installment' => $principal,
            'after_principal' => $afterPrincipal,
            'other_bank_loan' => $this->cost_other_bank,
            'monthly_income' => $monthly,
            'revenue' => $harvestIncome,
            'expense' => $totalCost,
            'net_profit' => $net,
        ];
    }

    /** Months between two installments: the system's period, a harvest when it has none, the whole tenor for a bullet loan. */
    private function installmentPeriod(int $tenor): int
    {
        $period = $this->loanAnalysis->loanApplication->installment?->period_months;

        if ($period === null) {
            return self::HARVEST_MONTHS;
        }

        return $period > 0 ? $period : $tenor;
    }

    /**
     * @return array<string, int|float>
     */
    private function serviceMetrics(): array
    {
        $expense = $this->vehicle_tax + $this->other_expense;

        return [
            'total_income' => $this->service_income,
            'total_expense' => $expense,
            'revenue' => $this->service_income,
            'expense' => $expense,
            'net_profit' => $this->service_income - $expense,
        ];
    }

    /**
     * @param  Collection<int, AnalysisBusinessItem>  $items
     * @return array<string, int|float>
     */
    private function otherMetrics(Collection $items): array
    {
        $income = (int) round($items->where('group', 'income')->sum(fn (AnalysisBusinessItem $i): int => $i->price));
        $operational = (int) round($items->where('group', 'expense')->sum(fn (AnalysisBusinessItem $i): int => $i->price));
        $material = (int) round($items->where('group', 'material')->sum(fn (AnalysisBusinessItem $i): float => $i->qty * $i->price));

        return [
            'business_income' => $income,
            'operational_cost' => $operational,
            'material_cost' => $material,
            'revenue' => $income,
            'expense' => $operational + $material,
            'net_profit' => $income - $operational - $material + $this->projection_addition,
        ];
    }
}
