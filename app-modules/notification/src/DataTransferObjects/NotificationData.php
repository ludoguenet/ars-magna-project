<?php

declare(strict_types=1);

namespace AppModules\Notification\src\DataTransferObjects;

use AppModules\Notification\src\Enums\NotificationType;

final readonly class NotificationData
{
    public function __construct(
        public int $userId,
        public NotificationType $type,
        public string $title,
        public string $message,
        public array $data = [],
    ) {}
}
