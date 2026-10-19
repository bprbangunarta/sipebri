<?php

namespace Database\Seeders;

use App\Audit\Audit;
use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * Starter people for testing: one or more of every role involved in the credit flow, so each screen can be
 * tried without waiting for a first Codex sign-in. They mirror what CodexUserSync would create (same ids,
 * usernames, offices, roles); signing in through Codex refreshes them. Nobody signs in with a local password.
 * Idempotent, and never runs in production. Committee members also get a fake NIK when they have none.
 */
class UserSeeder extends Seeder
{
    /** @var list<array{code: string, alias: string, name: string}> */
    private const OFFICES = [
        ['code' => '00', 'alias' => 'PMK', 'name' => 'Pamanukan'],
        ['code' => '01', 'alias' => 'CGK', 'name' => 'Jalancagak'],
        ['code' => '02', 'alias' => 'SBG', 'name' => 'Subang'],
        ['code' => '03', 'alias' => 'SKM', 'name' => 'Sukamandi'],
        ['code' => '04', 'alias' => 'PGD', 'name' => 'Pagaden'],
        ['code' => '05', 'alias' => 'KJT', 'name' => 'Kalijati'],
        ['code' => '06', 'alias' => 'PSK', 'name' => 'Pusakajaya'],
    ];

    /** @var list<array{id: int, username: string, name: string, email: string, office: string|null, role: string, active: bool}> */
    private const USERS = [
        ['id' => 1, 'username' => 'superadmin', 'name' => 'IT Support', 'email' => 'superadmin@contoh.test', 'office' => null, 'role' => 'Super Admin', 'active' => true],
        ['id' => 5, 'username' => '900000005', 'name' => 'Haryono Wibisono', 'email' => '900000005@contoh.test', 'office' => '00', 'role' => 'Direktur Utama', 'active' => true],
        ['id' => 6, 'username' => '900000006', 'name' => 'Siti Aminah Rahayu', 'email' => '900000006@contoh.test', 'office' => '03', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 8, 'username' => '900000008', 'name' => 'Darmawan Setiabudi', 'email' => '900000008@contoh.test', 'office' => '06', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 9, 'username' => '900000009', 'name' => 'Rudi Hartanto', 'email' => '900000009@contoh.test', 'office' => '00', 'role' => 'Direktur Bisnis', 'active' => true],
        ['id' => 10, 'username' => '900000010', 'name' => 'Lestari Anggraini', 'email' => '900000010@contoh.test', 'office' => '00', 'role' => 'Direktur Kepatuhan', 'active' => true],
        ['id' => 21, 'username' => '900000021', 'name' => 'Agus Pramono', 'email' => '900000021@contoh.test', 'office' => '00', 'role' => 'Kepala Bagian Analis', 'active' => true],
        ['id' => 23, 'username' => '900000023', 'name' => 'Nanda Pratiwi', 'email' => '900000023@contoh.test', 'office' => '00', 'role' => 'Staff Legal', 'active' => true],
        ['id' => 26, 'username' => '900000026', 'name' => 'Budi Santoso', 'email' => '900000026@contoh.test', 'office' => '00', 'role' => 'Kepala Seksi Analis', 'active' => true],
        ['id' => 27, 'username' => '900000027', 'name' => 'Maya Kusumawati', 'email' => '900000027@contoh.test', 'office' => '02', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 30, 'username' => '900000030', 'name' => 'Andi Wijaya', 'email' => '900000030@contoh.test', 'office' => '00', 'role' => 'Kepala Bagian Teknologi Informasi', 'active' => true],
        ['id' => 32, 'username' => '900000032', 'name' => 'Rina Marlina', 'email' => '900000032@contoh.test', 'office' => '05', 'role' => 'Customer Service', 'active' => true],
        ['id' => 37, 'username' => '900000037', 'name' => 'Teguh Prakoso', 'email' => '900000037@contoh.test', 'office' => '05', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 40, 'username' => '900000040', 'name' => 'Joko Susilo', 'email' => '900000040@contoh.test', 'office' => '00', 'role' => 'Kepala Seksi Analis', 'active' => true],
        ['id' => 43, 'username' => '900000043', 'name' => 'Putri Ayu Lestari', 'email' => '900000043@contoh.test', 'office' => '02', 'role' => 'Customer Service', 'active' => true],
        ['id' => 44, 'username' => '900000044', 'name' => 'Fajar Nugroho', 'email' => '900000044@contoh.test', 'office' => '02', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 45, 'username' => '900000045', 'name' => 'Dewi Safitri', 'email' => '900000045@contoh.test', 'office' => '05', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 46, 'username' => '900000046', 'name' => 'Irfan Maulana', 'email' => '900000046@contoh.test', 'office' => '04', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 51, 'username' => '900000051', 'name' => 'Wulan Purnamasari', 'email' => '900000051@contoh.test', 'office' => '01', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 53, 'username' => '900000053', 'name' => 'Hendra Gunawan', 'email' => '900000053@contoh.test', 'office' => '00', 'role' => 'Kepala Seksi Frontliner', 'active' => true],
        ['id' => 61, 'username' => '900000061', 'name' => 'Yusuf Ramadhan', 'email' => '900000061@contoh.test', 'office' => '04', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 63, 'username' => '900000063', 'name' => 'Ratna Sari Dewi', 'email' => '900000063@contoh.test', 'office' => '01', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 65, 'username' => '900000065', 'name' => 'Eko Prasetyo', 'email' => '900000065@contoh.test', 'office' => '00', 'role' => 'Kepala Seksi Administrasi Kredit', 'active' => true],
        ['id' => 68, 'username' => '900000068', 'name' => 'Rizky Fadillah', 'email' => '900000068@contoh.test', 'office' => '00', 'role' => 'Customer Service', 'active' => true],
        ['id' => 72, 'username' => '900000072', 'name' => 'Surya Atmaja', 'email' => '900000072@contoh.test', 'office' => '00', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 84, 'username' => '900000084', 'name' => 'Ayu Kartika Sari', 'email' => '900000084@contoh.test', 'office' => '02', 'role' => 'Customer Service', 'active' => true],
        ['id' => 85, 'username' => '900000085', 'name' => 'Muhammad Naufal Abdurrahman Zaki Pratama', 'email' => '900000085@contoh.test', 'office' => '04', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 500, 'username' => '900000500', 'name' => 'Anton Sudrajat', 'email' => '900000500@contoh.test', 'office' => null, 'role' => 'Staff Sistem & Jaringan', 'active' => false],
    ];

    /** Roles that sit in the default committee levels. */
    private const COMMITTEE_ROLES = ['Kepala Seksi Analis', 'Kepala Bagian Analis', 'Direktur Bisnis', 'Direktur Utama'];

    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        Audit::withoutAuditing(function (): void {
            $offices = [];

            foreach (self::OFFICES as $office) {
                $offices[$office['code']] = Office::query()->firstOrCreate(['code' => $office['code']], ['alias' => $office['alias'], 'name' => $office['name']])->id;
            }

            foreach (self::USERS as $row) {
                // A person who already exists (from Codex, or changed on screen) is left exactly as they are: the seeder only adds the missing ones.
                if (User::withTrashed()->whereKey($row['id'])->exists()) {
                    continue;
                }

                $user = new User;
                $user->fill([
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'office_id' => $row['office'] !== null ? $offices[$row['office']] : null,
                ]);
                // Nobody signs in with this; it only satisfies the column.
                $user->password = Hash::make(Str::random(64));

                $user->save();
                $row['active'] || $user->delete();
                $user->syncRoles([Role::findOrCreate($row['role'], 'web')]);

                // Committee members get a made-up NIK (obviously fake: 9999 prefix) so conflict detection can be tried;
                // a NIK that Codex or an admin already set is never replaced.
                if ($user->nik === null && in_array($row['role'], self::COMMITTEE_ROLES, true)) {
                    $user->setNik('9999'.str_pad((string) $row['id'], 12, '0', STR_PAD_LEFT), 'manual');
                }
            }
        });
    }
}
