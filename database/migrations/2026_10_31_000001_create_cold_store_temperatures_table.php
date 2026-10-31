<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cold_store_temperatures', function (Blueprint $table) {
            $table->id();
            $table->string('cold_store', 50);
            $table->float('temperature');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index(['cold_store', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cold_store_temperatures');
    }
};
