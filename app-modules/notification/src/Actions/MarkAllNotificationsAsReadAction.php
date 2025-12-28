<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Actions;

use AppModules\Notification\src\Contracts\NotificationRepositoryContract;

class MarkAllNotificationsAsReadAction
{
    public function __construct(
        private NotificationRepositoryContract $repository
    ) {}

    /**
     * Execute the action.
     */
    public function handle(int $userId): int
    {
        return $this->repository->markAllAsRead($userId);
    }
}
