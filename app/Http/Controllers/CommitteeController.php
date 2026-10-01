<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommitteePathRequest;
use App\Http\Requests\CommitteeTierRequest;
use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\Product;
use App\Models\User;
use App\Support\Committee;
use App\Support\CommitteeLevels;
use App\Support\CommitteeMembers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

/**
 * Credit committee rules. A **path** is a product (or all products) plus a condition/category with
 * a mechanism (authority by amount, or committee hierarchy). Each path has ordered **tiers**: the
 * deciding role, the amount range and the decisions allowed.
 */
class CommitteeController extends Controller
{
    /** The hub: the default ladder of authority, and how it works. */
    public function index(): Response
    {
        $default = CommitteeLevels::defaultPath();

        return Inertia::render('committees/levels', [
            'defaultId' => $default?->id,
            'levels' => $default !== null ? $default->tiers->map(fn (CommitteeTier $t): array => [
                ...$t->only(['id', 'sort', 'label', 'role', 'is_individual', 'min_amount', 'max_amount', 'can_escalate', 'can_approve', 'can_cancel', 'can_reject']),
                'people' => User::role($t->role)->count(),
            ])->values() : [],
            'followers' => CommitteeLevels::followers(),
            'special' => CommitteePath::query()->where('is_default', false)->where(fn ($q) => $q->where('follows_default', false)->orWhere('mechanism', '!=', 'plafon'))->count(),
            ...$this->authorityProps(),
            'canManage' => true,
        ]);
    }

    /** Paths that deviate from the default levels (hierarchy, or levels of their own); the rest can be shown on request. */
    public function paths(): Response
    {
        return Inertia::render('committees/special-rules', [
            'paths' => CommitteePath::with('product')->withCount('tiers')->where('is_default', false)->orderBy('product_id')->orderBy('condition')->get()
                ->map(fn (CommitteePath $p): array => $this->pathRow($p)),
            'productOptions' => Product::orderBy('code')->get()->map(fn (Product $p): array => ['value' => $p->id, 'label' => "{$p->alias} — {$p->name}"]),
            'pathOptions' => CommitteePath::with('product')->has('tiers')->where('is_default', false)->get()->map(fn (CommitteePath $p): array => ['value' => $p->id, 'label' => $p->title()]),
            'defaultLevels' => ($default = CommitteeLevels::defaultPath()) ? ['id' => $default->id, 'followers' => CommitteeLevels::followers()] : null,
            'mechanisms' => collect(CommitteePath::MECHANISMS)->map(fn (string $label, string $value): array => ['value' => $value, 'label' => $label])->values(),
            'canManage' => true,
        ]);
    }

    public function show(Request $request, CommitteePath $path): Response
    {
        $path->load(['product', 'tiers']);

        return Inertia::render('committees/show', [
            'path' => [
                ...$this->pathRow($path),
                'followers' => $path->is_default ? CommitteeLevels::followers() : null,
                'tiers' => $path->tiers->map(fn (CommitteeTier $t): array => $t->only(['id', 'sort', 'label', 'role', 'min_amount', 'max_amount', 'can_escalate', 'can_approve', 'can_cancel', 'can_reject']))->values(),
            ],
            'roles' => Role::orderBy('name')->pluck('name'),
            'canManage' => true,
        ]);
    }

    /** How an applicant who is a committee member is handled. */
    public function exceptions(): Response
    {
        return Inertia::render('committees/exceptions', [
            'members' => ['total' => CommitteeMembers::committee()->count(), 'withoutNik' => CommitteeMembers::committee()->whereNull('nik_hash')->count()],
            ...$this->authorityProps(),
        ]);
    }

    /**
     * Options for the authority check dialog.
     *
     * @return array<string, mixed>
     */
    private function authorityProps(): array
    {
        return [
            'productOptions' => Product::orderBy('code')->get()->map(fn (Product $p): array => ['value' => $p->id, 'label' => "{$p->alias} — {$p->name}"]),
            // Valid conditions per product: the product's own plus cross-product ones (e.g. RELOAN).
            'conditionMap' => CommitteePath::where('is_active', true)->get()
                ->groupBy(fn (CommitteePath $p) => $p->product_id ?? 'global')
                ->map(fn ($paths) => $paths->map(fn (CommitteePath $p): string => (string) $p->condition)->unique()->sort()->values()),
            'committeeMembers' => CommitteeMembers::options(),
        ];
    }

    /** Who decides for a given product, condition and amount. Reads rules only. */
    public function authority(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'condition' => ['nullable', 'string', 'max:30'],
            'amount' => ['required', 'integer', 'min:0'],
            'applicant_id' => ['nullable', 'integer', Rule::in(CommitteeMembers::query()->pluck('id')->all())],
        ], ['applicant_id.in' => 'Orang yang dipilih bukan anggota komite.'], ['applicant_id' => 'pemohon']);

        $applicant = isset($data['applicant_id']) ? User::find((int) $data['applicant_id']) : null;

        return response()->json(Committee::resolve($data['product_id'] ?? null, $data['condition'] ?? null, (int) $data['amount'], $applicant));
    }

    public function store(CommitteePathRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // Following the defaults is the normal case when they exist; copying another path's tiers means going its own way.
        $follows = ($data['follows_default'] ?? false) && blank($data['copy_from'] ?? null) && CommitteeLevels::defaultPath() !== null;
        $path = CommitteePath::create([...collect($data)->except(['copy_from', 'follows_default'])->all(), 'follows_default' => $follows]);

        if ($follows) {
            CommitteeLevels::attach($path);
        }

        if (filled($data['copy_from'] ?? null)) {
            foreach (CommitteeTier::query()->where('committee_path_id', $data['copy_from'])->orderBy('sort')->get() as $tier) {
                $path->tiers()->create($tier->only(['sort', 'label', 'role', 'min_amount', 'max_amount', 'can_escalate', 'can_approve', 'can_cancel', 'can_reject']));
            }
        }

        return to_route('committees.show', $path)->with('success', "Jalur komite {$path->title()} berhasil dibuat.");
    }

    public function update(CommitteePathRequest $request, CommitteePath $path): RedirectResponse
    {
        $path->update(collect($request->validated())->except(['copy_from', 'follows_default'])->all());

        // A path on the defaults reads its tiers from them, and a hierarchy path derives them differently from an amount path.
        if ($path->follows_default && $path->wasChanged('mechanism')) {
            CommitteeLevels::attach($path);
        }

        return back()->with('success', "Jalur komite {$path->title()} berhasil diperbarui.");
    }

    public function destroy(CommitteePath $path): RedirectResponse
    {
        abort_if($path->is_default, 403, 'Jenjang wewenang bawaan tidak bisa dihapus.');

        $title = $path->title();

        if (($count = $path->loanApplications()->count()) > 0) {
            return back()->with('error', "{$title} dipakai oleh {$count} pengajuan kredit sehingga tidak bisa dihapus. Nonaktifkan saja.");
        }

        $path->delete();

        return to_route('committees.paths')->with('success', "Jalur komite {$title} berhasil dihapus.");
    }

    public function storeTier(CommitteeTierRequest $request, CommitteePath $path): RedirectResponse
    {
        if ($path->follows_default) {
            return $this->followsDefault();
        }

        $tier = $path->tiers()->create([...$request->validated(), 'sort' => (int) $path->tiers()->max('sort') + 1]);

        return back()->with('success', "Jenjang {$tier->role} berhasil ditambahkan.".$this->spread($path));
    }

    public function updateTier(CommitteeTierRequest $request, CommitteePath $path, CommitteeTier $tier): RedirectResponse
    {
        if ($path->follows_default) {
            return $this->followsDefault();
        }

        $tier->update($request->validated());

        return back()->with('success', "Jenjang {$tier->role} berhasil diperbarui.".$this->spread($path));
    }

    public function destroyTier(CommitteePath $path, CommitteeTier $tier): RedirectResponse
    {
        if ($path->follows_default) {
            return $this->followsDefault();
        }

        $tier->delete();

        return back()->with('success', "Jenjang {$tier->role} berhasil dihapus.".$this->spread($path));
    }

    /**
     * Make a path follow the default authority levels (its own tiers are replaced by a copy), or let it go its own way
     * (it keeps the tiers it has and they become its own to edit).
     */
    public function follow(Request $request, CommitteePath $path): RedirectResponse
    {
        abort_if($path->is_default, 403);
        $data = $request->validate(['follow' => ['required', 'boolean']]);

        if ($data['follow'] && CommitteeLevels::defaultPath() === null) {
            return back()->with('error', 'Belum ada jenjang wewenang bawaan.');
        }

        $path->update(['follows_default' => (bool) $data['follow']]);

        if ($data['follow']) {
            CommitteeLevels::attach($path);
        }

        return back()->with('success', $data['follow'] ? 'Jalur ini sekarang mengikuti jenjang wewenang bawaan.' : 'Jalur ini sekarang memakai jenjang wewenang sendiri.');
    }

    /** Move a tier one step up or down. */
    public function moveTier(CommitteePath $path, CommitteeTier $tier, string $direction): RedirectResponse
    {
        if ($path->follows_default) {
            return $this->followsDefault();
        }

        $ordered = $path->tiers()->get()->values()->all();
        $index = (int) array_search($tier->id, array_map(fn (CommitteeTier $t): int => $t->id, $ordered), true);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= count($ordered)) {
            return back();
        }

        [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];

        foreach ($ordered as $position => $row) {
            $row->update(['sort' => $position + 1]);
        }

        return back()->with('success', 'Urutan jenjang diperbarui.'.$this->spread($path));
    }

    private function followsDefault(): RedirectResponse
    {
        return back()->with('error', 'Jalur ini mengikuti jenjang wewenang bawaan. Beri jenjang sendiri terlebih dahulu.');
    }

    /** After a change to the default levels, copy them to every path that follows them. */
    private function spread(CommitteePath $path): string
    {
        if (! $path->is_default) {
            return '';
        }

        $count = CommitteeLevels::propagate();

        return " Diterapkan ke {$count} jalur.";
    }

    /**
     * @return array<string, mixed>
     */
    private function pathRow(CommitteePath $path): array
    {
        return [
            'id' => $path->id,
            'product_id' => $path->product_id,
            'product_label' => $path->product ? "{$path->product->alias} — {$path->product->name}" : 'Semua produk',
            'condition' => $path->condition,
            'condition_label' => $path->condition ?: 'Normal',
            'mechanism' => $path->mechanism,
            'mechanism_label' => CommitteePath::MECHANISMS[$path->mechanism] ?? $path->mechanism,
            'is_active' => $path->is_active,
            'is_default' => $path->is_default,
            'follows_default' => $path->follows_default,
            'note' => $path->note,
            'tiers_count' => $path->tiers_count ?? $path->tiers()->count(),
            'title' => $path->title(),
        ];
    }
}
