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
        Schema::table('assignments', function (Blueprint $table) {
            $table->text('attachment_path')->nullable()->after('max_points');
            $table->string('attachment_type')->nullable()->after('attachment_path'); // image, document, file
            $table->boolean('is_active')->default(true)->after('attachment_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_type', 'is_active']);
        });
    }
};
