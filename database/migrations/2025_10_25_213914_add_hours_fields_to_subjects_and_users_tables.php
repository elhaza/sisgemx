<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->decimal('default_hours_per_week', 4, 2)->nullable()->after('description');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('max_hours_per_day')->default(8)->after('email');
            $table->integer('max_hours_per_week')->default(40)->after('max_hours_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('default_hours_per_week');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['max_hours_per_day', 'max_hours_per_week']);
        });
    }
};
