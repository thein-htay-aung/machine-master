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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_no');
            $table->foreignId('part_id')->constrained('parts')->restrictOnDelete();
            $table->foreignId('plant_id')->constrained()->restrictOnDelete();
            $table->enum('symbol', ['+', '-']);
            $table->unsignedInteger('qty')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('reason');
            $table->date('adjusted_date');
            $table->string('adjusted_by');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
