<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Client\src')
    ->not->toBeUsedIn('AppModules\Payment\src')
    ->not->toBeUsedIn('AppModules\Dashboard\src')
    ->not->toBeUsedIn('AppModules\Product\src')
    ->ignoring([
        'AppModules\Invoice\src\Contracts',
        'AppModules\Invoice\src\DataTransferObjects',
        'AppModules\Invoice\src\Events',
        'AppModules\Invoice\src\Enums',
        'AppModules\Invoice\src\Exceptions',
    ]);
