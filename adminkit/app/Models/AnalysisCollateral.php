<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Berita acara pemeriksaan agunan (bagian 4). Satu baris per agunan berkas. */
class AnalysisCollateral extends Model
{
    use TracksAuthor;

    public const KINDS = ['KENDARAAN', 'TANAH', 'LAINNYA'];

    /** Kolom yang hanya dipakai jenis pemeriksaan tertentu. */
    public const VEHICLE_FIELDS = ['merek', 'tipe_kendaraan', 'tahun', 'no_rangka', 'no_mesin', 'no_polisi', 'warna'];

    protected $guarded = ['id'];

    public function collateral(): BelongsTo
    {
        return $this->belongsTo(CollateralSimulation::class, 'collateral_simulation_id');
    }
}
