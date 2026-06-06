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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->after('status')
                ->constrained()
                ->nullOnDelete();
        });

        if (Schema::hasColumn('users', 'department')) {
            DB::table('users')
                ->whereNotNull('department')
                ->orderBy('id')
                ->get(['id', 'department'])
                ->each(function ($user): void {
                    $departmentId = DB::table('departments')
                        ->where('name', $user->department)
                        ->value('id');

                    if ($departmentId !== null) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update(['department_id' => $departmentId]);
                    }
                });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('department');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('status');
        });

        DB::table('users')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->update([
                'users.department' => DB::raw('departments.name'),
            ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn('department_id');
        });
    }
};
