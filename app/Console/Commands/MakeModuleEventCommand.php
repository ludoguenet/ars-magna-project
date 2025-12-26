<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleEventCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module-event {module : The module name} {name : The event name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event in a module';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('module'));
        $moduleDir = strtolower($this->argument('module'));
        $eventName = Str::studly($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (! File::exists($modulePath)) {
            $this->error("Module {$moduleName} does not exist!");

            return self::FAILURE;
        }

        $eventsPath = "{$modulePath}/src/Events";

        if (! File::isDirectory($eventsPath)) {
            File::makeDirectory($eventsPath, 0755, true);
        }

        $eventPath = "{$eventsPath}/{$eventName}.php";

        if (File::exists($eventPath)) {
            $this->error("Event {$eventName} already exists!");

            return self::FAILURE;
        }

        $stub = File::get(__DIR__.'/stubs/module-event.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{EventName}}', $eventName, $stub);

        File::put($eventPath, $stub);

        $this->info("Event {$eventName} created in module {$moduleName}!");

        return self::SUCCESS;
    }
}
