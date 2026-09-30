<?php

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;

it('seeds the starter people with their roles and offices, and can run again', function () {
    $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);
    $this->seed(UserSeeder::class);

    $person = User::withTrashed()->findOrFail(30);
    expect(User::withTrashed()->count())->toBe(28)
        ->and($person->username)->toBe('309011221')
        ->and($person->office->alias)->toBe('PMK')
        ->and($person->hasRole('Kepala Bagian Teknologi Informasi'))->toBeTrue()
        ->and(User::withTrashed()->findOrFail(500)->trashed())->toBeTrue()
        ->and(User::withTrashed()->findOrFail(1)->hasRole('Super Admin'))->toBeTrue();
});
