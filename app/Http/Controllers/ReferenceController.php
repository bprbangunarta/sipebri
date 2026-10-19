<?php

namespace App\Http\Controllers;

use App\Support\ReferenceResource;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Single CRUD controller for every lookup table registered in config/references.php.
 */
class ReferenceController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 25, 50];

    public function index(Request $request, string $resource): Response
    {
        $definition = ReferenceResource::find($resource);
        $usage = array_keys($definition->usage());
        $first = $definition->rawFields()[0]['name'];
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer'],
            'sort' => ['nullable', Rule::in($definition->sortKeys())],
            'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $sort = $filters['sort'] ?? $first;
        $direction = $filters['direction'] ?? 'asc';

        $query = $definition->model()::query()->withCount($definition->usageCounts());
        $definition->search($query, $filters['search'] ?? null);

        $perPage = in_array((int) ($filters['per_page'] ?? 0), self::PER_PAGE_OPTIONS, true) ? (int) $filters['per_page'] : 25;
        $items = $sort === ReferenceResource::USAGE_SORT
            ? $this->byUsage($query, $usage, $first, $direction, $perPage, $request)
            : $query->orderBy($sort, $direction)->orderBy($first)->orderBy('id')->paginate($perPage)->withQueryString();

        return Inertia::render('references/index', [
            'items' => $items->through(fn ($record): array => [
                ...$definition->toItem($record),
                'usage_count' => array_sum(array_map(fn (string $relation): int => (int) $record->getAttribute(Str::snake($relation).'_count'), $usage)),
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'sort' => $sort, 'direction' => $direction, 'per_page' => $items->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'canManage' => true,
            'resource' => [
                'slug' => $resource,
                'label' => $definition->label(),
                'section' => $definition->section(),
                'tracks_usage' => $usage !== [],
                'title_field' => $definition->titleField(),
                'fields' => $definition->fields(),
                'actions' => $definition->actions(),
                'import' => $definition->importable() ? [
                    'url' => route('references.import', $resource, false),
                    'template_url' => route('references.import.template', $resource, false),
                ] : null,
                'store_url' => route('references.store', $resource, false),
                'item_url' => route('references.update', [$resource, '__id__'], false),
            ],
        ]);
    }

    /**
     * The "Dipakai" column adds up several relations, which one SQL ORDER BY cannot do without building SQL text from names;
     * lookup tables that are used by other records stay small, so this one order is done on the rows in memory.
     *
     * @param  Builder<Model>  $query
     * @param  list<string>  $usage
     * @return LengthAwarePaginator<int, Model>
     */
    private function byUsage(Builder $query, array $usage, string $first, string $direction, int $perPage, Request $request): LengthAwarePaginator
    {
        $total = fn (Model $record): int => array_sum(array_map(fn (string $relation): int => (int) $record->getAttribute(Str::snake($relation).'_count'), $usage));
        $rows = $query->get()->sortBy([
            fn (Model $a, Model $b): int => $direction === 'asc' ? $total($a) <=> $total($b) : $total($b) <=> $total($a),
            fn (Model $a, Model $b): int => $a->getAttribute($first) <=> $b->getAttribute($first),
            fn (Model $a, Model $b): int => $a->getKey() <=> $b->getKey(),
        ])->values();
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator($rows->forPage($page, $perPage)->values(), $rows->count(), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = ReferenceResource::find($resource);
        $input = $definition->normalize($request->all());
        $data = validator($input, $definition->rules(null, $input))->validate();

        $this->saving($definition, fn () => $definition->model()::query()->create($data));

        return back()->with('success', "{$definition->label()} berhasil dibuat.");
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $definition = ReferenceResource::find($resource);
        $record = $definition->model()::query()->findOrFail($id);
        $input = $definition->normalize($request->all());
        $data = validator($input, $definition->rules($record, $input))->validate();

        $this->refuseUsedCodeChange($definition, $record, $data);

        $this->saving($definition, fn () => $record->update($data));

        return back()->with('success', "{$definition->label()} berhasil diperbarui.");
    }

    /**
     * A record that others refer to by its code, or that another system finds by it (`lock_code`), cannot get a new code:
     * those references would point nowhere.
     *
     * @param  array<string, mixed>  $data
     */
    private function refuseUsedCodeChange(ReferenceResource $definition, Model $record, array $data): void
    {
        if (! isset($data['code']) || $data['code'] === $record->getAttribute('code')) {
            return;
        }

        if (($held = $definition->lastHolderOfUsedCode($record)) !== null) {
            throw ValidationException::withMessages(['code' => sprintf('Kode tidak bisa diubah: dipakai oleh %d %s.', $held[0], $held[1])]);
        }

        foreach ($definition->usage() as $relation => $noun) {
            $link = $record->{$relation}();
            $byCode = $definition->locksCode() || ($link instanceof HasOneOrMany && $link->getLocalKeyName() === 'code');

            if ($byCode && ($count = $definition->usageOf($record, $relation)) > 0) {
                throw ValidationException::withMessages(['code' => sprintf('Kode tidak bisa diubah: dipakai oleh %d %s.', $count, $noun)]);
            }
        }
    }

    /**
     * Two people saving the same unique value at the same moment: the loser gets a sentence on the first field, not a server error.
     */
    private function saving(ReferenceResource $definition, Closure $write): void
    {
        try {
            $write();
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages([$definition->rawFields()[0]['name'] => 'Data yang sama baru saja disimpan oleh orang lain. Muat ulang halaman lalu coba lagi.']);
        }
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $definition = ReferenceResource::find($resource);
        $record = $definition->model()::query()->findOrFail($id);
        $name = $definition->title($record);

        foreach ($definition->usage() as $relation => $noun) {
            $count = $definition->usageOf($record, $relation);

            if ($count > 0) {
                return back()->with('error', $this->inUse($name, $count, $noun));
            }
        }

        if (($held = $definition->lastHolderOfUsedCode($record)) !== null) {
            return back()->with('error', $this->inUse($name, $held[0], $held[1]));
        }

        $record->delete();

        return back()->with('success', "{$definition->label()} berhasil dihapus.");
    }

    private function inUse(string $name, int $count, string $noun): string
    {
        return sprintf('"%s" dipakai oleh %d %s sehingga tidak bisa dihapus. Alihkan atau hapus dulu data tersebut.', $name, $count, $noun);
    }
}
