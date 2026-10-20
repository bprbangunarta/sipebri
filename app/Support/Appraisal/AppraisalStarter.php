<?php

namespace App\Support\Appraisal;

use App\Models\AppraisalEntry;
use App\Models\Collateral;
use App\Models\CollateralAppraisal;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Starts a new draft appraisal of a collateral. A collateral has one open draft at a time, and appraisals are numbered in order.
 */
final class AppraisalStarter
{
    /** Sections whose answers are never copied from an earlier appraisal: each appraisal must check them again. */
    public const NOT_COPIED = ['legal', 'independence'];

    /**
     * @throws RuntimeException when the collateral already has a draft
     */
    public static function start(Collateral $collateral, User $by, string $purpose, ?LoanApplication $loan, ?CollateralAppraisal $copyFrom): CollateralAppraisal
    {
        return DB::transaction(function () use ($collateral, $by, $purpose, $loan, $copyFrom): CollateralAppraisal {
            // The collateral row is the lock that serialises two people starting an appraisal at the same moment.
            Collateral::query()->whereKey($collateral->id)->lockForUpdate()->first();

            if ($collateral->appraisals()->where('status', CollateralAppraisal::DRAFT)->exists()) {
                throw new RuntimeException('Jaminan ini masih punya penilaian yang belum difinalkan.');
            }

            $template = AppraisalTemplates::for($collateral);

            if (($missing = $template->missingFacts()) !== null) {
                throw new RuntimeException($missing);
            }

            $appraisal = CollateralAppraisal::create([
                'collateral_id' => $collateral->id,
                'sequence' => (int) $collateral->appraisals()->max('sequence') + 1,
                'template' => $template->key(),
                'template_version' => $template->version(),
                'variant' => $template->variant(),
                'purpose' => $purpose,
                'credit_ceiling' => 0,
                'market_value' => 0,
                'liquidation_value' => 0,
                'appraiser_name' => $by->name,
                'created_by' => $by->id,
                ...self::fromLoan($loan),
            ]);

            if ($copyFrom !== null) {
                self::copy($copyFrom, $appraisal);
            }

            AppraisalFigures::refresh($appraisal, $template);

            return $appraisal;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private static function fromLoan(?LoanApplication $loan): array
    {
        if ($loan === null) {
            return [];
        }

        $loan->loadMissing('product:id,alias,name');

        return [
            'loan_application_id' => $loan->id,
            'debtor_name' => $loan->full_name,
            'debtor_cif' => $loan->cif_number,
            'product_label' => $loan->product ? "{$loan->product->alias} : {$loan->product->name}" : null,
            'credit_ceiling' => (int) $loan->requested_amount,
        ];
    }

    /** The answers of an earlier appraisal become the starting point of a review; photos and the checks stay behind. */
    private static function copy(CollateralAppraisal $from, CollateralAppraisal $to): void
    {
        $rows = $from->entries()->whereNotIn('section', self::NOT_COPIED)->get()->map(fn (AppraisalEntry $entry): array => [
            'collateral_appraisal_id' => $to->id, 'section' => $entry->section, 'item' => $entry->item, 'value' => $entry->value, 'note' => $entry->note,
            'created_at' => now(), 'updated_at' => now(),
        ])->all();

        if ($rows !== []) {
            // A bulk insert raises no model events; the copy is recorded by the controller as one entry.
            AppraisalEntry::query()->insert($rows);
        }

        $to->update($from->only(['owner_address', 'owner_relation', 'owner_contact', 'reviewer_name', 'inspection']));
    }
}
