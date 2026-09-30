<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Support\Modules;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles. Their names are exactly what the Codex sign-in API returns, so a person's Codex role name is
 * the key that links them to a role here (a name that is not listed makes the person a Guest).
 * Super Admin always holds every permission; the other roles are only given their default permissions
 * the first time, so changes made on the Roles screen are never overwritten. Safe to run again.
 */
class RoleSeeder extends Seeder
{
    /**
     * Every role Codex can report. Keep this list identical to Codex.
     *
     * @var list<string>
     */
    private const ROLES = [
        'Super Admin',
        'Komisaris Utama',
        'Komisaris',
        'Direktur Utama',
        'Direktur Kepatuhan',
        'Direktur Bisnis',
        'Direktur Operasional',
        'Kepala Kantor Kas',
        'Kepala Seksi Customer Care',
        'Kepala Bagian Kepatuhan',
        'Kepala Bagian Kredit',
        'Kepala Seksi Kredit',
        'Kepala Seksi Remedial',
        'Kepala Seksi Umum',
        'AO Kredit',
        'Kepala Seksi Dana',
        'AO Dana',
        'Customer Care',
        'Staff Umum',
        'Kepala Bagian Operasional',
        'Kepala Bagian Analis',
        'Kepala Seksi Akuntansi dan Keuangan',
        'Staff Legal',
        'Kepala Seksi Analis',
        'Kepala Bagian Audit Internal',
        'Kepala Seksi SDM',
        'Kepala Seksi Administrasi Kredit',
        'Kepala Bagian Teknologi Informasi',
        'Staff Sistem & Jaringan',
        'Customer Service',
        'Kepala Bagian SDM & Umum',
        'Staff Remedial',
        'Kepala Bagian Pendanaan',
        'Akunting',
        'Staff SDM',
        'Staff Analis & Appraisal',
        'Marketing Deposito',
        'Staff Web Development',
        'Staff Kepatuhan',
        'Staff Audit Internal',
        'Staff Administrasi Kredit',
        'Teller',
        'Kepala Seksi Frontliner',
        'Staff Teknologi Informasi',
        'Trainee',
        'Guest',
    ];

    /**
     * Initial permissions of the roles the credit workflow depends on (see App\Enums\RoleName); every
     * other role starts without permissions. Walk-in (KTA) files are handled by office staff, who
     * register and survey them.
     *
     * @var array<string, list<string>>
     */
    private const DEFAULT_PERMISSIONS = [
        'AO Kredit' => ['dashboard.view', 'loan-applications.view', 'loan-applications.manage', 'collaterals.view', 'collaterals.manage'],
        'Kepala Seksi Analis' => ['dashboard.view', 'loan-applications.view', 'scheduling.view', 'scheduling.manage', 'surveys.view', 'surveys.manage'],
        'Staff Analis & Appraisal' => ['dashboard.view', 'loan-applications.view', 'surveys.view', 'surveys.manage', 'analysis.view'],
        'Kepala Bagian Analis' => ['dashboard.view'],
        'Direktur Bisnis' => ['dashboard.view'],
        'Direktur Utama' => ['dashboard.view'],
        'Kepala Kantor Kas' => ['dashboard.view', 'loan-applications.view', 'loan-applications.manage', 'collaterals.view', 'collaterals.manage', 'surveys.view', 'surveys.manage', 'analysis.view'],
        'Customer Service' => ['dashboard.view', 'loan-applications.view', 'loan-applications.manage', 'collaterals.view', 'collaterals.manage', 'surveys.view', 'surveys.manage', 'analysis.view'],
        'Teller' => ['dashboard.view', 'loan-applications.view', 'loan-applications.manage', 'collaterals.view', 'collaterals.manage', 'surveys.view', 'surveys.manage', 'analysis.view'],
    ];

    /**
     * Permissions a workflow role must always hold (added without touching other, manually managed ones).
     * The surveyor ladder means senior roles survey a file again when an earlier survey was judged insufficient.
     *
     * @var array<string, list<string>>
     */
    private const REQUIRED = [
        'Staff Analis & Appraisal' => ['analysis.view'],
        'Kepala Kantor Kas' => ['analysis.view'],
        'Customer Service' => ['analysis.view'],
        'Teller' => ['analysis.view'],
        'Kepala Seksi Analis' => ['surveys.view', 'surveys.manage'],
        'Kepala Bagian Analis' => ['surveys.view', 'surveys.manage'],
        'Direktur Bisnis' => ['surveys.view', 'surveys.manage'],
        'Direktur Utama' => ['surveys.view', 'surveys.manage'],
    ];

    /**
     * @return list<string>
     */
    public static function roleNames(): array
    {
        return self::ROLES;
    }

    public function run(): void
    {
        $this->call(PermissionSeeder::class);

        foreach (self::ROLES as $name) {
            $role = Role::findOrCreate($name, 'web');

            if ($name === RoleName::SuperAdmin->value) {
                $role->syncPermissions(Modules::permissions());

                continue;
            }

            if (isset(self::DEFAULT_PERMISSIONS[$name]) && $role->permissions->isEmpty()) {
                $role->syncPermissions(self::DEFAULT_PERMISSIONS[$name]);
            }
        }

        foreach (self::REQUIRED as $name => $permissions) {
            Role::findOrCreate($name, 'web')->givePermissionTo($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
