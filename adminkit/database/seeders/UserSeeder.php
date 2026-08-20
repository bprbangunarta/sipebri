<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun bawaan. Kata sandi HANYA disetel saat akun dibuat pertama kali,
 * jadi menjalankan seeder ulang tidak mengubah kata sandi yang sudah dipakai.
 */
class UserSeeder extends Seeder
{
    /** Kata sandi awal seluruh akun pegawai uji. */
    private const DEFAULT_PASSWORD = 'password';

    /** [nama, username, email, kantor, peranan, alias, kode MSO, kode kolektor] */
    private const USERS = [
        ['Moh. Muksin', 'mohmuksin', 'mohmuksin@gmail.com', 'Pamanukan', 'Direktur Utama', 'MMN', null, null],
        ['Bonnie Andrew', 'bonnie', 'bonnieandrew30@gmail.com', 'Pamanukan', 'Direktur Bisnis', 'BAW', null, null],
        ['Asep Kurnia Efendi', '255010918', 'asepkurniaefendi@gmail.com', 'Pamanukan', 'Kabag Analis', 'AKE', null, null],
        ['Dede Doni', '286010620', 'doni_sanjaya@yahoo.com', 'Pamanukan', 'Kasi Analis', 'DDN', null, '585'],
        ['Jaelani', '338010523', 'jaelanimuhamad50@gmail.com', 'Pamanukan', 'Kasi Analis', 'JAE', null, null],
        ['Pajar Tri Darmanto', '347010823', 'fajartridarmanto98@gmail.com', 'Subang', 'Staff Analis', 'PTD', null, null],
        ['Doni Nurfadlilah', '349010923', 'dnurfadililah28@gmail.com', 'Subang', 'Staff Analis', 'DNF', null, null],
        ['Ahmad Fauzi', '350010923', 'ahmadfauzidali@gmail.com', 'Pagaden', 'Staff Analis', 'AFZ', null, null],
        ['Ryan Adi Susanto', '373010324', 'ryanadiisusantoo@gmail.com', 'Subang', 'Staff Analis', 'RAS', null, null],
        ['Hari Agung Riyadi', '394010724', 'hariagungriyadi017@gmail.com', 'Pamanukan', 'Staff Analis', 'HAR', null, null],
        ['Naufal Kurrez Zamy Kays Sansan Vivi', '411010325', 'naufalsv@gmail.com', 'Pagaden', 'Staff Analis', 'NKZ', null, null],
        ['Wawan Irawan', '31130999', 'wawan.ir.74@yahoo.com', 'Sukamandi', 'Kepala Kantor Kas', 'WWN', '0092', null],
        ['Sep Deden Sugeng Muhadi', '137010710', 'dedensugeng@gmail.com', 'Pusakajaya', 'Kepala Kantor Kas', 'SDN', '0019', null],
        ['Yolanda Ismi Sopandi', '287010620', 'yolandaismi23@gmail.com', 'Subang', 'Kepala Kantor Kas', 'YIS', null, null],
        ['Ari Rizal Zalaludin', '330011022', 'aririzalzalaludin@gmail.com', 'Kalijati', 'Kepala Kantor Kas', 'ARZ', '0010', null],
        ['Mala Septiana Putri', '355010923', 'malaseptianaputri@stiesa.ac.id', 'Jalancagak', 'Kepala Kantor Kas', 'MLS', '0043', null],
        ['Yusup Dillan', '370010324', 'yusupdillan@gmail.com', 'Pagaden', 'Kepala Kantor Kas', 'YDL', '0024', null],
        ['Handri Yani', '321010622', 'handryyanz01@gmail.com', 'Kalijati', 'Customer Service', 'HYN', '0032', null],
        ['Candra Kirana', '346010823', '2411crkn@gmail.com', 'Subang', 'Customer Service', 'CKN', '0016', null],
        ['Reynaldi Adrian Maulana', '378010524', 'amreynaldi20@gmail.com', 'Pamanukan', 'Customer Service', 'RAM', null, null],
        ['Pitri Yulianingsih', '410010325', 'pitriyulianingsih21@gmail.com', 'Subang', 'Customer Service', 'PYN', null, null],
    ];

    public function run(): void
    {
        // Akun pemilik sistem — kata sandinya berbeda dari akun pegawai uji.
        $this->upsert(
            ['IT Support', 'superadmin', 'sa@bprbangunarta.co.id', 'Kantor Pusat', RoleName::SuperAdmin->value, null, null, null],
            'SA@4dm1n',
        );

        foreach (self::USERS as $row) {
            $this->upsert($row, self::DEFAULT_PASSWORD);
        }
    }

    private function upsert(array $row, string $password): void
    {
        [$name, $username, $email, $office, $role, $alias, $msoCode, $collectorCode] = $row;

        $user = User::withTrashed()->firstOrNew(['email' => $email]);

        $user->fill([
            'name' => $name,
            'username' => $username,
            'office' => $office,
            'alias' => $alias,
            'mso_code' => $msoCode,
            'collector_code' => $collectorCode,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);
        $user->deleted_at = null;

        if (! $user->exists) {
            $user->password = $password;
        }

        $user->save();
        $user->setRoleName($role);
    }
}
