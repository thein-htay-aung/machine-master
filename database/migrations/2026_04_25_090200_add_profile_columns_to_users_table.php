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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('password')->constrained()->restrictOnDelete();
            $table->boolean('status')->default(true)->after('role_id');
            $table->foreignId('department_id')->nullable()->after('status')->constrained()->nullOnDelete();
            $table->foreignId('plant_id')->nullable()->after('department_id')->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['plant_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['plant_id', 'department_id', 'status', 'role_id']);
        });
    }
};
