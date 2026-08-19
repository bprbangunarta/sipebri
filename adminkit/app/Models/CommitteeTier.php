<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeTier extends Model
{
    protected $fillable = [
        'committee_path_id', 'sort', 'label', 'role',
        'min_amount', 'max_amount',
        'can_escalate', 'can_approve', 'can_cancel', 'can_reject',
    ];

    protected $casts = [
        'can_escalate' => 'boolean',
        'can_approve' => 'boolean',
        'can_cancel' => 'boolean',
        'can_reject' => 'boolean',
    ];

    public function path(): BelongsTo
    {
        return $this->belongsTo(CommitteePath::class, 'committee_path_id');
    }
}
