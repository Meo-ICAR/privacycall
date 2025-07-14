<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class GoogleDriveService
{
    protected $client;
    protected $drive;

    public function __construct($user = null)
    {
        $user = $user ?: Auth::user();
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        $token = json_decode($user->google_token, true);
        $this->client->setAccessToken($token);
        if ($this->client->isAccessTokenExpired() && isset($token['refresh_token'])) {
            $this->client->fetchAccessTokenWithRefreshToken($token['refresh_token']);
            $user->google_token = json_encode($this->client->getAccessToken());
            $user->save();
        }
        $this->drive = new Drive($this->client);
    }

    public function uploadFileToCompanyFolder($localPath, $fileName, Company $company)
    {
        // 1. Recupera nome holding
        $holdingName = $company->holding ? $company->holding->name : 'NONE';
        // 2. Trova o crea cartella holding
        $holdingFolderId = $this->findOrCreateFolder($holdingName, null);
        // 3. Trova o crea cartella company dentro holding
        $companyFolderId = $this->findOrCreateFolder($company->name, $holdingFolderId);
        // 4. Carica file nella cartella company
        $fileMetadata = new \Google\Service\Drive\DriveFile([
            'name' => $fileName,
            'parents' => [$companyFolderId]
        ]);
        $content = file_get_contents($localPath);
        $file = $this->drive->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => mime_content_type($localPath),
            'uploadType' => 'multipart',
            'fields' => 'id,webViewLink,webContentLink'
        ]);
        return $file->webViewLink ?? null;
    }

    protected function findOrCreateFolder($folderName, $parentId = null)
    {
        $query = sprintf(
            "name = '%s' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
            addslashes($folderName)
        );
        if ($parentId) {
            $query .= " and '$parentId' in parents";
        }
        $results = $this->drive->files->listFiles([
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
        $folder = $this->drive->files->create($fileMetadata, [
            'fields' => 'id'
        ]);
        return $folder->id;
    }

    public static function isEnabled(): bool
    {
        return (bool) config('services.enable_google_drive_upload');
    }
}
