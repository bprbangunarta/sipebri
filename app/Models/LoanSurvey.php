<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Field survey result. Once saved it is not changed.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property int $sequence
 * @property int|null $surveyor_id
 * @property string|null $surveyor_name
 * @property Carbon|null $survey_date
 * @property string|null $note
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $location_source how the position was set: gps, paste, map or photo
 * @property string|null $location_address approximate address of the position (reverse geocoding), a hint only
 * @property string $created_by
 * @property Carbon|null $created_at
 */
#[Fillable(['loan_application_id', 'sequence', 'surveyor_id', 'surveyor_name', 'survey_date', 'note', 'latitude', 'longitude', 'location_source', 'location_address', 'created_by'])]
class LoanSurvey extends Model
{
    use Auditable;

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['sequence' => 'integer', 'survey_date' => 'date', 'latitude' => 'decimal:7', 'longitude' => 'decimal:7'];
    }

    /**
     * @return BelongsTo<LoanApplication, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /**
     * @return HasMany<LoanSurveyPhoto, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(LoanSurveyPhoto::class)->orderBy('id');
    }
}
