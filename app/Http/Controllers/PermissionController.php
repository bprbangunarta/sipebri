<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\RoleName;
use App\Models\Permission;
use App\Support\Modules;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * The list of permission items, and a generator for the standard `entity.action` ones.
 *
 * System permissions come from App\Support\Modules and cannot be changed here. Permissions made by the generator are
 * "custom": they only become useful once a route or policy checks them, and they cannot be deleted while a role holds them.
 */
class PermissionController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'entity' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'in:system,custom'],
            'per_page' => ['nullable', 'integer'],
            'sort' => ['nullable', 'in:name,module,type,roles'],
            'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $sort = $filters['sort'] ?? 'name';
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = in_array((int) ($filters['per_page'] ?? 0), [10, 25, 50], true) ? (int) $filters['per_page'] : 25;

        $query = Permission::query()->withCount('roles')
            ->when($filters['search'] ?? null, fn ($q, string $term) => $q->where('name', 'like', '%'.addcslashes($term, '%_\\').'%'))
            ->when($filters['entity'] ?? null, fn ($q, string $entity) => $q->where('name', 'like', addcslashes($entity, '%_\\').'.%'))
            ->when(($filters['type'] ?? null) === 'system', fn ($q) => $q->where('is_custom', false))
            ->when(($filters['type'] ?? null) === 'custom', fn ($q) => $q->where('is_custom', true))
            ->when($sort !== 'module', fn ($q) => $q->orderBy(['name' => 'name', 'type' => 'is_custom', 'roles' => 'roles_count'][$sort], $direction))
            ->orderBy('name');
        $permissions = $sort === 'module' ? $this->byModule($query, $direction, $perPage, $request) : $query->paginate($perPage)->withQueryString();

        return Inertia::render('permissions/index', [
            'permissions' => $permissions->through(function (Permission $p): array {
                [$entity, $action] = array_pad(explode('.', $p->name, 2), 2, '');

                return [
                    'id' => $p->id, 'name' => $p->name, 'entity' => $entity, 'action' => $action,
                    'module' => Modules::map()[$entity]['label'] ?? null, 'custom' => (bool) $p->is_custom, 'roles' => $p->roles_count,
                ];
            }),
            'filters' => [
                'search' => $filters['search'] ?? '', 'entity' => $filters['entity'] ?? null, 'type' => $filters['type'] ?? null,
                'sort' => $sort, 'direction' => $direction, 'per_page' => $permissions->perPage(),
            ],
            'entities' => Permission::query()->pluck('name')->map(fn (string $n): string => explode('.', $n, 2)[0])->unique()->sort()->values(),
            'actions' => Modules::STANDARD_ACTIONS,
        ]);
    }

    /**
     * The module is a label that the code knows (`Modules::map()`), not a column, so this one order is done on the rows in memory; a table
     * of permissions is small by nature.
     *
     * @param  Builder<Permission>  $query
     * @return LengthAwarePaginator<int, Permission>
     */
    private function byModule(Builder $query, string $direction, int $perPage, Request $request): LengthAwarePaginator
    {
        $label = fn (Permission $p): string => (string) (Modules::map()[explode('.', $p->name, 2)[0]]['label'] ?? '');
        $rows = $query->get()->sort(fn (Permission $a, Permission $b): int => ($direction === 'asc' ? $label($a) <=> $label($b) : $label($b) <=> $label($a)) ?: strcmp($a->name, $b->name))->values();
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator($rows->forPage($page, $perPage)->values(), $rows->count(), $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);
    }

    /** Create `entity.action` permissions for the chosen standard actions; the ones that already exist are skipped. */
    public function generate(Request $request): RedirectResponse
    {
        if (is_string($request->input('entity'))) {
            $request->merge(['entity' => preg_replace('/[\s_]+/', '-', mb_strtolower(trim($request->input('entity'))))]);
        }

        $data = $request->validate([
            'entity' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/'],
            'actions' => ['required', 'array', 'min:1'],
            'actions.*' => ['string', Rule::in(Modules::STANDARD_ACTIONS)],
        ], ['entity.regex' => 'Gunakan huruf, angka, dan tanda hubung tunggal saja, mis. "credit-analysis".'], ['entity' => 'entitas']);

        $created = [];
        $skipped = [];

        foreach (array_values(array_unique($data['actions'])) as $action) {
            $name = "{$data['entity']}.{$action}";

            if (Permission::query()->where('name', $name)->where('guard_name', 'web')->exists()) {
                $skipped[] = $name;

                continue;
            }

            Permission::query()->create(['name' => $name, 'guard_name' => 'web', 'is_custom' => true]);
            $created[] = $name;
        }

        // Super Admin always holds every permission.
        if ($created !== []) {
            Role::query()->where('name', RoleName::SuperAdmin->value)->where('guard_name', 'web')->first()?->givePermissionTo($created);
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        // Nothing new means nothing to record.
        if ($created !== []) {
            Audit::record('permissions.generated', 'permissions', 'generated', new: ['created' => $created], context: ['entity' => $data['entity'], 'skipped' => $skipped], label: $data['entity']);
        }

        return back()->with('success', count($created).' izin berhasil dibuat'.($skipped === [] ? '.' : ', '.count($skipped).' sudah ada sebelumnya.'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        abort_unless($permission->is_custom, 403, 'Izin sistem tidak bisa dihapus.');

        // Super Admin holds every permission by itself, so it does not count as a holder; deleting the permission takes it from Super Admin too.
        if (($count = $permission->roles()->where('name', '!=', RoleName::SuperAdmin->value)->count()) > 0) {
            return back()->with('error', "\"{$permission->name}\" dipegang {$count} peran sehingga tidak bisa dihapus. Lepas dulu dari peran itu.");
        }

        $permission->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', 'Izin berhasil dihapus.');
    }
}
