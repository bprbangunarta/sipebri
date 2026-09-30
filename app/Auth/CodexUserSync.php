<?php

namespace App\Auth;

use App\Audit\Audit;
use App\Enums\RoleName;
use App\Models\Office;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Permission\Models\Role;

/**
 * Creates or updates the local user from a Codex response, so the local copy always mirrors what
 * Codex says (name, email, role, office, active flag). Only what this system uses is kept; the Codex API
 * serves other systems too and sends more (birthday, codes, device data, office coordinates, ...). The local id IS the Codex id, so other systems can
 * refer to a person by the same number. The local password is never used to sign in.
 */
class CodexUserSync
{
    /**
     * @param  array<string, mixed>  $user
     * @param  array<string, mixed>|null  $office
     */
    public function sync(array $user, ?array $office): User
    {
        return Audit::withContext(['source' => 'codex'], 'Codex sync', fn (): User => $this->mirror($user, $office));
    }

    /**
     * @param  array<string, mixed>  $user
     * @param  array<string, mixed>|null  $office
     */
    private function mirror(array $user, ?array $office): User
    {
        $id = (int) $user['id'];
        $username = (string) $user['username'];
        $local = User::withTrashed()->find($id) ?? $this->adoptByUsername($username, $id) ?? new User;

        $local->fill([
            'id' => $id,
            'username' => $username,
            'name' => $user['name'] ?? $username,
            'email' => $this->email($user['email'] ?? null, $username, $local),
            'office_id' => $this->syncOffice($office, $user['office'] ?? null)?->id,
        ]);

        if (! $local->exists) {
            // Nobody signs in with this; it only satisfies the column.
            $local->password = Hash::make(Str::random(64));
        }

        $local->save();

        // Codex decides whether the person is active; here that is the soft delete: active people are
        // restored, inactive ones are soft-deleted (and cannot sign in). This is automatic.
        if (($user['is_active'] ?? false) === true) {
            $local->trashed() && $local->restore();
        } elseif (! $local->trashed()) {
            $local->delete();
        }

        $this->syncRole($local, $user['role'] ?? null);

        return $local;
    }

    /**
     * Codex is the source of truth for offices: the row is created or refreshed from what Codex sends,
     * alias included. If Codex sends no alias, an existing one is kept and a new office falls back to its
     * code (unless that is taken). A Codex alias that another office already holds is not stored. When
     * Codex sends only the office name, an existing office with that name is used.
     *
     * @param  array<string, mixed>|null  $office
     */
    private function syncOffice(?array $office, ?string $name): ?Office
    {
        if (blank($office['code'] ?? null)) {
            return $name === null ? null : Office::query()->where('name', $name)->first();
        }

        $code = (string) $office['code'];
        $record = Office::query()->firstOrNew(['code' => $code]);

        $record->name = $office['name'] ?? $name ?? $code;

        $record->alias = $this->officeAlias($office['alias'] ?? null, $code, $record);

        $record->save();

        return $record;
    }

    private function officeAlias(?string $sent, string $code, Office $record): ?string
    {
        $alias = $sent === null ? '' : Str::upper(trim($sent));
        $candidate = $alias !== '' ? $alias : ($record->alias ?? $code);
        $taken = Office::query()->where('alias', $candidate)->where('id', '!=', $record->id ?? 0)->exists();

        if ($taken) {
            report(new RuntimeException("Office alias {$candidate} is already used by another office; not stored."));

            return $record->alias;
        }

        return $candidate;
    }

    /**
     * A person stored before ids were aligned (same username, other id) is moved to the Codex id,
     * together with their role assignments, instead of being duplicated.
     */
    private function adoptByUsername(string $username, int $id): ?User
    {
        $existing = User::withTrashed()->where('username', $username)->first();

        if ($existing === null) {
            return null;
        }

        DB::transaction(function () use ($existing, $id): void {
            DB::table('model_has_roles')->where('model_type', User::class)->where('model_id', $existing->id)->update(['model_id' => $id]);
            DB::table('model_has_permissions')->where('model_type', User::class)->where('model_id', $existing->id)->update(['model_id' => $id]);
            DB::table('users')->where('id', $existing->id)->update(['id' => $id]);
            // Raw updates raise no model events; the id change is a notable event, so it is recorded by hand.
            Audit::record('users.id_migrated', 'users', 'id_migrated', null, ['id' => $existing->id], ['id' => $id], label: (string) $existing->username);
        });

        return User::withTrashed()->find($id);
    }

    /** The email must be unique here; fall back to a placeholder if Codex has none or another user holds it. */
    private function email(?string $email, string $username, User $local): string
    {
        $email = $email !== null ? Str::lower(trim($email)) : '';
        $taken = $email === '' || User::withTrashed()->where('email', $email)->where('id', '!=', $local->id ?? 0)->exists();

        return $taken ? Str::lower($username).'@codex.invalid' : $email;
    }

    /**
     * The role is the one Codex reports, when it exists locally (see RoleSeeder); anything else makes
     * the person a Guest. Super Admin is a Codex role like any other.
     */
    private function syncRole(User $user, ?string $name): void
    {
        $role = $name === null ? null : Role::where('name', $name)->where('guard_name', 'web')->first();

        if ($role === null) {
            if ($name !== null) {
                report(new RuntimeException("Unknown role from Codex, treated as Guest: {$name}"));
            }

            $role = Role::findOrCreate(RoleName::Guest->value, 'web');
        }

        $before = $user->getRoleNames()->all();
        $user->syncRoles([$role]);

        if ($before !== [$role->name]) {
            Audit::record('users.role_changed', 'users', 'role_changed', $user, ['roles' => $before], ['roles' => [$role->name]]);
        }
    }
}
