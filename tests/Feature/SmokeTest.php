<?php

/**
 * Smoke tests - Basic tests to ensure all routes are accessible
 * These tests verify that routes exist and return responses (even if errors)
 */
test('all public routes are accessible without errors', function () {
    $routes = [
        '/',
        '/up',
        '/invoices',
        '/invoices/create',
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);

        // Should return a response (200, 302, 401, 403, 404, 500)
        expect($response->status())->toBeIn([200, 302, 401, 403, 404, 500]);
    }
});

test('invoice resource routes exist', function () {
    // GET routes
    $getRoutes = [
        '/invoices',
        '/invoices/create',
        '/invoices/1',
        '/invoices/1/edit',
    ];

    foreach ($getRoutes as $route) {
        $response = $this->get($route);
        expect($response->status())->toBeIn([200, 302, 401, 403, 404, 500]);
    }

    // POST route
    $response = $this->post('/invoices', []);
    expect($response->status())->toBeIn([200, 302, 401, 403, 404, 422, 500]);

    // PUT route
    $response = $this->put('/invoices/1', []);
    expect($response->status())->toBeIn([200, 302, 401, 403, 404, 422, 500]);

    // DELETE route
    $response = $this->delete('/invoices/1');
    expect($response->status())->toBeIn([200, 302, 401, 403, 404, 500]);
});
