<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_user_value = [
            'email_verified_at' => now(),
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token'    => Str::random(10),
        ];

        DB::beginTransaction();

        try {
            $admin = User::create(array_merge([
                'email'     => 'zulfadlirizal@gmail.com',
                'name'      => 'Zulfadli Rizal',
                'username'  => 'zulfame',
            ], $default_user_value));

            $admin = User::create(array_merge([
                'email'     => 'yandi@gmail.com',
                'name'      => 'Yandi',
                'username'  => 'yandi',
            ], $default_user_value));

            $writer = User::create(array_merge([
                'email'     => 'mutiawr27@gmail.com',
                'name'      => 'Mutia Wahida Rahmi',
                'username'  => 'mutia',
            ], $default_user_value));

            $role_admin   = Role::create(['name' => 'Administrator']);
            $role_writer    = Role::create(['name' => 'Writer']);

            $permission = Permission::create(['name' => 'read post']);

            $role_writer->givePermissionTo('read post');

            $admin->assignRole('Administrator');
            $writer->assignRole('Writer');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
