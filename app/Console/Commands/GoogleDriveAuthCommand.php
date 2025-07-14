<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Google\Client as GoogleClient;

class GoogleDriveAuthCommand extends Command
{
    protected $signature = 'google:auth';
    protected $description = 'Authenticate Google Drive for assistendoci@gmail.com and save token.';

    public function handle()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->addScope('https://www.googleapis.com/auth/drive');

        $authUrl = $client->createAuthUrl();
        $this->info('Open the following URL in your browser and authorize the app:');
        $this->line($authUrl);
        $code = $this->ask('Enter the authorization code');

        $token = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($token['error'])) {
            $this->error('Error fetching token: ' . $token['error_description']);
            return 1;
        }
        $tokenPath = storage_path('app/google_drive_token.json');
        file_put_contents($tokenPath, json_encode($token));
        $this->info('Token saved to ' . $tokenPath);
        return 0;
    }
}
