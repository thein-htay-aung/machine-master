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
        DB::table('parts')
            ->leftJoin('current_stocks', 'parts.id', '=', 'current_stocks.item_id')
            ->whereNull('current_stocks.id')
            ->select('parts.id')
            ->orderBy('parts.id')
            ->chunk(500, function ($parts) {
                $rows = $parts->map(fn ($part) => [
                    'item_id' => $part->id,
                    'qty' => 0,
                ])->all();

                DB::table('current_stocks')->insertOrIgnore($rows);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
