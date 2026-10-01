<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $loan_analysis_id
 * @property string $status requested, open, closed or declined
 * @property string $reason
 * @property int|null $requested_by
 * @property Carbon|null $requested_at
 * @property int|null $opened_by
 * @property Carbon|null $opened_at
 * @property int|null $resolved_by closed or declined by
 * @property Carbon|null $resolved_at
 * @property string|null $resolution_note
 * @property-read LoanAnalysis $loanAnalysis
 */
#[Fillable(['loan_analysis_id', 'status', 'reason', 'requested_by', 'requested_at', 'opened_by', 'opened_at', 'resolved_by', 'resolved_at', 'resolution_note'])]
class AnalysisCorrection extends Model
{
    use Auditable;

    public const REQUESTED = 'requested';

    public const OPEN = 'open';

    public const CLOSED = 'closed';

    public const DECLINED = 'declined';

    public const LABELS = [self::REQUESTED => 'Diminta', self::OPEN => 'Dibuka', self::CLOSED => 'Selesai', self::DECLINED => 'Ditolak'];

    protected function casts(): array
    {
        return ['requested_at' => 'datetime', 'opened_at' => 'datetime', 'resolved_at' => 'datetime'];
    }

    /**
     * @return BelongsTo<LoanAnalysis, $this>
     */
    public function loanAnalysis(): BelongsTo
    {
        return $this->belongsTo(LoanAnalysis::class);
    }

    public function auditLabel(): string
    {
        return 'Koreksi analisa #'.$this->loan_analysis_id;
    }
}
