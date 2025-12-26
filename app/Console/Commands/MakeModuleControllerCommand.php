<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-controller {module : The module name} {name : The controller name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $controllerName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $controllersPath = "{$modulePath}/src/Http/Controllers";

        if (! File::isDirectory($controllersPath)) {
            File::makeDirectory($controllersPath, 0755, true);
        }

        $controllerPath = "{$controllersPath}/{$controllerName}.php";

        if (File::exists($controllerPath)) {
            $this->error("Controller {$controllerName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-controller.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{ControllerName}}', $controllerName, $stub);

        File::put($controllerPath, $stub);

        $this->info("Controller {$controllerName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
