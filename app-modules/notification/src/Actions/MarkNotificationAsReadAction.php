<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Actions;

use AppModules\Notification\src\Contracts\NotificationRepositoryContract;
use AppModules\Notification\src\Models\Notification;

class MarkNotificationAsReadAction
{
    public function __construct(
        private NotificationRepositoryContract $repository
    ) {}

    /**
     * Execute the action.
     */
    public function handle(Notification $notification): bool
    {
        return $this->repository->markAsRead($notification);
    }
}
