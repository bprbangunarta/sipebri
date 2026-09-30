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
 * Idempotent, and never runs in production.
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
        ['id' => 1, 'username' => 'superadmin', 'name' => 'IT Support', 'email' => 'sa@bprbangunarta.co.id', 'office' => null, 'role' => 'Super Admin', 'active' => true],
        ['id' => 5, 'username' => 'mohmuksin', 'name' => 'Moh. Muksin', 'email' => 'mohmuksin@gmail.com', 'office' => '00', 'role' => 'Direktur Utama', 'active' => true],
        ['id' => 6, 'username' => '31130999', 'name' => 'Wawan Irawan', 'email' => 'wawan.ir.74@yahoo.com', 'office' => '03', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 8, 'username' => '137010710', 'name' => 'Sep Deden Sugeng Muhadi', 'email' => 'dedensugeng@gmail.com', 'office' => '06', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 9, 'username' => 'bonnie', 'name' => 'Bonnie Andrew', 'email' => 'bonnieandrew30@gmail.com', 'office' => '00', 'role' => 'Direktur Bisnis', 'active' => true],
        ['id' => 10, 'username' => '148010411', 'name' => 'Tri Sugiarto', 'email' => 'trisugiarto73@gmail.com', 'office' => '00', 'role' => 'Direktur Kepatuhan', 'active' => true],
        ['id' => 21, 'username' => '255010918', 'name' => 'Asep Kurnia Efendi', 'email' => 'asepkurniaefendi@gmail.com', 'office' => '00', 'role' => 'Kepala Bagian Analis', 'active' => true],
        ['id' => 23, 'username' => '265010719', 'name' => 'Hendra', 'email' => 'hendraregi@gmail.com', 'office' => '00', 'role' => 'Staff Legal', 'active' => true],
        ['id' => 26, 'username' => '286010620', 'name' => 'Dede Doni', 'email' => 'doni_sanjaya@yahoo.com', 'office' => '00', 'role' => 'Kepala Seksi Analis', 'active' => true],
        ['id' => 27, 'username' => '287010620', 'name' => 'Yolanda Ismi Sopandi', 'email' => 'yolandaismi23@gmail.com', 'office' => '02', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 30, 'username' => '309011221', 'name' => 'Zulfadli Rizal', 'email' => 'zulfadlirizal@gmail.com', 'office' => '00', 'role' => 'Kepala Bagian Teknologi Informasi', 'active' => true],
        ['id' => 32, 'username' => '321010622', 'name' => 'Handri Yani', 'email' => 'handryyanz01@gmail.com', 'office' => '05', 'role' => 'Customer Service', 'active' => true],
        ['id' => 37, 'username' => '330011022', 'name' => 'Ari Rizal Zalaludin', 'email' => 'aririzalzalaludin@gmail.com', 'office' => '05', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 40, 'username' => '338010523', 'name' => 'Jaelani', 'email' => 'jaelanimuhamad50@gmail.com', 'office' => '00', 'role' => 'Kepala Seksi Analis', 'active' => true],
        ['id' => 43, 'username' => '346010823', 'name' => 'Candra Kirana', 'email' => '2411crkn@gmail.com', 'office' => '02', 'role' => 'Customer Service', 'active' => true],
        ['id' => 44, 'username' => '347010823', 'name' => 'Pajar Tri Darmanto', 'email' => 'fajartridarmanto98@gmail.com', 'office' => '02', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 45, 'username' => '349010923', 'name' => 'Doni Nurfadlilah', 'email' => 'dnurfadililah28@gmail.com', 'office' => '05', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 46, 'username' => '350010923', 'name' => 'Ahmad Fauzi', 'email' => 'ahmadfauzidali@gmail.com', 'office' => '04', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 51, 'username' => '355010923', 'name' => 'Mala Septiana Putri', 'email' => 'malaseptianaputri@stiesa.ac.id', 'office' => '01', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 53, 'username' => '357011023', 'name' => 'Sandi Taufik Hidayat', 'email' => 'sanditaufikhidayat26@gmail.com', 'office' => '00', 'role' => 'Kepala Seksi Frontliner', 'active' => true],
        ['id' => 61, 'username' => '370010324', 'name' => 'Yusup Dillan', 'email' => 'yusupdillan@gmail.com', 'office' => '04', 'role' => 'Kepala Kantor Kas', 'active' => true],
        ['id' => 63, 'username' => '373010324', 'name' => 'Ryan Adi Susanto', 'email' => 'ryanadiisusantoo@gmail.com', 'office' => '01', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 65, 'username' => '375010524', 'name' => 'Arga Maska Lakadewa', 'email' => 'argaa006@gmail.com', 'office' => '00', 'role' => 'Kepala Seksi Administrasi Kredit', 'active' => true],
        ['id' => 68, 'username' => '378010524', 'name' => 'Reynaldi Adrian Maulana', 'email' => 'amreynaldi20@gmail.com', 'office' => '00', 'role' => 'Customer Service', 'active' => true],
        ['id' => 72, 'username' => '394010724', 'name' => 'Hari Agung Riyadi', 'email' => 'hariagungriyadi017@gmail.com', 'office' => '00', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 84, 'username' => '410010325', 'name' => 'Pitri Yulianingsih', 'email' => 'pitriyulianingsih21@gmail.com', 'office' => '02', 'role' => 'Customer Service', 'active' => true],
        ['id' => 85, 'username' => '411010325', 'name' => 'Naufal Kurrez Zamy Kays Sansan Vivi', 'email' => 'naufalsv@gmail.com', 'office' => '04', 'role' => 'Staff Analis & Appraisal', 'active' => true],
        ['id' => 500, 'username' => '310011221', 'name' => 'Apip', 'email' => 'apipsasa7@gmail.com', 'office' => null, 'role' => 'Staff Sistem & Jaringan', 'active' => false],
    ];

    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        Audit::withoutAuditing(function (): void {
            $offices = [];

            foreach (self::OFFICES as $office) {
                $offices[$office['code']] = Office::query()->updateOrCreate(['code' => $office['code']], ['alias' => $office['alias'], 'name' => $office['name']])->id;
            }

            foreach (self::USERS as $row) {
                $user = User::withTrashed()->find($row['id']) ?? new User;
                $user->fill([
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'office_id' => $row['office'] !== null ? $offices[$row['office']] : null,
                ]);

                if (! $user->exists) {
                    $user->password = Hash::make(Str::random(64));
                }

                $user->save();
                $row['active'] ? ($user->trashed() && $user->restore()) : ($user->trashed() || $user->delete());
                $user->syncRoles([Role::findOrCreate($row['role'], 'web')]);
            }
        });
    }
}
