<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Reporting\src')
    ->not->toBeUsedIn('AppModules\Invoice\src')
    ->not->toBeUsedIn('AppModules\Client\src')
    ->not->toBeUsedIn('AppModules\Payment\src')
    ->not->toBeUsedIn('AppModules\Dashboard\src')
    ->not->toBeUsedIn('AppModules\Product\src')
    ->ignoring([
        'AppModules\Reporting\src\Contracts',
        'AppModules\Reporting\src\DataTransferObjects',
        'AppModules\Reporting\src\Events',
        'AppModules\Reporting\src\Enums',
        'AppModules\Reporting\src\Exceptions',
    ]);
