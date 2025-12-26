<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Contracts;

use AppModules\Notification\src\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

interface NotificationRepositoryContract
{
    /**
     * Create a new notification.
     */
    public function create(array $data): Notification;

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): bool;

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int;

    /**
     * Get unread notifications for a user.
     *
     * @return Collection<int, Notification>
     */
    public function getUnreadForUser(int $userId): Collection;

    /**
     * Get all notifications for a user.
     *
     * @return Collection<int, Notification>
     */
    public function getForUser(int $userId): Collection;

    /**
     * Delete a notification.
     */
    public function delete(Notification $notification): bool;

    /**
     * Find a notification by ID.
     */
    public function find(int $id): ?Notification;

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId): int;
}
