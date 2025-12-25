<?php

declare(strict_types=1);

use App\Providers\ModuleServiceProvider;
use Illuminate\Support\Facades\File;

it('registers all module service providers', function () {
    // Verify that module service providers are registered (already loaded during bootstrap)
    $registeredProviders = array_keys(app()->getLoadedProviders());

    $expectedProviders = [
        'AppModules\Client\src\Providers\ClientServiceProvider',
        'AppModules\Dashboard\src\Providers\DashboardServiceProvider',
        'AppModules\Invoice\src\Providers\InvoiceServiceProvider',
        'AppModules\Payment\src\Providers\PaymentServiceProvider',
        'AppModules\Product\src\Providers\ProductServiceProvider',
        'AppModules\Quote\src\Providers\QuoteServiceProvider',
        'AppModules\Reporting\src\Providers\ReportingServiceProvider',
    ];

    $moduleProviders = array_filter($registeredProviders, fn ($provider) => str_starts_with($provider, 'AppModules'));

    foreach ($expectedProviders as $providerClass) {
        expect($moduleProviders)->toContain($providerClass);
    }
});

it('handles missing modules directory gracefully', function () {
    $modulesPath = base_path('app-modules');
    $backupPath = base_path('app-modules-backup');

    // Temporarily rename modules directory
    File::move($modulesPath, $backupPath);

    try {
        $provider = new ModuleServiceProvider(app());
        $provider->register();

        // Should not throw any errors
        expect(true)->toBeTrue();
    } finally {
        // Restore modules directory
        File::move($backupPath, $modulesPath);
    }
});

it('scans modules directory correctly', function () {
    $modulesPath = base_path('app-modules');

    expect(File::isDirectory($modulesPath))->toBeTrue();

    $modules = File::directories($modulesPath);

    expect($modules)->not->toBeEmpty();

    // Verify we have expected modules
    $moduleNames = array_map(fn ($path) => basename($path), $modules);

    expect($moduleNames)
        ->toContain('client')
        ->toContain('dashboard')
        ->toContain('invoice')
        ->toContain('payment')
        ->toContain('product')
        ->toContain('quote')
        ->toContain('reporting');
});

it('converts module directory names to studly case for provider class names', function () {
    // Test the logic used in ModuleServiceProvider
    $testCases = [
        'client' => 'Client',
        'dashboard' => 'Dashboard',
        'invoice' => 'Invoice',
        'my-module' => 'MyModule',
        'my_module' => 'MyModule',
    ];

    foreach ($testCases as $input => $expected) {
        expect(Str::studly($input))->toBe($expected);
    }
});

it('only registers providers that exist', function () {
    $provider = new ModuleServiceProvider(app());
    $provider->register();

    // Should not throw errors even if some module directories don't have providers
    expect(true)->toBeTrue();

    // Verify that at least one module provider was registered
    $registeredProviders = array_keys(app()->getLoadedProviders());
    $moduleProviders = array_filter($registeredProviders, fn ($provider) => str_starts_with($provider, 'AppModules'));

    expect($moduleProviders)->not->toBeEmpty();
});
