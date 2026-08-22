<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Jejak keputusan berjenjang atas satu berkas pengajuan. */
class LoanApproval extends Model
{
    /** TERUSKAN = naik ke jenjang berikutnya; sisanya keputusan akhir. */
    public const DECISIONS = ['TERUSKAN', 'DISETUJUI', 'DITOLAK', 'DIBATALKAN'];

    protected $guarded = ['id'];

    protected $casts = [
        'level' => 'integer',
        'decided_at' => 'datetime',
        'max_amount' => 'integer',
        'amount' => 'integer',
        'tenor' => 'integer',
        'interest_rate' => 'float',
        'provision_rate' => 'float',
        'admin_rate' => 'float',
        'rc_ratio' => 'float',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(Method::class);
    }
}
