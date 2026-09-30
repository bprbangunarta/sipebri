<?php

use App\Enums\RoleName;
use App\Support\Modules;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

it('creates exactly the roles Codex knows, and no duplicates', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(RoleSeeder::class);

    expect(Role::pluck('name')->sort()->values()->all())->toBe(collect(RoleSeeder::roleNames())->sort()->values()->all())
        ->and(RoleSeeder::roleNames())->toHaveCount(46)->and(array_unique(RoleSeeder::roleNames()))->toHaveCount(46);
});

it('knows every role the code refers to', function () {
    foreach (RoleName::cases() as $role) {
        expect(RoleSeeder::roleNames())->toContain($role->value);
    }

    foreach (config('credit.surveyor_ladder') as $name) {
        expect(RoleSeeder::roleNames())->toContain($name);
    }
});

it('gives Super Admin every permission and does not overwrite permissions changed by hand', function () {
    $this->seed(RoleSeeder::class);
    Role::findByName('AO Kredit')->syncPermissions(['dashboard.view']);
    $this->seed(RoleSeeder::class);

    expect(Role::findByName('Super Admin')->permissions->count())->toBe(count(Modules::permissions()))
        ->and(Role::findByName('AO Kredit')->permissions->pluck('name')->all())->toBe(['dashboard.view'])
        ->and(Role::findByName('Direktur Kepatuhan')->permissions)->toHaveCount(0);
});
