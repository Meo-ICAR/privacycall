<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope(Drive::DRIVE);
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        if ($request->has('code')) {
            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            $client->setAccessToken($token);

            // Salva il token nell'utente autenticato
            $user = Auth::user();
            $user->google_token = json_encode($token);
            $user->save();

            // Esempio: lista file su Drive
            $service = new Drive($client);
            $files = $service->files->listFiles(['pageSize' => 10]);

            return response()->json($files);
        }

        return redirect()->route('google.redirect');
    }

    // Metodo di esempio per usare il token salvato
    public function listDriveFiles()
    {
        $user = Auth::user();
        $token = json_decode($user->google_token, true);

        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setAccessToken($token);

        // Refresh token se scaduto
        if ($client->isAccessTokenExpired()) {
            if (isset($token['refresh_token'])) {
                $client->fetchAccessTokenWithRefreshToken($token['refresh_token']);
                $user->google_token = json_encode($client->getAccessToken());
                $user->save();
            } else {
                return redirect()->route('google.redirect');
            }
        }

        $service = new Drive($client);
        $files = $service->files->listFiles(['pageSize' => 10]);
        return response()->json($files);
    }
}
