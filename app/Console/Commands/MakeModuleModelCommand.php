<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-model {module : The module name} {name : The model name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $modelName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $modelsPath = "{$modulePath}/src/Models";

        if (! File::isDirectory($modelsPath)) {
            File::makeDirectory($modelsPath, 0755, true);
        }

        $modelPath = "{$modelsPath}/{$modelName}.php";

        if (File::exists($modelPath)) {
            $this->error("Model {$modelName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-model.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{ModelName}}', $modelName, $stub);

        File::put($modelPath, $stub);

        $this->info("Model {$modelName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
