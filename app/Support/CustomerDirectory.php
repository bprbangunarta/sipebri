<?php

namespace App\Support;

use App\Services\CodexClient;
use RuntimeException;

/**
 * Bridge to the customer master. The app never stores applicant identity — only the national ID
 * (NIK), name and CIF number. Always the Codex API; when it is not configured the lookup fails visibly instead of guessing.
 */
class CustomerDirectory
{
    private const GENDER = ['L' => 'MALE', 'P' => 'FEMALE'];

    private const MARITAL = ['1' => 'SINGLE', '2' => 'MARRIED', '3' => 'WIDOWED/DIVORCED'];

    /**
     * Customer identity for a national ID, or null when it is not registered.
     *
     * @return array<string, mixed>|null
     */
    public static function find(string $nik): ?array
    {
        $nik = trim($nik);

        if (blank(config('services.codex.endpoint'))) {
            throw new RuntimeException('The customer master (Codex) is not configured.');
        }

        $data = app(CodexClient::class)->customer($nik);

        return $data ? self::shape($data) : null;
    }

    /**
     * @param  array<string, mixed>  $d
     * @return array<string, mixed>
     */
    private static function shape(array $d): array
    {
        return [
            'nik' => $d['nomor_ktp'] ?? null,
            'cif_number' => $d['nomor_cif'] ?? null,
            'full_name' => $d['nama_lengkap'] ?? null,
            'birth_place' => $d['tempat_lahir'] ?? null,
            'birth_date' => $d['tanggal_lahir'] ?? null,
            'gender' => self::GENDER[$d['jenis_kelamin'] ?? ''] ?? null,
            'marital_status' => self::MARITAL[$d['marital_status'] ?? ''] ?? null,
            'address' => $d['alamat_ktp'] ?? null,
            'phone' => $d['nomor_hp'] ?? null,
            'employer_name' => $d['tempat_bekerja'] ?? null,
            'income' => isset($d['penghasilan']) ? (int) $d['penghasilan'] : null,
            'source' => 'Codex',
        ];
    }
}
