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
        // Plantillas reutilizables de cargos (Inscripción, Material, etc.)
        Schema::create('charge_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Inscripción 2025", "Material 5to Grado"
            $table->string('charge_type'); // inscription, materials, exam, etc.
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->date('default_due_date');
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('charge_type');
            $table->index('is_active');
        });

        // Asignaciones de cargos a estudiantes
        Schema::create('student_assigned_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('charge_template_id')->constrained('charge_templates')->cascadeOnDelete();
            $table->decimal('amount', 10, 2); // Puede variar del template
            $table->date('due_date');
            $table->boolean('is_paid')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('student_id');
            $table->index('charge_template_id');
            $table->index('is_paid');
            $table->unique(['student_id', 'charge_template_id'], 'unique_student_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assigned_charges');
        Schema::dropIfExists('charge_templates');
    }
};
