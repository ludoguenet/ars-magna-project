<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleActionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-action {module : The module name} {name : The action name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $actionName = Str::studly($this->argument('name'));
        $actionPath = base_path("app-modules/{$moduleName}/domain/actions/{$actionName}.php");

        if (File::exists($actionPath)) {
            $this->error("Action {$actionName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-action.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{ActionName}}', $actionName, $stub);

        File::put($actionPath, $stub);

        $this->info("Action {$actionName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
