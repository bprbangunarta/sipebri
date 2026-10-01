<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommitteePathRequest;
use App\Http\Requests\CommitteeTierRequest;
use App\Models\CommitteePath;
use App\Models\CommitteeTier;
use App\Models\Product;
use App\Models\User;
use App\Support\Committee;
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
    public function index(Request $request): Response
    {
        return Inertia::render('committees/index', [
            'paths' => CommitteePath::with('product')->withCount('tiers')->orderBy('product_id')->orderBy('condition')->get()
                ->map(fn (CommitteePath $p): array => $this->pathRow($p)),
            'productOptions' => Product::orderBy('code')->get()->map(fn (Product $p): array => ['value' => $p->id, 'label' => "{$p->alias} — {$p->name}"]),
            'pathOptions' => CommitteePath::with('product')->has('tiers')->get()->map(fn (CommitteePath $p): array => ['value' => $p->id, 'label' => $p->title()]),
            // Valid conditions per product for the authority check: the product's own plus cross-product ones (e.g. RELOAN).
            'conditionMap' => CommitteePath::where('is_active', true)->get()
                ->groupBy(fn (CommitteePath $p) => $p->product_id ?? 'global')
                ->map(fn ($paths) => $paths->map(fn (CommitteePath $p): string => (string) $p->condition)->unique()->sort()->values()),
            'committeeMembers' => CommitteeMembers::options(),
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
                'tiers' => $path->tiers->map(fn (CommitteeTier $t): array => $t->only(['id', 'sort', 'label', 'role', 'min_amount', 'max_amount', 'can_escalate', 'can_approve', 'can_cancel', 'can_reject']))->values(),
            ],
            'roles' => Role::orderBy('name')->pluck('name'),
            'canManage' => true,
        ]);
    }

    /** Who decides for a given product, condition and amount. Reads rules only. */
    public function authority(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'condition' => ['nullable', 'string', 'max:30'],
            'amount' => ['required', 'integer', 'min:0'],
            'applicant_id' => ['nullable', 'integer', Rule::in(CommitteeMembers::query()->pluck('id')->all())],
        ], ['applicant_id.in' => 'The selected person is not a committee member.'], ['applicant_id' => 'applicant']);

        $applicant = isset($data['applicant_id']) ? User::find((int) $data['applicant_id']) : null;

        return response()->json(Committee::resolve($data['product_id'] ?? null, $data['condition'] ?? null, (int) $data['amount'], $applicant));
    }

    public function store(CommitteePathRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $path = CommitteePath::create(collect($data)->except('copy_from')->all());

        if (filled($data['copy_from'] ?? null)) {
            foreach (CommitteeTier::query()->where('committee_path_id', $data['copy_from'])->orderBy('sort')->get() as $tier) {
                $path->tiers()->create($tier->only(['sort', 'label', 'role', 'min_amount', 'max_amount', 'can_escalate', 'can_approve', 'can_cancel', 'can_reject']));
            }
        }

        return to_route('committees.show', $path)->with('success', "Committee path {$path->title()} created.");
    }

    public function update(CommitteePathRequest $request, CommitteePath $path): RedirectResponse
    {
        $path->update(collect($request->validated())->except('copy_from')->all());

        return back()->with('success', "Committee path {$path->title()} updated.");
    }

    public function destroy(CommitteePath $path): RedirectResponse
    {
        $title = $path->title();

        if (($count = $path->loanApplications()->count()) > 0) {
            return back()->with('error', "{$title} is used by {$count} loan ".str('application')->plural($count).' and cannot be deleted. Set it inactive instead.');
        }

        $path->delete();

        return to_route('committees.index')->with('success', "Committee path {$title} deleted.");
    }

    public function storeTier(CommitteeTierRequest $request, CommitteePath $path): RedirectResponse
    {
        $tier = $path->tiers()->create([...$request->validated(), 'sort' => (int) $path->tiers()->max('sort') + 1]);

        return back()->with('success', "Tier {$tier->role} added.");
    }

    public function updateTier(CommitteeTierRequest $request, CommitteePath $path, CommitteeTier $tier): RedirectResponse
    {
        $tier->update($request->validated());

        return back()->with('success', "Tier {$tier->role} updated.");
    }

    public function destroyTier(CommitteePath $path, CommitteeTier $tier): RedirectResponse
    {
        $tier->delete();

        return back()->with('success', "Tier {$tier->role} deleted.");
    }

    /** Move a tier one step up or down. */
    public function moveTier(CommitteePath $path, CommitteeTier $tier, string $direction): RedirectResponse
    {
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

        return back()->with('success', 'Tier order updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function pathRow(CommitteePath $path): array
    {
        return [
            'id' => $path->id,
            'product_id' => $path->product_id,
            'product_label' => $path->product ? "{$path->product->alias} — {$path->product->name}" : 'All products',
            'condition' => $path->condition,
            'condition_label' => $path->condition ?: 'Normal',
            'mechanism' => $path->mechanism,
            'mechanism_label' => CommitteePath::MECHANISMS[$path->mechanism] ?? $path->mechanism,
            'is_active' => $path->is_active,
            'note' => $path->note,
            'tiers_count' => $path->tiers_count ?? $path->tiers()->count(),
            'title' => $path->title(),
        ];
    }
}
