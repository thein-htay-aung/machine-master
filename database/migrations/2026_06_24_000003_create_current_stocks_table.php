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
        Schema::create('current_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->unique()->constrained('parts')->restrictOnDelete();
            $table->unsignedInteger('qty')->default(0);
            $table->decimal('last_purchase_price', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_stocks');
    }
};
