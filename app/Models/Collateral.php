<?php

namespace App\Models;

use App\Audit\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $location_source how the position was set: paste, map or photo
 * @property Carbon|null $located_at
 * @property string|null $located_by
 * @property string|null $location_address approximate address of the position (reverse geocoding), a hint only
 * @property string|null $province
 * @property string|null $postal_code
 * @property string|null $route_hint
 * @property string|null $right_kind
 * @property string|null $right_holder_name
 * @property Carbon|null $right_issued_at
 * @property Carbon|null $right_expires_at
 * @property string|null $survey_document
 * @property Carbon|null $survey_document_date
 * @property int|null $certificate_land_area
 * @property string|null $land_office
 * @property string|null $parcel_number
 * @property string|null $acquisition_origin
 * @property string|null $tax_object_number
 * @property string|null $building_permit_number
 * @property Carbon|null $building_permit_date
 * @property string|null $operating_certificate_number
 * @property Carbon|null $operating_certificate_date
 * @property int|null $permitted_building_area
 * @property string|null $permitted_use
 * @property string|null $vehicle_kind
 * @property string|null $vehicle_brand
 * @property string|null $vehicle_type
 * @property string|null $vehicle_model
 * @property int|null $manufacture_year
 * @property int|null $registration_year
 * @property string|null $vehicle_color
 * @property string|null $plate_number
 * @property string|null $chassis_number
 * @property string|null $engine_number
 * @property int|null $engine_capacity
 * @property string|null $fuel
 * @property string|null $transmission
 * @property string|null $drive
 * @property string|null $unit_number
 * @property string|null $bpkb_number
 * @property Carbon|null $bpkb_date
 * @property string|null $bpkb_name
 * @property string|null $bpkb_address
 * @property string|null $stnk_number
 * @property string|null $stnk_name
 */
#[Fillable([
    'cbs_id', 'credit_account', 'collateral_type_code', 'binding_type_code', 'document_number', 'description', 'owner_name',
    'owner_address', 'region_code', 'region_label', 'guarantee_value', 'adjustment_value', 'fair_value', 'njop_value',
    'appraisal_value', 'independent_value', 'appraiser_name', 'appraised_at', 'independent_name', 'independent_at',
    'condition_code', 'condition_date', 'insurance_code', 'insurance_date', 'ppap_code', 'created_by',
    'latitude', 'longitude', 'location_source', 'located_at', 'located_by', 'location_address',
    'province', 'postal_code', 'route_hint', 'right_kind', 'right_holder_name', 'right_issued_at', 'right_expires_at', 'survey_document',
    'survey_document_date', 'certificate_land_area', 'land_office', 'parcel_number', 'acquisition_origin', 'tax_object_number',
    'building_permit_number', 'building_permit_date', 'operating_certificate_number', 'operating_certificate_date', 'permitted_building_area', 'permitted_use',
    'vehicle_kind', 'vehicle_brand', 'vehicle_type', 'vehicle_model', 'manufacture_year', 'registration_year', 'vehicle_color', 'plate_number', 'chassis_number', 'engine_number', 'engine_capacity', 'fuel', 'transmission', 'drive', 'unit_number', 'bpkb_number', 'bpkb_date', 'bpkb_name', 'bpkb_address', 'stnk_number', 'stnk_name',
])]
class Collateral extends Model
{
    use Auditable;
    use SoftDeletes;

    /** Text typed by users is stored in UPPERCASE, as core banking does. */
    public const UPPERCASE = ['cbs_id', 'document_number', 'description', 'owner_name', 'owner_address', 'appraiser_name', 'independent_name'];

    /** Facts of a piece of land and its building that an appraisal reads; they do not go to core banking. */
    public const LAND_FACTS = [
        'province', 'postal_code', 'route_hint', 'right_kind', 'right_holder_name', 'right_issued_at', 'right_expires_at', 'survey_document',
        'survey_document_date', 'certificate_land_area', 'land_office', 'parcel_number', 'acquisition_origin', 'tax_object_number',
        'building_permit_number', 'building_permit_date', 'operating_certificate_number', 'operating_certificate_date', 'permitted_building_area', 'permitted_use',
    ];

    /** Facts of a motorcycle or a car that every appraisal reads; they do not go to core banking. */
    public const VEHICLE_FACTS = [
        'vehicle_kind', 'vehicle_brand', 'vehicle_type', 'vehicle_model', 'manufacture_year', 'registration_year', 'vehicle_color', 'plate_number',
        'chassis_number', 'engine_number', 'engine_capacity', 'fuel', 'transmission', 'drive', 'unit_number', 'bpkb_number', 'bpkb_date', 'bpkb_name',
        'bpkb_address', 'stnk_number', 'stnk_name',
    ];

    public const VEHICLE_KINDS = ['wheel2' => 'Roda 2', 'wheel4' => 'Roda 4'];

    public const TRANSMISSIONS = ['manual' => 'Manual', 'automatic' => 'Otomatis'];

    public const LAND_DATES = ['right_issued_at', 'right_expires_at', 'survey_document_date', 'building_permit_date', 'operating_certificate_date'];

    public const RIGHT_KINDS = ['SHM' => 'SHM', 'SHGB' => 'SHGB', 'SHP' => 'SHP', 'OTHER' => 'Lainnya'];

    public const VALUES = ['guarantee_value', 'adjustment_value', 'fair_value', 'njop_value', 'appraisal_value', 'independent_value'];

    protected function casts(): array
    {
        return [
            'guarantee_value' => 'integer', 'adjustment_value' => 'integer', 'fair_value' => 'integer', 'njop_value' => 'integer',
            'appraisal_value' => 'integer', 'independent_value' => 'integer',
            'appraised_at' => 'date', 'independent_at' => 'date', 'condition_date' => 'date', 'insurance_date' => 'date',
            'latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'located_at' => 'datetime',
            'right_issued_at' => 'date', 'right_expires_at' => 'date', 'survey_document_date' => 'date', 'building_permit_date' => 'date', 'operating_certificate_date' => 'date',
            'certificate_land_area' => 'integer', 'permitted_building_area' => 'integer',
            'bpkb_date' => 'date', 'manufacture_year' => 'integer', 'registration_year' => 'integer', 'engine_capacity' => 'integer',
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
     * The appraisals of this collateral, newest first: the history that management reads.
     *
     * @return HasMany<CollateralAppraisal, $this>
     */
    public function appraisals(): HasMany
    {
        return $this->hasMany(CollateralAppraisal::class)->orderByDesc('sequence');
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
