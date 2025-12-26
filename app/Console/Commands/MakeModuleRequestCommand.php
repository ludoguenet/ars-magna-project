<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleRequestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-request {module : The module name} {name : The request name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $requestName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $requestsPath = "{$modulePath}/src/Http/Requests";

        if (! File::isDirectory($requestsPath)) {
            File::makeDirectory($requestsPath, 0755, true);
        }

        $requestPath = "{$requestsPath}/{$requestName}.php";

        if (File::exists($requestPath)) {
            $this->error("Request {$requestName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-request.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{RequestName}}', $requestName, $stub);

        File::put($requestPath, $stub);

        $this->info("Request {$requestName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
