<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Baris lembar analisa: OBLIGATION (kewajiban untuk … + nominal)
 * dan ASSET (harta lain, hanya nama).
 */
class AnalysisSheetItem extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function sheet(): BelongsTo
    {
        return $this->belongsTo(AnalysisSheet::class, 'analysis_sheet_id');
    }
}
