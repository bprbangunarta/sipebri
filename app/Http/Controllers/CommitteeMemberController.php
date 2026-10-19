<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CommitteeAuthorityOptions;
use App\Support\CommitteeMemberOrder;
use App\Support\CommitteeMembers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The people on the credit committee (derived from the roles that decide in a committee tier) and their national ID, which is
 * how an applicant who is a committee member is recognised. The NIK normally arrives from Codex; a Super Admin can type it
 * until Codex sends it. It is only ever shown masked.
 */
class CommitteeMemberController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer'],
            'sort' => ['nullable', Rule::in(CommitteeMemberOrder::KEYS)],
            'direction' => ['nullable', 'in:asc,desc'],
        ]);
        $sort = $filters['sort'] ?? 'name';
        $direction = ($filters['direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        $roles = CommitteeMembers::committeeRoles();

        $members = CommitteeMembers::committee()->with(['roles:id,name', 'office:id,alias,name'])
            ->when($filters['search'] ?? null, function ($q, string $term) {
                $like = '%'.addcslashes($term, '%_\\').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('username', 'like', $like));
            })
            // A role that no committee level uses holds no member; asking for it is an empty list, not an error.
            ->when($filters['role'] ?? null, fn ($q, string $role) => in_array($role, $roles, true) ? $q->role($role) : $q->whereRaw('1 = 0'))
            ->tap(fn ($q) => CommitteeMemberOrder::apply($q, $sort, $direction))
            ->orderBy('name')->orderBy('id')
            ->paginate(in_array((int) ($filters['per_page'] ?? 0), [10, 25, 50], true) ? (int) $filters['per_page'] : 25)
            ->withQueryString();

        return Inertia::render('committees/members', [
            'members' => $members->through(fn (User $u): array => [
                'id' => $u->id, 'name' => $u->name, 'username' => $u->username, 'role' => $u->getRoleNames()->first(),
                'office' => $u->office?->alias, 'nik' => CommitteeMembers::mask($u->nik), 'nik_source' => $u->nik_source,
            ]),
            'filters' => ['search' => $filters['search'] ?? '', 'role' => $filters['role'] ?? null, 'sort' => $sort, 'direction' => $direction, 'per_page' => $members->perPage()],
            'roles' => $roles,
            'withoutNik' => CommitteeMembers::committee()->whereNull('nik_hash')->count(),
            'authority' => CommitteeAuthorityOptions::props(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(CommitteeMembers::isMember($user), 404);

        $data = $request->validate(['nik' => ['required', 'digits:16']], [], ['nik' => 'NIK']);

        $taken = User::query()->where('nik_hash', User::nikHash($data['nik']))->whereKeyNot($user->id)->exists();

        if ($taken) {
            throw ValidationException::withMessages(['nik' => 'NIK ini sudah dimiliki orang lain.']);
        }

        $user->setNik($data['nik'], 'manual');

        return back()->with('success', "NIK {$user->name} berhasil disimpan.");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless(CommitteeMembers::isMember($user), 404);

        $user->setNik(null, 'manual');

        return back()->with('success', "NIK {$user->name} berhasil dihapus.");
    }
}
