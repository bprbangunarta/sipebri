<?php

namespace App\Support\Appraisal;

use App\Models\AppraisalPhoto;
use App\Models\Collateral;
use App\Models\CollateralAppraisal;
use App\Models\CollateralType;

/**
 * What the appraisal screens are given: the form, the answers, the figures the system worked out, how far each section is,
 * and what still stops the appraisal from being finalised.
 */
final class AppraisalPayload
{
    /** The fixed facts of a collateral, in the order they are shown: column, label, how to show it. */
    private const FACTS = [
        ['document_number', 'Nomor dokumen', 'text'], ['owner_name', 'Nama pemilik', 'text'], ['owner_address', 'Alamat agunan', 'text'], ['region_label', 'Kabupaten/kota', 'text'],
        ['province', 'Provinsi', 'text'], ['postal_code', 'Kode pos', 'text'], ['route_hint', 'Patokan/rute', 'text'],
        ['right_kind', 'Jenis hak', 'right'], ['right_holder_name', 'Pemegang hak', 'text'], ['right_issued_at', 'Tanggal terbit', 'date'], ['right_expires_at', 'Tanggal berakhir hak', 'date'],
        ['survey_document', 'Surat ukur/gambar situasi', 'text'], ['survey_document_date', 'Tanggal surat ukur', 'date'], ['certificate_land_area', 'Luas menurut sertipikat (m²)', 'integer'],
        ['land_office', 'Kantor pertanahan', 'text'], ['parcel_number', 'NIB', 'text'], ['acquisition_origin', 'Asal perolehan', 'text'], ['tax_object_number', 'NOP PBB', 'text'],
        ['building_permit_number', 'Nomor PBG/IMB', 'text'], ['building_permit_date', 'Tanggal PBG/IMB', 'date'], ['operating_certificate_number', 'Nomor SLF', 'text'],
        ['operating_certificate_date', 'Tanggal SLF', 'date'], ['permitted_building_area', 'Luas bangunan menurut izin (m²)', 'integer'], ['permitted_use', 'Peruntukan dalam izin', 'text'],
    ];

    /**
     * @return array<string, mixed>
     */
    public static function show(CollateralAppraisal $appraisal, AppraisalTemplate $template, bool $canManage): array
    {
        $appraisal->load('entries', 'photos', 'collateral');
        $values = AppraisalValues::of($appraisal);
        $computation = $template->compute($values, AppraisalValues::context($appraisal));
        $items = AppraisalTemplates::items($template);

        return [
            'appraisal' => self::summary($appraisal) + ['loan_linked' => $appraisal->loan_application_id !== null],
            'collateral' => self::collateral($appraisal->collateral),
            'template' => ['key' => $template->key(), 'version' => $template->version(), 'sections' => $template->sections()],
            'values' => $values,
            'notes' => AppraisalValues::notes($appraisal),
            'computed' => $computation->cells,
            'progress' => self::progress($items, $values, $appraisal),
            'photos' => $appraisal->photos->groupBy('item')->map(fn ($photos) => $photos->map(fn (AppraisalPhoto $photo): array => ['id' => $photo->id, 'url' => $photo->url(), 'created_by' => $photo->created_by])->values())->all(),
            'locked' => $appraisal->loan_application_id !== null ? array_map(fn (string $column): string => "h:$column", AppraisalSaver::LOCKED_BY_LOAN) : [],
            'blockers' => $appraisal->isFinal() ? [] : AppraisalFinalisation::blockers($appraisal, $template),
            'maxPhotos' => (int) config('credit.max_appraisal_photos'),
            'canEdit' => $canManage && ! $appraisal->isFinal(),
        ];
    }

    /**
     * One line of the history of a collateral.
     *
     * @return array<string, mixed>
     */
    public static function summary(CollateralAppraisal $appraisal): array
    {
        return [
            'id' => $appraisal->id,
            'sequence' => $appraisal->sequence,
            'status' => $appraisal->status,
            'purpose' => $appraisal->purpose,
            'purpose_label' => CollateralAppraisal::PURPOSES[$appraisal->purpose] ?? $appraisal->purpose,
            'effective_date' => $appraisal->effective_date?->toDateString(),
            'valid_until' => $appraisal->valid_until?->toDateString(),
            'appraiser_name' => $appraisal->appraiser_name,
            'market_value' => $appraisal->market_value,
            'liquidation_value' => $appraisal->liquidation_value,
            'decision' => $appraisal->decision,
            'decision_label' => CollateralAppraisal::DECISIONS[$appraisal->decision] ?? null,
            'finalized_at' => $appraisal->finalized_at?->toIso8601String(),
            'template' => $appraisal->template,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function collateral(Collateral $collateral): array
    {
        $facts = [['label' => 'Jenis agunan', 'value' => CollateralType::query()->where('code', $collateral->collateral_type_code)->value('name') ?? $collateral->collateral_type_code, 'type' => 'text']];

        foreach (self::FACTS as [$column, $label, $type]) {
            $value = $collateral->getAttribute($column);

            if ($value !== null && $value !== '') {
                $facts[] = ['label' => $label, 'type' => $type === 'right' ? 'text' : $type, 'value' => match (true) {
                    $type === 'right' => Collateral::RIGHT_KINDS[$value] ?? $value,
                    $type === 'date' => $value->toDateString(),
                    default => $value,
                }];
            }
        }

        return ['id' => $collateral->id, 'label' => $collateral->cbs_id ?? "Jaminan #{$collateral->id}", 'facts' => $facts];
    }

    /**
     * How many items of each section are answered (or have a photo).
     *
     * @param  list<array<string, mixed>>  $items
     * @param  array<string, string>  $values
     * @return array<string, array{0: int, 1: int}>
     */
    private static function progress(array $items, array $values, CollateralAppraisal $appraisal): array
    {
        $withPhotos = $appraisal->photos->pluck('item')->unique()->all();
        $progress = [];

        foreach ($items as $item) {
            $counts = $item['type'] === 'photo' || AppraisalRules::answerable($item);

            if (! $counts) {
                continue;
            }

            $progress[$item['section']] ??= [0, 0];
            $progress[$item['section']][1]++;
            $progress[$item['section']][0] += isset($values[$item['key']]) || in_array($item['key'], $withPhotos, true) ? 1 : 0;
        }

        return $progress;
    }
}
