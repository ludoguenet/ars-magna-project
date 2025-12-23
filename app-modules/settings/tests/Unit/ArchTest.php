<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Settings')
    ->toOnlyBeUsedIn('AppModules\Settings')
    ->ignoring([
        'AppModules\Settings\Contracts',
        'AppModules\Settings\DataTransferObjects',
        'AppModules\Settings\Events',
        'AppModules\Settings\Enums',
        'AppModules\Settings\Exceptions',
    ]);
