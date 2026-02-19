<?php

namespace Database\Factories;

use App\Enums\ReminderType;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(4, false),
            'remind_at' => fake()->dateTimeBetween('now', '+30 days'),
            'type' => ReminderType::Custom->value,
            'notes' => fake()->optional()->sentence(),
            'is_sent' => false,
            'sent_at' => null,
            'is_recurring' => false,
            'recurrence_rule' => null,
            'recurrence_ends_at' => null,
        ];
    }
}
