<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CopySqliteToMysql extends Command
{
    protected $signature = 'db:copy-sqlite-to-mysql';
    protected $description = 'Copy data from SQLite to MySQL';

    public function handle()
    {
        $sqlitePath = database_path('database.sqlite');

        if (!file_exists($sqlitePath)) {
            $this->error("SQLite file not found: {$sqlitePath}");
            return 1;
        }

        config([
            'database.connections.sqlite_old' => [
                'driver' => 'sqlite',
                'database' => $sqlitePath,
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);

        $tables = [
            'roles',
            'departments',
            'plants',
            'statuses',
            'users',
            'machines',
            'units',
            'categories',
            'parts',
            'machine_part',
            'purchases',
            'current_stocks',
            'daily_stocks',
            'issues',
            'stock_adjustments',
        ];

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            if (!Schema::connection('sqlite_old')->hasTable($table)) {
                $this->warn("Skip {$table}: not found in SQLite");
                continue;
            }

            if (!Schema::connection('mysql')->hasTable($table)) {
                $this->warn("Skip {$table}: not found in MySQL");
                continue;
            }

            $this->info("Copying {$table}...");

            DB::connection('mysql')->table($table)->truncate();

            DB::connection('sqlite_old')->table($table)
                ->orderBy('id')
                ->chunk(500, function ($rows) use ($table) {
                    $data = json_decode(json_encode($rows), true);

                    if (!empty($data)) {
                        DB::connection('mysql')->table($table)->insert($data);
                    }
                });
        }

        DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info('Finished copying SQLite data to MySQL.');
        return 0;
    }
}