<?php

namespace Database\Seeders;

use App\Support\Modules;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Modules::permissions() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // Drop permissions of modules that no longer exist.
        Permission::query()->whereNotIn('name', Modules::permissions())->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
