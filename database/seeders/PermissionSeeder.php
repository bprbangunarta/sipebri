<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Support\Modules;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Modules::systemPermissions() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        // Drop system permissions of modules that no longer exist; permissions from the generator are kept.
        Permission::query()->where('is_custom', false)->whereNotIn('name', Modules::systemPermissions())->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
