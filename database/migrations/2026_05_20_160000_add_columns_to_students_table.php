<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id')->unique()->after('id');
            $table->string('name')->after('student_id');
            $table->string('email')->after('name');
            $table->string('program')->after('email');
            $table->string('year_level')->after('program');
            $table->string('status')->default('active')->after('year_level');
            $table->date('last_visit')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'student_id',
                'name',
                'email',
                'program',
                'year_level',
                'status',
                'last_visit',
            ]);
        });
    }
};
