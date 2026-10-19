<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\UserListOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
            'per_page' => ['nullable', 'integer'],
            'sort' => ['nullable', Rule::in(UserListOrder::USER_KEYS)],
            'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $sort = $filters['sort'] ?? 'name';
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $roles = Role::query()->orderBy('name')->pluck('name');

        // Inactive people (soft deleted, as reported by Codex) stay listed; the status filter narrows them down.
        $users = User::withTrashed()->with(['roles:id,name', 'office:id,alias,name'])
            ->when($filters['search'] ?? null, function ($q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('username', 'like', $like));
            })
            // A role that does not exist holds nobody; asking for it is an empty list, not an error.
            ->when($filters['role'] ?? null, fn ($q, string $role) => $roles->contains($role) ? $q->role($role) : $q->whereRaw('1 = 0'))
            ->when(($filters['status'] ?? null) === 'active', fn ($q) => $q->whereNull('users.deleted_at'))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($q) => $q->whereNotNull('users.deleted_at'))
            ->tap(fn ($q) => UserListOrder::apply($q, $sort, $direction))
            ->orderBy('name')->orderBy('users.id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), [10, 25, 50], true) ? (int) $filters['per_page'] : 25)
            ->withQueryString();

        return Inertia::render('users/index', [
            'users' => $users->through(fn (User $u): array => [
                'id' => $u->id, 'name' => $u->name, 'username' => $u->username, 'email' => $u->email,
                'office' => $u->office?->label(),
                'active' => ! $u->trashed(),
                'role' => $u->getRoleNames()->first(), 'created_at' => $u->created_at?->toDateString(),
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'role' => $filters['role'] ?? null, 'status' => $filters['status'] ?? null, 'sort' => $sort, 'direction' => $direction, 'per_page' => $users->perPage()],
            'roles' => $roles,
        ]);
    }
}
