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
        Schema::create('payment_receipt_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by_id')->constrained('users')->cascadeOnDelete();
            $table->enum('previous_status', ['pending', 'validated', 'rejected'])->nullable();
            $table->enum('new_status', ['pending', 'validated', 'rejected']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipt_status_logs');
    }
};
