<?php

namespace App\Models;

use App\Audit\Auditable;
use App\Enums\LoanStatus;
use App\Support\CreditAnalysis\AnalysisTemplates;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * The credit analysis of a loan file: the template it follows and who submitted it to the committee. The sections
 * (businesses, finance, collateral checks, 5C, qualitative, memorandum, administration) hang on it.
 *
 * @property int $id
 * @property int $loan_application_id
 * @property string $template
 * @property int $template_version
 * @property Carbon|null $submitted_at
 * @property int|null $submitted_by
 * @property-read LoanApplication $loanApplication
 * @property-read User|null $submitter
 * @property-read AnalysisFinance|null $finance
 * @property-read AnalysisFiveC|null $fiveC
 * @property-read AnalysisQualitative|null $qualitative
 * @property-read AnalysisMemorandum|null $memorandum
 * @property-read AnalysisAdministration|null $administration
 */
#[Fillable(['loan_application_id', 'template', 'template_version', 'submitted_at', 'submitted_by'])]
class LoanAnalysis extends Model
{
    use Auditable;

    protected $table = 'loan_analyses';

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime', 'template_version' => 'integer'];
    }

    /**
     * The analysis of a file, created at the first save. A file that was only surveyed moves to the analysis stage then.
     */
    public static function begin(LoanApplication $loan): self
    {
        $analysis = self::query()->firstOrCreate(
            ['loan_application_id' => $loan->id],
            ['template' => AnalysisTemplates::for($loan), 'template_version' => AnalysisTemplates::CURRENT_VERSION],
        );

        if ($loan->status === LoanStatus::Survey) {
            $loan->update(['status' => LoanStatus::Analysis]);
        }

        return $analysis;
    }

    /**
     * @return BelongsTo<LoanApplication, $this>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * @return HasMany<AnalysisBusiness, $this>
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(AnalysisBusiness::class)->orderBy('id');
    }

    /**
     * @return HasOne<AnalysisFinance, $this>
     */
    public function finance(): HasOne
    {
        return $this->hasOne(AnalysisFinance::class);
    }

    /**
     * @return HasMany<AnalysisCollateral, $this>
     */
    public function collateralChecks(): HasMany
    {
        return $this->hasMany(AnalysisCollateral::class);
    }

    /**
     * @return HasOne<AnalysisFiveC, $this>
     */
    public function fiveC(): HasOne
    {
        return $this->hasOne(AnalysisFiveC::class);
    }

    /**
     * @return HasOne<AnalysisQualitative, $this>
     */
    public function qualitative(): HasOne
    {
        return $this->hasOne(AnalysisQualitative::class);
    }

    /**
     * @return HasOne<AnalysisMemorandum, $this>
     */
    public function memorandum(): HasOne
    {
        return $this->hasOne(AnalysisMemorandum::class);
    }

    /**
     * @return HasOne<AnalysisAdministration, $this>
     */
    public function administration(): HasOne
    {
        return $this->hasOne(AnalysisAdministration::class);
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    /**
     * What must be filled in before the file may go to the committee.
     *
     * @return list<string>
     */
    public function gaps(): array
    {
        $gaps = [];

        if (! $this->businesses()->exists()) {
            $gaps[] = 'Analisa Usaha (minimal satu usaha)';
        }

        $finance = $this->finance ?? new AnalysisFinance(['loan_analysis_id' => $this->id]);

        if ($finance->metrics()['household_cost'] <= 0) {
            $gaps[] = 'Analisa Keuangan (biaya rumah tangga)';
        }

        if (($this->fiveC ?? new AnalysisFiveC)->metrics()['grade'] === null) {
            $gaps[] = 'Analisa 5C';
        }

        if (($this->memorandum->proposed_amount ?? 0) <= 0) {
            $gaps[] = 'Memorandum (usulan plafon)';
        }

        return $gaps;
    }
}
