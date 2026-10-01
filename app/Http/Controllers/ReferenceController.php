<?php

namespace App\Http\Controllers;

use App\Support\ReferenceResource;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'per_page' => ['nullable', 'integer']]);
        $usage = array_keys($definition->usage());
        $first = $definition->rawFields()[0]['name'];

        $query = $definition->model()::query()->withCount($usage);
        $definition->search($query, $filters['search'] ?? null);

        $items = $query->orderBy($first)->orderBy('id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), self::PER_PAGE_OPTIONS, true) ? (int) $filters['per_page'] : 25)
            ->withQueryString();

        return Inertia::render('references/index', [
            'items' => $items->through(fn ($record): array => [
                ...$definition->toItem($record),
                'usage_count' => array_sum(array_map(fn (string $relation): int => (int) $record->{$relation.'_count'}, $usage)),
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'per_page' => $items->perPage()],
            'perPageOptions' => self::PER_PAGE_OPTIONS,
            'canManage' => true,
            'resource' => [
                'slug' => $resource,
                'label' => $definition->label(),
                'section' => $definition->section(),
                'tracks_usage' => $usage !== [],
                'fields' => $definition->fields(),
                'actions' => $definition->actions(),
                'store_url' => route('references.store', $resource, false),
                'item_url' => route('references.update', [$resource, '__id__'], false),
            ],
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        $definition = ReferenceResource::find($resource);
        $input = $definition->normalize($request->all());
        $data = validator($input, $definition->rules(null, $input))->validate();

        $definition->model()::query()->create($data);

        return back()->with('success', "{$definition->label()} berhasil dibuat.");
    }

    public function update(Request $request, string $resource, int $id): RedirectResponse
    {
        $definition = ReferenceResource::find($resource);
        $record = $definition->model()::query()->findOrFail($id);
        $input = $definition->normalize($request->all());
        $data = validator($input, $definition->rules($record, $input))->validate();

        // A record other tables refer to by its code cannot get a new code: those references would point nowhere.
        if (isset($data['code']) && $data['code'] !== $record->getAttribute('code')) {
            foreach ($definition->usage() as $relation => $noun) {
                $link = $record->{$relation}();

                if ($link instanceof HasOneOrMany && $link->getLocalKeyName() === 'code' && ($count = $link->count()) > 0) {
                    throw ValidationException::withMessages(['code' => sprintf('Kode tidak bisa diubah: dipakai oleh %d %s.', $count, $noun)]);
                }
            }
        }

        $record->update($data);

        return back()->with('success', "{$definition->label()} berhasil diperbarui.");
    }

    public function destroy(string $resource, int $id): RedirectResponse
    {
        $definition = ReferenceResource::find($resource);
        $record = $definition->model()::query()->findOrFail($id);
        $name = $record->name ?? $record->getKey();

        foreach ($definition->usage() as $relation => $noun) {
            $count = $record->{$relation}()->count();

            if ($count > 0) {
                return back()->with('error', $this->inUse($name, $count, $noun));
            }
        }

        $record->delete();

        return back()->with('success', "{$definition->label()} berhasil dihapus.");
    }

    private function inUse(string $name, int $count, string $noun): string
    {
        return sprintf('"%s" dipakai oleh %d %s sehingga tidak bisa dihapus. Alihkan atau hapus dulu data tersebut.', $name, $count, $noun);
    }
}
