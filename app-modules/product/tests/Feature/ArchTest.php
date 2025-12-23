<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Product')
    ->toOnlyBeUsedIn('AppModules\Product')
    ->ignoring([
        'AppModules\Product\Contracts',
        'AppModules\Product\DataTransferObjects',
        'AppModules\Product\Events',
        'AppModules\Product\Enums',
        'AppModules\Product\Exceptions',
    ]);
