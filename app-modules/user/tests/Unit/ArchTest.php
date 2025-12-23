<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\User')
    ->toOnlyBeUsedIn('AppModules\User')
    ->ignoring([
        'AppModules\User\Contracts',
        'AppModules\User\DataTransferObjects',
        'AppModules\User\Events',
        'AppModules\User\Enums',
        'AppModules\User\Exceptions',
    ]);
