<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
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
            $sa = User::create(array_merge([
                'email'     => 'zulfadlirizal@gmail.com',
                'name'      => 'Zulfadli Rizal',
                'username'  => 'zulfame',
                'region'    => 'PMK',
                'user_code' => 'ZFR',
                'phone'     => '6282320099971',
            ], $default_user_value));

            $admin = User::create(array_merge([
                'email'     => 'yandi@gmail.com',
                'name'      => 'Yandi Rosyandi',
                'username'  => 'yandi',
                'region'    => 'PMK',
                'user_code' => 'YRY',
                'phone'     => '6281242758273',
            ], $default_user_value));

            $dirut = User::create(array_merge([
                'email'     => 'mohmuksin@gmail.com',
                'name'      => 'Moh. Muksin',
                'username'  => 'dirut',
                'region'    => 'PMK',
                'user_code' => 'MMS',
                'phone'     => '6281242758273',
            ], $default_user_value));
            $direksi = User::create(array_merge([
                'email'     => 'dedi_kusnadi123@yahoo.co.id',
                'name'      => 'Dedi Kusnadi',
                'username'  => 'direksi',
                'region'    => 'PMK',
                'user_code' => 'DKN',
                'phone'     => '628122476871',
            ], $default_user_value));
            $kabag_a = User::create(array_merge([
                'email'     => 'sopyanasror78@gmail.com',
                'name'      => 'Sopyan Asror',
                'username'  => 'sopyan',
                'region'    => 'PMK',
                'user_code' => 'SYA',
                'phone'     => '628112341865',
            ], $default_user_value));
            $kabag_o = User::create(array_merge([
                'email'     => 'ununnurainun@gmail.com',
                'name'      => 'Unun Nurainun',
                'username'  => 'unun',
                'region'    => 'PMK',
                'user_code' => 'UNA',
                'phone'     => '6285691741888',
            ], $default_user_value));

            $kasi_a = User::create(array_merge([
                'email'     => 'doni_sanjaya@yahoo.com',
                'name'      => 'Dede Doni',
                'username'  => 'dedon',
                'region'    => 'PMK',
                'user_code' => 'DDN',
                'phone'     => '6285221129262',
            ], $default_user_value));
            $ht = User::create(array_merge([
                'email'     => 'dwigustianisamalik@gmail.com',
                'name'      => 'Dwi Gustianisa Malik',
                'username'  => 'dwi',
                'region'    => 'PMK',
                'user_code' => 'DGM',
                'phone'     => '6281394728514',
            ], $default_user_value));
            $kasi_adm = User::create(array_merge([
                'email'     => 'yudhasaktipratama1955@gmail.com',
                'name'      => 'Yudha Sakti Pratama',
                'username'  => 'yudha',
                'region'    => 'PMK',
                'user_code' => 'YSP',
                'phone'     => '628991699083',
            ], $default_user_value));
            $cs = User::create(array_merge([
                'email'     => 'ariyantiaja05@gmail.com',
                'name'      => 'Ariyanti',
                'username'  => 'ariyanti',
                'region'    => 'PMK',
                'user_code' => 'ARY',
                'phone'     => '6281914960070',
            ], $default_user_value));
            $realisasi = User::create(array_merge([
                'email'     => 'sitipondia5@gmail.com',
                'name'      => 'Siti Pondia',
                'username'  => 'sipon',
                'region'    => 'PMK',
                'user_code' => 'SPN',
                'phone'     => '6281228170484',
            ], $default_user_value));
            $kry = User::create(array_merge([
                'email'     => 'apipsasa7@gmail.com',
                'name'      => 'Apip',
                'username'  => 'apip',
                'region'    => 'PMK',
                'user_code' => 'APP',
                'phone'     => '6285221561458',
            ], $default_user_value));

            Role::create(['name' => 'Administrator']);
            Role::create(['name' => 'Direksi']);
            Role::create(['name' => 'Kabag Analis']);
            Role::create(['name' => 'Kabag Operasional']);
            Role::create(['name' => 'Kasi Analis']);
            Role::create(['name' => 'Head Teller']);
            Role::create(['name' => 'Kasi Admin']);
            Role::create(['name' => 'Staff Analis']);
            Role::create(['name' => 'Customer Service']);
            Role::create(['name' => 'Realisasi']);
            Role::create(['name' => 'Karyawan']);

            $sa->assignRole('Administrator');
            $admin->assignRole('Administrator');
            $dirut->assignRole('Direksi');
            $direksi->assignRole('Direksi');
            $kabag_a->assignRole('Kabag Analis');
            $kabag_o->assignRole('Kabag Operasional');
            $kasi_a->assignRole('Kasi Analis');
            $ht->assignRole('Head Teller');
            $kasi_adm->assignRole('Kasi Admin');
            $cs->assignRole('Customer Service');
            $realisasi->assignRole('Realisasi');
            $kry->assignRole('Karyawan');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
        }
    }
}
