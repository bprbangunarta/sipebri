<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One answer of an appraisal: the value (and the note) an appraiser gave to one item of the template.
 *
 * @property int $id
 * @property int $collateral_appraisal_id
 * @property string $section
 * @property string $item
 * @property string|null $value
 * @property string|null $note
 */
#[Fillable(['collateral_appraisal_id', 'section', 'item', 'value', 'note'])]
class AppraisalEntry extends Model
{
    use Auditable;

    protected $table = 'collateral_appraisal_entries';

    /**
     * @return BelongsTo<CollateralAppraisal, $this>
     */
    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(CollateralAppraisal::class, 'collateral_appraisal_id');
    }

    public function auditModule(): string
    {
        return 'collateral_appraisals';
    }

    public function auditLabel(): string
    {
        return "Penilaian #{$this->collateral_appraisal_id}: {$this->item}";
    }
}
