<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleContractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-contract {module : The module name} {name : The contract name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new contract (interface) in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $contractName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $contractsPath = "{$modulePath}/src/Contracts";

        if (! File::isDirectory($contractsPath)) {
            File::makeDirectory($contractsPath, 0755, true);
        }

        $contractPath = "{$contractsPath}/{$contractName}.php";

        if (File::exists($contractPath)) {
            $this->error("Contract {$contractName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-contract.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{ContractName}}', $contractName, $stub);

        File::put($contractPath, $stub);

        $this->info("Contract {$contractName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
