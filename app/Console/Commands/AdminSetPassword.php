<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class AdminSetPassword extends Command
{
    protected $signature   = 'admin:set-password';
    protected $description = 'Generate a bcrypt hash for ADMIN_PASSWORD in .env (more secure than plaintext)';

    public function handle(): int
    {
        $this->info('This command generates a bcrypt hash to store as ADMIN_PASSWORD in your .env file.');
        $this->line('');

        $password = $this->secret('Enter new super-admin password');

        if (empty($password)) {
            $this->error('Password cannot be empty.');
            return self::FAILURE;
        }

        $confirm = $this->secret('Confirm password');

        if ($password !== $confirm) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }

        if (strlen($password) < 12) {
            $this->warn('Password is shorter than 12 characters. Consider a longer password.');
        }

        $hash = Hash::make($password);

        $this->line('');
        $this->info('Set this in your .env file:');
        $this->line('');
        $this->line("ADMIN_PASSWORD={$hash}");
        $this->line('');
        $this->warn('Keep your .env file secure — NEVER commit it to version control.');

        return self::SUCCESS;
    }
}
