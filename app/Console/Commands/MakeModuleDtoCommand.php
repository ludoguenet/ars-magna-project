<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleDtoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-dto {module : The module name} {name : The DTO name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new data transfer object in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $dtoName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $dtosPath = "{$modulePath}/src/DataTransferObjects";

        if (! File::isDirectory($dtosPath)) {
            File::makeDirectory($dtosPath, 0755, true);
        }

        $dtoPath = "{$dtosPath}/{$dtoName}.php";

        if (File::exists($dtoPath)) {
            $this->error("DTO {$dtoName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-dto.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{DtoName}}', $dtoName, $stub);

        File::put($dtoPath, $stub);

        $this->info("DTO {$dtoName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
