<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleEnumCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-enum {module : The module name} {name : The enum name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new enum in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $enumName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $enumsPath = "{$modulePath}/src/Enums";

        if (! File::isDirectory($enumsPath)) {
            File::makeDirectory($enumsPath, 0755, true);
        }

        $enumPath = "{$enumsPath}/{$enumName}.php";

        if (File::exists($enumPath)) {
            $this->error("Enum {$enumName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-enum.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{EnumName}}', $enumName, $stub);

        File::put($enumPath, $stub);

        $this->info("Enum {$enumName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
