<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Tutors
            $table->foreignId('tutor_1_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('tutor_2_id')->nullable()->after('tutor_1_id')->constrained('users')->nullOnDelete();

            // Billing
            $table->boolean('requires_invoice')->default(false)->after('parent_email');
            $table->string('billing_name')->nullable()->after('requires_invoice');
            $table->string('billing_zip_code', 10)->nullable()->after('billing_name');
            $table->string('billing_rfc', 13)->nullable()->after('billing_zip_code');
            $table->string('billing_tax_regime')->nullable()->after('billing_rfc');
            $table->string('billing_cfdi_use')->nullable()->after('billing_tax_regime');
            $table->string('tax_certificate_file')->nullable()->after('billing_cfdi_use');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['tutor_1_id']);
            $table->dropForeign(['tutor_2_id']);
            $table->dropColumn([
                'tutor_1_id',
                'tutor_2_id',
                'requires_invoice',
                'billing_name',
                'billing_zip_code',
                'billing_rfc',
                'billing_tax_regime',
                'billing_cfdi_use',
                'tax_certificate_file',
            ]);
        });
    }
};
