<?php

namespace App\Support\Appraisal;

/**
 * The form for every collateral that has no form of its own yet: gold, savings and deposits, shares, stock, documents,
 * and the like. It records who and what, the legal checks, the value, loan-to-value, risks, and the conclusion. A kind that gets
 * its own form from the management gets its own template; this one keeps covering the rest.
 */
final class GeneralTemplate implements AppraisalTemplate
{
    public const KEY = 'general';

    public const STRESS = ['s10' => 10.0, 's20' => 20.0, 's30' => 30.0];

    public function key(): string
    {
        return self::KEY;
    }

    public function version(): int
    {
        return 1;
    }

    public function hardStops(): array
    {
        return ['legal.original_documents', 'legal.identity', 'legal.no_dispute', 'legal.no_blockade', 'legal.not_pledged', 'legal.can_be_bound'];
    }

    public function sections(): array
    {
        return [
            ['key' => 'identity', 'label' => 'A. Identitas penilaian', 'blocks' => [['layout' => 'fields', 'items' => CommonSections::identity(['full' => 'Penuh', 'desktop' => 'Desktop'])]]],
            ['key' => 'object', 'label' => 'B. Objek', 'blocks' => [['layout' => 'fields', 'items' => [
                Fields::item('object.description', 'Deskripsi objek', 'textarea'),
                Fields::item('object.location', 'Lokasi/penyimpanan', 'text'),
                Fields::item('object.documents', 'Dokumen/bukti kepemilikan', 'textarea'),
            ]]]],
            ['key' => 'legal', 'label' => 'C. Pemeriksaan legal', 'blocks' => [['layout' => 'checks', 'items' => Fields::many('yesno', [
                'original_documents' => 'Dokumen asli telah dilihat', 'identity' => 'Identitas pemilik sesuai', 'no_dispute' => 'Tidak terdapat sengketa',
                'no_blockade' => 'Tidak terdapat blokir/sita', 'not_pledged' => 'Tidak dijaminkan kepada pihak lain', 'can_be_bound' => 'Dapat diikat sesuai jenis agunan',
                'consent' => 'Persetujuan pasangan/pemilik tersedia', 'third_party' => 'Agunan pihak ketiga dianalisa',
            ], 'legal.')]]],
            ['key' => 'value', 'label' => 'D. Penetapan nilai', 'blocks' => [['layout' => 'fields', 'items' => [
                Fields::item('value.basis', 'Dasar penilaian dan sumber data', 'textarea'),
                Fields::item('value.market_value', 'Nilai pasar', 'money'),
                Fields::item('h:haircut_percent', 'Haircut nilai likuidasi (%)', 'percent'),
                Fields::item('value.haircut_basis', 'Dasar haircut', 'text'),
                Fields::item('value.liquidation_value', 'Nilai likuidasi', 'computed', ['format' => 'money']),
                Fields::item('h:forced_sale_value', 'Nilai jual cepat/forced sale (jika digunakan)', 'money'),
                Fields::item('h:rounded_value', 'Pembulatan nilai', 'money'),
            ]]]],
            ['key' => 'ltv', 'label' => 'E. LTV dan kecukupan agunan', 'blocks' => CommonSections::loanToValue(['s10' => 'Nilai turun 10%', 's20' => 'Nilai turun 20%', 's30' => 'Nilai turun 30%'])],
            ['key' => 'risks', 'label' => 'F. Penilaian risiko agunan', 'blocks' => [['layout' => 'ratings', 'items' => Fields::many('risk', [
                'legal' => 'Legalitas dan kepemilikan', 'binding' => 'Pengikatan', 'marketability' => 'Marketability dan waktu jual', 'volatility' => 'Volatilitas nilai',
                'custody' => 'Penyimpanan dan pengamanan', 'third_party' => 'Agunan pihak ketiga',
            ], 'risks.')]]],
            ['key' => 'binding', 'label' => 'G. Pengikatan, asuransi, dan syarat', 'blocks' => [['layout' => 'fields', 'items' => Fields::many('text', [
                'kind' => 'Jenis pengikatan', 'insurance' => 'Asuransi', 'custody' => 'Penyimpanan agunan/dokumen', 'pre_disbursement' => 'Syarat pra-pencairan', 'monitoring' => 'Syarat monitoring berkala',
            ], 'binding.')]]],
            ['key' => 'photos', 'label' => 'H. Dokumentasi', 'blocks' => [['layout' => 'photos', 'items' => Fields::many('photo', [
                'object' => 'Foto objek', 'documents' => 'Foto dokumen', 'other' => 'Bukti lain',
            ], 'photos.')]]],
            ['key' => 'conclusion', 'label' => 'I. Kesimpulan penilai', 'blocks' => CommonSections::conclusion()],
            ['key' => 'independence', 'label' => 'J. Pernyataan independensi', 'blocks' => [['layout' => 'checks', 'items' => Fields::many('yesno', [
                'no_interest' => 'Tidak memiliki kepentingan atas objek/pemilik/debitur', 'no_benefit' => 'Tidak menerima manfaat terkait hasil nilai',
                'not_by_ceiling' => 'Nilai tidak ditentukan oleh kebutuhan plafon',
            ], 'independence.')]]],
        ];
    }

    public function compute(array $values, AppraisalContext $context): Computation
    {
        $market = Amounts::whole($values['value.market_value'] ?? null) ?? 0;
        $liquidation = Haircut::liquidation($market, Amounts::decimal($values['h:haircut_percent'] ?? null));
        $loanToValue = LoanToValue::cells($context->ceiling, $liquidation, self::STRESS, Amounts::whole($values['ltv.execution_cost'] ?? null));

        return new Computation($market, $liquidation, $loanToValue + [
            'value.liquidation_value' => $liquidation,
            'ltv.ceiling' => $context->ceiling,
            'ltv.liquidation' => $liquidation,
            'conclusion.market_value' => $market,
            'conclusion.liquidation_value' => $liquidation,
            'conclusion.ltv' => $loanToValue['ltv.ltv'],
            'conclusion.coverage' => $loanToValue['ltv.coverage'],
        ]);
    }
}
