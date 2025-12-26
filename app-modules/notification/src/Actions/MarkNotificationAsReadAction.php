<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Actions;

use AppModules\Notification\src\Models\Notification;
use AppModules\Notification\src\Repositories\NotificationRepository;

class MarkNotificationAsReadAction
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    /**
     * Execute the action.
     */
    public function handle(Notification $notification): bool
    {
        return $this->repository->markAsRead($notification);
    }
}
