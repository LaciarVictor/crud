<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken;

class CleanTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tokens:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired tokens from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
                // Obtener todos los tokens expirados
                $expiredTokens = PersonalAccessToken::where('expires_at', '<', now())->get();

                // Revocar y eliminar cada token expirado
                foreach ($expiredTokens as $token) {
                    $token->delete();
                }
        
                $this->info('Expired tokens deleted successfully.');
    }
}
