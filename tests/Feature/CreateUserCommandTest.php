<?php

use App\Models\User;

it('creates a user with the provided details', function () {
    $this->artisan('users:create')
        ->expectsQuestion('Name', 'John Doe')
        ->expectsQuestion('Email', 'john@example.com')
        ->expectsQuestion('Password', 'password')
        ->expectsOutput('User [john@example.com] created successfully.')
        ->assertSuccessful();

    expect(User::query()->where('email', 'john@example.com')->exists())->toBeTrue();
});

it('fails when the email is already taken', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $this->artisan('users:create')
        ->expectsQuestion('Name', 'John Doe')
        ->expectsQuestion('Email', 'john@example.com')
        ->expectsOutput('A user with email [john@example.com] already exists.')
        ->assertFailed();
});

it('hashes the password', function () {
    $this->artisan('users:create')
        ->expectsQuestion('Name', 'John Doe')
        ->expectsQuestion('Email', 'john@example.com')
        ->expectsQuestion('Password', 'secret123')
        ->assertSuccessful();

    $user = User::query()->where('email', 'john@example.com')->first();

    expect(password_verify('secret123', $user->password))->toBeTrue();
});
