<?php

test('the application returns a successful response', function () {
    $response = $this->get('/');

    // Home route redirects to dashboard
    $response->assertStatus(302);
});
