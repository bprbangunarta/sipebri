<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
        ]);

        // Inactive people (soft deleted, as reported by Codex) stay listed; the status filter narrows them down.
        $users = User::withTrashed()->with(['roles:id,name', 'office:id,alias,name'])
            ->when($filters['search'] ?? null, function ($q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('email', 'like', $like)->orWhere('username', 'like', $like));
            })
            ->when($filters['role'] ?? null, fn ($q, string $role) => $q->role($role))
            ->when(($filters['status'] ?? null) === 'active', fn ($q) => $q->whereNull('users.deleted_at'))
            ->when(($filters['status'] ?? null) === 'inactive', fn ($q) => $q->whereNotNull('users.deleted_at'))
            ->orderBy('name')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), [10, 25, 50], true) ? (int) $filters['per_page'] : 10)
            ->withQueryString();

        return Inertia::render('users/index', [
            'users' => $users->through(fn (User $u): array => [
                'id' => $u->id, 'name' => $u->name, 'username' => $u->username, 'email' => $u->email,
                'office' => $u->office ? trim(($u->office->alias ? $u->office->alias.' : ' : '').$u->office->name) : null,
                'active' => ! $u->trashed(),
                'role' => $u->getRoleNames()->first(), 'created_at' => $u->created_at?->toDateString(),
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'role' => $filters['role'] ?? null, 'status' => $filters['status'] ?? null, 'per_page' => $users->perPage()],
            'roles' => Role::query()->orderBy('name')->pluck('name'),
        ]);
    }
}
