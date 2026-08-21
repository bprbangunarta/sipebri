<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris histori penjadwalan survei. Bersifat append-only: tidak ada
 * `updated_at`, tidak pernah diubah atau dihapus.
 */
class LoanSchedule extends Model
{
    public const ACTION_SCHEDULE = 'JADWAL';

    public const ACTION_RESCHEDULE = 'JADWAL ULANG';

    public const ACTION_CANCEL = 'BATAL';

    /** Batas penjadwalan yang disarankan; melebihi ini hanya memunculkan peringatan. */
    public const MAX_SCHEDULES = 3;

    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'sequence' => 'integer',
        'survey_date' => 'date:Y-m-d',
        'created_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }
}
