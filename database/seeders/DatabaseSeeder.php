<?php

namespace Database\Seeders;

use App\Audit\Audit;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeding is not user activity; keep it out of the audit trail.
        Audit::withoutAuditing($this->seed(...));
    }

    private function seed(): void
    {
        $this->call([PermissionSeeder::class, RoleSeeder::class, CreditReferenceSeeder::class, CommitteeSeeder::class, UserSeeder::class]);
    }
}
