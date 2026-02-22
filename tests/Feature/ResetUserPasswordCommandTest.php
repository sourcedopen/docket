<?php

use App\Models\User;

it('resets a user\'s password', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $this->artisan('users:reset-password')
        ->expectsQuestion('Email', 'john@example.com')
        ->expectsQuestion('New Password', 'new-secret')
        ->expectsOutput('Password for [john@example.com] has been reset successfully.')
        ->assertSuccessful();

    $user = User::query()->where('email', 'john@example.com')->first();

    expect(password_verify('new-secret', $user->password))->toBeTrue();
});

it('fails when the user does not exist', function () {
    $this->artisan('users:reset-password')
        ->expectsQuestion('Email', 'nonexistent@example.com')
        ->expectsOutput('No user found with email [nonexistent@example.com].')
        ->assertFailed();
});
