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
        Schema::create('subject_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('grade_section_id')->constrained('grade_sections')->cascadeOnDelete();
            $table->decimal('hours_per_day', 4, 2)->nullable();
            $table->decimal('hours_per_week', 4, 2)->nullable();
            $table->integer('min_consecutive_minutes')->nullable();
            $table->timestamps();

            $table->unique(['subject_id', 'grade_section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_requirements');
    }
};
