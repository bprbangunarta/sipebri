<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Analisa 5C. Tiap aspek dinilai dengan skor (skala berbeda per aspek, lihat
 * ASPECTS). Kolom EVALUASI tidak diinput: dihitung dari rata-rata persentase
 * skor terhadap skala maksimum masing-masing aspek.
 */
class AnalysisFiveC extends Model
{
    use TracksAuthor;

    protected $table = 'analysis_five_c';

    /** kelompok => [kolom => skor maksimum] */
    public const ASPECTS = [
        'character' => [
            'gaya_hidup' => 3, 'pengendalian_emosi' => 3, 'perbuatan_tercela' => 3, 'harmonis' => 3,
            'konsisten' => 3, 'kepatuhan' => 3, 'hubungan_sosial' => 3,
        ],
        'capacity' => [
            'kontinuitas' => 3, 'pengalaman_usaha' => 5, 'pertumbuhan_usaha' => 3, 'laporan_keuangan' => 3,
            'catatan_kredit' => 3, 'kondisi_slik' => 3, 'aset_diluar_usaha' => 3, 'aset_terkait_usaha' => 3,
        ],
        'capital' => ['sumber_modal' => 3],
        'collateral' => [
            'agunan_utama' => 3, 'legalitas_agunan' => 3, 'mudah_diuangkan' => 3, 'kondisi_kendaraan' => 3,
            'aspek_hukum' => 4, 'agunan_tambahan' => 3, 'legalitas_agunan_tambahan' => 3,
            'stabilitas_harga' => 3, 'lokasi_shm' => 3,
        ],
        'condition' => ['kondisi_alam' => 5, 'persaingan_usaha' => 3, 'regulasi_pemerintah' => 4],
    ];

    protected $guarded = ['id'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /** Evaluasi per kelompok + nilai keseluruhan. */
    public function metrics(): array
    {
        $groups = [];

        foreach (self::ASPECTS as $group => $aspects) {
            $filled = 0;
            $score = 0;
            $max = 0;

            foreach ($aspects as $column => $scale) {
                if ($this->{$column} === null) {
                    continue;
                }

                $filled++;
                $score += (int) $this->{$column};
                $max += $scale;
            }

            $percent = $max > 0 ? round($score / $max * 100, 2) : 0.0;

            $groups[$group] = [
                'filled' => $filled,
                'total' => count($aspects),
                'score' => $score,
                'max' => $max,
                'percent' => $percent,
                'grade' => $filled > 0 ? self::grade($percent) : null,
            ];
        }

        $scored = collect($groups)->filter(fn ($g) => $g['filled'] > 0);
        $overall = $scored->isEmpty() ? 0.0 : round($scored->avg('percent'), 2);

        return [
            'groups' => $groups,
            'percent' => $overall,
            'grade' => $scored->isEmpty() ? null : self::grade($overall),
        ];
    }

    /** Predikat mengikuti persentase skor: ≥80 BAIK, ≥60 CUKUP BAIK, sisanya KURANG BAIK. */
    public static function grade(float $percent): string
    {
        return match (true) {
            $percent >= 80 => 'BAIK',
            $percent >= 60 => 'CUKUP BAIK',
            default => 'KURANG BAIK',
        };
    }
}
