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
        Schema::create('daily_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('parts')->restrictOnDelete();
            $table->date('date');
            $table->unsignedInteger('in_qty')->default(0);
            $table->unsignedInteger('out_qty')->default(0);
            $table->unsignedInteger('stock_qty')->default(0);
            $table->unique(['item_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_stocks');
    }
};
