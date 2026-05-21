<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counselors', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('specialization')->after('phone');
            $table->string('availability')->nullable()->after('specialization');
            $table->unsignedInteger('total_sessions')->default(0)->after('availability');
        });
    }

    public function down(): void
    {
        Schema::table('counselors', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'phone', 'specialization', 'availability', 'total_sessions']);
        });
    }
};
