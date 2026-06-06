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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        $statuses = ['Operational', 'Out of Service', 'Not in Use'];
        foreach ($statuses as $status) {
            DB::table('statuses')->insert([
                'name' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('machines', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('location')->constrained('statuses')->restrictOnDelete();
        });

        foreach ($statuses as $status) {
            $statusId = DB::table('statuses')->where('name', $status)->value('id');
            DB::table('machines')->where('status', $status)->update(['status_id' => $statusId]);
        }

        $fallbackStatusId = DB::table('statuses')->where('name', 'Not in Use')->value('id');
        if ($fallbackStatusId) {
            DB::table('machines')
                ->whereNotNull('status')
                ->whereNotIn('status', $statuses)
                ->update(['status_id' => $fallbackStatusId]);
        }

        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->string('status')->nullable()->after('location');
        });

        $statuses = ['Operational', 'Out of Service', 'Not in Use'];
        foreach ($statuses as $status) {
            $statusId = DB::table('statuses')->where('name', $status)->value('id');
            if ($statusId) {
                DB::table('machines')->where('status_id', $statusId)->update(['status' => $status]);
            }
        }

        Schema::table('machines', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        Schema::dropIfExists('statuses');
    }
};
