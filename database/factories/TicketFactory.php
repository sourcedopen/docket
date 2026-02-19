<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ticket_type_id' => TicketType::factory(),
            'reference_number' => 'TKT-'.fake()->year().'-'.str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'external_reference' => fake()->optional()->bothify('REF-####??'),
            'title' => fake()->sentence(6, false),
            'description' => fake()->optional()->paragraph(),
            'status' => TicketStatus::Draft->value,
            'priority' => TicketPriority::Medium->value,
            'filed_date' => fake()->optional()->date(),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+60 days')?->format('Y-m-d'),
            'custom_fields' => null,
            'parent_ticket_id' => null,
        ];
    }
}
