<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleListenerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-listener {module : The module name} {name : The listener name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event listener in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $listenerName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $listenersPath = "{$modulePath}/src/Listeners";

        if (! File::isDirectory($listenersPath)) {
            File::makeDirectory($listenersPath, 0755, true);
        }

        $listenerPath = "{$listenersPath}/{$listenerName}.php";

        if (File::exists($listenerPath)) {
            $this->error("Listener {$listenerName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-listener.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{ListenerName}}', $listenerName, $stub);

        File::put($listenerPath, $stub);

        $this->info("Listener {$listenerName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
