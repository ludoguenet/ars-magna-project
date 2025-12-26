<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Actions;

use AppModules\Notification\src\Repositories\NotificationRepository;

class MarkAllNotificationsAsReadAction
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    /**
     * Execute the action.
     */
    public function handle(int $userId): int
    {
        return $this->repository->markAllAsRead($userId);
    }
}
