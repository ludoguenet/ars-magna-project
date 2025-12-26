<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Services;

use AppModules\Notification\src\Actions\CreateNotificationAction;
use AppModules\Notification\src\Actions\DeleteNotificationAction;
use AppModules\Notification\src\Actions\MarkAllNotificationsAsReadAction;
use AppModules\Notification\src\Actions\MarkNotificationAsReadAction;
use AppModules\Notification\src\DataTransferObjects\NotificationData;
use AppModules\Notification\src\Models\Notification;
use AppModules\Notification\src\Repositories\NotificationRepository;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function __construct(
        private NotificationRepository $repository,
        private CreateNotificationAction $createNotificationAction,
        private MarkNotificationAsReadAction $markAsReadAction,
        private MarkAllNotificationsAsReadAction $markAllAsReadAction,
        private DeleteNotificationAction $deleteNotificationAction
    ) {}

    /**
     * Send a notification.
     */
    public function send(NotificationData $data): Notification
    {
        return $this->createNotificationAction->handle($data);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): bool
    {
        return $this->markAsReadAction->handle($notification);
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return $this->markAllAsReadAction->handle($userId);
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->repository->getUnreadCount($userId);
    }

    /**
     * Get all notifications for a user.
     *
     * @return Collection<int, Notification>
     */
    public function getForUser(int $userId): Collection
    {
        return $this->repository->getForUser($userId);
    }

    /**
     * Delete a notification.
     */
    public function delete(Notification $notification): bool
    {
        return $this->deleteNotificationAction->handle($notification);
    }
}
