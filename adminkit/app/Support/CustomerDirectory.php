<?php

namespace App\Support;

/**
 * Jembatan ke sistem pengelola data nasabah.
 *
 * MOCK: endpoint API belum tersedia, jadi data contoh dipakai supaya alur pengajuan
 * bisa diuji. SIPEBRI TIDAK menyimpan data pemohon — hanya nomor KTP & nama.
 * Ganti isi find() dengan panggilan HTTP saat endpoint siap.
 */
class CustomerDirectory
{
    /** Data contoh (MOCK) — kunci = nomor KTP. */
    private const SAMPLES = [
        '3213011203950001' => [
            'nik' => '3213011203950001',
            'cif_number' => 'CIF-000123',
            'full_name' => 'YAYAT SUHAYAT',
            'birth_place' => 'SUBANG',
            'birth_date' => '1995-03-12',
            'gender' => 'LAKI-LAKI',
            'marital_status' => 'MENIKAH',
            'address' => 'KAMPUNG CICARIU RT/RW 24/04 BUNIHAYU JALANCAGAK SUBANG',
            'phone' => '081234567890',
            'occupation' => 'KARYAWAN SWASTA',
            'employer_name' => 'PT. TAEKWANG SUBANG',
            'monthly_income' => 5200000,
            'monthly_expense' => 2100000,
            'companion' => ['name' => 'SITI AMINAH', 'nik' => '3213014506970002', 'relation' => 'ISTRI'],
        ],
        '3213012509880007' => [
            'nik' => '3213012509880007',
            'cif_number' => 'CIF-000456',
            'full_name' => 'KANA SUTISNA',
            'birth_place' => 'PAMANUKAN',
            'birth_date' => '1988-09-25',
            'gender' => 'LAKI-LAKI',
            'marital_status' => 'MENIKAH',
            'address' => 'PAMANUKAN PAMANUKAN SUBANG JAWA BARAT',
            'phone' => '081987654321',
            'occupation' => 'PEDAGANG',
            'employer_name' => 'TOKO SEMBAKO KANA',
            'monthly_income' => 8750000,
            'monthly_expense' => 3400000,
            'companion' => ['name' => 'ROHMAH', 'nik' => '3213016112900011', 'relation' => 'ISTRI'],
        ],
        '3213015207920003' => [
            'nik' => '3213015207920003',
            'cif_number' => null,
            'full_name' => 'YOYOH TOHAROH',
            'birth_place' => 'TANJUNGSIANG',
            'birth_date' => '1992-07-12',
            'gender' => 'PEREMPUAN',
            'marital_status' => 'JANDA/DUDA',
            'address' => 'DUSUN KOSEDAN SELATAN RT/RW 10/02 TANJUNGSIANG SUBANG',
            'phone' => '082112223334',
            'occupation' => 'IBU RUMAH TANGGA',
            'employer_name' => null,
            'monthly_income' => 3100000,
            'monthly_expense' => 1500000,
            'companion' => null,
        ],
    ];

    /** Identitas nasabah berdasarkan nomor KTP, null bila belum terdaftar. */
    public static function find(string $nik): ?array
    {
        return self::SAMPLES[trim($nik)] ?? null;
    }

    /** Nomor KTP contoh untuk pengujian alur. */
    public static function sampleNiks(): array
    {
        return array_keys(self::SAMPLES);
    }
}
