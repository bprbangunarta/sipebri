<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Jejak keputusan berjenjang atas satu berkas pengajuan. */
class LoanApproval extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'level' => 'integer',
        'decided_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }
}
