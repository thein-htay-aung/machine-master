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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('control_no')->unique();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('supplier')->nullable();
            $table->date('arrived_date')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('statuses')->restrictOnDelete();
            $table->string('category')->default('General');
            $table->string('dimension')->nullable();
            $table->string('weight')->nullable();
            $table->string('electrical')->nullable();
            $table->string('image')->nullable();
            $table->enum('currency', ['MMK', 'USD', 'SGD', 'JPY', 'CNY'])->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->boolean('is_fixed_asset')->default(false);
            $table->text('remark')->nullable();
            $table->foreignId('plant_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
