<?php

declare(strict_types=1);

use App\Models\User;
use AppModules\Notification\src\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('displays the notifications index page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Notification::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $response = $this->get(route('notification::index'));

    $response->assertSuccessful()
        ->assertViewIs('notification::index')
        ->assertViewHas('notifications');
});

it('displays only the authenticated user\'s notifications', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    Notification::factory()->count(2)->create([
        'user_id' => $user1->id,
    ]);

    Notification::factory()->count(3)->create([
        'user_id' => $user2->id,
    ]);

    $response = $this->get(route('notification::index'));

    $response->assertSuccessful();
    $notifications = $response->viewData('notifications');

    expect($notifications)->toHaveCount(2);
    expect($notifications->every(fn ($notification) => $notification->user_id === $user1->id))->toBeTrue();
});

it('displays a specific notification', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification = Notification::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->get(route('notification::show', $notification->id));

    $response->assertSuccessful()
        ->assertViewIs('notification::show')
        ->assertViewHas('notification', $notification);
});

it('returns 404 when viewing another user\'s notification', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    $notification = Notification::factory()->create([
        'user_id' => $user2->id,
    ]);

    $response = $this->get(route('notification::show', $notification->id));

    $response->assertNotFound();
});

it('returns 404 when viewing non-existent notification', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('notification::show', 99999));

    $response->assertNotFound();
});

it('marks a notification as read', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification = Notification::factory()->create([
        'user_id' => $user->id,
        'read_at' => null,
    ]);

    $response = $this->patch(route('notification::markAsRead', $notification->id));

    $response->assertRedirect(route('notification::index'))
        ->assertSessionHas('success', 'Notification marked as read');

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('returns 404 when marking another user\'s notification as read', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    $notification = Notification::factory()->create([
        'user_id' => $user2->id,
    ]);

    $response = $this->patch(route('notification::markAsRead', $notification->id));

    $response->assertNotFound();
});

it('marks all notifications as read for the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Notification::factory()->count(3)->create([
        'user_id' => $user->id,
        'read_at' => null,
    ]);

    $response = $this->patch(route('notification::markAllAsRead'));

    $response->assertRedirect(route('notification::index'))
        ->assertSessionHas('success', 'All notifications marked as read');

    expect(Notification::where('user_id', $user->id)->whereNull('read_at')->count())->toBe(0);
});

it('only marks notifications as read for the authenticated user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    Notification::factory()->count(2)->create([
        'user_id' => $user1->id,
        'read_at' => null,
    ]);

    Notification::factory()->count(3)->create([
        'user_id' => $user2->id,
        'read_at' => null,
    ]);

    $this->patch(route('notification::markAllAsRead'));

    expect(Notification::where('user_id', $user1->id)->whereNull('read_at')->count())->toBe(0);
    expect(Notification::where('user_id', $user2->id)->whereNull('read_at')->count())->toBe(3);
});

it('deletes a notification', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $notification = Notification::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->delete(route('notification::destroy', $notification->id));

    $response->assertRedirect(route('notification::index'))
        ->assertSessionHas('success', 'Notification deleted');

    $this->assertDatabaseMissing('notifications', [
        'id' => $notification->id,
    ]);
});

it('returns 404 when deleting another user\'s notification', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $this->actingAs($user1);

    $notification = Notification::factory()->create([
        'user_id' => $user2->id,
    ]);

    $response = $this->delete(route('notification::destroy', $notification->id));

    $response->assertNotFound();

    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
    ]);
});

it('requires authentication to access notifications', function () {
    $response = $this->get(route('notification::index'));

    $response->assertRedirect(route('login'));
});
