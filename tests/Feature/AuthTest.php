<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('redirects guests from dashboard to login', function () {
    get('/dashboard')->assertRedirect('/login');
});

it('shows the login page', function () {
    get('/login')->assertSuccessful();
});

it('authenticates a user with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password'),
    ]);

    post('/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $this->from('/login')->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ])->assertRedirect('/login');

    $this->assertGuest();
});

it('redirects authenticated users away from login', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    get('/login')->assertRedirect('/dashboard');
});

it('logs out an authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    post('/logout')->assertRedirect('/');

    $this->assertGuest();
});

it('does not expose a registration page', function () {
    get('/register')->assertNotFound();
});
