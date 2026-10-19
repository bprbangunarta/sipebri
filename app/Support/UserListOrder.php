<?php

namespace App\Support;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

/**
 * How each sort key of a list of people orders the rows (Data Pengguna and the committee members). A key that is not here is not an order;
 * each screen keeps its own whitelist of the keys it offers.
 */
class UserListOrder
{
    public const USER_KEYS = ['name', 'username', 'email', 'role', 'office', 'status', 'created'];

    public const MEMBER_KEYS = ['name', 'username', 'role', 'office', 'nik'];

    /**
     * @param  Builder<User>  $query
     * @param  'asc'|'desc'  $direction
     * @return Builder<User>
     */
    public static function apply(Builder $query, string $key, string $direction): Builder
    {
        $order = match ($key) {
            'role' => Role::query()->select('roles.name')
                ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->whereColumn('model_has_roles.model_id', 'users.id')
                ->where('model_has_roles.model_type', (new User)->getMorphClass())
                ->limit(1),
            'office' => Office::query()->select('alias')->whereColumn('offices.id', 'users.office_id'),
            // Typed or from Codex, and those without a number first when ascending.
            'nik' => 'nik_source',
            'status' => 'users.deleted_at',
            'created' => 'users.created_at',
            'username', 'email' => $key,
            default => 'name',
        };

        return $query->orderBy($order, $direction);
    }
}
