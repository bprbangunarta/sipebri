<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\RoleName;
use App\Models\Permission;
use App\Support\Modules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        ]);

        $permissions = Permission::query()->withCount('roles')
            ->when($filters['search'] ?? null, fn ($q, string $term) => $q->where('name', 'like', '%'.addcslashes($term, '%_\\').'%'))
            ->when($filters['entity'] ?? null, fn ($q, string $entity) => $q->where('name', 'like', addcslashes($entity, '%_\\').'.%'))
            ->when(($filters['type'] ?? null) === 'system', fn ($q) => $q->where('is_custom', false))
            ->when(($filters['type'] ?? null) === 'custom', fn ($q) => $q->where('is_custom', true))
            ->orderBy('name')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), [10, 25, 50], true) ? (int) $filters['per_page'] : 25)
            ->withQueryString();

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
                'per_page' => $permissions->perPage(),
            ],
            'entities' => Permission::query()->pluck('name')->map(fn (string $n): string => explode('.', $n, 2)[0])->unique()->sort()->values(),
            'actions' => Modules::STANDARD_ACTIONS,
        ]);
    }

    /** Create `entity.action` permissions for the chosen standard actions; the ones that already exist are skipped. */
    public function generate(Request $request): RedirectResponse
    {
        $request->merge(['entity' => preg_replace('/[\s_]+/', '-', mb_strtolower(trim($request->string('entity')->toString())))]);

        $data = $request->validate([
            'entity' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/'],
            'actions' => ['required', 'array', 'min:1'],
            'actions.*' => ['string', Rule::in(Modules::STANDARD_ACTIONS)],
        ], ['entity.regex' => 'Use letters, numbers and single hyphens only, e.g. "credit-analysis".'], ['entity' => 'entity']);

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

        Audit::record('permissions.generated', 'permissions', 'generated', new: ['created' => $created], context: ['entity' => $data['entity'], 'skipped' => $skipped], label: $data['entity']);

        return back()->with('success', count($created).' permission(s) created'.($skipped === [] ? '.' : ', '.count($skipped).' already existed.'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        abort_unless($permission->is_custom, 403, 'System permissions cannot be deleted.');

        if (($count = $permission->roles()->count()) > 0) {
            return back()->with('error', "\"{$permission->name}\" is held by {$count} ".str('role')->plural($count).' and cannot be deleted. Remove it from those roles first.');
        }

        $permission->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', 'Permission deleted.');
    }
}
