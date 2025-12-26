<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Repositories;

use AppModules\Notification\src\Contracts\NotificationRepositoryContract;
use AppModules\Notification\src\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository implements NotificationRepositoryContract
{
    /**
     * Create a new notification.
     */
    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification): bool
    {
        $notification->markAsRead();

        return true;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread notifications for a user.
     *
     * @return Collection<int, Notification>
     */
    public function getUnreadForUser(int $userId): Collection
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->unread()
            ->latest()
            ->get();
    }

    /**
     * Get all notifications for a user.
     *
     * @return Collection<int, Notification>
     */
    public function getForUser(int $userId): Collection
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    /**
     * Delete a notification.
     */
    public function delete(Notification $notification): bool
    {
        return $notification->delete();
    }

    /**
     * Find a notification by ID.
     */
    public function find(int $id): ?Notification
    {
        return Notification::find($id);
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::query()
            ->where('user_id', $userId)
            ->unread()
            ->count();
    }
}
