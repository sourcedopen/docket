<?php

use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;

use function Pest\Laravel\get;

it('redirects guests to the login page', function () {
    get(route('activity.index'))->assertRedirect('/login');
});

it('renders the activity page successfully for authenticated users', function () {
    $this->actingAs(User::factory()->create());

    get(route('activity.index'))->assertSuccessful();
});

it('renders activity with array properties without errors', function () {
    $user = User::factory()->create();

    $ticketType = TicketType::factory()->create([
        'schema_definition' => ['fields' => [['key' => 'test_field', 'label' => 'Test', 'type' => 'string']]],
    ]);

    $ticket = Ticket::factory()->for($ticketType)->create([
        'custom_fields' => ['test_field' => 'hello'],
    ]);

    $this->actingAs($user);

    get(route('activity.index'))->assertSuccessful();
});
