<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Collateral offered against a loan. Nothing is calculated here: values are stored as entered
 * and forwarded to core banking (see toCbsPayload()).
 *
 * @property int $id
 * @property string|null $cbs_id
 * @property string|null $credit_account
 * @property string $collateral_type_code
 * @property string|null $binding_type_code
 * @property string|null $document_number
 * @property string|null $description
 * @property string|null $owner_name
 * @property string|null $owner_address
 * @property string|null $region_code
 * @property string|null $region_label
 * @property int $guarantee_value
 * @property int $adjustment_value
 * @property int $fair_value
 * @property int $njop_value
 * @property int $appraisal_value
 * @property int $independent_value
 * @property string|null $appraiser_name
 * @property Carbon|null $appraised_at
 * @property string|null $independent_name
 * @property Carbon|null $independent_at
 * @property string|null $condition_code
 * @property Carbon|null $condition_date
 * @property string $insurance_code
 * @property Carbon|null $insurance_date
 * @property string $ppap_code
 */
#[Fillable([
    'cbs_id', 'credit_account', 'collateral_type_code', 'binding_type_code', 'document_number', 'description', 'owner_name',
    'owner_address', 'region_code', 'region_label', 'guarantee_value', 'adjustment_value', 'fair_value', 'njop_value',
    'appraisal_value', 'independent_value', 'appraiser_name', 'appraised_at', 'independent_name', 'independent_at',
    'condition_code', 'condition_date', 'insurance_code', 'insurance_date', 'ppap_code', 'created_by',
])]
class Collateral extends Model
{
    use Auditable;
    use SoftDeletes;

    /** Text typed by users is stored in UPPERCASE, as core banking does. */
    public const UPPERCASE = ['cbs_id', 'document_number', 'description', 'owner_name', 'owner_address', 'appraiser_name', 'independent_name'];

    public const VALUES = ['guarantee_value', 'adjustment_value', 'fair_value', 'njop_value', 'appraisal_value', 'independent_value'];

    protected function casts(): array
    {
        return [
            'guarantee_value' => 'integer', 'adjustment_value' => 'integer', 'fair_value' => 'integer', 'njop_value' => 'integer',
            'appraisal_value' => 'integer', 'independent_value' => 'integer',
            'appraised_at' => 'date', 'independent_at' => 'date', 'condition_date' => 'date', 'insurance_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<LoanApplication, $this>
     */
    public function loanApplications(): BelongsToMany
    {
        return $this->belongsToMany(LoanApplication::class, 'loan_application_collaterals');
    }

    /**
     * Payload of the core banking collateral API.
     *
     * @return array<string, mixed>
     */
    public function toCbsPayload(): array
    {
        return [
            'keterangan' => (string) $this->description,
            'pemilik_nama' => (string) $this->owner_name,
            'pemilik_alamat' => (string) $this->owner_address,
            'lokasi' => (string) $this->region_code,
            'asuransi' => $this->insurance_code,
            'startdate' => $this->insurance_date?->toDateString() ?? '',
            'jenis' => $this->collateral_type_code,
            'jenis_pengikat' => (string) $this->binding_type_code,
            'nilai' => [
                'njop' => $this->njop_value, 'jaminan' => $this->guarantee_value, 'adjust' => $this->adjustment_value,
                'wajar' => $this->fair_value, 'taksasi' => $this->appraisal_value, 'independen' => $this->independent_value,
            ],
            'penaksir' => [
                'taksasi' => ['penaksir' => (string) $this->appraiser_name, 'tanggal' => $this->appraised_at?->toDateString() ?? ''],
                'independen' => ['penaksir' => (string) $this->independent_name, 'tanggal' => $this->independent_at?->toDateString() ?? ''],
            ],
            'ppap' => (int) $this->ppap_code,
        ];
    }
}
