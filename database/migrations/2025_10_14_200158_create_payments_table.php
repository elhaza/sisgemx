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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->enum('payment_type', ['tuition', 'books', 'uniform', 'enrollment', 'other']);
            $table->string('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->integer('month')->nullable();
            $table->integer('year');
            $table->date('due_date');
            $table->boolean('is_paid')->default(false);
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
