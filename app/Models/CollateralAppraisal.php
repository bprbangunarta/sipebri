<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A dated report on one collateral. Once finalised it is history and never changes (see CollateralAppraisalController).
 *
 * @property int $id
 * @property int $collateral_id
 * @property int $sequence
 * @property string $template
 * @property int $template_version
 * @property string $status draft or final
 * @property string $purpose
 * @property string|null $inspection
 * @property int|null $loan_application_id
 * @property string|null $debtor_name
 * @property string|null $debtor_cif
 * @property string|null $product_label
 * @property int $credit_ceiling
 * @property string|null $owner_address
 * @property string|null $owner_relation
 * @property string|null $owner_contact
 * @property string|null $appraiser_name
 * @property string|null $reviewer_name
 * @property Carbon|null $effective_date
 * @property Carbon|null $report_date
 * @property Carbon|null $valid_until
 * @property string|null $haircut_percent
 * @property int $market_value
 * @property int $liquidation_value
 * @property int|null $forced_sale_value
 * @property int|null $rounded_value
 * @property int|null $credit_basis_value
 * @property string|null $decision
 * @property string|null $main_conditions
 * @property string|null $reappraisal_frequency
 * @property Carbon|null $finalized_at
 * @property string|null $finalized_by
 * @property int|null $created_by
 * @property-read Collateral $collateral
 */
#[Fillable([
    'collateral_id', 'sequence', 'template', 'template_version', 'status', 'purpose', 'inspection', 'loan_application_id', 'debtor_name', 'debtor_cif',
    'product_label', 'credit_ceiling', 'owner_address', 'owner_relation', 'owner_contact', 'appraiser_name', 'reviewer_name', 'effective_date',
    'report_date', 'valid_until', 'haircut_percent', 'market_value', 'liquidation_value', 'forced_sale_value', 'rounded_value',
    'credit_basis_value', 'decision', 'main_conditions', 'reappraisal_frequency', 'finalized_at', 'finalized_by', 'created_by',
])]
class CollateralAppraisal extends Model
{
    use Auditable;

    public const DRAFT = 'draft';

    public const FINAL = 'final';

    public const PURPOSES = ['new' => 'Kredit baru', 'top_up' => 'Top-up', 'review' => 'Review', 'other' => 'Lainnya'];

    public const INSPECTIONS = ['full' => 'Penuh', 'external' => 'Eksternal', 'desktop' => 'Desktop'];

    public const DECISIONS = ['accepted' => 'Dapat diterima', 'conditional' => 'Dapat diterima bersyarat', 'rejected' => 'Tidak dapat diterima'];

    /** Columns an appraiser fills in, by the item key a template uses for them (`h:` prefix); the value is the column. */
    public const HEADER_FIELDS = [
        'purpose', 'inspection', 'debtor_name', 'debtor_cif', 'product_label', 'credit_ceiling', 'owner_address', 'owner_relation', 'owner_contact',
        'appraiser_name', 'reviewer_name', 'effective_date', 'report_date', 'valid_until', 'haircut_percent', 'forced_sale_value', 'rounded_value',
        'credit_basis_value', 'decision', 'main_conditions', 'reappraisal_frequency',
    ];

    protected function casts(): array
    {
        return [
            'credit_ceiling' => 'integer', 'market_value' => 'integer', 'liquidation_value' => 'integer', 'forced_sale_value' => 'integer',
            'rounded_value' => 'integer', 'credit_basis_value' => 'integer', 'template_version' => 'integer', 'sequence' => 'integer',
            'effective_date' => 'date', 'report_date' => 'date', 'valid_until' => 'date', 'finalized_at' => 'datetime',
        ];
    }

    public function isFinal(): bool
    {
        return $this->status === self::FINAL;
    }

    /**
     * @return BelongsTo<Collateral, $this>
     */
    public function collateral(): BelongsTo
    {
        return $this->belongsTo(Collateral::class);
    }

    /**
     * @return BelongsTo<LoanApplication, $this>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return HasMany<AppraisalEntry, $this>
     */
    public function entries(): HasMany
    {
        return $this->hasMany(AppraisalEntry::class);
    }

    /**
     * @return HasMany<AppraisalPhoto, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(AppraisalPhoto::class);
    }

    public function auditLabel(): string
    {
        return "Penilaian #{$this->sequence} agunan #{$this->collateral_id}";
    }
}
