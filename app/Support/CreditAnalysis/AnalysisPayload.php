<?php

namespace App\Support\CreditAnalysis;

use App\Models\AnalysisAdministration;
use App\Models\AnalysisBusiness;
use App\Models\AnalysisBusinessItem;
use App\Models\AnalysisCollateral;
use App\Models\AnalysisFinance;
use App\Models\AnalysisFinanceItem;
use App\Models\AnalysisFiveC;
use App\Models\AnalysisMemorandum;
use App\Models\AnalysisQualitative;
use App\Models\Collateral;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;

/**
 * What the analysis pages receive: every section of the worksheet as plain arrays, with the figures worked out by the
 * models. A section that was never saved reads as an empty one, so the pages need no special case.
 */
final class AnalysisPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function build(LoanApplication $loan, ?LoanAnalysis $analysis): array
    {
        $analysis ??= new LoanAnalysis(['loan_application_id' => $loan->id]);
        $finance = self::finance($analysis);

        return [
            'businesses' => $analysis->exists ? $analysis->businesses->map(fn (AnalysisBusiness $b): array => self::businessRow($b))->values()->all() : [],
            'finance' => $finance,
            'fiveC' => self::fiveC($analysis),
            'qualitative' => self::qualitative($analysis),
            'collaterals' => self::collaterals($loan, $analysis),
            'memorandum' => self::memorandum($loan, $analysis, $finance['metrics']['monthly_balance']),
            'administration' => self::administration($analysis),
            'submission' => [
                'gaps' => $analysis->gaps(),
                'submitted_at' => $analysis->submitted_at?->isoFormat('D MMM YYYY HH:mm'),
                'submitted_by' => $analysis->submitter?->name,
            ],
            'options' => [
                'assets' => AnalysisFinance::ASSETS,
                'qualitativeChoices' => AnalysisQualitative::CHOICES,
                'collateralKinds' => AnalysisCollateral::KINDS,
                'bindings' => AnalysisMemorandum::BINDINGS,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function businessRow(AnalysisBusiness $business): array
    {
        return [
            'id' => $business->id,
            'type' => $business->type,
            'code' => $business->code,
            'name' => $business->name,
            'revenue' => $business->revenue,
            'expense' => $business->expense,
            'net_profit' => $business->net_profit,
            'monthly_income' => $business->monthly_income,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function business(AnalysisBusiness $business): array
    {
        return [
            ...$business->only([
                'id', 'type', 'code', 'name', 'business_length', 'address',
                'daily_purchase', 'cost_of_goods', ...AnalysisBusiness::TRADE_COSTS,
                'economy_sector', 'plant_type', 'area_own', 'area_rent', 'area_pawn',
                'harvest_quintals', 'price_per_quintal', ...AnalysisBusiness::FARM_COSTS,
                'addition_result', 'other_bank_loan', 'principal_installment',
                'service_income', 'vehicle_tax', 'other_expense', 'business_kind', 'projection_addition',
                'revenue', 'expense', 'net_profit', 'monthly_income',
            ]),
            'updated_at' => $business->updated_at?->isoFormat('D MMM YYYY HH:mm'),
            'items' => $business->items->map(fn (AnalysisBusinessItem $i): array => [
                'id' => $i->id, 'group' => $i->group, 'name' => $i->name, 'qty' => $i->qty, 'price' => $i->price, 'sell_price' => $i->sell_price,
            ])->values()->all(),
            'metrics' => $business->metrics(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function finance(LoanAnalysis $analysis): array
    {
        $finance = $analysis->finance ?? new AnalysisFinance(['loan_analysis_id' => $analysis->id]);
        $finance->loadMissing('items');

        return [
            ...collect([...AnalysisFinance::HOUSEHOLD, ...array_keys(AnalysisFinance::ASSETS)])->mapWithKeys(fn (string $c): array => [$c => $finance->getAttribute($c) ?? (in_array($c, AnalysisFinance::HOUSEHOLD, true) ? 0 : null)])->all(),
            'obligations' => $finance->items->where('group', 'obligation')->map(fn (AnalysisFinanceItem $i): array => ['name' => $i->name, 'amount' => $i->amount])->values()->all(),
            'assets' => $finance->items->where('group', 'asset')->map(fn (AnalysisFinanceItem $i): array => ['name' => $i->name])->values()->all(),
            'metrics' => $finance->metrics(),
            'updated_at' => $finance->updated_at?->isoFormat('D MMM YYYY HH:mm'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function fiveC(LoanAnalysis $analysis): array
    {
        $five = $analysis->fiveC ?? new AnalysisFiveC;
        $columns = collect(AnalysisFiveC::ASPECTS)->flatMap(fn (array $a): array => array_keys($a));

        return [
            ...$columns->mapWithKeys(fn (string $c): array => [$c => $five->getAttribute($c)])->all(),
            'metrics' => $five->metrics(),
            'updated_at' => $five->updated_at?->isoFormat('D MMM YYYY HH:mm'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function qualitative(LoanAnalysis $analysis): array
    {
        $record = $analysis->qualitative ?? new AnalysisQualitative;

        return [
            ...collect(AnalysisQualitative::columns())->mapWithKeys(fn (string $c): array => [$c => $record->getAttribute($c)])->all(),
            'updated_at' => $record->updated_at?->isoFormat('D MMM YYYY HH:mm'),
        ];
    }

    /**
     * One row per collateral of the file: what was checked, or the start of a check taken from the collateral itself.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function collaterals(LoanApplication $loan, LoanAnalysis $analysis): array
    {
        $checks = $analysis->exists ? $analysis->collateralChecks->keyBy('collateral_id') : collect();

        return $loan->collaterals->map(function (Collateral $collateral) use ($checks): array {
            $check = $checks->get($collateral->id);

            return [
                'collateral_id' => $collateral->id,
                'label' => $collateral->description,
                'document_number' => $collateral->document_number,
                'owner_name' => $collateral->owner_name,
                'cbs_appraisal' => $collateral->appraisal_value,
                'kind' => $check->kind ?? 'other',
                'brand' => $check->brand ?? '',
                'vehicle_type' => $check->vehicle_type ?? '',
                'year' => $check->year ?? '',
                'chassis_number' => $check->chassis_number ?? '',
                'engine_number' => $check->engine_number ?? '',
                'plate_number' => $check->plate_number ?? '',
                'color' => $check->color ?? '',
                'land_area' => $check->land_area ?? 0,
                'location' => $check->location ?? '',
                'market_value' => $check->market_value ?? 0,
                'appraisal_value' => $check->appraisal_value ?? $collateral->appraisal_value,
                'notes' => $check->notes ?? '',
            ];
        })->values()->all();
    }

    /**
     * @return array<string, mixed>
     */
    private static function memorandum(LoanApplication $loan, LoanAnalysis $analysis, int $monthlyBalance): array
    {
        $record = $analysis->memorandum ?? new AnalysisMemorandum;
        $text = [...array_map(fn (string $c): string => "{$c}_note", AnalysisMemorandum::NEEDS), 'before_disbursement', 'additional_terms', 'binding'];
        $numbers = [...AnalysisMemorandum::NEEDS, ...AnalysisMemorandum::RATES, 'proposed_amount', 'term_months'];

        return [
            ...collect($numbers)->mapWithKeys(fn (string $c): array => [$c => $record->getAttribute($c) ?? 0])->all(),
            ...collect($text)->mapWithKeys(fn (string $c): array => [$c => $record->getAttribute($c)])->all(),
            'requested_amount' => $loan->requested_amount,
            'requested_tenor' => $loan->requested_tenor,
            'appraisal_total' => (int) $loan->collaterals->sum('appraisal_value'),
            'monthly_balance' => $monthlyBalance,
            'updated_at' => $record->updated_at?->isoFormat('D MMM YYYY HH:mm'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function administration(LoanAnalysis $analysis): array
    {
        $record = $analysis->administration ?? new AnalysisAdministration;

        return [
            ...collect(AnalysisAdministration::FEES)->mapWithKeys(fn (string $c): array => [$c => (int) $record->getAttribute($c)])->all(),
            'total' => $record->total(),
            'updated_at' => $record->updated_at?->isoFormat('D MMM YYYY HH:mm'),
        ];
    }
}
