<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-migration {module : The module name} {name : The migration name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = strtolower($this->argument('module'));
        $migrationName = $this->argument('name');
        $modulePath = base_path("app-modules/{$moduleName}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $migrationsPath = "{$modulePath}/database/migrations";

        if (! File::isDirectory($migrationsPath)) {
            File::makeDirectory($migrationsPath, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$migrationName}.php";
        $filePath = "{$migrationsPath}/{$fileName}";

        if (File::exists($filePath)) {
            $this->error("Migration {$fileName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-migration.stub');
        $className = Str::studly(Str::replace(['-', '_'], '', $migrationName));

        File::put($filePath, $stub);

        $this->info("Migration {$fileName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
