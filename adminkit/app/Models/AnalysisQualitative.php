<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Analisa Kualitatif: karakter, usaha, SWOT dan catatan tambahan. */
class AnalysisQualitative extends Model
{
    use TracksAuthor;

    protected $table = 'analysis_qualitative';

    /** Kolom skor: kolom => skor maksimum. */
    public const SCORES = ['bi_checking' => 4, 'pihak_berwajib' => 2];

    /** Kolom pilihan teks: kolom => daftar pilihan. */
    public const CHOICES = [
        'hubungan_tetangga' => ['BAIK', 'CUKUP BAIK', 'KURANG BAIK'],
        'pengalaman_tki' => ['PERNAH', 'TIDAK PERNAH'],
        'ket_pengalaman' => ['PEMOHON', 'PENDAMPING'],
        'kewajiban1' => ['BANK UMUM', 'BPR', 'KOPERASI', 'LEASING', 'LAINNYA'],
        'kewajiban2' => ['BANK UMUM', 'BPR', 'KOPERASI', 'LEASING', 'LAINNYA'],
        'kewajiban3' => ['BANK UMUM', 'BPR', 'KOPERASI', 'LEASING', 'LAINNYA'],
        'status1' => ['LANCAR', 'KURANG LANCAR', 'DIRAGUKAN', 'MACET'],
        'status2' => ['LANCAR', 'KURANG LANCAR', 'DIRAGUKAN', 'MACET'],
        'status3' => ['LANCAR', 'KURANG LANCAR', 'DIRAGUKAN', 'MACET'],
    ];

    /** Kolom teks singkat (maks 255). */
    public const TEXTS = [
        'pemohon_ada', 'pendamping_ada', 'info_masyarakat',
        'ket_kewajiban1', 'ket_kewajiban2', 'ket_kewajiban3',
        'bahan_baku', 'proses_olah', 'target_market', 'pembayaran',
        'pendukung_usaha', 'pengurang_usaha',
        'kekuatan', 'kelemahan', 'peluang', 'ancaman',
    ];

    /** Kolom uraian panjang. */
    public const NOTES = ['trade_checking', 'catatan', 'trade_checking_usaha'];

    protected $guarded = ['id'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class, 'loan_application_id');
    }

    /** Seluruh kolom yang dikelola form. */
    public static function columns(): array
    {
        return [
            ...array_keys(self::SCORES),
            ...array_keys(self::CHOICES),
            ...self::TEXTS,
            ...self::NOTES,
        ];
    }
}
