<?php

use App\Models\Ticket;
use App\Models\TicketType;

it('removes fee_paid from ticket type schema definitions', function () {
    $ticketType = TicketType::factory()->create([
        'schema_definition' => [
            'fields' => [
                ['key' => 'pio_name', 'label' => 'PIO Name', 'type' => 'string', 'required' => true],
                ['key' => 'fee_paid', 'label' => 'Fee Paid (₹)', 'type' => 'number', 'default' => 10],
                ['key' => 'department', 'label' => 'Department', 'type' => 'string'],
            ],
        ],
    ]);

    $this->artisan('schema:cleanse')
        ->assertSuccessful();

    $schema = $ticketType->fresh()->schema_definition;

    expect($schema['fields'])->toHaveCount(2)
        ->and(array_column($schema['fields'], 'key'))->not->toContain('fee_paid');
});

it('removes fee_paid from ticket custom fields', function () {
    $ticket = Ticket::factory()->create([
        'custom_fields' => ['pio_name' => 'John', 'fee_paid' => '10', 'department' => 'Revenue'],
    ]);

    $this->artisan('schema:cleanse')
        ->assertSuccessful();

    $customFields = $ticket->fresh()->custom_fields;

    expect($customFields)->toHaveCount(2)
        ->and($customFields)->not->toHaveKey('fee_paid')
        ->and($customFields)->toHaveKeys(['pio_name', 'department']);
});

it('skips records without fee_paid', function () {
    $ticketType = TicketType::factory()->create([
        'schema_definition' => [
            'fields' => [
                ['key' => 'pio_name', 'label' => 'PIO Name', 'type' => 'string'],
            ],
        ],
    ]);

    $ticket = Ticket::factory()->create([
        'custom_fields' => ['pio_name' => 'John'],
    ]);

    $this->artisan('schema:cleanse')
        ->expectsOutputToContain('Cleaned 0 ticket type schema(s) and 0 ticket custom field(s)')
        ->assertSuccessful();

    expect($ticketType->fresh()->schema_definition['fields'])->toHaveCount(1)
        ->and($ticket->fresh()->custom_fields)->toHaveCount(1);
});
