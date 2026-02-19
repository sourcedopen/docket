<?php

use App\Models\User;

use function Pest\Laravel\get;

it('redirects guests to the login page', function () {
    get('/dashboard')->assertRedirect('/login');
});

it('renders successfully for authenticated users', function () {
    $this->actingAs(User::factory()->create());

    get('/dashboard')->assertSuccessful();
});

it('displays the welcome message', function () {
    $this->actingAs(User::factory()->create());

    get('/dashboard')->assertSee('Welcome to Open Docket');
});
