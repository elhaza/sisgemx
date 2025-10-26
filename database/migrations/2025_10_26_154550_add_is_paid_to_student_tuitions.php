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
        Schema::table('student_tuitions', function (Blueprint $table) {
            $table->boolean('is_paid')->default(false)->after('due_date');
            $table->timestamp('paid_at')->nullable()->after('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_tuitions', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'paid_at']);
        });
    }
};
