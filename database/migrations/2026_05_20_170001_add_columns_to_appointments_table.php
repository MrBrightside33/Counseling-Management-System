<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('student_id')->constrained()->cascadeOnDelete()->after('id');
            $table->foreignId('counselor_id')->constrained()->cascadeOnDelete()->after('student_id');
            $table->date('date')->after('counselor_id');
            $table->string('time')->after('date');
            $table->string('type')->after('time');
            $table->string('status')->default('scheduled')->after('type');
            $table->text('notes')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropForeign(['counselor_id']);
            $table->dropColumn(['student_id', 'counselor_id', 'date', 'time', 'type', 'status', 'notes']);
        });
    }
};
