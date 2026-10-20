<?php

namespace App\Support\Appraisal;

use App\Audit\Audit;
use App\Models\AppraisalEntry;
use App\Models\CollateralAppraisal;
use Illuminate\Support\Facades\DB;

/**
 * Saves the answers of one section. Only what changed is written, and the whole section is one entry on the audit trail
 * (a form of seventy answers must not become seventy entries); saving a section that was not touched writes nothing.
 */
final class AppraisalSaver
{
    /** Columns taken from the loan file while the appraisal is tied to one. */
    public const LOCKED_BY_LOAN = ['debtor_name', 'debtor_cif', 'product_label', 'credit_ceiling'];

    /**
     * @param  array<string, array{value: ?string, note: ?string}>  $answers  by item key; items not listed stay as they are
     */
    public static function save(CollateralAppraisal $appraisal, AppraisalTemplate $template, string $section, array $answers): bool
    {
        $items = collect(AppraisalTemplates::items($template))->where('section', $section)->filter(fn (array $item): bool => AppraisalRules::answerable($item))->keyBy('key');
        $changes = DB::transaction(function () use ($appraisal, $items, $answers): array {
            $header = self::header($appraisal, $items->all(), $answers);
            $entries = self::entries($appraisal, $items->all(), $answers);

            return ['old' => $header['old'] + $entries['old'], 'new' => $header['new'] + $entries['new']];
        });

        if ($changes['new'] === [] && $changes['old'] === []) {
            return false;
        }

        Audit::record('collateral_appraisals.section_saved', 'collateral_appraisals', 'section_saved', $appraisal, $changes['old'], $changes['new'], ['section' => $section]);
        AppraisalFigures::refresh($appraisal, $template);

        return true;
    }

    /**
     * @param  array<string, array<string, mixed>>  $items
     * @param  array<string, array{value: ?string, note: ?string}>  $answers
     * @return array{old: array<string, mixed>, new: array<string, mixed>}
     */
    private static function header(CollateralAppraisal $appraisal, array $items, array $answers): array
    {
        $old = $new = $attributes = [];

        foreach ($items as $key => $item) {
            $column = str_starts_with($key, 'h:') ? substr($key, 2) : null;

            if ($column === null || ! array_key_exists($key, $answers) || ($appraisal->loan_application_id !== null && in_array($column, self::LOCKED_BY_LOAN, true))) {
                continue;
            }

            $value = self::cast($item['type'], $answers[$key]['value']);
            $current = $appraisal->getAttribute($column);
            $current = $current instanceof \DateTimeInterface ? $current->format('Y-m-d') : $current;

            if ((string) $current !== (string) $value) {
                $old[$key] = $current;
                $new[$key] = $value;
                $attributes[$column] = $value;
            }
        }

        if ($attributes !== []) {
            $appraisal->update($attributes);
        }

        return ['old' => $old, 'new' => $new];
    }

    /**
     * @param  array<string, array<string, mixed>>  $items
     * @param  array<string, array{value: ?string, note: ?string}>  $answers
     * @return array{old: array<string, mixed>, new: array<string, mixed>}
     */
    private static function entries(CollateralAppraisal $appraisal, array $items, array $answers): array
    {
        $existing = $appraisal->entries()->get()->keyBy('item');
        $old = $new = $rows = $removed = [];

        foreach ($items as $key => $item) {
            if (str_starts_with($key, 'h:') || ! array_key_exists($key, $answers)) {
                continue;
            }

            $value = $answers[$key]['value'] ?? null;
            $note = $answers[$key]['note'] ?? null;
            $before = $existing->get($key);

            if ($before !== null && (string) $before->value === (string) $value && (string) $before->note === (string) $note) {
                continue;
            }

            $old[$key] = $before?->value;
            $new[$key] = $value;

            if ($note !== null || $before?->note !== null) {
                $old["$key.note"] = $before?->note;
                $new["$key.note"] = $note;
            }

            if ($value === null && $note === null) {
                $removed[] = $key;
            } else {
                $rows[] = ['collateral_appraisal_id' => $appraisal->id, 'section' => $item['section'], 'item' => $key, 'value' => $value, 'note' => $note, 'created_at' => now(), 'updated_at' => now()];
            }
        }

        // Bulk writes raise no model events; the section is recorded as one entry by save().
        if ($rows !== []) {
            AppraisalEntry::query()->upsert($rows, ['collateral_appraisal_id', 'item'], ['section', 'value', 'note', 'updated_at']);
        }

        if ($removed !== []) {
            AppraisalEntry::query()->where('collateral_appraisal_id', $appraisal->id)->whereIn('item', $removed)->delete();
        }

        return ['old' => $old, 'new' => $new];
    }

    private static function cast(string $type, ?string $value): int|string|null
    {
        if ($value === null) {
            return null;
        }

        return in_array($type, ['money', 'integer'], true) ? (int) $value : $value;
    }
}
