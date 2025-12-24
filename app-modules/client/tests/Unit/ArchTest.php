<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Client\src')
    ->not->toBeUsedIn('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Payment\src')
    ->not->toBeUsedIn('AppModules\Dashboard\src')
    ->not->toBeUsedIn('AppModules\Product\src')
    ->ignoring([
        'AppModules\Client\src\Contracts',
        'AppModules\Client\src\DataTransferObjects',
        'AppModules\Client\src\Events',
        'AppModules\Client\src\Enums',
        'AppModules\Client\src\Exceptions',
    ]);
