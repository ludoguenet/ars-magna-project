<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Payment\src')
    ->not->toBeUsedIn('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Client\src')
    ->not->toBeUsedIn('AppModules\Dashboard\src')
    ->not->toBeUsedIn('AppModules\Product\src')
    ->ignoring([
        'AppModules\Payment\src\Contracts',
        'AppModules\Payment\src\DataTransferObjects',
        'AppModules\Payment\src\Events',
        'AppModules\Payment\src\Enums',
        'AppModules\Payment\src\Exceptions',
    ]);
