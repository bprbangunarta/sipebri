<?php

namespace App\Models;

use App\Audit\Auditable;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * The permission model of spatie/laravel-permission (see config/permission.php), with the flag the generator sets.
 *
 * @property bool $is_custom created from the permission generator rather than defined in App\Support\Modules
 * @property-read int|null $roles_count
 */
class Permission extends SpatiePermission
{
    use Auditable;

    protected function casts(): array
    {
        return ['is_custom' => 'boolean'];
    }
}
