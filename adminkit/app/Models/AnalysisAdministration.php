<?php

namespace App\Models;

use App\Models\Concerns\TracksAuthor;
use Illuminate\Database\Eloquent\Model;

/** Administrasi (bagian 8): rincian biaya yang ditanggung nasabah. */
class AnalysisAdministration extends Model
{
    use TracksAuthor;

    public const FEES = [
        'administrasi', 'provisi', 'materai',
        'asuransi_jiwa_menurun1', 'asuransi_jiwa_menurun2', 'asuransi_jiwa_menurun3',
        'asuransi_jiwa_tetap1', 'asuransi_jiwa_tetap2', 'asuransi_jiwa',
        'asuransi_kendaraan_motor', 'transaksi_kredit', 'proses_shm',
        'polis_materai', 'pajak_stnk', 'proses_apht', 'by_fiducia',
    ];

    protected $guarded = ['id'];

    public function total(): int
    {
        return collect(self::FEES)->sum(fn ($c) => (int) $this->{$c});
    }
}
