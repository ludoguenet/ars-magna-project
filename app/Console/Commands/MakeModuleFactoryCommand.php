<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleFactoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-factory {module : The module name} {name : The factory name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new factory in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $factoryName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $factoriesPath = "{$modulePath}/database/factories";

        if (! File::isDirectory($factoriesPath)) {
            File::makeDirectory($factoriesPath, 0755, true);
        }

        $factoryPath = "{$factoriesPath}/{$factoryName}.php";

        if (File::exists($factoryPath)) {
            $this->error("Factory {$factoryName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-factory.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{FactoryName}}', $factoryName, $stub);

        // Extract model name from factory name (remove "Factory" suffix if present)
        $modelName = Str::endsWith($factoryName, 'Factory')
            ? Str::replaceLast('Factory', '', $factoryName)
            : $factoryName;
        $stub = str_replace('{{ModelName}}', $modelName, $stub);

        File::put($factoryPath, $stub);

        $this->info("Factory {$factoryName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
