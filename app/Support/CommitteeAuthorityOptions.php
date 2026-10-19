<?php

namespace App\Support;

use App\Models\CommitteePath;
use App\Models\Product;

/**
 * What the "Cek wewenang" dialog needs to offer: the products, the conditions that are valid per product, and the committee
 * members that can stand in as applicant. Every tab of Data Komite carries the dialog, so every tab passes the same options.
 */
class CommitteeAuthorityOptions
{
    /**
     * @return array<string, mixed>
     */
    public static function props(): array
    {
        return [
            'productOptions' => Product::orderBy('code')->get()->map(fn (Product $p): array => ['value' => $p->id, 'label' => "{$p->alias} · {$p->name}"]),
            // Valid conditions per product: the product's own plus cross-product ones (e.g. RELOAN).
            'conditionMap' => CommitteePath::where('is_active', true)->get()
                ->groupBy(fn (CommitteePath $p) => $p->product_id ?? 'global')
                ->map(fn ($paths) => $paths->map(fn (CommitteePath $p): string => (string) $p->condition)->unique()->sort()->values()),
            'committeeMembers' => CommitteeMembers::options(),
        ];
    }
}
