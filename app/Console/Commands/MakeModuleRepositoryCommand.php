<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleRepositoryCommand extends MakeModuleActionCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-repository {module : The module name} {name : The repository name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $repositoryName = Str::studly($this->argument('name'));
        $repositoryPath = base_path("app-modules/{$moduleName}/infrastructure/repositories/{$repositoryName}.php");

        if (File::exists($repositoryPath)) {
            $this->error("Repository {$repositoryName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-repository.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{RepositoryName}}', $repositoryName, $stub);
        $stub = str_replace('{{ModelName}}', Str::singular($repositoryName), $stub);

        File::put($repositoryPath, $stub);

        $this->info("Repository {$repositoryName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
