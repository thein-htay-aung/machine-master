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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice');
            $table->foreignId('part_id')->constrained('parts')->restrictOnDelete();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('qty')->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->text('remark')->nullable();
            $table->date('purchased_date');
            $table->string('purchase_by')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
