<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name : The name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module with Artisan Airlines structure';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $moduleName = Str::studly($this->argument('name'));
        $moduleDir = strtolower($this->argument('name'));
        $modulePath = base_path("app-modules/{$moduleDir}");

        if (File::exists($modulePath)) {
            $this->error("Module {$moduleName} already exists!");

            return self::FAILURE;
        }

        $this->info("Creating module: {$moduleName}");

        // Create directory structure (simplified - matching Ryuta's approach)
        $directories = [
            'src/Http/Controllers',
            'src/Http/Requests',
            'src/Models',
            'src/Repositories',
            'src/Services',
            'src/Actions',
            'src/DataTransferObjects',
            'src/Events',
            'src/Enums',
            'src/Exceptions',
            'src/Contracts',
            'src/Jobs',
            'src/Listeners',
            'src/Providers',
            'routes',
            'tests/unit',
            'tests/feature',
            'database/migrations',
            'database/factories',
            'database/seeders',
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$modulePath}/{$directory}", 0755, true);
        }

        // Create Service Provider
        $this->createServiceProvider($moduleName, $modulePath);

        // Create base files
        $this->createBaseFiles($moduleName, $modulePath);

        $this->info("Module {$moduleName} created successfully!");
        $this->info("Don't forget to run: composer dump-autoload");

        return self::SUCCESS;
    }

    /**
     * Create the module service provider.
     */
    protected function createServiceProvider(string $moduleName, string $modulePath): void
    {
        $stub = File::get(__DIR__.'/stubs/module-service-provider.stub');
        $stub = str_replace('{{ModuleName}}', $moduleName, $stub);
        $stub = str_replace('{{moduleName}}', strtolower($moduleName), $stub);
        $stub = str_replace('{{module_snake}}', Str::snake($moduleName), $stub);

        File::put("{$modulePath}/src/Providers/{$moduleName}ServiceProvider.php", $stub);
    }

    /**
     * Create base files for the module.
     */
    protected function createBaseFiles(string $moduleName, string $modulePath): void
    {
        // Create .gitkeep files in empty directories
        File::put("{$modulePath}/src/Contracts/.gitkeep", '');
        File::put("{$modulePath}/src/DataTransferObjects/.gitkeep", '');
        File::put("{$modulePath}/src/Events/.gitkeep", '');
        File::put("{$modulePath}/src/Enums/.gitkeep", '');
        File::put("{$modulePath}/src/Exceptions/.gitkeep", '');
        File::put("{$modulePath}/src/Jobs/.gitkeep", '');
        File::put("{$modulePath}/src/Listeners/.gitkeep", '');

        // Create routes/web.php file
        File::put("{$modulePath}/routes/web.php", "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n// Module routes here\n");
    }
}
