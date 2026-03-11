<?php

namespace Database\Factories;

use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TicketType>
 */
class TicketTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->optional()->sentence(),
            'default_sla_days' => fake()->optional()->numberBetween(7, 60),
            'schema_definition' => ['fields' => []],
            'allowed_statuses' => null,
            'icon' => null,
            'color' => null,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
