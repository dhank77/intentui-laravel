<?php

use App\Models\User;
use function Pest\Browser\visit;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('password confirmation screen can be rendered in browser', function () {
    $user = User::factory()->create();

    $page = test()->visit('/confirm-password')
        ->actingAs($user);

    $page->assertSee('Confirm password')
         ->assertSee('This is a secure area of the application');
});

test('user can confirm password through browser', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $page = test()->visit('/confirm-password')  
        ->actingAs($user);

    $page->assertSee('Confirm password')
         ->fill('password', 'password')
         ->click('Confirm')
         ->assertUrlIs('/dashboard');
});

test('user cannot confirm with wrong password in browser', function () {
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    $page = test()->visit('/confirm-password')
        ->actingAs($user);

    $page->fill('password', 'wrong-password')
         ->click('Confirm')
         ->assertSee('The provided password does not match your current password.');
});

test('password confirmation form validation works in browser', function () {
    $user = User::factory()->create();

    $page = test()->visit('/confirm-password')
        ->actingAs($user);

    $page->click('Confirm')
         ->assertSee('The password field is required.');
});

test('authenticated user can access password confirmation page', function () {
    $user = User::factory()->create();

    $page = test()->visit('/confirm-password')
        ->actingAs($user);

    $page->assertStatus(200)
         ->assertSee('Confirm password');
});

test('guest user is redirected from password confirmation page', function () {
    $page = test()->visit('/confirm-password');

    $page->assertUrlIs('/login');
});
