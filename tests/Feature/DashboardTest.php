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

it('displays the dashboard stats', function () {
    $this->actingAs(User::factory()->create());

    get('/dashboard')->assertSee('Open Tickets')->assertSee('Overdue');
});
