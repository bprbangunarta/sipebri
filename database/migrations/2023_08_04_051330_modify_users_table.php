<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 25)->after('email')->unique()->nullable();
            $table->string('region', 25)->after('username')->nullable();
            $table->string('user_code', 3)->after('region')->nullable();
            $table->string('photo', 128)->after('user_code')->default('default.jpg')->nullable();
            $table->string('phone', 15)->after('photo')->nullable();
            $table->string('is_active', 1)->after('phone')->default(1)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('region');
            $table->dropColumn('user_code');
            $table->dropColumn('photo');
            $table->dropColumn('phone');
            $table->dropColumn('is_active');
        });
    }
};
