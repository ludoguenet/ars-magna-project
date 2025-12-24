<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Product\src')
    ->not->toBeUsedIn('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Payment\src')
    ->not->toBeUsedIn('AppModules\Dashboard\src')
    ->not->toBeUsedIn('AppModules\Client\src')
    ->ignoring([
        'AppModules\Product\src\Contracts',
        'AppModules\Product\src\DataTransferObjects',
        'AppModules\Product\src\Events',
        'AppModules\Product\src\Enums',
        'AppModules\Product\src\Exceptions',
    ]);
