<?php

use App\Support\Appraisal\Amounts;
use App\Support\Appraisal\AppraisalContext;
use App\Support\Appraisal\GeneralTemplate;
use App\Support\Appraisal\Haircut;
use App\Support\Appraisal\LandBuildingTemplate;
use App\Support\Appraisal\LoanToValue;

/** Answers of a land and building appraisal worked out by hand: see the comments for each figure. */
function landAnswers(array $overrides = []): array
{
    return [
        'comparables.land_per_m2.c1' => '2000000', 'comparables.land_per_m2.c2' => '2200000', 'comparables.land_per_m2.c3' => '1800000',
        'adjustments.time.c1' => '5', 'adjustments.location.c1' => '-3', 'adjustments.shape.c2' => '-10',
        'adjustments.selected' => '1950000', 'land_physical.area' => '100',
        'cost.physical' => '10', 'cost.functional' => '5', 'cost.external' => '5',
        'cost.main.area' => '80', 'cost.main.unit_cost' => '3000000',
        'cost.fence.area' => '20', 'cost.fence.unit_cost' => '500000', 'cost.fence.depreciation' => '50',
        'reconciliation.land.selected' => '195000000', 'reconciliation.building.selected' => '190000000',
        'reconciliation.facilities.selected' => '5000000', 'reconciliation.deductions.selected' => '10000000',
        'h:haircut_percent' => '20', 'ltv.execution_cost' => '10000000',
        ...$overrides,
    ];
}

it('works out the indicated land value of each comparable from its price and adjustments', function () {
    $cells = (new LandBuildingTemplate)->compute(landAnswers(), new AppraisalContext(200_000_000, null))->cells;

    // c1: 2.000.000 plus 5% minus 3% = 2.040.000; c2: 2.200.000 minus 10% = 1.980.000; c3 has no adjustment: 1.800.000
    expect($cells['adjustments.total.c1'])->toBe(2.0)
        ->and($cells['adjustments.indicative.c1'])->toBe(2_040_000)
        ->and($cells['adjustments.total.c2'])->toBe(-10.0)
        ->and($cells['adjustments.indicative.c2'])->toBe(1_980_000)
        ->and($cells['adjustments.total.c3'])->toBeNull()
        ->and($cells['adjustments.indicative.c3'])->toBe(1_800_000)
        ->and($cells['adjustments.range_min'])->toBe(1_800_000)
        ->and($cells['adjustments.range_max'])->toBe(2_040_000)
        ->and($cells['adjustments.suggested'])->toBe(1_940_000);
});

it('depreciates the buildings by the total of the three kinds unless a row has its own percentage', function () {
    $cells = (new LandBuildingTemplate)->compute(landAnswers(), new AppraisalContext(200_000_000, null))->cells;

    // 10% + 5% + 5% = 20%: 80 m² at 3.000.000 is 240.000.000 new and 192.000.000 now; the fence uses its own 50%: 10.000.000 new and 5.000.000 now
    expect($cells['cost.total'])->toBe(20.0)
        ->and($cells['cost.main.new_cost'])->toBe(240_000_000)
        ->and($cells['cost.main.present'])->toBe(192_000_000)
        ->and($cells['cost.fence.new_cost'])->toBe(10_000_000)
        ->and($cells['cost.fence.present'])->toBe(5_000_000)
        ->and($cells['cost.additional.present'])->toBeNull()
        ->and($cells['reconciliation.building.cost'])->toBe(192_000_000)
        ->and($cells['reconciliation.facilities.cost'])->toBe(5_000_000);
});

it('never lets the depreciation of the three kinds pass 100 percent', function () {
    $cells = (new LandBuildingTemplate)->compute(landAnswers(['cost.physical' => '70', 'cost.functional' => '40']), new AppraisalContext(0, null))->cells;

    expect($cells['cost.total'])->toBe(100.0)->and($cells['cost.main.present'])->toBe(0);
});

it('values the land from the selected price per square metre and the land area, falling back to the certificate', function () {
    $answers = landAnswers();
    $withArea = (new LandBuildingTemplate)->compute($answers, new AppraisalContext(0, 80))->cells;
    unset($answers['land_physical.area']);
    $fromCertificate = (new LandBuildingTemplate)->compute($answers, new AppraisalContext(0, 80))->cells;
    $nothing = (new LandBuildingTemplate)->compute($answers, new AppraisalContext(0, null))->cells;

    expect($withArea['reconciliation.land.market'])->toBe(195_000_000)
        ->and($fromCertificate['reconciliation.land.market'])->toBe(156_000_000)
        ->and($nothing['reconciliation.land.market'])->toBe(0);
});

it('reconciles the selected values to a market value and takes the haircut to a liquidation value', function () {
    $result = (new LandBuildingTemplate)->compute(landAnswers(), new AppraisalContext(200_000_000, null));

    // 195.000.000 + 190.000.000 + 5.000.000 - 10.000.000 = 380.000.000; 20% haircut leaves 304.000.000
    expect($result->marketValue)->toBe(380_000_000)
        ->and($result->liquidationValue)->toBe(304_000_000)
        ->and($result->cells['reconciliation.total.selected'])->toBe(380_000_000)
        ->and($result->cells['reconciliation.total.market'])->toBe(195_000_000)
        ->and($result->cells['conclusion.liquidation_value'])->toBe(304_000_000);
});

it('has no liquidation value until a haircut is set, and no market value below zero', function () {
    $noHaircut = (new LandBuildingTemplate)->compute(landAnswers(['h:haircut_percent' => null]), new AppraisalContext(200_000_000, null));
    $deductedAll = (new LandBuildingTemplate)->compute(landAnswers(['reconciliation.deductions.selected' => '900000000']), new AppraisalContext(200_000_000, null));

    expect($noHaircut->liquidationValue)->toBe(0)->and($noHaircut->cells['ltv.ltv'])->toBeNull()
        ->and($deductedAll->marketValue)->toBe(0);
});

it('works out loan-to-value, coverage, and each stress scenario against the ceiling', function () {
    $cells = (new LandBuildingTemplate)->compute(landAnswers(), new AppraisalContext(200_000_000, null))->cells;

    // liquidation 304.000.000: 200/304 = 65,79%; 304/200 = 152%; 10% off: 273.600.000 (73,10%); 15% off: 258.400.000 (77,40%); 20% off: 243.200.000 (82,24%); minus 10.000.000 costs: 294.000.000 (68,03%)
    expect($cells['ltv.ltv'])->toBe(65.79)->and($cells['ltv.coverage'])->toBe(152.0)
        ->and($cells['ltv.s10.value'])->toBe(273_600_000)->and($cells['ltv.s10.ltv'])->toBe(73.1)
        ->and($cells['ltv.s15.value'])->toBe(258_400_000)->and($cells['ltv.s15.ltv'])->toBe(77.4)
        ->and($cells['ltv.s20.value'])->toBe(243_200_000)->and($cells['ltv.s20.ltv'])->toBe(82.24)
        ->and($cells['ltv.exec.value'])->toBe(294_000_000)->and($cells['ltv.exec.ltv'])->toBe(68.03)
        ->and([$cells['ltv.s10.status'], $cells['ltv.s20.status'], $cells['ltv.exec.status']])->toBe(['M', 'M', 'M']);
});

it('marks a scenario that no longer covers the ceiling as not meeting', function () {
    $cells = (new LandBuildingTemplate)->compute(landAnswers(), new AppraisalContext(300_000_000, null))->cells;

    // 300/304 = 98,68% still meets; after 10% the value is 273.600.000 and 300/273,6 = 109,65% does not
    expect($cells['ltv.ltv'])->toBe(98.68)->and($cells['ltv.s10.status'])->toBe('TM')
        ->and(LoanToValue::cells(304_000_000, 304_000_000, ['s10' => 10.0], null)['ltv.ltv'])->toBe(100.0)
        ->and(LoanToValue::cells(304_000_000, 304_000_000, [], null)['ltv.ltv'])->toBe(100.0);
});

it('leaves the stress cells empty when there is no liquidation value or no execution cost', function () {
    $cells = LoanToValue::cells(100, 0, ['s10' => 10.0], null);
    $noCost = LoanToValue::cells(100, 200, ['s10' => 10.0], null);

    expect($cells['ltv.s10.value'])->toBeNull()->and($cells['ltv.s10.status'])->toBeNull()
        ->and($noCost['ltv.exec.status'])->toBeNull()->and($noCost['ltv.s10.status'])->toBe('M');
});

it('takes the market value of the general form as entered and applies the haircut', function () {
    $result = (new GeneralTemplate)->compute(['value.market_value' => '50000000', 'h:haircut_percent' => '10'], new AppraisalContext(40_000_000, null));

    expect($result->marketValue)->toBe(50_000_000)->and($result->liquidationValue)->toBe(45_000_000)
        ->and($result->cells['ltv.ltv'])->toBe(88.89)->and($result->cells['ltv.s30.value'])->toBe(31_500_000)->and($result->cells['ltv.s30.status'])->toBe('TM');
});

it('reads answers as numbers only when they are numbers', function (?string $value, ?int $whole, ?float $decimal) {
    expect(Amounts::whole($value))->toBe($whole)->and(Amounts::decimal($value))->toBe($decimal);
})->with([
    'a number' => ['1500000', 1_500_000, 1_500_000.0],
    'a decimal' => ['12.5', 13, 12.5],
    'nothing' => [null, null, null],
    'empty' => ['', null, null],
    'text' => ['abc', null, null],
]);

it('keeps a haircut between nothing and everything', function () {
    expect(Haircut::liquidation(1_000, 150.0))->toBe(0)->and(Haircut::liquidation(1_000, -5.0))->toBe(1_000)->and(Haircut::liquidation(1_000, null))->toBe(0);
});
