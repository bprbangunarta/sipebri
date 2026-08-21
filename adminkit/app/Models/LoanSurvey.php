<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Hasil survei lapangan. Sekali disimpan tidak diubah lagi. */
class LoanSurvey extends Model
{
    public const UPDATED_AT = null;

    protected $guarded = ['id'];

    protected $casts = [
        'sequence' => 'integer',
        'survey_date' => 'date:Y-m-d',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LoanSurveyPhoto::class, 'loan_survey_id')->orderBy('id');
    }
}
