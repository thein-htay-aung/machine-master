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
        Schema::table('issues', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->default(0)->after('qty');
            $table->decimal('amount', 15, 2)->default(0)->after('price');
        });

        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->default(0)->after('qty');
            $table->decimal('amount', 15, 2)->default(0)->after('price');
        });

        DB::statement('UPDATE issues SET price = COALESCE((SELECT last_purchase_price FROM current_stocks WHERE current_stocks.item_id = issues.part_id), 0)');
        DB::statement('UPDATE issues SET amount = price * qty');

        DB::statement('UPDATE stock_adjustments SET price = COALESCE((SELECT last_purchase_price FROM current_stocks WHERE current_stocks.item_id = stock_adjustments.part_id), 0)');
        DB::statement('UPDATE stock_adjustments SET amount = price * qty');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropColumn(['price', 'amount']);
        });

        Schema::table('issues', function (Blueprint $table) {
            $table->dropColumn(['price', 'amount']);
        });
    }
};
