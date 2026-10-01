<?php

namespace App\Support\CreditAnalysis;

use App\Models\AnalysisAdministration;
use App\Models\AnalysisBusiness;
use App\Models\AnalysisCollateral;
use App\Models\AnalysisFinance;
use App\Models\AnalysisFiveC;
use App\Models\AnalysisMemorandum;
use App\Models\LoanAnalysis;

/**
 * The figures the committee decided on: the proposal of the memorandum, what the businesses earn, the household's costs,
 * the 5C evaluation, the values of the collateral and the fees. While an approved file is open for a correction none of
 * them may change; a change to any of them needs the committee to decide again.
 */
final class BaseFigures
{
    /**
     * @return array<string, int|float|string|null>
     */
    public static function snapshot(LoanAnalysis $analysis): array
    {
        $analysis = LoanAnalysis::query()->with(['memorandum', 'finance.items', 'fiveC', 'administration', 'collateralChecks', 'businesses'])->findOrFail($analysis->id);
        $memorandum = $analysis->memorandum ?? new AnalysisMemorandum;
        $finance = ($analysis->finance ?? new AnalysisFinance(['loan_analysis_id' => $analysis->id]))->metrics();
        $five = ($analysis->fiveC ?? new AnalysisFiveC)->metrics();

        $figures = [
            'Usulan plafon' => $memorandum->proposed_amount,
            'Jangka waktu' => $memorandum->term_months,
            'Biaya admin (%)' => $memorandum->admin_rate,
            'Suku bunga (%)' => $memorandum->interest_rate,
            'Biaya provisi (%)' => $memorandum->provision_rate,
            'Biaya penalti (%)' => $memorandum->penalty_rate,
            'Kebutuhan dana' => $memorandum->totalNeed(),
            'Total biaya administrasi' => ($analysis->administration ?? new AnalysisAdministration)->total(),
            'Pendapatan usaha perdagangan' => $finance['trade_income'],
            'Pendapatan usaha pertanian' => $finance['farm_income'],
            'Pendapatan usaha jasa' => $finance['service_income'],
            'Pendapatan usaha lainnya' => $finance['other_income'],
            'Biaya rumah tangga' => $finance['household_cost'],
            'Kewajiban lainnya' => $finance['obligation_cost'],
            'Keuangan perbulan' => $finance['monthly_balance'],
            'Nilai 5C' => $five['percent'],
            'Predikat 5C' => $five['grade'],
        ];

        foreach ($five['groups'] as $group => $values) {
            $figures['5C '.ucfirst($group)] = $values['score'];
        }

        /** @var AnalysisCollateral $check */
        foreach ($analysis->collateralChecks->sortBy('collateral_id') as $check) {
            $figures["Agunan #{$check->collateral_id}: nilai pasar"] = $check->market_value;
            $figures["Agunan #{$check->collateral_id}: nilai taksasi"] = $check->appraisal_value;
        }

        // A business without any figure changes nothing, so adding or removing an empty one is not a change.
        /** @var AnalysisBusiness $business */
        foreach ($analysis->businesses as $business) {
            $values = [$business->revenue, $business->expense, $business->net_profit, $business->monthly_income];

            if (array_filter($values) !== []) {
                $figures["Usaha {$business->code}"] = implode('/', $values);
            }
        }

        return $figures;
    }

    /**
     * The names of the figures that differ.
     *
     * @param  array<string, int|float|string|null>  $before
     * @param  array<string, int|float|string|null>  $after
     * @return list<string>
     */
    public static function changes(array $before, array $after): array
    {
        $changed = [];

        foreach (array_keys($before + $after) as $name) {
            if (($before[$name] ?? null) != ($after[$name] ?? null)) { // loose: 1.5 and "1.50" are the same figure
                $changed[] = $name;
            }
        }

        return $changed;
    }
}
