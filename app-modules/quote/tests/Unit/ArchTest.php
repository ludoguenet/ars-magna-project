<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Quote')
    ->toOnlyBeUsedIn('AppModules\Quote')
    ->ignoring([
        'AppModules\Quote\Contracts',
        'AppModules\Quote\DataTransferObjects',
        'AppModules\Quote\Events',
        'AppModules\Quote\Enums',
        'AppModules\Quote\Exceptions',
    ]);
