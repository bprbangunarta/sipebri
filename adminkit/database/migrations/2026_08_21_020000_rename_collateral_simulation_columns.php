<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Menyelaraskan nama kolom agunan dengan rancangan skema (Skema Migrasi #1). */
return new class extends Migration
{
    private array $renames = [
        'value_guarantee' => 'guarantee_value',
        'value_fair' => 'fair_value',
        'value_njop' => 'njop_value',
        'value_adjustment' => 'adjustment_value',
        'value_appraisal' => 'appraisal_value',
        'value_independent' => 'independent_value',
        'independent_appraiser_name' => 'independent_name',
        'independent_appraised_at' => 'independent_at',
        'insurance_start_date' => 'insurance_date',
        'insured' => 'insurance_code',
    ];

    public function up(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            foreach ($this->renames as $from => $to) {
                $table->renameColumn($from, $to);
            }
        });

        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->string('ppap_code')->default('1')->change();
            $table->unique('collateral_id');
        });
    }

    public function down(): void
    {
        Schema::table('collateral_simulations', function (Blueprint $table) {
            $table->dropUnique(['collateral_id']);

            foreach ($this->renames as $from => $to) {
                $table->renameColumn($to, $from);
            }
        });
    }
};
