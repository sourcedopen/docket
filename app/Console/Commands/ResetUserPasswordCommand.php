<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswordCommand extends Command
{
    protected $signature = 'users:reset-password';

    protected $description = 'Reset a user\'s password';

    public function handle(): int
    {
        $email = $this->ask('Email');

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email [{$email}].");

            return self::FAILURE;
        }

        $password = $this->secret('New Password');

        $user->update([
            'password' => Hash::make($password),
        ]);

        $this->info("Password for [{$email}] has been reset successfully.");

        return self::SUCCESS;
    }
}
