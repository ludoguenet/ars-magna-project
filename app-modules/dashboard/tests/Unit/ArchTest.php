<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Dashboard')
    ->toOnlyBeUsedIn('AppModules\Dashboard')
    ->ignoring([
        'AppModules\Dashboard\Contracts',
        'AppModules\Dashboard\DataTransferObjects',
        'AppModules\Dashboard\Events',
        'AppModules\Dashboard\Enums',
        'AppModules\Dashboard\Exceptions',
    ]);
