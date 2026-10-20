<?php

namespace App\Support\Appraisal;

use App\Models\CollateralAppraisal;

/**
 * Blocks that every template shares: who and what is appraised, loan-to-value, and the conclusion.
 */
final class CommonSections
{
    /**
     * @param  array<string, string>  $inspections
     * @return list<array<string, mixed>>
     */
    public static function identity(array $inspections): array
    {
        return [
            Fields::choice('h:purpose', 'Tujuan penilaian', CollateralAppraisal::PURPOSES),
            Fields::choice('h:inspection', 'Jenis inspeksi', $inspections),
            Fields::item('h:debtor_name', 'Nama debitur', 'text'),
            Fields::item('h:debtor_cif', 'CIF', 'text'),
            Fields::item('h:product_label', 'Produk', 'text'),
            Fields::item('h:credit_ceiling', 'Plafon', 'money'),
            Fields::item('h:owner_address', 'Alamat pemilik', 'text'),
            Fields::item('h:owner_relation', 'Hubungan dengan debitur', 'text'),
            Fields::item('h:owner_contact', 'Nomor kontak', 'text'),
            Fields::item('h:appraiser_name', 'Appraiser/petugas', 'text'),
            Fields::item('h:reviewer_name', 'Reviewer', 'text'),
            Fields::item('h:effective_date', 'Tanggal efektif nilai', 'date'),
            Fields::item('h:report_date', 'Tanggal laporan', 'date'),
        ];
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    public static function comparableColumns(): array
    {
        return [['key' => 'c1', 'label' => 'Pembanding 1'], ['key' => 'c2', 'label' => 'Pembanding 2'], ['key' => 'c3', 'label' => 'Pembanding 3']];
    }

    /**
     * @param  array<string, string>  $scenarios  row key => label (the extra "execution cost" row is added here)
     * @return list<array<string, mixed>>
     */
    public static function loanToValue(array $scenarios): array
    {
        $rows = [];

        foreach ($scenarios as $key => $label) {
            $rows[] = ['key' => $key, 'label' => $label];
        }

        $rows[] = ['key' => 'exec', 'label' => 'Setelah biaya eksekusi/penjualan'];

        return [
            ['layout' => 'fields', 'items' => [
                Fields::item('ltv.ceiling', 'Plafon kredit', 'computed', ['format' => 'money', 'hint' => 'Diisi di bagian A.']),
                Fields::item('ltv.liquidation', 'Nilai likuidasi diakui', 'computed', ['format' => 'money']),
                Fields::item('ltv.ltv', 'LTV: plafon ÷ nilai likuidasi', 'computed', ['format' => 'percent']),
                Fields::item('ltv.coverage', 'Coverage: nilai likuidasi ÷ plafon', 'computed', ['format' => 'percent']),
                Fields::item('ltv.execution_cost', 'Biaya eksekusi/penjualan', 'money'),
            ]],
            ['layout' => 'grid', 'rows' => $rows, 'columns' => [
                ['key' => 'value', 'label' => 'Nilai agunan', 'computed' => true, 'format' => 'money'],
                ['key' => 'ltv', 'label' => 'LTV', 'computed' => true, 'format' => 'percent'],
                ['key' => 'status', 'label' => 'Status (M memenuhi, TM tidak)', 'computed' => true, 'format' => 'text'],
            ]],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function conclusion(): array
    {
        return [
            ['layout' => 'fields', 'items' => [
                Fields::item('conclusion.summary', 'Deskripsi singkat objek', 'textarea'),
                Fields::item('conclusion.strengths', 'Kekuatan utama', 'textarea'),
                Fields::item('conclusion.weaknesses', 'Kelemahan/risiko utama', 'textarea'),
                Fields::item('conclusion.assumptions', 'Asumsi dan kondisi pembatas', 'textarea'),
                Fields::item('conclusion.legal', 'Kecukupan legalitas', 'textarea'),
                Fields::item('conclusion.physical', 'Kecukupan fisik/marketability', 'textarea'),
                Fields::item('conclusion.market_value', 'Nilai pasar', 'computed', ['format' => 'money']),
                Fields::item('conclusion.liquidation_value', 'Nilai likuidasi', 'computed', ['format' => 'money']),
                Fields::item('conclusion.ltv', 'LTV', 'computed', ['format' => 'percent']),
                Fields::item('conclusion.coverage', 'Coverage', 'computed', ['format' => 'percent']),
                Fields::item('conclusion.mitigation', 'Mitigasi/syarat', 'textarea'),
                Fields::item('h:reappraisal_frequency', 'Frekuensi penilaian ulang', 'text'),
            ]],
            ['layout' => 'fields', 'items' => [
                Fields::choice('h:decision', 'Status agunan', CollateralAppraisal::DECISIONS),
                Fields::item('h:credit_basis_value', 'Dasar nilai untuk kredit', 'money'),
                Fields::item('h:main_conditions', 'Syarat utama', 'textarea'),
                Fields::item('h:valid_until', 'Masa berlaku penilaian sampai tanggal', 'date'),
            ]],
        ];
    }
}
