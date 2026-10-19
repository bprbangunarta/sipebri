<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<string, array<string, string>> table => index name => column */
    private const INDEXES = [
        'loan_applications' => [
            'loan_apps_application_date_idx' => 'application_date',
            'loan_apps_full_name_idx' => 'full_name',
            'loan_apps_requested_amount_idx' => 'requested_amount',
            'loan_apps_requested_tenor_idx' => 'requested_tenor',
            'loan_apps_survey_date_idx' => 'survey_date',
        ],
        'collaterals' => [
            'collaterals_owner_name_idx' => 'owner_name',
            'collaterals_document_number_idx' => 'document_number',
            'collaterals_appraisal_value_idx' => 'appraisal_value',
            'collaterals_created_at_idx' => 'created_at',
        ],
    ];

    public function up(): void
    {
        foreach (self::INDEXES as $table => $indexes) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $indexes) {
                foreach ($indexes as $name => $column) {
                    // Another list may already have indexed the same column under its own name.
                    if (! Schema::hasIndex($table, [$column])) {
                        $blueprint->index($column, $name);
                    }
                }
            });
        }

        // `description` is a TEXT column: MySQL can only index a prefix of it, which is enough to order the list.
        if (! Schema::hasIndex('collaterals', ['description'])) {
            Schema::table('collaterals', function (Blueprint $table) {
                if (DB::getDriverName() === 'mysql') {
                    $table->rawIndex('description(191)', 'collaterals_description_idx');
                } else {
                    $table->index('description', 'collaterals_description_idx');
                }
            });
        }
    }

    public function down(): void
    {
        foreach ([...self::INDEXES, 'collaterals' => [...self::INDEXES['collaterals'], 'collaterals_description_idx' => 'description']] as $table => $indexes) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $indexes) {
                foreach (array_keys($indexes) as $name) {
                    if (Schema::hasIndex($table, $name)) {
                        $blueprint->dropIndex($name);
                    }
                }
            });
        }
    }
};
