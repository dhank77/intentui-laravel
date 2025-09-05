<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('login page can be visited', function () {
    $page = test()->visit('/login');
    
    $page->assertSee('Login')
         ->assertSee('Sign in with your email or continue with a connected account.');
});

test('user can login with valid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $page = test()->visit('/login');

    $page->type('input[name="email"]', 'test@example.com')
         ->type('input[name="password"]', 'password')
         ->click('button[type="submit"]')
         ->waitForUrl('/dashboard')
         ->assertSee('Dashboard');
});

test('user cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com', 
        'password' => 'password',
    ]);

    $page = test()->visit('/login');

    $page->type('input[name="email"]', 'test@example.com')
         ->type('input[name="password"]', 'wrong-password')
         ->click('button[type="submit"]')
         ->waitFor('.text-red-600, .text-destructive')
         ->assertSee('These credentials do not match our records.');
});

test('login form shows validation errors for empty fields', function () {
    $page = test()->visit('/login');

    $page->click('button[type="submit"]')
         ->waitFor('.text-red-600, .text-destructive')
         ->assertSee('required');
});

test('remember me checkbox works', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $page = test()->visit('/login');

    $page->type('input[name="email"]', 'test@example.com')
         ->type('input[name="password"]', 'password')
         ->check('input[name="remember"]')
         ->click('button[type="submit"]')
         ->waitForUrl('/dashboard');
});
