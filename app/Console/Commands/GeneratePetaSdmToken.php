<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GeneratePetaSdmToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peta-sdm:token {--name=Peta SDM System}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Sanctum Personal Access Token for Peta SDM system integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name');

        $systemUser = User::firstOrCreate(
            ['email' => 'system-peta-sdm@academy.portal'],
            [
                'name' => 'Peta SDM Integration System',
                'password' => bcrypt(Str::random(32)),
            ]
        );

        // Delete existing tokens with same name if any
        $systemUser->tokens()->where('name', $name)->delete();

        // Create new token
        $token = $systemUser->createToken($name);

        $this->info('=====================================================');
        $this->info('  PERSONAL ACCESS TOKEN GENERATED FOR PETA SDM SYSTEM');
        $this->info('=====================================================');
        $this->line('');
        $this->comment('Token Name: '.$name);
        $this->comment('User Account: '.$systemUser->email);
        $this->line('');
        $this->question('PLAIN TEXT TOKEN (Save this in your Peta SDM .env):');
        $this->line('<fg=yellow;bg=black>'.$token->plainTextToken.'</>');
        $this->line('');
        $this->info('Header usage example:');
        $this->line('Authorization: Bearer '.$token->plainTextToken);
        $this->info('=====================================================');

        return 0;
    }
}
