<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * One step of the committee route of a file: the tier that has to act, and what it decided. A step is written when the
 * analysis goes to the committee and stays pending (no decision) until its tier decides.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property int|null $committee_tier_id
 * @property int $sort
 * @property string|null $tier_label
 * @property string $role
 * @property bool $is_individual the person who holds the file decides (analyst staff), not a sitting committee
 * @property bool $waived decides although outside its own limit, because the applicant is a committee member
 * @property string|null $decision forward, approve, reject or cancel; null while pending
 * @property int|null $decided_by
 * @property string|null $decided_by_name
 * @property int|null $method_id
 * @property int $amount
 * @property int $tenor
 * @property string $interest_rate
 * @property string $provision_rate
 * @property string $admin_rate
 * @property int $max_amount the most the applicant's capacity allows with these terms
 * @property string $rc_ratio amount as a percentage of `max_amount`
 * @property string|null $note
 * @property Carbon|null $decided_at
 * @property-read LoanApplication $loanApplication
 * @property-read CommitteeTier|null $tier
 * @property-read Method|null $method
 */
#[Fillable([
    'loan_application_id', 'committee_tier_id', 'sort', 'tier_label', 'role', 'is_individual', 'waived', 'decision', 'decided_by', 'decided_by_name',
    'method_id', 'amount', 'tenor', 'interest_rate', 'provision_rate', 'admin_rate', 'max_amount', 'rc_ratio', 'note', 'decided_at',
])]
class LoanApproval extends Model
{
    use Auditable;

    public const FORWARD = 'forward';

    public const APPROVE = 'approve';

    public const REJECT = 'reject';

    public const CANCEL = 'cancel';

    public const DECISIONS = [self::FORWARD, self::APPROVE, self::REJECT, self::CANCEL];

    public const LABELS = [self::FORWARD => 'Diteruskan', self::APPROVE => 'Disetujui', self::REJECT => 'Ditolak', self::CANCEL => 'Dibatalkan'];

    protected function casts(): array
    {
        return [
            'is_individual' => 'boolean',
            'waived' => 'boolean',
            'amount' => 'integer',
            'tenor' => 'integer',
            'max_amount' => 'integer',
            'decided_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<LoanApplication, $this>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return BelongsTo<CommitteeTier, $this>
     */
    public function tier(): BelongsTo
    {
        return $this->belongsTo(CommitteeTier::class, 'committee_tier_id');
    }

    /**
     * @return BelongsTo<Method, $this>
     */
    public function method(): BelongsTo
    {
        return $this->belongsTo(Method::class);
    }

    public function isPending(): bool
    {
        return $this->decision === null;
    }

    public function auditLabel(): string
    {
        return "Persetujuan #{$this->loan_application_id} jenjang {$this->sort}";
    }
}
