<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $committee_path_id
 * @property int $sort
 * @property string|null $label
 * @property string $role Spatie role that holds the decision authority.
 * @property int|null $min_amount
 * @property int|null $max_amount
 * @property bool $can_escalate
 * @property bool $can_approve
 * @property bool $can_cancel
 * @property bool $can_reject
 */
#[Fillable(['committee_path_id', 'sort', 'label', 'role', 'min_amount', 'max_amount', 'can_escalate', 'can_approve', 'can_cancel', 'can_reject'])]
class CommitteeTier extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['can_escalate' => 'boolean', 'can_approve' => 'boolean', 'can_cancel' => 'boolean', 'can_reject' => 'boolean'];
    }

    /**
     * @return BelongsTo<CommitteePath, $this>
     */
    public function path(): BelongsTo
    {
        return $this->belongsTo(CommitteePath::class, 'committee_path_id');
    }

    public function canDecide(): bool
    {
        return $this->can_approve || $this->can_cancel || $this->can_reject;
    }
}
