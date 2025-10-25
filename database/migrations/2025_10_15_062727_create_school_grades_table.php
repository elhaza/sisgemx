<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_grades', function (Blueprint $table) {
            $table->id();
            $table->integer('level'); // Ej: 1, 2, 3, etc.
            $table->string('name'); // Ej: "Primero", "Segundo", etc.
            $table->char('section')->nullable()->default('A'); // Ej: "A", "B",
            $table->unsignedBigInteger('school_year_id');
            $table->foreign('school_year_id')->references('id')->on('school_years')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
