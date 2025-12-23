<?php

declare(strict_types=1);

arch()
    ->expect('AppModules\Document')
    ->toOnlyBeUsedIn('AppModules\Document')
    ->ignoring([
        'AppModules\Document\Contracts',
        'AppModules\Document\DataTransferObjects',
        'AppModules\Document\Events',
        'AppModules\Document\Enums',
        'AppModules\Document\Exceptions',
    ]);
