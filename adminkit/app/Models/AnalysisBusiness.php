<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu unit usaha pemohon pada tahap Analisa Usaha.
 * Semua kolom hasil hitung TIDAK diinput: dihitung di `metrics()` dan
 * disimpan ringkasannya (revenue/expense/net_profit) lewat `recalculate()`.
 */
class AnalysisBusiness extends Model
{
    use TracksAuthor;

    /** Lama siklus setoran bawaan (bulan) bila sistem cicilan berkas tidak jelas. */
    public const HARVEST_MONTHS = 6;

    public const TYPES = ['PERDAGANGAN', 'PERTANIAN', 'JASA', 'LAINNYA'];

    public const PREFIX = [
        'PERDAGANGAN' => 'AUPG',
        'PERTANIAN' => 'AUP',
        'JASA' => 'AUJ',
        'LAINNYA' => 'AUL',
    ];

    public const LENGTHS = ['1 TAHUN', '2 TAHUN', '3 TAHUN', '4 TAHUN', '>5 TAHUN'];

    public const SECTORS = ['PERTANIAN', 'PERKEBUNAN'];

    public const PLANTS = [
        'PADI KETAN', 'PADI INPARI', 'PADI CIHERANG', 'PADI 42',
        'PADI IR64', 'PADI MUNCUL', 'PADI PANDAN WANGI', 'LAINNYA',
    ];

    public const KINDS = [
        'MAKANAN', 'MINUMAN', 'KELONTONG', 'KERAJINAN', 'PETERNAKAN',
        'PERIKANAN', 'BENGKEL', 'KONVEKSI', 'LAINNYA',
    ];

    public const GROUPS = ['GOODS', 'MATERIAL', 'INCOME', 'EXPENSE'];

    /** Pos biaya pertanian (dijumlahkan menjadi pengeluaran biaya usaha). */
    public const FARM_COSTS = [
        'cost_land', 'cost_seed', 'cost_fertilizer', 'cost_pesticide', 'cost_labor',
        'cost_irrigation', 'cost_harvest', 'cost_sharecropper', 'cost_tax',
        'cost_village', 'cost_amortization', 'cost_other_bank',
    ];

    /** Biaya harian usaha perdagangan (di luar pokok penjualan). */
    public const TRADE_COSTS = [
        'transport_cost', 'employee_cost', 'retribution_cost',
        'unload_cost', 'gatel_cost', 'rent_cost',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'harvest_kw' => 'float',
        'net_profit' => 'integer',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AnalysisBusinessItem::class)->orderBy('sort')->orderBy('id');
    }

    public static function nextCode(string $type): string
    {
        $prefix = self::PREFIX[$type];

        $last = self::query()
            ->where('code', 'like', $prefix.'%')
            ->orderByRaw('LENGTH(code) desc')
            ->orderBy('code', 'desc')
            ->value('code');

        $number = $last ? (int) substr($last, strlen($prefix)) : 0;

        return $prefix.str_pad((string) ($number + 1), 5, '0', STR_PAD_LEFT);
    }

    /** Seluruh angka hasil hitung — satu-satunya sumber rumus di backend. */
    public function metrics(): array
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return match ($this->type) {
            'PERDAGANGAN' => $this->tradeMetrics($items),
            'PERTANIAN' => $this->farmMetrics(),
            'JASA' => $this->serviceMetrics(),
            default => $this->otherMetrics($items),
        };
    }

    public function recalculate(): void
    {
        $m = $this->metrics();

        $this->revenue = max(0, $m['revenue']);
        $this->expense = max(0, $m['expense']);
        $this->net_profit = $m['net_profit'];
        // Kontribusi PER BULAN ke kemampuan keuangan: pertanian dibagi siklus panen.
        $this->monthly_income = $m['monthly_income'] ?? $m['net_profit'];
    }

    private function tradeMetrics($items): array
    {
        $goods = $items->where('group', 'GOODS');
        $totalBuy = (int) $goods->sum('price');
        $totalSell = (int) $goods->sum('sell_price');
        $totalProfit = $totalSell - $totalBuy;
        // Margin = laba dibagi HARGA BELI (markup) dan DIBULATKAN 2 desimal
        // dulu — omset harian memakai persentase yang tampil, sesuai sistem lama.
        $margin = $totalBuy > 0 ? round($totalProfit / $totalBuy * 100, 2) : 0.0;

        $dailyRevenue = (int) round($this->daily_purchase * (1 + $margin / 100));
        $dailyProfit = $dailyRevenue - (int) $this->cost_of_goods;
        $dailyCost = collect(self::TRADE_COSTS)->sum(fn ($c) => (int) $this->{$c});

        $monthlyProfit = $dailyProfit * 30;
        $monthlyCost = $dailyCost * 30;
        $net = $monthlyProfit - $monthlyCost + (int) $this->projection_addition;

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
            'net_profit' => $net,
        ];
    }

    /**
     * Pertanian. Pinjaman bank lain SUDAH termasuk pos biaya. Periode setoran
     * diambil dari sistem cicilan berkas (MUSIMAN = 6 bulan, BULANAN = 1,
     * NON ANGSURAN = sepanjang jangka waktu):
     * setoran pokok = plafon ÷ (jangka waktu ÷ periode),
     * pendapatan per bulan = floor((hasil bersih − setoran pokok) ÷ periode) + penambahan.
     */
    private function farmMetrics(): array
    {
        $harvestIncome = (int) round($this->harvest_kw * $this->price_per_kw);
        $totalCost = collect(self::FARM_COSTS)->sum(fn ($c) => (int) $this->{$c});
        $net = $harvestIncome - $totalCost;

        $application = $this->application;
        $tenor = (int) ($application?->requested_tenor ?? 0);
        $period = $this->installmentPeriod($tenor);
        $terms = $period > 0 ? $tenor / $period : 0;

        $principal = $terms > 0 ? (int) round((int) $application->requested_amount / $terms) : 0;
        $afterPrincipal = $net - $principal;
        $monthly = $period > 0
            ? (int) floor($afterPrincipal / $period) + (int) $this->addition_result
            : 0;

        return [
            'total_area' => (int) $this->area_own + (int) $this->area_rent + (int) $this->area_pawn,
            'harvest_income' => $harvestIncome,
            'total_cost' => $totalCost,
            'installment_period' => $period,
            'principal_installment' => $principal,
            'after_principal' => $afterPrincipal,
            'other_bank_loan' => (int) $this->cost_other_bank,
            'monthly_income' => $monthly,
            'revenue' => $harvestIncome,
            'expense' => $totalCost,
            'net_profit' => $net,
        ];
    }

    /** Kelipatan bulan setoran; 0 pada sistem cicilan non angsuran → sepanjang jangka waktu. */
    private function installmentPeriod(int $tenor): int
    {
        $period = $this->application?->installment?->period_months;

        if ($period === null) {
            return self::HARVEST_MONTHS;
        }

        return $period > 0 ? $period : $tenor;
    }

    private function serviceMetrics(): array
    {
        $income = (int) $this->service_income;
        $expense = (int) $this->vehicle_tax + (int) $this->other_expense;

        return [
            'total_income' => $income,
            'total_expense' => $expense,
            'revenue' => $income,
            'expense' => $expense,
            'net_profit' => $income - $expense,
        ];
    }

    private function otherMetrics($items): array
    {
        $sum = fn (string $group, callable $value) => (int) round($items->where('group', $group)->sum($value));

        $income = $sum('INCOME', fn ($i) => (int) $i->price);
        $operational = $sum('EXPENSE', fn ($i) => (int) $i->price);
        $material = $sum('MATERIAL', fn ($i) => (float) $i->qty * (int) $i->price);
        $net = $income - $operational - $material + (int) $this->projection_addition;

        return [
            'business_income' => $income,
            'operational_cost' => $operational,
            'material_cost' => $material,
            'revenue' => $income,
            'expense' => $operational + $material,
            'net_profit' => $net,
        ];
    }
}
