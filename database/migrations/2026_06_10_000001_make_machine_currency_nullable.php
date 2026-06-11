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
        if (!Schema::hasTable('machines') || !Schema::hasColumn('machines', 'currency')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $this->recreateMachinesTable(true);
            return;
        }

        DB::statement("ALTER TABLE machines MODIFY currency ENUM('MMK','USD','SGD','JPY','CNY') NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('machines') || !Schema::hasColumn('machines', 'currency')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $this->recreateMachinesTable(false);
            return;
        }

        DB::statement("ALTER TABLE machines MODIFY currency ENUM('MMK','USD','SGD','JPY','CNY') NOT NULL DEFAULT 'MMK'");
    }

    private function recreateMachinesTable(bool $nullable): void
    {
        Schema::disableForeignKeyConstraints();

        $backupTable = 'machines_backup_' . time();
        $newTable = 'machines_new_' . time();

        Schema::rename('machines', $backupTable);

        Schema::create($newTable, function (Blueprint $table) use ($nullable) {
            $table->id();
            $table->string('control_no')->unique();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('supplier')->nullable();
            $table->date('arrived_date')->nullable();
            $table->string('location')->nullable();
            $table->string('dimension')->nullable();
            $table->string('weight')->nullable();
            $table->string('electrical')->nullable();
            $currencyColumn = $table->enum('currency', ['MMK', 'USD', 'SGD', 'JPY', 'CNY']);
            if ($nullable) {
                $currencyColumn->nullable();
            }
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->boolean('is_fixed_asset')->default(false);
            $table->text('remark')->nullable();
            $table->foreignId('plant_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });

        $columns = [
            'id',
            'control_no',
            'name',
            'brand',
            'model',
            'serial_no',
            'supplier',
            'arrived_date',
            'location',
            'dimension',
            'weight',
            'electrical',
            'currency',
            'unit_price',
            'is_fixed_asset',
            'remark',
            'plant_id',
            'status_id',
            'image',
            'created_at',
            'updated_at',
        ];

        $columnList = implode(', ', $columns);

        DB::statement("INSERT INTO {$newTable} ({$columnList}) SELECT {$columnList} FROM {$backupTable}");

        Schema::dropIfExists($backupTable);
        Schema::rename($newTable, 'machines');

        Schema::enableForeignKeyConstraints();
    }
};
