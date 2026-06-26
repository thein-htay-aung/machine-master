<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('units')->whereNull('plant_id')->update(['plant_id' => 1]);
        DB::table('categories')->whereNull('plant_id')->update(['plant_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('units')->where('plant_id', 1)->update(['plant_id' => null]);
        DB::table('categories')->where('plant_id', 1)->update(['plant_id' => null]);
    }
};
