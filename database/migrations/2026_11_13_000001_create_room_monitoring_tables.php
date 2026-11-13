<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_monitoring_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('location', 100)->unique();
            $table->string('device_id', 100);
            $table->float('temperature');
            $table->float('humidity');
            $table->timestamp('recorded_at');
            $table->timestamp('last_logged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('room_monitoring_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 100);
            $table->string('location', 100);
            $table->float('temperature');
            $table->float('humidity');
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['location', 'recorded_at']);
            $table->index(['device_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_monitoring_logs');
        Schema::dropIfExists('room_monitoring_statuses');
    }
};
