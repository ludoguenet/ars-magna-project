<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Invoice')
    ->toOnlyBeUsedIn('AppModules\Invoice')
    ->ignoring([
        'AppModules\Invoice\Contracts',
        'AppModules\Invoice\DataTransferObjects',
        'AppModules\Invoice\Events',
        'AppModules\Invoice\Enums',
        'AppModules\Invoice\Exceptions',
    ]);
