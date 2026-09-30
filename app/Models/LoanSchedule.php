<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One row of survey scheduling history. Append-only: never edited or deleted.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property int $sequence
 * @property string $action
 * @property Carbon|null $survey_date
 * @property int|null $surveyor_id
 * @property string|null $surveyor_name
 * @property string|null $note
 * @property string|null $reason
 * @property string $created_by
 * @property Carbon|null $created_at
 */
#[Fillable(['loan_application_id', 'sequence', 'action', 'survey_date', 'surveyor_id', 'surveyor_name', 'note', 'reason', 'created_by'])]
class LoanSchedule extends Model
{
    use Auditable;

    public const ACTION_SCHEDULE = 'schedule';

    public const ACTION_RESCHEDULE = 'reschedule';

    public const ACTION_CANCEL = 'cancel';

    public const ACTION_RESURVEY = 'resurvey';

    public const ACTION_VOID = 'void';

    public const LABELS = [
        self::ACTION_SCHEDULE => 'Scheduled',
        self::ACTION_RESCHEDULE => 'Rescheduled',
        self::ACTION_CANCEL => 'Schedule cancelled',
        self::ACTION_RESURVEY => 'Re-survey scheduled',
        self::ACTION_VOID => 'Application voided',
    ];

    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['sequence' => 'integer', 'survey_date' => 'date'];
    }

    /**
     * @return BelongsTo<LoanApplication, $this>
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }
}
