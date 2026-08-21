<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Berkas pengajuan kredit: tahap pendaftaran sampai keputusan komite. */
class LoanApplication extends Model
{
    use SoftDeletes, TracksAuthor;

    /** Nomor berkas dimulai dari 00700001 agar tidak bentrok dengan sistem lama. */
    public const CODE_START = 700000;

    protected $guarded = ['id'];

    protected $casts = [
        'application_date' => 'date:Y-m-d',
        'requested_amount' => 'integer',
        'requested_tenor' => 'integer',
        'tenor_principal' => 'integer',
        'tenor_interest' => 'integer',
        'interest_rate' => 'decimal:2',
        'provision_rate' => 'decimal:2',
        'admin_rate' => 'decimal:2',
        'analyzed_at' => 'datetime',
        'rc_ratio' => 'decimal:2',
        'repayment_capacity' => 'integer',
        'recommended_amount' => 'integer',
        'recommended_tenor' => 'integer',
        'decided_at' => 'datetime',
        'approved_amount' => 'integer',
        'approved_tenor' => 'integer',
        'approved_rate' => 'decimal:2',
        'disbursed_at' => 'date:Y-m-d',
        'survey_date' => 'date:Y-m-d',
    ];

    /** Kode berkas berikutnya, format 8 digit (00700001, 00700002, ...). */
    public static function nextCode(): string
    {
        $last = (int) static::withTrashed()->max('application_code');

        return str_pad((string) (max($last, self::CODE_START) + 1), 8, '0', STR_PAD_LEFT);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(Method::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(LoanSchedule::class)->orderBy('id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LoanSurveyPhoto::class)->orderBy('id');
    }

    public function collaterals(): BelongsToMany
    {
        return $this->belongsToMany(
            CollateralSimulation::class,
            'loan_application_collaterals',
            'loan_application_id',
            'collateral_simulation_id',
        );
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(LoanApproval::class)->orderBy('level');
    }
}
