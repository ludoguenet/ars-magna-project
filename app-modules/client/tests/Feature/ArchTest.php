<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Client')
    ->toOnlyBeUsedIn('AppModules\Client')
    ->ignoring([
        'AppModules\Client\Contracts',
        'AppModules\Client\DataTransferObjects',
        'AppModules\Client\Events',
        'AppModules\Client\Enums',
        'AppModules\Client\Exceptions',
    ]);
