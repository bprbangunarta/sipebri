<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $loan_analysis_id
 * @property int $administration
 * @property int $provision
 * @property int $stamp_duty
 * @property int $declining_life_insurance_1
 * @property int $declining_life_insurance_2
 * @property int $declining_life_insurance_3
 * @property int $flat_life_insurance_1
 * @property int $flat_life_insurance_2
 * @property int $life_insurance
 * @property int $motorcycle_insurance
 * @property int $credit_transaction
 * @property int $shm_processing
 * @property int $policy_stamp
 * @property int $vehicle_tax
 * @property int $apht_processing
 * @property int $fiducia_fee
 */
#[Fillable(['loan_analysis_id', 'administration', 'provision', 'stamp_duty', 'declining_life_insurance_1', 'declining_life_insurance_2', 'declining_life_insurance_3', 'flat_life_insurance_1', 'flat_life_insurance_2', 'life_insurance', 'motorcycle_insurance', 'credit_transaction', 'shm_processing', 'policy_stamp', 'vehicle_tax', 'apht_processing', 'fiducia_fee'])]
class AnalysisAdministration extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['administration' => 'integer', 'provision' => 'integer', 'stamp_duty' => 'integer', 'declining_life_insurance_1' => 'integer', 'declining_life_insurance_2' => 'integer', 'declining_life_insurance_3' => 'integer', 'flat_life_insurance_1' => 'integer', 'flat_life_insurance_2' => 'integer', 'life_insurance' => 'integer', 'motorcycle_insurance' => 'integer', 'credit_transaction' => 'integer', 'shm_processing' => 'integer', 'policy_stamp' => 'integer', 'vehicle_tax' => 'integer', 'apht_processing' => 'integer', 'fiducia_fee' => 'integer'];
    }

    public const FEES = ['administration', 'provision', 'stamp_duty', 'declining_life_insurance_1', 'declining_life_insurance_2', 'declining_life_insurance_3', 'flat_life_insurance_1', 'flat_life_insurance_2', 'life_insurance', 'motorcycle_insurance', 'credit_transaction', 'shm_processing', 'policy_stamp', 'vehicle_tax', 'apht_processing', 'fiducia_fee'];

    public function auditLabel(): string
    {
        return 'Administrasi #'.$this->loan_analysis_id;
    }

    public function total(): int
    {
        return (int) collect(self::FEES)->sum(fn (string $column): int => (int) $this->getAttribute($column));
    }
}
