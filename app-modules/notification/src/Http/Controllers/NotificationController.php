<?php

declare(strict_types=1);

namespace AppModules\Notification\src\Http\Controllers;

use AppModules\Notification\src\Http\Requests\MarkNotificationAsReadRequest;
use AppModules\Notification\src\Models\Notification;
use AppModules\Notification\src\Repositories\NotificationRepository;
use AppModules\Notification\src\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController
{
    public function __construct(
        private NotificationService $notificationService,
        private NotificationRepository $repository
    ) {}

    /**
     * Display a listing of notifications.
     */
    public function index(): View
    {
        $notifications = $this->repository->getForUser(auth()->id());

        return view('notification::index', compact('notifications'));
    }

    /**
     * Display the specified notification.
     */
    public function show(int $id): View
    {
        $notification = $this->repository->find($id);

        if (! $notification || $notification->user_id !== auth()->id()) {
            abort(404);
        }

        return view('notification::show', compact('notification'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(MarkNotificationAsReadRequest $request, int $id): RedirectResponse
    {
        $notification = $this->repository->find($id);

        if (! $notification || $notification->user_id !== auth()->id()) {
            abort(404);
        }

        $this->notificationService->markAsRead($notification);

        return redirect()
            ->route('notification::index')
            ->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        $this->notificationService->markAllAsRead(auth()->id());

        return redirect()
            ->route('notification::index')
            ->with('success', 'All notifications marked as read');
    }

    /**
     * Remove the specified notification.
     */
    public function destroy(int $id): RedirectResponse
    {
        $notification = $this->repository->find($id);

        if (! $notification || $notification->user_id !== auth()->id()) {
            abort(404);
        }

        $this->notificationService->delete($notification);

        return redirect()
            ->route('notification::index')
            ->with('success', 'Notification deleted');
    }
}
