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
        Schema::table('parts', function (Blueprint $table) {
            $table->foreignId('plant_id')->default(1)->after('location');
        });

        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('parts', function (Blueprint $table) {
                $table->foreign('plant_id')->references('id')->on('plants')->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['plant_id']);
            }

            $table->dropColumn('plant_id');
        });
    }
};
