<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Lembar analisa satu berkas: Analisa Keuangan (biaya rumah tangga + kewajiban)
 * dan Analisa Kepemilikan (harta). Pendapatan usaha diambil otomatis dari
 * hasil bersih tiap usaha pada Analisa Usaha.
 */
class AnalysisSheet extends Model
{
    use TracksAuthor;

    /** Pos biaya rumah tangga per bulan. */
    public const HOUSEHOLD = [
        'cost_staple', 'cost_education', 'cost_children', 'cost_cigarette',
        'cost_health', 'cost_gatel', 'cost_social',
    ];

    public const HOUSE_OPTIONS = ['PERMANEN', 'SEDERHANA', 'SEMI PERMANEN'];

    public const UNIT_OPTIONS = ['TIDAK ADA', '1 UNIT', '2 UNIT', '3 UNIT', '4 UNIT', '5 UNIT'];

    public const EXIST_OPTIONS = ['ADA', 'TIDAK ADA'];

    public const TV_OPTIONS = ['LCD', 'LED', 'CRT FLAT', 'CRT CEMBUNG', 'TIDAK ADA'];

    /** Kolom harta → daftar opsinya. */
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

    protected $guarded = ['id'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AnalysisSheetItem::class)->orderBy('sort')->orderBy('id');
    }

    /** Kemampuan keuangan per bulan. Pendapatan usaha = hasil bersih Analisa Usaha. */
    public function metrics(): array
    {
        $net = AnalysisBusiness::query()
            ->where('loan_application_id', $this->loan_application_id)
            ->selectRaw('type, SUM(monthly_income) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $trade = (int) ($net['PERDAGANGAN'] ?? 0);
        $farm = (int) ($net['PERTANIAN'] ?? 0);
        $service = (int) ($net['JASA'] ?? 0);
        $other = (int) ($net['LAINNYA'] ?? 0);

        $household = collect(self::HOUSEHOLD)->sum(fn ($c) => (int) $this->{$c});
        $obligation = (int) $this->items->where('group', 'OBLIGATION')->sum('amount');
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
