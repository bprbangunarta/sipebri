<?php

namespace App\Models;

use App\Support\FileStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Foto lokasi survei beserta koordinat saat pengambilan. */
class LoanSurveyPhoto extends Model
{
    public const UPDATED_AT = null;

    /** Batas foto per berkas. */
    public const MAX_PHOTOS = 5;

    protected $guarded = ['id'];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    public function getUrlAttribute(): ?string
    {
        return FileStorage::url($this->path);
    }
}
