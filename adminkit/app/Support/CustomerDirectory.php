<?php

namespace App\Support;

use App\Services\CodexClient;

/**
 * Jembatan ke sistem pengelola data nasabah (API Codex).
 * SIPEBRI TIDAK menyimpan data pemohon — hanya nomor KTP, nama, dan nomor CIF.
 */
class CustomerDirectory
{
    private const GENDER = ['L' => 'LAKI-LAKI', 'P' => 'PEREMPUAN'];

    private const MARITAL = ['1' => 'BELUM MENIKAH', '2' => 'MENIKAH', '3' => 'JANDA/DUDA'];

    /** Identitas nasabah berdasarkan nomor KTP, null bila belum terdaftar. */
    public static function find(string $nik): ?array
    {
        $data = app(CodexClient::class)->customer(trim($nik));

        return $data ? self::shape($data) : null;
    }

    /** Nomor KTP contoh untuk pengujian alur. */
    public static function sampleNiks(): array
    {
        return (array) config('services.codex.sample_niks', []);
    }

    /** Bentuk data yang dipakai SIPEBRI. Kolom mentah tetap dibawa di `raw`. */
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
            'mother_name' => $d['ibu_kandung'] ?? null,
            'npwp' => $d['npwp'] ?? null,
            'address' => $d['alamat_ktp'] ?? null,
            'region_code' => $d['kode_dati2'] ?? null,
            'region_label' => trim(implode(', ', array_filter([
                $d['kelurahan'] ?? null, $d['kecamatan'] ?? null, $d['kabupaten'] ?? null,
            ]))) ?: null,
            'phone' => $d['nomor_hp'] ?? null,
            'email' => $d['alamat_email'] ?? null,
            'employer_name' => $d['tempat_bekerja'] ?? null,
            'income' => isset($d['penghasilan']) ? (int) $d['penghasilan'] : null,
            'dependents' => isset($d['tanggungan']) ? (int) $d['tanggungan'] : null,
            'companion' => filled($d['nama_lengkap_pasangan'] ?? null) ? [
                'name' => $d['nama_lengkap_pasangan'],
                'nik' => $d['nomor_ktp_pasangan'] ?? null,
                'phone' => $d['nomor_hp_pasangan'] ?? null,
            ] : null,
            'raw' => $d,
        ];
    }
}
