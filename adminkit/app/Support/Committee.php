<?php

namespace App\Support;

use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\User;

/**
 * Menentukan jalur & pemutus komite untuk kombinasi produk, kondisi/kategori,
 * dan plafon. Murni membaca aturan (tidak menyentuh data berkas) sehingga bisa
 * dipakai untuk simulasi maupun nanti oleh alur pengajuan kredit.
 */
class Committee
{
    /** @return array<string, mixed> */
    public static function resolve(?int $productId, ?string $condition, int $amount): array
    {
        $condition = filled($condition) ? mb_strtoupper(trim($condition)) : null;
        $path = self::path($productId, $condition);

        if (! $path) {
            return [
                'found' => false,
                'message' => $condition === null
                    ? 'Belum ada jalur komite untuk produk ini pada kondisi Normal.'
                    : "Kondisi/kategori {$condition} bukan peruntukan produk ini — belum ada jalur komitenya.",
                'chain' => [],
                'warnings' => [],
            ];
        }

        $chain = $path->mechanism === 'plafon'
            ? self::plafonChain($path, $amount)
            : self::hierarkiChain($path);

        return [
            'found' => true,
            'path' => [
                'id' => $path->id,
                'title' => $path->title(),
                'product_label' => $path->product ? "{$path->product->alias} — {$path->product->name}" : 'Semua Produk',
                'condition_label' => $path->condition ?: 'Normal',
                'mechanism' => $path->mechanism,
                'mechanism_label' => CommitteePath::MECHANISMS[$path->mechanism] ?? $path->mechanism,
                'matched_globally' => $path->product_id === null && $productId !== null,
            ],
            'chain' => $chain,
            'decider' => collect($chain)->firstWhere('status', 'decider'),
            'warnings' => self::warnings($path, $amount, $chain),
        ];
    }

    /**
     * Kondisi/kategori dicari apa adanya: jalur produk lebih dulu, lalu jalur
     * lintas produk (mis. RELOAN). TIDAK ada penurunan ke jalur Normal — supaya
     * kombinasi yang bukan peruntukannya (mis. KRU + PERLELEAN) tidak terhitung.
     */
    private static function path(?int $productId, ?string $condition): ?CommitteePath
    {
        $candidates = [$productId, null];

        foreach ($candidates as $candidateProduct) {
            if ($candidateProduct === null && $productId !== null && $condition === null) {
                continue;
            }

            $path = CommitteePath::with(['product', 'tiers'])
                ->where('is_active', true)
                ->where('product_id', $candidateProduct)
                ->where(fn ($q) => $condition === null
                    ? $q->whereNull('condition')
                    : $q->where('condition', $condition))
                ->first();

            if ($path && $path->tiers->isNotEmpty()) {
                return $path;
            }
        }

        return null;
    }

    /** @return array<int, array<string, mixed>> */
    private static function plafonChain(CommitteePath $path, int $amount): array
    {
        $deciderFound = false;

        return $path->tiers->map(function (CommitteeTier $tier) use ($amount, &$deciderFound) {
            $min = $tier->min_amount ?? 0;
            $max = $tier->max_amount;
            $inRange = $amount >= $min && ($max === null || $amount <= $max);

            if ($inRange && ! $deciderFound && self::canDecide($tier)) {
                $deciderFound = true;
                $status = 'decider';
            } elseif (! $deciderFound && $max !== null && $amount > $max) {
                $status = $tier->can_escalate ? 'escalate' : 'blocked';
            } else {
                $status = 'not_needed';
            }

            return self::row($tier, $status);
        })->all();
    }

    /** @return array<int, array<string, mixed>> */
    private static function hierarkiChain(CommitteePath $path): array
    {
        $lastDecider = $path->tiers->last(fn (CommitteeTier $t) => self::canDecide($t));

        return $path->tiers->map(fn (CommitteeTier $tier) => self::row(
            $tier,
            $lastDecider && $tier->is($lastDecider)
                ? 'decider'
                : ($tier->can_escalate ? 'escalate' : 'blocked'),
        ))->all();
    }

    private static function canDecide(CommitteeTier $tier): bool
    {
        return $tier->can_approve || $tier->can_cancel || $tier->can_reject;
    }

    /** @return array<string, mixed> */
    private static function row(CommitteeTier $tier, string $status): array
    {
        $labels = [
            'decider' => 'Pemutus',
            'escalate' => 'Naik Komite',
            'blocked' => 'Tidak dapat memutus / menaikkan',
            'not_needed' => 'Tidak diperlukan',
        ];

        return [
            'id' => $tier->id,
            'sort' => $tier->sort,
            'label' => $tier->label,
            'role' => $tier->role,
            'min_amount' => $tier->min_amount,
            'max_amount' => $tier->max_amount,
            'decisions' => collect([
                'Naik Komite' => $tier->can_escalate,
                'Disetujui' => $tier->can_approve,
                'Dibatalkan' => $tier->can_cancel,
                'Ditolak' => $tier->can_reject,
            ])->filter()->keys()->all(),
            'status' => $status,
            'status_label' => $labels[$status],
            'user_count' => User::role($tier->role)->count(),
        ];
    }

    /** @return array<int, string> */
    private static function warnings(CommitteePath $path, int $amount, array $chain): array
    {
        $warnings = [];

        if (! collect($chain)->contains(fn ($row) => $row['status'] === 'decider')) {
            $warnings[] = 'Tidak ada jenjang yang berwenang memutus pada plafon ini — periksa batas plafon tiap jenjang.';
        }

        foreach ($chain as $row) {
            if ($row['status'] === 'decider' && $row['user_count'] === 0) {
                $warnings[] = "Belum ada pengguna aktif dengan peranan {$row['role']}, sehingga tidak ada yang dapat memutus.";
            }
        }

        if ($path->mechanism !== 'plafon') {
            return $warnings;
        }

        $ranges = $path->tiers
            ->filter(fn (CommitteeTier $t) => self::canDecide($t))
            ->sortBy(fn (CommitteeTier $t) => $t->min_amount ?? 0)
            ->values();

        foreach ($ranges as $i => $tier) {
            $next = $ranges[$i + 1] ?? null;

            if (! $next || $tier->max_amount === null) {
                continue;
            }

            $nextMin = $next->min_amount ?? 0;

            if ($nextMin > $tier->max_amount + 1) {
                $warnings[] = "Ada celah plafon antara {$tier->role} dan {$next->role} (".
                    number_format($tier->max_amount + 1, 0, ',', '.').' – '.
                    number_format($nextMin - 1, 0, ',', '.').').';
            }

            if ($nextMin <= $tier->max_amount) {
                $warnings[] = "Rentang plafon {$tier->role} dan {$next->role} saling bertumpuk.";
            }
        }

        return $warnings;
    }
}
