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
        if ($this->indexExists('issues', 'issues_issue_no_unique')) {
            Schema::table('issues', function (Blueprint $table) {
                $table->dropUnique(['issue_no']);
            });
        }

        if ($this->indexExists('stock_adjustments', 'stock_adjustments_adjustment_no_unique')) {
            Schema::table('stock_adjustments', function (Blueprint $table) {
                $table->dropUnique(['adjustment_no']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

    private function indexExists(string $table, string $index): bool
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return collect(DB::select("PRAGMA index_list('$table')"))
                ->contains(fn ($row) => $row->name === $index);
        }

        return Schema::hasIndex($table, $index);
    }
};
