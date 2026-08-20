<?php

namespace Database\Seeders;

use App\Models\SchemaDraft;
use App\Support\SchemaDesign;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Rancangan tabel bawaan pada modul Skema Migrasi (alat developer).
 * Rancangan tabel yang sudah ada di database diambil langsung dari skema nyata,
 * sehingga diff-nya selalu bersih setelah seeding.
 */
class SchemaDraftSeeder extends Seeder
{
    /** Rancangan tabel yang belum dimigrasikan. */
    private const PLANNED = [];

    /** Rancangan yang mengikuti tabel nyata. `lead` = kolom yang ditaruh paling atas. */
    private const MIRRORED = [
        [
            'name' => 'Agunan Kredit',
            'table_name' => 'collateral_simulations',
            'note' => 'Cerminan skema tabel agunan yang berjalan.',
            'with_soft_deletes' => true,
            'lead' => ['credit_account', 'collateral_id'],
        ],
        [
            'name' => 'Pengajuan Kredit',
            'table_name' => 'loan_applications',
            'note' => 'Berkas pengajuan sampai keputusan komite (analisa per produk menyusul).',
            'with_soft_deletes' => true,
            'lead' => ['application_code', 'application_date', 'status'],
        ],
        [
            'name' => 'Agunan pada Pengajuan',
            'table_name' => 'loan_application_collaterals',
            'note' => 'Penghubung berkas pengajuan dengan agunan yang dipakai.',
        ],
        [
            'name' => 'Persetujuan Komite',
            'table_name' => 'loan_approvals',
            'note' => 'Jejak keputusan berjenjang mengikuti jalur komite produk.',
        ],
    ];

    public function run(): void
    {
        foreach (self::MIRRORED as $item) {
            if (! Schema::hasTable($item['table_name'])) {
                continue;
            }

            $lead = $item['lead'] ?? [];
            unset($item['lead']);

            $draft = SchemaDraft::updateOrCreate(['table_name' => $item['table_name']], $item);
            $draft->columns()->delete();
            $draft->columns()->createMany(SchemaDesign::importFrom($item['table_name']));

            foreach ($lead as $index => $name) {
                $draft->columns()->where('name', $name)->update(['sort' => $index - count($lead)]);
            }
        }

        foreach (self::PLANNED as $item) {
            $columns = $item['columns'];
            unset($item['columns']);

            $draft = SchemaDraft::updateOrCreate(['table_name' => $item['table_name']], $item);
            $draft->columns()->delete();
            $draft->columns()->createMany(
                collect($columns)->map(fn (array $c, int $i) => [...$c, 'sort' => $i])->all(),
            );
        }
    }
}
