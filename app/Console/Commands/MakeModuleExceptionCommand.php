<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleExceptionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-exception {module : The module name} {name : The exception name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new exception in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $exceptionName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $exceptionsPath = "{$modulePath}/src/Exceptions";

        if (! File::isDirectory($exceptionsPath)) {
            File::makeDirectory($exceptionsPath, 0755, true);
        }

        $exceptionPath = "{$exceptionsPath}/{$exceptionName}.php";

        if (File::exists($exceptionPath)) {
            $this->error("Exception {$exceptionName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-exception.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{ExceptionName}}', $exceptionName, $stub);

        File::put($exceptionPath, $stub);

        $this->info("Exception {$exceptionName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
