<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('curp', 18)->nullable()->after('enrollment_number');
            $table->string('gender')->nullable()->after('curp');
            $table->string('birth_country')->default('México')->after('gender');
            $table->string('birth_state')->nullable()->after('birth_country');
            $table->string('birth_city')->nullable()->after('birth_state');
            $table->string('phone_number')->nullable()->after('birth_city');
            $table->text('address')->nullable()->after('phone_number');
            $table->string('parent_email')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'curp',
                'gender',
                'birth_country',
                'birth_state',
                'birth_city',
                'phone_number',
                'address',
                'parent_email',
            ]);
        });
    }
};
