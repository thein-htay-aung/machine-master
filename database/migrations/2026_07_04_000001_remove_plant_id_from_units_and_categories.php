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
        foreach (['units', 'categories'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'plant_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                try {
                    $table->dropForeign(['plant_id']);
                } catch (\Throwable) {
                    //
                }

                $table->dropColumn('plant_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['units', 'categories'] as $tableName) {
            if (Schema::hasColumn($tableName, 'plant_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('plant_id')->nullable()->constrained()->nullOnDelete();
            });
        }
    }
};
