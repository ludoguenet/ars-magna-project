<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Shared')
    ->toOnlyBeUsedIn('AppModules\Shared')
    ->ignoring([
        'AppModules\Shared\Contracts',
        'AppModules\Shared\DataTransferObjects',
        'AppModules\Shared\Events',
        'AppModules\Shared\Enums',
        'AppModules\Shared\Exceptions',
        'AppModules\Shared\ValueObjects',
    ]);
