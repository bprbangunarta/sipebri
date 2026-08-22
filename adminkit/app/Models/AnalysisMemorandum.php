<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;

/** Memorandum (bagian 7): kebutuhan dana & usulan fasilitas. */
class AnalysisMemorandum extends Model
{
    use TracksAuthor;

    /** Pos kebutuhan dana; jumlahnya dihitung sistem. */
    protected $table = 'analysis_memorandums';

    public const NEEDS = ['modal_kerja', 'investasi', 'konsumtif', 'pelunasan_kredit', 'take_over'];

    public const RATES = ['b_admin', 's_bunga', 'b_provisi', 'b_penalti'];

    public const BINDINGS = ['NOTARIIL', 'BAWAH TANGAN', 'FIDUCIA', 'APHT', 'TANPA PENGIKATAN'];

    protected $guarded = ['id'];

    protected $casts = [
        'b_admin' => 'float',
        's_bunga' => 'float',
        'b_provisi' => 'float',
        'b_penalti' => 'float',
    ];

    public function totalNeed(): int
    {
        return collect(self::NEEDS)->sum(fn ($c) => (int) $this->{$c});
    }
}
