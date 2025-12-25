<?php

declare(strict_types=1);

/**
 * Global Architecture Tests for Modular Monolith
 * These tests ensure architectural boundaries are respected across all modules.
 */

// Ensure modules don't use core App models except User
arch('modules should not use core App models except User')
    ->expect('AppModules')
    ->not->toUse('App\Models')
    ->ignoring('App\Models\User');

// Strict types declaration
arch('files declare strict types')
    ->expect('AppModules')
    ->toUseStrictTypes();

arch('core app files declare strict types')
    ->expect('App')
    ->toUseStrictTypes()
    ->ignoring('App\Providers\AppServiceProvider');

// Ensure no debugging statements in production code
arch('no debugging functions are used')
    ->expect(['dd', 'dump', 'var_dump', 'ray'])
    ->not->toBeUsed();

// Ensure no direct environment calls outside config
arch('no direct env() calls outside config')
    ->expect('env')
    ->not->toBeUsedIn('AppModules')
    ->not->toBeUsedIn('App\Http')
    ->not->toBeUsedIn('App\Console')
    ->not->toBeUsedIn('App\Models');
