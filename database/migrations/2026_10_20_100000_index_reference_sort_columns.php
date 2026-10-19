<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The reference lists sort by every column (config/references.php); each sort key gets an index. `code` is already unique.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'regions' => ['regency', 'district', 'village', 'postal_code'],
        'offices' => ['alias', 'name'],
        'institutions' => ['name'],
        'products' => ['alias', 'name', 'is_active'],
        'installments' => ['name', 'period_months'],
        'methods' => ['name'],
        'collateral_types' => ['name'],
        'binding_types' => ['name'],
        'collateral_conditions' => ['name'],
        'collateral_methods' => ['name'],
        'ownership_statuses' => ['name'],
    ];

    public function up(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns): void {
                foreach ($columns as $column) {
                    if (! Schema::hasIndex($table, [$column])) {
                        $blueprint->index($column);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::COLUMNS as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns): void {
                foreach ($columns as $column) {
                    $blueprint->dropIndex([$column]);
                }
            });
        }
    }
};
