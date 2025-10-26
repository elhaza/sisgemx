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
        Schema::table('grade_sections', function (Blueprint $table) {
            $table->time('break_time_start')->nullable()->comment('Override break start time for this group');
            $table->time('break_time_end')->nullable()->comment('Override break end time for this group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grade_sections', function (Blueprint $table) {
            $table->dropColumn(['break_time_start', 'break_time_end']);
        });
    }
};
