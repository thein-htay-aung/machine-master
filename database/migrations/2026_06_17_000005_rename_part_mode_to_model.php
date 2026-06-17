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
        if (Schema::hasTable('parts') && Schema::hasColumn('parts', 'mode')) {
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('ALTER TABLE parts RENAME COLUMN mode TO model');
            } else {
                Schema::table('parts', function (Blueprint $table) {
                    $table->renameColumn('mode', 'model');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parts') && Schema::hasColumn('parts', 'model')) {
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('ALTER TABLE parts RENAME COLUMN model TO mode');
            } else {
                Schema::table('parts', function (Blueprint $table) {
                    $table->renameColumn('model', 'mode');
                });
            }
        }
    }
};
