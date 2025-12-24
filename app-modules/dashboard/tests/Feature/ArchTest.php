<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Dashboard\src')
    ->not->toBeUsedIn('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Client\src')
    ->not->toBeUsedIn('AppModules\Payment\src')
    ->not->toBeUsedIn('AppModules\Product\src')
    ->ignoring([
        'AppModules\Dashboard\src\Contracts',
        'AppModules\Dashboard\src\DataTransferObjects',
        'AppModules\Dashboard\src\Events',
        'AppModules\Dashboard\src\Enums',
        'AppModules\Dashboard\src\Exceptions',
    ]);
