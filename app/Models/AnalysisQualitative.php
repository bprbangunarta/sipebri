<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $loan_analysis_id
 * @property int|null $slik_check
 * @property int|null $police_record
 * @property string|null $neighbor_relations
 * @property string|null $migrant_worker_experience
 * @property string|null $experience_by
 * @property string|null $applicant_at_home
 * @property string|null $companion_at_home
 * @property string|null $community_info
 * @property string|null $obligation1_type
 * @property string|null $obligation1_note
 * @property string|null $obligation1_status
 * @property string|null $obligation2_type
 * @property string|null $obligation2_note
 * @property string|null $obligation2_status
 * @property string|null $obligation3_type
 * @property string|null $obligation3_note
 * @property string|null $obligation3_status
 * @property string|null $raw_materials
 * @property string|null $processing
 * @property string|null $market_area
 * @property string|null $payment_system
 * @property string|null $business_supporters
 * @property string|null $business_detractors
 * @property string|null $strength
 * @property string|null $weakness
 * @property string|null $opportunity
 * @property string|null $threat
 * @property string|null $trade_checking
 * @property string|null $notes
 * @property string|null $business_trade_checking
 */
#[Fillable(['loan_analysis_id', 'slik_check', 'police_record', 'neighbor_relations', 'migrant_worker_experience', 'experience_by', 'applicant_at_home', 'companion_at_home', 'community_info', 'obligation1_type', 'obligation1_note', 'obligation1_status', 'obligation2_type', 'obligation2_note', 'obligation2_status', 'obligation3_type', 'obligation3_note', 'obligation3_status', 'raw_materials', 'processing', 'market_area', 'payment_system', 'business_supporters', 'business_detractors', 'strength', 'weakness', 'opportunity', 'threat', 'trade_checking', 'notes', 'business_trade_checking'])]
class AnalysisQualitative extends Model
{
    use Auditable;

    protected function casts(): array
    {
        return ['slik_check' => 'integer', 'police_record' => 'integer'];
    }

    protected $table = 'analysis_qualitative';

    /** Scored answers: column => highest score. */
    public const SCORES = ['slik_check' => 4, 'police_record' => 2];

    /** Answers picked from a list: column => the choices. */
    public const CHOICES = [
        'neighbor_relations' => ['BAIK', 'CUKUP BAIK', 'KURANG BAIK'],
        'migrant_worker_experience' => ['PERNAH', 'TIDAK PERNAH'],
        'experience_by' => ['PEMOHON', 'PENDAMPING'],
        'obligation1_type' => ['BANK UMUM', 'BPR', 'KOPERASI', 'LEASING', 'LAINNYA'],
        'obligation2_type' => ['BANK UMUM', 'BPR', 'KOPERASI', 'LEASING', 'LAINNYA'],
        'obligation3_type' => ['BANK UMUM', 'BPR', 'KOPERASI', 'LEASING', 'LAINNYA'],
        'obligation1_status' => ['LANCAR', 'KURANG LANCAR', 'DIRAGUKAN', 'MACET'],
        'obligation2_status' => ['LANCAR', 'KURANG LANCAR', 'DIRAGUKAN', 'MACET'],
        'obligation3_status' => ['LANCAR', 'KURANG LANCAR', 'DIRAGUKAN', 'MACET'],
    ];

    /** Short free text (at most 255 characters). */
    public const TEXTS = [
        'applicant_at_home', 'companion_at_home', 'community_info',
        'obligation1_note', 'obligation2_note', 'obligation3_note',
        'raw_materials', 'processing', 'market_area', 'payment_system',
        'business_supporters', 'business_detractors',
        'strength', 'weakness', 'opportunity', 'threat',
    ];

    /** Long free text. */
    public const NOTES = ['trade_checking', 'notes', 'business_trade_checking'];

    /**
     * Every column the form manages.
     *
     * @return list<string>
     */
    public static function columns(): array
    {
        return [...array_keys(self::SCORES), ...array_keys(self::CHOICES), ...self::TEXTS, ...self::NOTES];
    }

    public function auditLabel(): string
    {
        return 'Analisa kualitatif #'.$this->loan_analysis_id;
    }
}
