<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Auth')
    ->toOnlyBeUsedIn('AppModules\Auth')
    ->ignoring([
        'AppModules\Auth\Contracts',
        'AppModules\Auth\DataTransferObjects',
        'AppModules\Auth\Events',
        'AppModules\Auth\Enums',
        'AppModules\Auth\Exceptions',
    ]);
