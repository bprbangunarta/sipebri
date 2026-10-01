<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The credit analysis worksheet of a loan file. `loan_analyses` is the header (one per file, with the template the
 * worksheet follows); every section hangs on it and is cleaned up with it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('template', 30)->default('general');
            $table->unsignedSmallInteger('template_version')->default(1);
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('analysis_businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // trade, farm, service, other
            $table->string('code', 20)->unique();
            $table->string('name', 150);
            $table->string('business_length', 20)->nullable();
            $table->string('address', 255)->nullable();

            // trade
            foreach (['daily_purchase', 'cost_of_goods', 'transport_cost', 'employee_cost', 'retribution_cost', 'unload_cost', 'gatel_cost', 'rent_cost'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }

            // farm
            $table->string('economy_sector', 30)->nullable();
            $table->string('plant_type', 50)->nullable();
            foreach (['area_own', 'area_rent', 'area_pawn'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }
            $table->decimal('harvest_quintals', 12, 2)->default(0);
            foreach (['price_per_quintal', 'cost_land', 'cost_seed', 'cost_fertilizer', 'cost_pesticide', 'cost_labor', 'cost_irrigation', 'cost_harvest', 'cost_sharecropper', 'cost_tax', 'cost_village', 'cost_amortization', 'cost_other_bank', 'addition_result', 'other_bank_loan', 'principal_installment'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }

            // service
            foreach (['service_income', 'vehicle_tax', 'other_expense'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }

            // other
            $table->string('business_kind', 50)->nullable();
            $table->unsignedBigInteger('projection_addition')->default(0);

            // results, kept in step by the model whenever the business is saved
            $table->unsignedBigInteger('revenue')->default(0);
            $table->unsignedBigInteger('expense')->default(0);
            $table->bigInteger('net_profit')->default(0);
            $table->bigInteger('monthly_income')->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('analysis_business_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_business_id')->constrained()->cascadeOnDelete();
            $table->string('group', 20); // goods, material, income, expense
            $table->string('name', 150);
            $table->decimal('qty', 14, 2)->default(0);
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('sell_price')->default(0);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Household finance and what the applicant owns share one row.
        Schema::create('analysis_finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->unique()->constrained()->cascadeOnDelete();

            foreach (['cost_staple', 'cost_education', 'cost_children', 'cost_cigarette', 'cost_health', 'cost_gatel', 'cost_social'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }

            foreach (['asset_house', 'asset_car', 'asset_motorcycle', 'asset_computer', 'asset_washer', 'asset_tv', 'asset_chair', 'asset_cabinet'] as $column) {
                $table->string($column, 20)->nullable();
            }

            $table->timestamps();
        });

        Schema::create('analysis_finance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_finance_id')->constrained()->cascadeOnDelete();
            $table->string('group', 20); // obligation, asset
            $table->string('name', 150);
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('analysis_collaterals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collateral_id')->constrained()->restrictOnDelete();
            $table->string('kind', 20)->default('other'); // vehicle, land, other
            $table->string('brand', 100)->nullable();
            $table->string('vehicle_type', 100)->nullable();
            $table->string('year', 4)->nullable();
            $table->string('chassis_number', 50)->nullable();
            $table->string('engine_number', 50)->nullable();
            $table->string('plate_number', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->unsignedBigInteger('land_area')->default(0);
            $table->string('location', 255)->nullable();
            $table->unsignedBigInteger('market_value')->default(0);
            $table->unsignedBigInteger('appraisal_value')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['loan_analysis_id', 'collateral_id']);
        });

        Schema::create('analysis_five_c', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->unique()->constrained()->cascadeOnDelete();

            foreach ([
                'lifestyle', 'emotional_control', 'disreputable_acts', 'family_harmony', 'consistency', 'compliance', 'social_relations',
                'continuity', 'business_experience', 'business_growth', 'financial_records', 'credit_history', 'slik_condition', 'non_business_assets', 'business_assets',
                'capital_source',
                'main_collateral_ownership', 'main_collateral_legality', 'liquidity', 'vehicle_condition', 'legal_binding', 'additional_collateral_ownership', 'additional_collateral_legality', 'price_stability', 'shm_location',
                'natural_conditions', 'competition', 'regulations',
            ] as $column) {
                $table->unsignedTinyInteger($column)->nullable();
            }

            $table->timestamps();
        });

        Schema::create('analysis_qualitative', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->unique()->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('slik_check')->nullable();
            $table->unsignedTinyInteger('police_record')->nullable();
            $table->string('neighbor_relations', 30)->nullable();
            $table->string('migrant_worker_experience', 30)->nullable();
            $table->string('experience_by', 30)->nullable();

            foreach (['applicant_at_home', 'companion_at_home'] as $column) {
                $table->string($column, 150)->nullable();
            }
            $table->string('community_info', 255)->nullable();

            foreach ([1, 2, 3] as $i) {
                $table->string("obligation{$i}_type", 30)->nullable();
                $table->string("obligation{$i}_note", 150)->nullable();
                $table->string("obligation{$i}_status", 30)->nullable();
            }

            foreach (['raw_materials', 'processing', 'market_area', 'payment_system', 'business_supporters', 'business_detractors', 'strength', 'weakness', 'opportunity', 'threat'] as $column) {
                $table->string($column, 255)->nullable();
            }
            foreach (['trade_checking', 'notes', 'business_trade_checking'] as $column) {
                $table->text($column)->nullable();
            }

            $table->timestamps();
        });

        Schema::create('analysis_memorandums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->unique()->constrained()->cascadeOnDelete();

            foreach (['working_capital', 'investment', 'consumption', 'loan_settlement', 'take_over'] as $column) {
                $table->unsignedBigInteger($column)->default(0);
                $table->string("{$column}_note", 255)->nullable();
            }

            $table->unsignedBigInteger('proposed_amount')->default(0);
            $table->unsignedInteger('term_months')->default(0);
            foreach (['admin_rate', 'interest_rate', 'provision_rate', 'penalty_rate'] as $column) {
                $table->decimal($column, 6, 2)->default(0);
            }
            $table->string('before_disbursement', 255)->nullable();
            $table->string('additional_terms', 255)->nullable();
            $table->string('binding', 100)->nullable();

            $table->timestamps();
        });

        Schema::create('analysis_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_analysis_id')->unique()->constrained()->cascadeOnDelete();

            foreach ([
                'administration', 'provision', 'stamp_duty',
                'declining_life_insurance_1', 'declining_life_insurance_2', 'declining_life_insurance_3',
                'flat_life_insurance_1', 'flat_life_insurance_2', 'life_insurance',
                'motorcycle_insurance', 'credit_transaction', 'shm_processing',
                'policy_stamp', 'vehicle_tax', 'apht_processing', 'fiducia_fee',
            ] as $column) {
                $table->unsignedBigInteger($column)->default(0);
            }

            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['analysis_administrations', 'analysis_memorandums', 'analysis_qualitative', 'analysis_five_c', 'analysis_collaterals', 'analysis_finance_items', 'analysis_finances', 'analysis_business_items', 'analysis_businesses', 'loan_analyses'] as $name) {
            Schema::dropIfExists($name);
        }
    }
};
