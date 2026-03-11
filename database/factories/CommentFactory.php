<?php

namespace Database\Factories;

use App\Enums\CommentType;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'body' => fake()->paragraph(),
            'type' => CommentType::Update->value,
            'is_internal' => false,
            'commented_at' => now(),
        ];
    }
}
