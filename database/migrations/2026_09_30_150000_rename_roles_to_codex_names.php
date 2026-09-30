<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Role names now follow the Codex sign-in API. Existing roles are renamed in place (keeping their users
 * and permissions), committee tiers that point at them are updated, and old roles that have no
 * counterpart and no users are dropped.
 */
return new class extends Migration
{
    /** @var array<string, string> old name => Codex name */
    private const RENAMES = [
        'Kasi Analis' => 'Kepala Seksi Analis',
        'Kabag Analis' => 'Kepala Bagian Analis',
        'Staff Analis' => 'Staff Analis & Appraisal',
        'Dewan Komisaris' => 'Komisaris',
        'Kabag Audit Intern' => 'Kepala Bagian Audit Internal',
        'Kabag Kepatuhan' => 'Kepala Bagian Kepatuhan',
        'Kabag Kredit' => 'Kepala Bagian Kredit',
        'Kabag Operasional' => 'Kepala Bagian Operasional',
        'Kabag Pendanaan' => 'Kepala Bagian Pendanaan',
        'Kabag SDM dan Umum' => 'Kepala Bagian SDM & Umum',
        'Kabag Teknologi Informasi' => 'Kepala Bagian Teknologi Informasi',
        'Kasi Administrasi Kredit' => 'Kepala Seksi Administrasi Kredit',
        'Kasi Frontliner' => 'Kepala Seksi Frontliner',
        'Kasi Keuangan dan Akuntansi' => 'Kepala Seksi Akuntansi dan Keuangan',
        'Kasi Kredit' => 'Kepala Seksi Kredit',
        'Kasi Pendanaan' => 'Kepala Seksi Dana',
        'Kasi Remedial' => 'Kepala Seksi Remedial',
        'Kasi Umum' => 'Kepala Seksi Umum',
        'AO Funding' => 'AO Dana',
        'Costumer Care' => 'Customer Care',
        'Staff Admin SDM' => 'Staff SDM',
        'Staff Audit Intern' => 'Staff Audit Internal',
        'Staff Electronic Data Processing' => 'Staff Teknologi Informasi',
        'Staff Jaringan Sistem Operasi' => 'Staff Sistem & Jaringan',
        'Staff Kepatuhan APU-PPT PPPSPM' => 'Staff Kepatuhan',
        'Staff Kepatuhan Manajemen Risiko' => 'Staff Kepatuhan',
        'Staff System Development' => 'Staff Web Development',
    ];

    /** Old roles with no Codex counterpart: removed only when nobody holds them. */
    private const RETIRED = ['Collection Funding Officer', 'Driver', 'Satpam', 'Staff Digital Marketing', 'HR Admin', 'Viewer'];

    public function up(): void
    {
        foreach (self::RENAMES as $old => $new) {
            $oldId = DB::table('roles')->where('name', $old)->where('guard_name', 'web')->value('id');

            if ($oldId === null) {
                continue;
            }

            DB::table('committee_tiers')->where('role', $old)->update(['role' => $new]);

            if (DB::table('roles')->where('name', $new)->where('guard_name', 'web')->doesntExist()) {
                DB::table('roles')->where('id', $oldId)->update(['name' => $new]);

                continue;
            }

            $this->dropIfUnused($oldId);
        }

        foreach (self::RETIRED as $name) {
            $id = DB::table('roles')->where('name', $name)->where('guard_name', 'web')->value('id');

            if ($id !== null) {
                $this->dropIfUnused($id);
            }
        }
    }

    public function down(): void {}

    private function dropIfUnused(int $roleId): void
    {
        if (DB::table('model_has_roles')->where('role_id', $roleId)->doesntExist()) {
            DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }
    }
};
