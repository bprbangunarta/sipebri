<?php

namespace App\Models;

use App\Audit\Auditable;
use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * A loan file, from registration to the committee decision.
 *
 * @property int $id
 * @property string $application_code
 * @property Carbon $application_date
 * @property LoanStatus $status
 * @property string $nik
 * @property string $full_name
 * @property string|null $cif_number
 * @property int|null $office_id
 * @property int|null $product_id
 * @property int|null $institution_id
 * @property int|null $committee_path_id
 * @property string|null $marketing
 * @property string|null $usage_type
 * @property int $requested_amount
 * @property int $requested_tenor
 * @property int|null $method_id
 * @property int|null $installment_id
 * @property string|null $interest_rate
 * @property string|null $note
 * @property int|null $supervisor_id
 * @property int|null $surveyor_id
 * @property Carbon|null $survey_date
 * @property int|null $created_by
 * @property string|null $survey_latitude
 * @property string|null $survey_longitude
 * @property string|null $survey_source how the position was set: paste, map or photo
 * @property Carbon|null $survey_located_at
 * @property string|null $survey_located_by
 * @property int|null $committee_conflict_user_id the committee member who is the applicant, if any
 * @property string|null $committee_conflict_source how that was found out: nik (matched by national ID) or manual
 * @property string|null $survey_address approximate address of the survey location (reverse geocoding), a hint only
 * @property-read Product|null $product
 * @property-read Office|null $office
 * @property-read Method|null $method
 * @property-read Installment|null $installment
 * @property-read CommitteePath|null $committeePath
 * @property-read LoanAnalysis|null $analysis
 */
#[Fillable([
    'application_code', 'application_date', 'status', 'nik', 'full_name', 'cif_number', 'office_id', 'product_id', 'institution_id',
    'committee_path_id', 'marketing', 'usage_type', 'requested_amount', 'requested_tenor', 'method_id', 'installment_id',
    'interest_rate', 'note', 'supervisor_id', 'surveyor_id', 'survey_date', 'created_by',
    'survey_latitude', 'survey_longitude', 'survey_source', 'survey_located_at', 'survey_located_by', 'survey_address',
    'committee_conflict_user_id', 'committee_conflict_source',
])]
class LoanApplication extends Model
{
    use Auditable;
    use SoftDeletes;

    /** File numbers start at 00700001 so they never clash with the legacy system. */
    public const CODE_START = 700000;

    public const USAGE_TYPES = ['CONSUMPTIVE', 'WORKING CAPITAL', 'INVESTMENT', 'OTHER'];

    protected function casts(): array
    {
        return [
            'application_date' => 'date',
            'status' => LoanStatus::class,
            'requested_amount' => 'integer',
            'requested_tenor' => 'integer',
            'interest_rate' => 'decimal:2',
            'survey_date' => 'date',
            'survey_latitude' => 'decimal:7',
            'survey_longitude' => 'decimal:7',
            'survey_located_at' => 'datetime',
        ];
    }

    /** Next file code, 8 digits (00700001, 00700002, ...). */
    public static function nextCode(): string
    {
        $last = (int) static::withTrashed()->max('application_code');

        return str_pad((string) (max($last, self::CODE_START) + 1), 8, '0', STR_PAD_LEFT);
    }

    public function isDraft(): bool
    {
        return $this->status === LoanStatus::Draft;
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<Office, $this>
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * @return BelongsTo<Institution, $this>
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * @return BelongsTo<Method, $this>
     */
    public function method(): BelongsTo
    {
        return $this->belongsTo(Method::class);
    }

    /**
     * @return HasOne<LoanAnalysis, $this>
     */
    public function analysis(): HasOne
    {
        return $this->hasOne(LoanAnalysis::class);
    }

    /**
     * @return BelongsTo<Installment, $this>
     */
    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }

    /**
     * @return BelongsTo<CommitteePath, $this>
     */
    public function committeePath(): BelongsTo
    {
        return $this->belongsTo(CommitteePath::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    /**
     * @return HasMany<LoanSchedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(LoanSchedule::class)->orderBy('id');
    }

    /**
     * @return HasMany<LoanSurvey, $this>
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(LoanSurvey::class)->orderBy('id');
    }

    /**
     * @return HasMany<LoanSurveyPhoto, $this>
     */
    public function photos(): HasMany
    {
        return $this->hasMany(LoanSurveyPhoto::class)->orderBy('id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function conflictUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'committee_conflict_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<Collateral, $this>
     */
    public function collaterals(): BelongsToMany
    {
        return $this->belongsToMany(Collateral::class, 'loan_application_collaterals');
    }
}
