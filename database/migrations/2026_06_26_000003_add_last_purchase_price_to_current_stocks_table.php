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
        Schema::table('current_stocks', function (Blueprint $table) {
            $table->decimal('last_purchase_price', 15, 2)->default(0)->after('qty');
        });

        DB::table('purchases')
            ->orderBy('part_id')
            ->orderByDesc('purchased_date')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get(['part_id', 'price'])
            ->unique('part_id')
            ->each(function ($purchase) {
                DB::table('current_stocks')
                    ->where('item_id', $purchase->part_id)
                    ->update(['last_purchase_price' => $purchase->price]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('current_stocks', function (Blueprint $table) {
            $table->dropColumn('last_purchase_price');
        });
    }
};
