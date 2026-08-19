<?php

namespace App\Http\Controllers;

use App\Http\Requests\Committee\StorePathRequest;
use App\Http\Requests\Committee\StoreTierRequest;
use App\Models\ActivityLog;
use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\Product;
use App\Support\Excel;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Aturan komite kredit. Satu **jalur** = produk (atau semua produk) + kondisi/
 * kategori, dengan **mekanisme** kewenangan plafon atau hierarki komite.
 * Tiap jalur berisi **jenjang** berurutan: peranan pemutus, batas plafon, dan
 * keputusan yang diizinkan.
 */
class CommitteeController extends Controller
{
    private const MODULE = 'Komite Kredit';

    public function index(): Response
    {
        return Inertia::render('Committees', [
            'paths' => CommitteePath::with('product')->withCount('tiers')
                ->orderBy('product_id')->orderBy('condition')
                ->get()
                ->map(fn (CommitteePath $p) => $this->pathRow($p))
                ->all(),
            'productOptions' => $this->productOptions(),
            'pathOptions' => CommitteePath::with('product')->has('tiers')->get()
                ->map(fn (CommitteePath $p) => ['value' => (string) $p->id, 'label' => $p->title()])
                ->all(),
        ]);
    }

    public function show(CommitteePath $path): Response
    {
        $path->load(['product', 'tiers']);

        return Inertia::render('CommitteeDetail', [
            'path' => [
                ...$this->pathRow($path),
                'tiers' => $path->tiers->map(fn (CommitteeTier $t) => [
                    'id' => $t->id,
                    'sort' => $t->sort,
                    'label' => $t->label,
                    'role' => $t->role,
                    'min_amount' => $t->min_amount,
                    'max_amount' => $t->max_amount,
                    'can_escalate' => $t->can_escalate,
                    'can_approve' => $t->can_approve,
                    'can_cancel' => $t->can_cancel,
                    'can_reject' => $t->can_reject,
                ])->all(),
            ],
            'roleOptions' => Role::orderBy('name')->pluck('name')
                ->map(fn ($n) => ['value' => $n, 'label' => $n])->all(),
        ]);
    }

    /** Unduh seluruh jalur beserta jenjangnya dalam satu berkas Excel (untuk review). */
    public function export(): StreamedResponse
    {
        $rows = [];

        foreach (CommitteePath::with(['product', 'tiers'])->orderBy('product_id')->orderBy('condition')->get() as $path) {
            $base = [
                $path->product?->alias ?? 'SEMUA',
                $path->product?->name ?? 'Semua Produk',
                $path->condition ?: 'Normal',
                CommitteePath::MECHANISMS[$path->mechanism] ?? $path->mechanism,
                $path->is_active ? 'Aktif' : 'Nonaktif',
            ];

            if ($path->tiers->isEmpty()) {
                $rows[] = [...$base, null, 'Belum ada jenjang', null, null, null, null, $path->note];

                continue;
            }

            foreach ($path->tiers as $index => $tier) {
                $decisions = collect([
                    'Naik Komite' => $tier->can_escalate,
                    'Disetujui' => $tier->can_approve,
                    'Dibatalkan' => $tier->can_cancel,
                    'Ditolak' => $tier->can_reject,
                ])->filter()->keys()->implode(', ');

                $rows[] = [
                    ...$base,
                    $index + 1,
                    $tier->label,
                    $tier->role,
                    $path->mechanism === 'plafon' ? $tier->min_amount : null,
                    $path->mechanism === 'plafon' ? $tier->max_amount : null,
                    $decisions ?: '—',
                    $path->note,
                ];
            }
        }

        ActivityLog::record('Mengekspor aturan komite kredit (Excel)', self::MODULE, 'info');

        return Excel::download(
            Excel::filename('komite-kredit'),
            [
                'Kode Produk', 'Nama Produk', 'Kondisi / Kategori', 'Mekanisme', 'Status Jalur',
                'Urutan', 'Nama Jenjang', 'Peranan Pemutus', 'Plafon Minimal', 'Plafon Maksimal',
                'Keputusan Diizinkan', 'Catatan',
            ],
            $rows,
            'Komite Kredit',
        );
    }

    public function store(StorePathRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $path = CommitteePath::create(collect($data)->except('copy_from')->all());

        if (filled($data['copy_from'] ?? null)) {
            $this->copyTiers((int) $data['copy_from'], $path);
        }

        ActivityLog::record("Menambah jalur komite {$path->title()}", self::MODULE, 'success', $path);

        return to_route('committees.show', $path)->with('success', "Jalur komite {$path->title()} ditambahkan.");
    }

    public function update(StorePathRequest $request, CommitteePath $path): RedirectResponse
    {
        $before = $path->getOriginal();
        $path->update(collect($request->validated())->except('copy_from')->all());

        ActivityLog::record(
            "Memperbarui jalur komite {$path->title()}",
            self::MODULE,
            'info',
            $path,
            ActivityLog::diffOf($path, $before),
        );

        return back()->with('success', "Jalur komite {$path->title()} diperbarui.");
    }

    public function destroy(CommitteePath $path): RedirectResponse
    {
        $title = $path->title();
        $path->delete();

        ActivityLog::record("Menghapus jalur komite {$title}", self::MODULE, 'warning');

        return to_route('committees.index')->with('success', "Jalur komite {$title} dihapus.");
    }

    public function storeTier(StoreTierRequest $request, CommitteePath $path): RedirectResponse
    {
        $tier = $path->tiers()->create([
            ...$request->validated(),
            'sort' => (int) $path->tiers()->max('sort') + 1,
        ]);

        ActivityLog::record(
            "Menambah jenjang {$tier->role} pada jalur {$path->title()}",
            self::MODULE,
            'success',
            $tier,
        );

        return back()->with('success', "Jenjang {$tier->role} ditambahkan.");
    }

    public function updateTier(StoreTierRequest $request, CommitteePath $path, CommitteeTier $tier): RedirectResponse
    {
        $before = $tier->getOriginal();
        $tier->update($request->validated());

        ActivityLog::record(
            "Memperbarui jenjang {$tier->role} pada jalur {$path->title()}",
            self::MODULE,
            'info',
            $tier,
            ActivityLog::diffOf($tier, $before),
        );

        return back()->with('success', "Jenjang {$tier->role} diperbarui.");
    }

    public function destroyTier(CommitteePath $path, CommitteeTier $tier): RedirectResponse
    {
        $role = $tier->role;
        $tier->delete();

        ActivityLog::record("Menghapus jenjang {$role} pada jalur {$path->title()}", self::MODULE, 'warning');

        return back()->with('success', "Jenjang {$role} dihapus.");
    }

    /** Geser jenjang satu langkah ke atas/bawah. */
    public function moveTier(CommitteePath $path, CommitteeTier $tier, string $direction): RedirectResponse
    {
        $tiers = $path->tiers()->get();
        $index = $tiers->search(fn (CommitteeTier $t) => $t->is($tier));
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $tiers->count()) {
            return back();
        }

        $ordered = $tiers->values()->all();
        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        foreach ($ordered as $position => $row) {
            $row->update(['sort' => $position + 1]);
        }

        return back()->with('success', 'Urutan jenjang diperbarui.');
    }

    private function copyTiers(int $sourceId, CommitteePath $target): void
    {
        $source = CommitteePath::with('tiers')->find($sourceId);

        foreach ($source?->tiers ?? [] as $tier) {
            $target->tiers()->create($tier->only([
                'sort', 'label', 'role', 'min_amount', 'max_amount',
                'can_escalate', 'can_approve', 'can_cancel', 'can_reject',
            ]));
        }
    }

    private function pathRow(CommitteePath $path): array
    {
        return [
            'id' => $path->id,
            'product_id' => $path->product_id,
            'product_label' => $path->product
                ? "{$path->product->alias} — {$path->product->name}"
                : 'Semua Produk',
            'condition' => $path->condition,
            'condition_label' => $path->condition ?: 'Normal',
            'mechanism' => $path->mechanism,
            'mechanism_label' => CommitteePath::MECHANISMS[$path->mechanism] ?? $path->mechanism,
            'is_active' => $path->is_active,
            'status_label' => $path->is_active ? 'Aktif' : 'Nonaktif',
            'note' => $path->note,
            'tiers_count' => $path->tiers_count ?? $path->tiers()->count(),
            'title' => $path->title(),
        ];
    }

    /** @return array<int, array{value: string, label: string}> */
    private function productOptions(): array
    {
        return [
            ['value' => '', 'label' => 'Semua Produk'],
            ...Product::orderBy('code')->get()
                ->map(fn (Product $p) => [
                    'value' => (string) $p->id,
                    'label' => "{$p->alias} — {$p->name}",
                ])->all(),
        ];
    }
}
