<?php

namespace Database\Factories;

use App\Enums\ContactType;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'designation' => fake()->optional()->jobTitle(),
            'organization' => fake()->optional()->company(),
            'email' => fake()->optional()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'address' => fake()->optional()->address(),
            'type' => fake()->randomElement(ContactType::cases())->value,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
