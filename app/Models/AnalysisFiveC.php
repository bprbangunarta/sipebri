<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $loan_analysis_id
 * @property int|null $lifestyle
 * @property int|null $emotional_control
 * @property int|null $disreputable_acts
 * @property int|null $family_harmony
 * @property int|null $consistency
 * @property int|null $compliance
 * @property int|null $social_relations
 * @property int|null $continuity
 * @property int|null $business_experience
 * @property int|null $business_growth
 * @property int|null $financial_records
 * @property int|null $credit_history
 * @property int|null $slik_condition
 * @property int|null $non_business_assets
 * @property int|null $business_assets
 * @property int|null $capital_source
 * @property int|null $main_collateral_ownership
 * @property int|null $main_collateral_legality
 * @property int|null $liquidity
 * @property int|null $vehicle_condition
 * @property int|null $legal_binding
 * @property int|null $additional_collateral_ownership
 * @property int|null $additional_collateral_legality
 * @property int|null $price_stability
 * @property int|null $shm_location
 * @property int|null $natural_conditions
 * @property int|null $competition
 * @property int|null $regulations
 */
#[Fillable(['loan_analysis_id', 'lifestyle', 'emotional_control', 'disreputable_acts', 'family_harmony', 'consistency', 'compliance', 'social_relations', 'continuity', 'business_experience', 'business_growth', 'financial_records', 'credit_history', 'slik_condition', 'non_business_assets', 'business_assets', 'capital_source', 'main_collateral_ownership', 'main_collateral_legality', 'liquidity', 'vehicle_condition', 'legal_binding', 'additional_collateral_ownership', 'additional_collateral_legality', 'price_stability', 'shm_location', 'natural_conditions', 'competition', 'regulations'])]
class AnalysisFiveC extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['lifestyle' => 'integer', 'emotional_control' => 'integer', 'disreputable_acts' => 'integer', 'family_harmony' => 'integer', 'consistency' => 'integer', 'compliance' => 'integer', 'social_relations' => 'integer', 'continuity' => 'integer', 'business_experience' => 'integer', 'business_growth' => 'integer', 'financial_records' => 'integer', 'credit_history' => 'integer', 'slik_condition' => 'integer', 'non_business_assets' => 'integer', 'business_assets' => 'integer', 'capital_source' => 'integer', 'main_collateral_ownership' => 'integer', 'main_collateral_legality' => 'integer', 'liquidity' => 'integer', 'vehicle_condition' => 'integer', 'legal_binding' => 'integer', 'additional_collateral_ownership' => 'integer', 'additional_collateral_legality' => 'integer', 'price_stability' => 'integer', 'shm_location' => 'integer', 'natural_conditions' => 'integer', 'competition' => 'integer', 'regulations' => 'integer'];
    }

    protected $table = 'analysis_five_c';

    /** Group => [aspect => highest score]. The evaluation column of the worksheet is worked out, never typed. */
    public const ASPECTS = [
        'character' => ['lifestyle' => 3, 'emotional_control' => 3, 'disreputable_acts' => 3, 'family_harmony' => 3, 'consistency' => 3, 'compliance' => 3, 'social_relations' => 3],
        'capacity' => ['continuity' => 3, 'business_experience' => 5, 'business_growth' => 3, 'financial_records' => 3, 'credit_history' => 3, 'slik_condition' => 3, 'non_business_assets' => 3, 'business_assets' => 3],
        'capital' => ['capital_source' => 3],
        'collateral' => ['main_collateral_ownership' => 3, 'main_collateral_legality' => 3, 'liquidity' => 3, 'vehicle_condition' => 3, 'legal_binding' => 4, 'additional_collateral_ownership' => 3, 'additional_collateral_legality' => 3, 'price_stability' => 3, 'shm_location' => 3],
        'condition' => ['natural_conditions' => 5, 'competition' => 3, 'regulations' => 4],
    ];

    public function auditLabel(): string
    {
        return 'Analisa 5C #'.$this->loan_analysis_id;
    }

    /**
     * Percentage of the highest possible score per group, their average, and the grade of each.
     *
     * @return array{groups: array<string, array<string, mixed>>, percent: float, grade: string|null}
     */
    public function metrics(): array
    {
        $groups = [];

        foreach (self::ASPECTS as $group => $aspects) {
            $filled = 0;
            $score = 0;
            $max = 0;

            foreach ($aspects as $column => $scale) {
                $value = $this->getAttribute($column);

                if ($value === null) {
                    continue;
                }

                $filled++;
                $score += (int) $value;
                $max += $scale;
            }

            $percent = $max > 0 ? round($score / $max * 100, 2) : 0.0;

            $groups[$group] = [
                'filled' => $filled,
                'total' => count($aspects),
                'score' => $score,
                'max' => $max,
                'percent' => $percent,
                'grade' => $filled > 0 ? self::grade($percent) : null,
            ];
        }

        $scored = collect($groups)->filter(fn (array $g): bool => $g['filled'] > 0);
        $overall = $scored->isEmpty() ? 0.0 : round((float) $scored->avg('percent'), 2);

        return [
            'groups' => $groups,
            'percent' => $overall,
            'grade' => $scored->isEmpty() ? null : self::grade($overall),
        ];
    }

    /** At least 80% is good, at least 60% fairly good, the rest not good. */
    public static function grade(float $percent): string
    {
        return match (true) {
            $percent >= 80 => 'BAIK',
            $percent >= 60 => 'CUKUP BAIK',
            default => 'KURANG BAIK',
        };
    }
}
