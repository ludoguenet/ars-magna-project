<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Payment')
    ->toOnlyBeUsedIn('AppModules\Payment')
    ->ignoring([
        'AppModules\Payment\Contracts',
        'AppModules\Payment\DataTransferObjects',
        'AppModules\Payment\Events',
        'AppModules\Payment\Enums',
        'AppModules\Payment\Exceptions',
    ]);
