<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class GoogleDriveService
{
    protected function getClientAndDrive()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $tokenPath = storage_path('app/google_drive_token.json');
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($token);
            if ($client->isAccessTokenExpired() && isset($token['refresh_token'])) {
                $client->fetchAccessTokenWithRefreshToken($token['refresh_token']);
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
        } else {
            throw new \Exception('Google Drive token file not found. Please authenticate assistendoci@gmail.com.');
        }
        $drive = new Drive($client);
        return [$client, $drive];
    }

    public function uploadFileToCompanyFolder($localPath, $fileName, Company $company)
    {
        try {
            list($client, $drive) = $this->getClientAndDrive();
            // 1. Recupera nome holding
            $holdingName = $company->holding ? $company->holding->name : 'NONE';
            // 2. Trova o crea cartella holding
            $holdingFolderId = $this->findOrCreateFolder($drive, $holdingName, null);
            // 3. Trova o crea cartella company dentro holding
            $companyFolderId = $this->findOrCreateFolder($drive, $company->name, $holdingFolderId);
            // 4. Carica file nella cartella company
            $fileMetadata = new \Google\Service\Drive\DriveFile([
                'name' => $fileName,
                'parents' => [$companyFolderId]
            ]);
            $content = file_get_contents($localPath);
            $file = $drive->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => mime_content_type($localPath),
                'uploadType' => 'multipart',
                'fields' => 'id,webViewLink,webContentLink'
            ]);
            return $file->webViewLink ?? null;
        } catch (\Exception $e) {
            \Log::error('Errore upload su Google Drive: ' . $e->getMessage());
            return null;
        }
    }

    protected function findOrCreateFolder($drive, $folderName, $parentId = null)
    {
        $query = sprintf(
            "name = '%s' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
            addslashes($folderName)
        );
        if ($parentId) {
            $query .= " and '$parentId' in parents";
        }
        $results = $drive->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
            'spaces' => 'drive',
        ]);
        if (count($results->files) > 0) {
            return $results->files[0]->id;
        }
        // Crea la cartella se non esiste
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name' => $folderName,
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);
        if ($parentId) {
            $fileMetadata->setParents([$parentId]);
        }
        $folder = $drive->files->create($fileMetadata, [
            'fields' => 'id'
        ]);
        return $folder->id;
    }

    public static function isEnabled(): bool
    {
        return (bool) config('services.enable_google_drive_upload');
    }
}
