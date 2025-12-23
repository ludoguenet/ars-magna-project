<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleServiceCommand extends MakeModuleActionCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-service {module : The module name} {name : The service name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $serviceName = Str::studly($this->argument('name'));
        $servicePath = base_path("app-modules/{$moduleName}/domain/services/{$serviceName}.php");

        if (File::exists($servicePath)) {
            $this->error("Service {$serviceName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-service.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{ServiceName}}', $serviceName, $stub);

        File::put($servicePath, $stub);

        $this->info("Service {$serviceName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
