<?php

declare(strict_types=1);

use App\Models\User;
use AppModules\Notification\src\Actions\CreateNotificationAction;
use AppModules\Notification\src\Actions\DeleteNotificationAction;
use AppModules\Notification\src\Actions\MarkAllNotificationsAsReadAction;
use AppModules\Notification\src\Actions\MarkNotificationAsReadAction;
use AppModules\Notification\src\DataTransferObjects\NotificationData;
use AppModules\Notification\src\Enums\NotificationType;
use AppModules\Notification\src\Models\Notification;
use AppModules\Notification\src\Repositories\NotificationRepository;
use AppModules\Notification\src\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\mock;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('sends a notification', function () {
    $user = User::factory()->create();
    $repository = mock(NotificationRepository::class);
    $createAction = mock(CreateNotificationAction::class);
    $markAsReadAction = mock(MarkNotificationAsReadAction::class);
    $markAllAsReadAction = mock(MarkAllNotificationsAsReadAction::class);
    $deleteAction = mock(DeleteNotificationAction::class);

    $notificationData = new NotificationData(
        userId: $user->id,
        type: NotificationType::INVOICE_CREATED,
        title: 'Test Title',
        message: 'Test Message',
        data: ['key' => 'value'],
    );

    $notification = Notification::factory()->make([
        'user_id' => $user->id,
        'type' => NotificationType::INVOICE_CREATED->value,
        'title' => 'Test Title',
        'message' => 'Test Message',
    ]);

    $createAction->shouldReceive('handle')
        ->once()
        ->with($notificationData)
        ->andReturn($notification);

    $service = new NotificationService(
        $repository,
        $createAction,
        $markAsReadAction,
        $markAllAsReadAction,
        $deleteAction
    );

    $result = $service->send($notificationData);

    expect($result)->toBe($notification);
});

it('marks a notification as read', function () {
    $notification = Notification::factory()->create();
    $repository = mock(NotificationRepository::class);
    $createAction = mock(CreateNotificationAction::class);
    $markAsReadAction = mock(MarkNotificationAsReadAction::class);
    $markAllAsReadAction = mock(MarkAllNotificationsAsReadAction::class);
    $deleteAction = mock(DeleteNotificationAction::class);

    $markAsReadAction->shouldReceive('handle')
        ->once()
        ->with($notification)
        ->andReturn(true);

    $service = new NotificationService(
        $repository,
        $createAction,
        $markAsReadAction,
        $markAllAsReadAction,
        $deleteAction
    );

    $result = $service->markAsRead($notification);

    expect($result)->toBeTrue();
});

it('marks all notifications as read for a user', function () {
    $user = User::factory()->create();
    $repository = mock(NotificationRepository::class);
    $createAction = mock(CreateNotificationAction::class);
    $markAsReadAction = mock(MarkNotificationAsReadAction::class);
    $markAllAsReadAction = mock(MarkAllNotificationsAsReadAction::class);
    $deleteAction = mock(DeleteNotificationAction::class);

    $markAllAsReadAction->shouldReceive('handle')
        ->once()
        ->with($user->id)
        ->andReturn(5);

    $service = new NotificationService(
        $repository,
        $createAction,
        $markAsReadAction,
        $markAllAsReadAction,
        $deleteAction
    );

    $result = $service->markAllAsRead($user->id);

    expect($result)->toBe(5);
});

it('gets unread count for a user', function () {
    $user = User::factory()->create();
    $repository = mock(NotificationRepository::class);
    $createAction = mock(CreateNotificationAction::class);
    $markAsReadAction = mock(MarkNotificationAsReadAction::class);
    $markAllAsReadAction = mock(MarkAllNotificationsAsReadAction::class);
    $deleteAction = mock(DeleteNotificationAction::class);

    $repository->shouldReceive('getUnreadCount')
        ->once()
        ->with($user->id)
        ->andReturn(3);

    $service = new NotificationService(
        $repository,
        $createAction,
        $markAsReadAction,
        $markAllAsReadAction,
        $deleteAction
    );

    $result = $service->getUnreadCount($user->id);

    expect($result)->toBe(3);
});

it('gets all notifications for a user', function () {
    $user = User::factory()->create();
    $repository = mock(NotificationRepository::class);
    $createAction = mock(CreateNotificationAction::class);
    $markAsReadAction = mock(MarkNotificationAsReadAction::class);
    $markAllAsReadAction = mock(MarkAllNotificationsAsReadAction::class);
    $deleteAction = mock(DeleteNotificationAction::class);

    $notifications = Notification::factory()->count(3)->make([
        'user_id' => $user->id,
    ]);

    $repository->shouldReceive('getForUser')
        ->once()
        ->with($user->id)
        ->andReturn($notifications);

    $service = new NotificationService(
        $repository,
        $createAction,
        $markAsReadAction,
        $markAllAsReadAction,
        $deleteAction
    );

    $result = $service->getForUser($user->id);

    expect($result)->toBe($notifications);
});

it('deletes a notification', function () {
    $notification = Notification::factory()->create();
    $repository = mock(NotificationRepository::class);
    $createAction = mock(CreateNotificationAction::class);
    $markAsReadAction = mock(MarkNotificationAsReadAction::class);
    $markAllAsReadAction = mock(MarkAllNotificationsAsReadAction::class);
    $deleteAction = mock(DeleteNotificationAction::class);

    $deleteAction->shouldReceive('handle')
        ->once()
        ->with($notification)
        ->andReturn(true);

    $service = new NotificationService(
        $repository,
        $createAction,
        $markAsReadAction,
        $markAllAsReadAction,
        $deleteAction
    );

    $result = $service->delete($notification);

    expect($result)->toBeTrue();
});
