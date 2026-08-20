<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Contoh data agunan. Tidak ada perhitungan di sini — nilai disimpan apa adanya
 * lalu diteruskan ke CBS lewat API (lihat toCbsPayload()).
 */
class CollateralSimulation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'value_guarantee' => 'integer',
        'value_adjustment' => 'integer',
        'value_fair' => 'integer',
        'value_njop' => 'integer',
        'value_appraisal' => 'integer',
        'value_independent' => 'integer',
        'appraised_at' => 'date:Y-m-d',
        'independent_appraised_at' => 'date:Y-m-d',
        'condition_date' => 'date:Y-m-d',
        'insurance_start_date' => 'date:Y-m-d',
    ];

    /** Bentuk payload POST agunan sesuai kontrak API CBS. */
    public function toCbsPayload(): array
    {
        return [
            // Kolom no_rek & kepemilikan tetap ada di kontrak CBS namun tidak lagi disimpan SIPEBRI.
            'no_rek' => '',
            'kepemilikan' => '',
            'keterangan' => (string) ($this->description ?? ''),
            'pemilik_nama' => (string) ($this->owner_name ?? ''),
            'pemilik_alamat' => (string) ($this->owner_address ?? ''),
            'lokasi' => (string) ($this->region_code ?? ''),
            'asuransi' => $this->insured,
            'startdate' => $this->insurance_start_date?->format('Y-m-d') ?? '',
            'peringkat_sb' => (string) ($this->securities_rank ?? ''),
            'pemeringkat_sb' => (string) ($this->rating_agency ?? ''),
            'jenis' => $this->collateral_type_code,
            'jenis_pengikat' => (string) ($this->binding_type_code ?? ''),
            'nilai' => [
                'njop' => $this->value_njop,
                'jaminan' => $this->value_guarantee,
                'adjust' => $this->value_adjustment,
                'wajar' => $this->value_fair,
                'taksasi' => $this->value_appraisal,
                'independen' => $this->value_independent,
            ],
            'penaksir' => [
                'taksasi' => [
                    'penaksir' => (string) ($this->appraiser_name ?? ''),
                    'tanggal' => $this->appraised_at?->format('Y-m-d') ?? '',
                ],
                'independen' => [
                    'penaksir' => (string) ($this->independent_appraiser_name ?? ''),
                    'tanggal' => $this->independent_appraised_at?->format('Y-m-d') ?? '',
                ],
            ],
            'ppap' => (int) $this->ppap_code,
        ];
    }
}
