<?php

use App\Enums\LoanStatus;
use App\Models\AnalysisAdministration;
use App\Models\AnalysisBusiness;
use App\Models\AnalysisBusinessItem;
use App\Models\AnalysisFinance;
use App\Models\AnalysisFinanceItem;
use App\Models\AnalysisFiveC;
use App\Models\AnalysisMemorandum;
use App\Models\Installment;
use App\Models\LoanAnalysis;
use App\Models\LoanApplication;

/**
 * The figures of the worksheet must equal those of the analysis worksheet used before this system. The expected numbers
 * below are worked out by hand from that system's formulas.
 */
function analysedLoan(?int $periodMonths = 6): LoanAnalysis
{
    $installment = $periodMonths === null ? null : Installment::create(['code' => 'T'.$periodMonths, 'name' => "Every {$periodMonths}", 'period_months' => $periodMonths]);

    $loan = LoanApplication::create([
        'application_code' => LoanApplication::nextCode(), 'application_date' => now(), 'status' => LoanStatus::Survey,
        'nik' => '3201000000000001', 'full_name' => 'Siti', 'requested_amount' => 12_000_000, 'requested_tenor' => 12,
        'installment_id' => $installment?->id,
    ]);

    return LoanAnalysis::begin($loan);
}

function addBusiness(LoanAnalysis $analysis, string $type, array $attributes = [], array $items = []): AnalysisBusiness
{
    $business = AnalysisBusiness::create([
        'loan_analysis_id' => $analysis->id, 'type' => $type, 'code' => AnalysisBusiness::nextCode($type), 'name' => 'TEST', ...$attributes,
    ]);

    foreach ($items as $i => $item) {
        AnalysisBusinessItem::create(['analysis_business_id' => $business->id, 'sort' => $i, ...$item]);
    }

    $business->load('items');
    $business->recalculate();
    $business->save();

    return $business->fresh();
}

it('works out a trade business: margin of the goods, daily and monthly profit, projection', function () {
    $business = addBusiness(analysedLoan(), 'trade', [
        'daily_purchase' => 1_000_000, 'cost_of_goods' => 900_000,
        'transport_cost' => 20_000, 'employee_cost' => 30_000, 'rent_cost' => 10_000, 'projection_addition' => 500_000,
    ], [
        ['group' => 'goods', 'name' => 'A', 'qty' => 3, 'price' => 10_000, 'sell_price' => 12_000],
        ['group' => 'goods', 'name' => 'B', 'qty' => 2, 'price' => 5_000, 'sell_price' => 6_000],
    ]);

    // margin = (18.000 - 15.000) / 15.000 = 20%; daily revenue 1.200.000; daily profit 300.000; cost 60.000 a day
    expect($business->metrics())->toMatchArray(['margin_percent' => 20.0, 'daily_revenue' => 1_200_000, 'daily_profit' => 300_000, 'daily_cost' => 60_000, 'monthly_profit' => 9_000_000, 'monthly_cost' => 1_800_000, 'total_stock' => 5.0])
        ->and($business->net_profit)->toBe(7_700_000)
        ->and($business->monthly_income)->toBe(7_700_000)
        ->and($business->code)->toBe('AUPG00001');
});

it('works out a farm business with the installment period of the system', function () {
    $business = addBusiness(analysedLoan(6), 'farm', [
        'harvest_quintals' => 20, 'price_per_quintal' => 600_000, 'cost_seed' => 1_000_000, 'cost_fertilizer' => 2_000_000, 'cost_labor' => 1_500_000, 'addition_result' => 100_000,
    ]);

    // harvest 12.000.000 - costs 4.500.000 = 7.500.000; two installments of 6.000.000; (7.500.000 - 6.000.000) / 6 = 250.000, plus 100.000
    expect($business->metrics())->toMatchArray(['harvest_income' => 12_000_000, 'total_cost' => 4_500_000, 'installment_period' => 6, 'principal_installment' => 6_000_000, 'after_principal' => 1_500_000, 'monthly_income' => 350_000])
        ->and($business->net_profit)->toBe(7_500_000)
        ->and($business->monthly_income)->toBe(350_000)
        ->and($business->code)->toBe('AUP00001');
});

it('takes a harvest of six months when there is no installment system and the whole tenor for a bullet loan', function () {
    $noSystem = addBusiness(analysedLoan(null), 'farm', ['harvest_quintals' => 20, 'price_per_quintal' => 600_000, 'cost_seed' => 4_500_000]);
    expect($noSystem->metrics()['installment_period'])->toBe(6);

    $bullet = addBusiness(analysedLoan(0), 'farm', ['harvest_quintals' => 20, 'price_per_quintal' => 600_000, 'cost_seed' => 4_500_000]);
    // period = tenor 12; one installment of 12.000.000; (7.500.000 - 12.000.000) / 12 = -375.000
    expect($bullet->metrics())->toMatchArray(['installment_period' => 12, 'principal_installment' => 12_000_000, 'monthly_income' => -375_000]);
});

it('works out service and other businesses', function () {
    $analysis = analysedLoan();

    $service = addBusiness($analysis, 'service', ['service_income' => 3_000_000, 'vehicle_tax' => 200_000, 'other_expense' => 300_000]);
    expect($service->net_profit)->toBe(2_500_000)->and($service->revenue)->toBe(3_000_000)->and($service->expense)->toBe(500_000);

    $other = addBusiness($analysis, 'other', ['projection_addition' => 100_000], [
        ['group' => 'income', 'name' => 'Sales', 'price' => 2_000_000],
        ['group' => 'income', 'name' => 'Rent', 'price' => 1_500_000],
        ['group' => 'expense', 'name' => 'Power', 'price' => 500_000],
        ['group' => 'material', 'name' => 'Flour', 'qty' => 2.5, 'price' => 100_000],
    ]);
    // 3.500.000 - 500.000 - 250.000 + 100.000
    expect($other->net_profit)->toBe(2_850_000)->and($other->code)->toBe('AUL00001');
});

it('numbers business codes per type', function () {
    $analysis = analysedLoan();

    expect(addBusiness($analysis, 'trade')->code)->toBe('AUPG00001')
        ->and(addBusiness($analysis, 'trade')->code)->toBe('AUPG00002')
        ->and(addBusiness($analysis, 'service')->code)->toBe('AUJ00001');
});

it('adds the income of every business to the household balance', function () {
    $analysis = analysedLoan();
    addBusiness($analysis, 'trade', ['daily_purchase' => 1_000_000, 'cost_of_goods' => 900_000, 'transport_cost' => 20_000, 'employee_cost' => 30_000, 'rent_cost' => 10_000, 'projection_addition' => 500_000], [
        ['group' => 'goods', 'name' => 'A', 'price' => 10_000, 'sell_price' => 12_000],
        ['group' => 'goods', 'name' => 'B', 'price' => 5_000, 'sell_price' => 6_000],
    ]);
    addBusiness($analysis, 'service', ['service_income' => 3_000_000, 'vehicle_tax' => 200_000, 'other_expense' => 300_000]);

    $finance = AnalysisFinance::create(['loan_analysis_id' => $analysis->id, 'cost_staple' => 2_000_000, 'cost_education' => 500_000]);
    AnalysisFinanceItem::create(['analysis_finance_id' => $finance->id, 'group' => 'obligation', 'name' => 'Bank', 'amount' => 1_000_000]);
    AnalysisFinanceItem::create(['analysis_finance_id' => $finance->id, 'group' => 'asset', 'name' => 'Land', 'amount' => 0]);

    // 7.700.000 + 2.500.000 - 2.500.000 - 1.000.000
    expect($finance->fresh()->metrics())->toBe([
        'trade_income' => 7_700_000, 'farm_income' => 0, 'service_income' => 2_500_000, 'other_income' => 0,
        'business_income' => 10_200_000, 'household_cost' => 2_500_000, 'obligation_cost' => 1_000_000, 'monthly_balance' => 6_700_000,
    ]);
});

it('grades the 5C by the share of the highest possible score', function () {
    $analysis = analysedLoan();
    $five = AnalysisFiveC::create(['loan_analysis_id' => $analysis->id]);

    expect($five->metrics()['grade'])->toBeNull();

    $five->update([
        'lifestyle' => 3, 'emotional_control' => 3, 'disreputable_acts' => 3, 'family_harmony' => 3, 'consistency' => 3, 'compliance' => 3, 'social_relations' => 3, // 100%
        'capital_source' => 3, // 100%
        'natural_conditions' => 2, 'competition' => 1, 'regulations' => 2, // 5 of 12 = 41.67%
    ]);
    $m = $five->fresh()->metrics();

    // (100 + 100 + 41.67) / 3 = 80.56 -> good
    expect($m['groups']['character'])->toMatchArray(['score' => 21, 'max' => 21, 'percent' => 100.0, 'grade' => 'BAIK'])
        ->and($m['groups']['condition'])->toMatchArray(['score' => 5, 'max' => 12, 'percent' => 41.67, 'grade' => 'KURANG BAIK'])
        ->and($m['groups']['capacity']['grade'])->toBeNull()
        ->and($m['percent'])->toBe(80.56)->and($m['grade'])->toBe('BAIK');

    expect(AnalysisFiveC::grade(79.99))->toBe('CUKUP BAIK')->and(AnalysisFiveC::grade(60.0))->toBe('CUKUP BAIK')->and(AnalysisFiveC::grade(59.99))->toBe('KURANG BAIK');
});

it('adds up the funds needed and the administration fees', function () {
    $analysis = analysedLoan();

    $memorandum = AnalysisMemorandum::create(['loan_analysis_id' => $analysis->id, 'working_capital' => 5_000_000, 'investment' => 2_000_000, 'take_over' => 500_000]);
    $administration = AnalysisAdministration::create(['loan_analysis_id' => $analysis->id, 'administration' => 100_000, 'provision' => 200_000, 'fiducia_fee' => 50_000]);

    expect($memorandum->totalNeed())->toBe(7_500_000)->and($administration->total())->toBe(350_000);
});

it('lists what is missing before a file may go to the committee', function () {
    $analysis = analysedLoan();

    expect($analysis->gaps())->toBe(['Analisa Usaha (minimal satu usaha)', 'Analisa Keuangan (biaya rumah tangga)', 'Analisa 5C', 'Memorandum (usulan plafon)']);

    addBusiness($analysis, 'service', ['service_income' => 1]);
    AnalysisFinance::create(['loan_analysis_id' => $analysis->id, 'cost_staple' => 1]);
    AnalysisFiveC::create(['loan_analysis_id' => $analysis->id, 'capital_source' => 3]);
    AnalysisMemorandum::create(['loan_analysis_id' => $analysis->id, 'proposed_amount' => 1]);

    expect($analysis->fresh()->gaps())->toBe([]);
});

it('moves a surveyed file to the analysis stage when the analysis begins', function () {
    $analysis = analysedLoan();

    expect($analysis->loanApplication->fresh()->status)->toBe(LoanStatus::Analysis)->and($analysis->template)->toBe('general');
});
