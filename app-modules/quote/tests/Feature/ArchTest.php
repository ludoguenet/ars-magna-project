<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Quote\src')
    ->not->toBeUsedIn('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Client\src')
    ->not->toBeUsedIn('AppModules\Payment\src')
    ->not->toBeUsedIn('AppModules\Dashboard\src')
    ->not->toBeUsedIn('AppModules\Product\src')
    ->ignoring([
        'AppModules\Quote\src\Contracts',
        'AppModules\Quote\src\DataTransferObjects',
        'AppModules\Quote\src\Events',
        'AppModules\Quote\src\Enums',
        'AppModules\Quote\src\Exceptions',
    ]);
