<?php

namespace App\Http\Controllers;

use App\Audit\Audit;
use App\Enums\RoleName;
use App\Models\CommitteeTier;
use App\Models\User;
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
            'role' => [
                'id' => $role->id, 'name' => $role->name, 'locked' => $role->name === RoleName::SuperAdmin->value,
                // Named by the code, or held by people: the name stays and the role cannot be deleted (Codex matches roles by name).
                'named_by_code' => $this->namedByCode($role), 'holders' => $this->holders($role),
            ],
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
        abort_if($role->name === RoleName::SuperAdmin->value, 403, 'Peran Super Admin tidak bisa diubah.');
        $data = $request->validate(['name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role)]]);
        $new = trim($data['name']);

        if ($role->name === $new) {
            return back()->with('success', 'Nama peran tidak berubah.');
        }

        // Codex names a person's role and the code refers to some roles by name, so the name is the key: it only changes while nothing depends on it.
        if ($this->namedByCode($role)) {
            return back()->withErrors(['name' => "\"{$role->name}\" dipakai kode aplikasi lewat namanya sehingga namanya tidak bisa diubah."]);
        }

        if (($count = $this->holders($role)) > 0) {
            return back()->withErrors(['name' => "\"{$role->name}\" dipegang {$count} pengguna (termasuk yang nonaktif) sehingga namanya tidak bisa diubah: Codex mencocokkan peran lewat nama."]);
        }

        if (($count = CommitteeTier::query()->where('role', $role->name)->count()) > 0) {
            return back()->withErrors(['name' => "\"{$role->name}\" memutus di {$count} jenjang komite sehingga namanya tidak bisa diubah. Ubah jenjang itu terlebih dahulu."]);
        }

        $old = $role->name;
        $role->update(['name' => $new]);
        Audit::record('roles.renamed', 'roles', 'updated', $role, ['name' => $old], ['name' => $role->name], label: $role->name);

        return back()->with('success', 'Nama peran berhasil diubah.');
    }

    public function syncPermissions(Request $request, Role $role): RedirectResponse
    {
        abort_if($role->name === RoleName::SuperAdmin->value, 403, 'Peran Super Admin selalu punya semua izin.');
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

        if ($before === $after) {
            return back()->with('success', 'Izin tidak berubah.');
        }

        Audit::record('roles.permissions_changed', 'roles', 'permissions_changed', $role, ['added' => [], 'removed' => array_values(array_diff($before, $after))], ['added' => array_values(array_diff($after, $before)), 'removed' => []], label: $role->name);

        return back()->with('success', 'Izin berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === RoleName::SuperAdmin->value, 403, 'Peran Super Admin tidak bisa dihapus.');

        if ($this->namedByCode($role)) {
            return back()->with('error', "\"{$role->name}\" dipakai kode aplikasi lewat namanya sehingga tidak bisa dihapus.");
        }

        // A deactivated person still holds the role; deleting it would strip it from them without a word.
        if (($count = $this->holders($role)) > 0) {
            return back()->with('error', "\"{$role->name}\" dipegang {$count} pengguna (termasuk yang nonaktif) sehingga tidak bisa dihapus.");
        }

        if (($count = CommitteeTier::query()->where('role', $role->name)->count()) > 0) {
            return back()->with('error', "\"{$role->name}\" memutus di {$count} jenjang komite sehingga tidak bisa dihapus. Ubah jenjang itu terlebih dahulu.");
        }

        Audit::record('roles.deleted', 'roles', 'deleted', $role, ['name' => $role->name], label: $role->name);
        $role->delete();

        return to_route('roles.index')->with('success', 'Peran berhasil dihapus.');
    }

    /** The role is one of those the code refers to by name (`RoleName`). */
    private function namedByCode(Role $role): bool
    {
        return RoleName::tryFrom($role->name) !== null;
    }

    /** How many people hold the role, those who are deactivated included. */
    private function holders(Role $role): int
    {
        return User::withTrashed()->role($role->name)->count();
    }
}
