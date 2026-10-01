<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $loan_analysis_id
 * @property int $collateral_id
 * @property string $kind
 * @property string|null $brand
 * @property string|null $vehicle_type
 * @property string|null $year
 * @property string|null $chassis_number
 * @property string|null $engine_number
 * @property string|null $plate_number
 * @property string|null $color
 * @property int $land_area
 * @property string|null $location
 * @property int $market_value
 * @property int $appraisal_value
 * @property string|null $notes
 * @property-read Collateral $collateral
 */
#[Fillable(['loan_analysis_id', 'collateral_id', 'kind', 'brand', 'vehicle_type', 'year', 'chassis_number', 'engine_number', 'plate_number', 'color', 'land_area', 'location', 'market_value', 'appraisal_value', 'notes'])]
class AnalysisCollateral extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['land_area' => 'integer', 'market_value' => 'integer', 'appraisal_value' => 'integer'];
    }

    public const KINDS = ['vehicle', 'land', 'other'];

    public const VEHICLE_FIELDS = ['brand', 'vehicle_type', 'year', 'chassis_number', 'engine_number', 'plate_number', 'color'];

    /**
     * @return BelongsTo<Collateral, $this>
     */
    public function collateral(): BelongsTo
    {
        return $this->belongsTo(Collateral::class);
    }

    public function auditLabel(): string
    {
        return 'Pemeriksaan agunan #'.$this->collateral_id;
    }
}
