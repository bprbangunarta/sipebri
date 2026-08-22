<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Detail keputusan komite per jenjang (cermin dialog "Persetujuan Komite" sistem lama):
 * metode RPS, biaya, suku bunga, usulan plafon, jangka waktu, max plafon dan RC.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_approvals', function (Blueprint $table) {
            $table->foreignId('method_id')->nullable()->after('user_id')->constrained('methods')->nullOnDelete();
            $table->unsignedBigInteger('max_amount')->default(0)->after('decision');
            $table->unsignedBigInteger('amount')->default(0)->after('max_amount');
            $table->unsignedInteger('tenor')->default(0)->after('amount');
            $table->decimal('interest_rate', 6, 2)->default(0)->after('tenor');
            $table->decimal('provision_rate', 6, 2)->default(0)->after('interest_rate');
            $table->decimal('admin_rate', 6, 2)->default(0)->after('provision_rate');
            $table->decimal('rc_ratio', 8, 2)->default(0)->after('admin_rate');
        });
    }

    public function down(): void
    {
        Schema::table('loan_approvals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('method_id');
            $table->dropColumn([
                'max_amount', 'amount', 'tenor',
                'interest_rate', 'provision_rate', 'admin_rate', 'rc_ratio',
            ]);
        });
    }
};
