<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'token:create {email=admin@example.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an API token for a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }
        
        $token = $user->createToken('api-token')->plainTextToken;
        
        $this->info("API Token created successfully!");
        $this->line("Token: {$token}");
        $this->line("");
        $this->line("Test the API with:");
        $this->line("curl -H 'Authorization: Bearer {$token}' http://localhost:8000/api/contacts");
        
        return 0;
    }
}

