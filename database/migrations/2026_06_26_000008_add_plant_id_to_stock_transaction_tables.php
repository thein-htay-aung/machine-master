<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('plant_id')->nullable()->after('part_id')->constrained()->restrictOnDelete();
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->foreignId('plant_id')->nullable()->after('part_id')->constrained()->restrictOnDelete();
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->foreignId('plant_id')->nullable()->after('part_id')->constrained()->restrictOnDelete();
        });

        DB::statement('UPDATE purchases SET plant_id = (SELECT plant_id FROM parts WHERE parts.id = purchases.part_id) WHERE plant_id IS NULL');
        DB::statement('UPDATE issues SET plant_id = (SELECT plant_id FROM parts WHERE parts.id = issues.part_id) WHERE plant_id IS NULL');
        DB::statement('UPDATE stock_adjustments SET plant_id = (SELECT plant_id FROM parts WHERE parts.id = stock_adjustments.part_id) WHERE plant_id IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['plant_id']);
            $table->dropColumn('plant_id');
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['plant_id']);
            $table->dropColumn('plant_id');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['plant_id']);
            $table->dropColumn('plant_id');
        });
    }
};
