<?php

declare(strict_types=1);

use App\Models\User;
use AppModules\Notification\src\Enums\NotificationType;
use AppModules\Notification\src\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('belongs to a user', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($notification->user)
        ->toBeInstanceOf(User::class)
        ->id->toBe($user->id);
});

it('can mark as read', function () {
    $notification = Notification::factory()->create([
        'read_at' => null,
    ]);

    $notification->markAsRead();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('has unread scope', function () {
    $user = User::factory()->create();

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

    $unreadNotifications = Notification::query()
        ->where('user_id', $user->id)
        ->unread()
        ->get();

    expect($unreadNotifications)->toHaveCount(2);
    expect($unreadNotifications->pluck('id')->toArray())->toContain($unread1->id, $unread2->id);
});

it('has read scope', function () {
    $user = User::factory()->create();

    Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => null,
    ]);

    $read1 = Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => now(),
    ]);

    $read2 = Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => now(),
    ]);

    $readNotifications = Notification::query()
        ->where('user_id', $user->id)
        ->read()
        ->get();

    expect($readNotifications)->toHaveCount(2);
    expect($readNotifications->pluck('id')->toArray())->toContain($read1->id, $read2->id);
});

it('has byType scope', function () {
    $user = User::factory()->create();

    $invoiceNotification = Notification::factory()->create([
        'user_id' => $user->id,
        'type' => NotificationType::INVOICE_CREATED->value,
    ]);

    Notification::factory()->create([
        'user_id' => $user->id,
        'type' => NotificationType::PAYMENT_RECEIVED->value,
    ]);

    $notifications = Notification::query()
        ->where('user_id', $user->id)
        ->byType(NotificationType::INVOICE_CREATED->value)
        ->get();

    expect($notifications)->toHaveCount(1);
    expect($notifications->first()->id)->toBe($invoiceNotification->id);
});

it('casts data to array', function () {
    $notification = Notification::factory()->create([
        'data' => ['key' => 'value', 'number' => 123],
    ]);

    expect($notification->data)
        ->toBeArray()
        ->toHaveKey('key')
        ->toHaveKey('number');
    expect($notification->data['key'])->toBe('value');
    expect($notification->data['number'])->toBe(123);
});

it('casts read_at to datetime', function () {
    $notification = Notification::factory()->create([
        'read_at' => now(),
    ]);

    expect($notification->read_at)
        ->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});
