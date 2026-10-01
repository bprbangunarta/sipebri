<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\RoleName;
use App\Models\CommitteeTier;
use App\Support\Modules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:name,users_count,permissions_count'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer'],
        ]);
        $sort = $filters['sort'] ?? 'name';
        $direction = $filters['direction'] ?? 'asc';

        $roles = Role::query()->withCount(['users', 'permissions'])
            ->when($filters['search'] ?? null, fn ($q, string $term) => $q->where('name', 'like', '%'.addcslashes($term, '%_\\').'%'))
            ->orderBy($sort, $direction)->orderBy('name')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), [10, 25, 50], true) ? (int) $filters['per_page'] : 25)
            ->withQueryString();

        return Inertia::render('roles/index', [
            'roles' => $roles->through(fn (Role $r): array => ['id' => $r->id, 'name' => $r->name, 'users_count' => $r->users_count, 'permissions_count' => $r->permissions_count, 'locked' => $r->name === RoleName::SuperAdmin->value]),
            'filters' => ['search' => $filters['search'] ?? '', 'sort' => $sort, 'direction' => $direction, 'per_page' => $roles->perPage()],
            'canManage' => true,
        ]);
    }

    public function show(Request $request, Role $role): Response
    {
        return Inertia::render('roles/show', [
            'role' => ['id' => $role->id, 'name' => $role->name, 'locked' => $role->name === RoleName::SuperAdmin->value],
            'matrix' => Modules::matrix(),
            'granted' => $role->permissions()->pluck('name'),
            'canManage' => true,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')]]);
        $role = Role::query()->create(['name' => trim($data['name']), 'guard_name' => 'web']);
        Audit::record('roles.created', 'roles', 'created', $role, new: ['name' => $role->name], label: $role->name);

        return to_route('roles.show', $role)->with('success', 'Peran berhasil dibuat. Pilih izinnya di bawah.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->name === RoleName::SuperAdmin->value, 403, 'The Super Admin role cannot be changed.');
        $data = $request->validate(['name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role)]]);
        if ($role->name !== trim($data['name']) && ($count = CommitteeTier::query()->where('role', $role->name)->count()) > 0) {
            return back()->withErrors(['name' => "\"{$role->name}\" decides in {$count} committee ".str('tier')->plural($count).' and cannot be renamed. Change those tiers first.']);
        }

        $old = $role->name;
        $role->update(['name' => trim($data['name'])]);
        Audit::record('roles.renamed', 'roles', 'updated', $role, ['name' => $old], ['name' => $role->name], label: $role->name);

        return back()->with('success', 'Nama peran berhasil diubah.');
    }

    public function syncPermissions(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->name === RoleName::SuperAdmin->value, 403, 'The Super Admin role always has every permission.');
        $data = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in(Modules::permissions())],
        ]);

        // A "manage" permission is meaningless without its "view" counterpart.
        $names = $data['permissions'] ?? [];
        foreach ($names as $name) {
            if (str_ends_with($name, '.manage')) {
                $names[] = str_replace('.manage', '.view', $name);
            }
        }

        $before = $role->permissions()->pluck('name')->sort()->values()->all();
        $role->syncPermissions(array_values(array_unique($names)));
        $after = $role->permissions()->pluck('name')->sort()->values()->all();

        Audit::record('roles.permissions_changed', 'roles', 'permissions_changed', $role, ['added' => [], 'removed' => array_values(array_diff($before, $after))], ['added' => array_values(array_diff($after, $before)), 'removed' => []], label: $role->name);

        return back()->with('success', 'Izin berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === RoleName::SuperAdmin->value, 403, 'The Super Admin role cannot be deleted.');

        if ($role->users()->exists()) {
            return back()->with('error', "\"{$role->name}\" dipakai pengguna sehingga tidak bisa dihapus.");
        }

        if (($count = CommitteeTier::query()->where('role', $role->name)->count()) > 0) {
            return back()->with('error', "\"{$role->name}\" memutus di {$count} jenjang komite sehingga tidak bisa dihapus. Ubah jenjang itu terlebih dahulu.");
        }

        Audit::record('roles.deleted', 'roles', 'deleted', $role, ['name' => $role->name], label: $role->name);
        $role->delete();

        return to_route('roles.index')->with('success', 'Peran berhasil dihapus.');
    }
}
