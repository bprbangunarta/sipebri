<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Support\Modules;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Peranan bawaan. Super Admin selalu memegang SELURUH izin.
 * Peranan lain dibuat tanpa izin — atur lewat modul Peranan bila perlu.
 */
class RoleSeeder extends Seeder
{
    /** Peranan tambahan: nama => daftar izin. */
    private const ROLES = [
        'Guest' => [],
        'Dewan Komisaris' => [],
        'Direktur Utama' => ['dashboard.view', 'profile.view', 'approval-simulation.view', 'approval-simulation.manage'],
        'Direktur Bisnis' => ['dashboard.view', 'profile.view', 'approval-simulation.view', 'approval-simulation.manage'],
        'Direktur Kepatuhan' => ['dashboard.view', 'profile.view', 'approval-simulation.view'],
        'Kabag Analis' => ['dashboard.view', 'profile.view', 'approval-simulation.view', 'approval-simulation.manage'],
        'Kabag Audit Intern' => [],
        'Kabag Kepatuhan' => [],
        'Kabag Kredit' => [],
        'Kabag Operasional' => [],
        'Kabag Pendanaan' => [],
        'Kabag SDM dan Umum' => [],
        'Kabag Teknologi Informasi' => [],
        'Kasi Administrasi Kredit' => [],
        'Kasi Analis' => [
            'dashboard.view', 'profile.view',
            'loan-simulation.view',
            'scheduling-simulation.view', 'scheduling-simulation.manage',
            'approval-simulation.view',
        ],
        'Kasi Frontliner' => [],
        'Kasi Keuangan dan Akuntansi' => [],
        'Kasi Kredit' => [],
        'Kasi Pendanaan' => [],
        'Kasi Remedial' => [],
        'Kasi Umum' => [],
        'Kepala Kantor Kas' => [],
        'AO Funding' => [],
        'AO Kredit' => [],
        'Akunting' => [],
        'Collection Funding Officer' => [],
        'Costumer Care' => [],
        'Customer Service' => [],
        'Teller' => [],
        'Staff Admin SDM' => [],
        'Staff Administrasi Kredit' => [],
        'Staff Analis' => [
            'dashboard.view', 'profile.view',
            'loan-simulation.view',
            'survey-simulation.view', 'survey-simulation.manage',
            'analysis-simulation.view', 'analysis-simulation.manage',
        ],
        'Staff Audit Intern' => [],
        'Staff Digital Marketing' => [],
        'Staff Electronic Data Processing' => [],
        'Staff Jaringan Sistem Operasi' => [],
        'Staff Kepatuhan APU-PPT PPPSPM' => [],
        'Staff Kepatuhan Manajemen Risiko' => [],
        'Staff Legal' => [],
        'Staff Remedial' => [],
        'Staff System Development' => [],
        'Staff Umum' => [],
        'Driver' => [],
        'Satpam' => [],
    ];

    /** Peranan yang menjadi jenjang pemutus komite (lihat CommitteeSeeder). */
    private const COMMITTEE_ROLES = ['Kasi Analis', 'Kabag Analis', 'Direktur Bisnis', 'Direktur Utama'];

    public function run(): void
    {
        Role::findOrCreate(RoleName::SuperAdmin->value, 'web')
            ->syncPermissions(Modules::permissions());

        foreach (self::ROLES as $name => $permissions) {
            $role = Role::findOrCreate($name, 'web');

            // Hanya menyetel izin saat peranan belum punya izin sama sekali,
            // supaya perubahan manual di aplikasi tidak tertimpa.
            if ($permissions !== [] && $role->permissions->isEmpty()) {
                $role->syncPermissions($permissions);
            }
        }

        // Pemutus komite wajib bisa menyimpan keputusan (izin baru, ditambahkan
        // tanpa menimpa izin lain yang mungkin sudah diatur manual).
        foreach (self::COMMITTEE_ROLES as $name) {
            Role::findOrCreate($name, 'web')->givePermissionTo('approval-simulation.manage');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
