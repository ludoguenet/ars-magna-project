<?php

declare(strict_types=1);

use App\Models\User;
use AppModules\Notification\src\Enums\NotificationType;
use AppModules\Notification\src\Models\Notification;
use AppModules\Notification\src\Repositories\NotificationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('creates a notification', function () {
    $user = User::factory()->create();
    $repository = new NotificationRepository;

    $data = [
        'user_id' => $user->id,
        'title' => 'Test Title',
        'type' => NotificationType::INVOICE_CREATED->value,
        'message' => 'Test Message',
        'data' => ['key' => 'value'],
    ];

    $notification = $repository->create($data);

    expect($notification)
        ->toBeInstanceOf(Notification::class)
        ->user_id->toBe($user->id)
        ->title->toBe('Test Title')
        ->type->toBe(NotificationType::INVOICE_CREATED->value)
        ->message->toBe('Test Message');

    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'user_id' => $user->id,
        'title' => 'Test Title',
    ]);
});

it('marks a notification as read', function () {
    $notification = Notification::factory()->create([
        'read_at' => null,
    ]);

    $repository = new NotificationRepository;

    $result = $repository->markAsRead($notification);

    expect($result)->toBeTrue();
    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('marks all notifications as read for a user', function () {
    $user = User::factory()->create();
    $repository = new NotificationRepository;

    Notification::factory()->count(3)->create([
        'user_id' => $user->id,
        'read_at' => null,
    ]);

    Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => now(),
    ]);

    $count = $repository->markAllAsRead($user->id);

    expect($count)->toBe(3);
    expect(Notification::where('user_id', $user->id)->whereNull('read_at')->count())->toBe(0);
});

it('gets unread notifications for a user', function () {
    $user = User::factory()->create();
    $repository = new NotificationRepository;

    $unread1 = Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => null,
    ]);

    $unread2 = Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => null,
    ]);

    Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => now(),
    ]);

    $notifications = $repository->getUnreadForUser($user->id);

    expect($notifications)->toHaveCount(2);
    expect($notifications->pluck('id')->toArray())->toContain($unread1->id, $unread2->id);
    expect($notifications->every(fn ($notification) => $notification->read_at === null))->toBeTrue();
});

it('gets all notifications for a user', function () {
    $user = User::factory()->create();
    $repository = new NotificationRepository;

    Notification::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    Notification::factory()->count(2)->create([
        'user_id' => User::factory()->create()->id,
    ]);

    $notifications = $repository->getForUser($user->id);

    expect($notifications)->toHaveCount(3);
    expect($notifications->every(fn ($notification) => $notification->user_id === $user->id))->toBeTrue();
});

it('deletes a notification', function () {
    $notification = Notification::factory()->create();
    $repository = new NotificationRepository;

    $result = $repository->delete($notification);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('notifications', [
        'id' => $notification->id,
    ]);
});

it('finds a notification by id', function () {
    $notification = Notification::factory()->create();
    $repository = new NotificationRepository;

    $found = $repository->find($notification->id);

    expect($found)
        ->not->toBeNull()
        ->id->toBe($notification->id);
});

it('returns null when notification not found', function () {
    $repository = new NotificationRepository;

    $found = $repository->find(99999);

    expect($found)->toBeNull();
});

it('gets unread count for a user', function () {
    $user = User::factory()->create();
    $repository = new NotificationRepository;

    Notification::factory()->count(2)->create([
        'user_id' => $user->id,
        'read_at' => null,
    ]);

    Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => now(),
    ]);

    $count = $repository->getUnreadCount($user->id);

    expect($count)->toBe(2);
});
