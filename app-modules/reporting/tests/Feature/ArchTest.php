<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Reporting')
    ->toOnlyBeUsedIn('AppModules\Reporting')
    ->ignoring([
        'AppModules\Reporting\Contracts',
        'AppModules\Reporting\DataTransferObjects',
        'AppModules\Reporting\Events',
        'AppModules\Reporting\Enums',
        'AppModules\Reporting\Exceptions',
    ]);
