<?php

use App\Models\User;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('redirects unauthenticated users from dashboard to login', function () {
    get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('logs in seeded user and redirects to dashboard', function () {
    // Ensure the seeded user exists
    $user = User::firstOrCreate(
        ['email' => 'test@example.com'],
        ['name' => 'Test User', 'password' => 'password']
    );

    $response = post(route('authenticate'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    assertAuthenticated();
});
