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
    private const PLANNED = [
        [
            'name' => 'Berkas Pengajuan',
            'table_name' => 'credit_applications',
            'note' => 'Rangka tahap 1 dari 9: pintu masuk berkas kredit.',
            'columns' => [
                ['name' => 'register_number', 'type' => 'string', 'length' => '30', 'is_unique' => true],
                ['name' => 'product_id', 'type' => 'foreignId', 'foreign_table' => 'products'],
            ],
        ],
    ];

    /** Rancangan yang mengikuti tabel nyata. `lead` = kolom yang ditaruh paling atas. */
    private const MIRRORED = [
        [
            'name' => 'Agunan Kredit',
            'table_name' => 'collateral_simulations',
            'note' => 'Cerminan skema tabel agunan yang berjalan.',
            'lead' => ['credit_account', 'collateral_id'],
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
