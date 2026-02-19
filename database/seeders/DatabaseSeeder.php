<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TicketTypeSeeder::class,
            ContactSeeder::class,
            TicketSeeder::class,
            CommentSeeder::class,
            ReminderSeeder::class,
        ]);
    }
}
