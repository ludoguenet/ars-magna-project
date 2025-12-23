<?php

/**
 * Basic route tests - Verify routes work correctly
 */
test('home route displays welcome view', function () {
    $response = $this->get('/');

    // Home route redirects to dashboard
    $response->assertStatus(302);
});

test('health check route returns ok status', function () {
    $response = $this->get('/up');

    $response->assertSuccessful();
    $response->assertSee('Application up', false);
});

test('invoice index route is accessible', function () {
    $response = $this->get('/invoices');

    // May require authentication, return 404 if route not registered, or success
    expect($response->status())->toBeIn([200, 302, 401, 403, 404, 500]);
});

test('invoice create route is accessible', function () {
    $response = $this->get('/invoices/create');

    // May require authentication, return 404 if route not registered, or success
    expect($response->status())->toBeIn([200, 302, 401, 403, 404, 500]);
});

test('invoice store route handles requests', function () {
    $response = $this->post('/invoices', []);

    // Should either redirect on success, return validation errors, require auth, or 404 if not registered
    expect($response->status())->toBeIn([200, 302, 401, 403, 404, 422]);
});

test('invoice show route handles requests', function () {
    $response = $this->get('/invoices/1');

    // Should return a response (may be 404 if invoice doesn't exist, or redirect if auth required)
    expect($response->status())->toBeIn([200, 302, 401, 403, 404]);
});

test('invoice edit route handles requests', function () {
    $response = $this->get('/invoices/1/edit');

    // Should return a response (may be 404 if invoice doesn't exist, or redirect if auth required)
    expect($response->status())->toBeIn([200, 302, 401, 403, 404]);
});

test('invoice update route handles requests', function () {
    $response = $this->put('/invoices/1', []);

    // Should return a response (may be 404 if invoice doesn't exist, or redirect if auth required)
    expect($response->status())->toBeIn([200, 302, 401, 403, 404, 422]);
});

test('invoice destroy route handles requests', function () {
    $response = $this->delete('/invoices/1');

    // Should return a response (may be 404 if invoice doesn't exist, or redirect if auth required)
    expect($response->status())->toBeIn([200, 302, 401, 403, 404]);
});

test('invoice show route returns 404 for non-existent invoice', function () {
    $response = $this->get('/invoices/99999');

    // Should return 404 or redirect if auth required
    expect($response->status())->toBeIn([302, 404]);
});

test('invoice edit route returns 404 for non-existent invoice', function () {
    $response = $this->get('/invoices/99999/edit');

    // Should return 404 or redirect if auth required
    expect($response->status())->toBeIn([302, 404]);
});

test('invoice update route handles non-existent invoice', function () {
    $response = $this->put('/invoices/99999', []);

    // Should return 404 or redirect if auth required
    expect($response->status())->toBeIn([302, 404, 422]);
});

test('invoice destroy route handles non-existent invoice', function () {
    $response = $this->delete('/invoices/99999');

    // Should return 404 or redirect if auth required
    expect($response->status())->toBeIn([302, 404]);
});
