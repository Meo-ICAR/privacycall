<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmailIntegrationService
{
    protected $gmailApiKey;
    protected $gmailApiUrl = 'https://gmail.googleapis.com/gmail/v1/users/me';

    public function __construct()
    {
        $this->gmailApiKey = config('services.gmail.api_key');
    }

    /**
     * Fetch emails for a company's data controller contact.
     */
    public function fetchEmailsForCompany(Company $company, $maxResults = 50): array
    {
        if (!$company->data_controller_contact) {
            Log::warning("No data controller contact email for company: {$company->id}");
            return [];
        }

        try {
            // For demo purposes, we'll simulate email fetching
            // In production, you would integrate with Gmail API, IMAP, or other email services
            $emails = $this->simulateEmailFetch($company, $maxResults);

            $processedCount = 0;
            foreach ($emails as $emailData) {
                if ($this->processEmail($company, $emailData)) {
                    $processedCount++;
                }
            }

            Log::info("Processed {$processedCount} emails for company: {$company->id}");
            return ['success' => true, 'processed' => $processedCount];

        } catch (\Exception $e) {
            Log::error("Error fetching emails for company {$company->id}: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Process a single email and store it in the database.
     */
    protected function processEmail(Company $company, array $emailData): bool
    {
        try {
            // Check if email already exists
            $existingEmail = CompanyEmail::where('email_id', $emailData['email_id'])
                ->where('company_id', $company->id)
                ->first();

            if ($existingEmail) {
                return false; // Email already processed
            }

            // Determine if email is GDPR-related
            $isGdprRelated = $this->isGdprRelatedEmail($emailData['subject'], $emailData['body']);

            // Determine priority
            $priority = $this->determinePriority($emailData['subject'], $emailData['body']);

            // Determine category
            $category = $this->determineCategory($emailData['subject'], $emailData['body']);

            // Create email record
            $email = CompanyEmail::create([
                'company_id' => $company->id,
                'email_id' => $emailData['email_id'],
                'thread_id' => $emailData['thread_id'] ?? null,
                'from_email' => $emailData['from_email'],
                'from_name' => $emailData['from_name'] ?? null,
                'to_email' => $company->data_controller_contact,
                'subject' => $emailData['subject'],
                'body' => $emailData['body'],
                'body_plain' => $emailData['body_plain'] ?? null,
                'attachments' => $emailData['attachments'] ?? null,
                'headers' => $emailData['headers'] ?? null,
                'received_at' => $emailData['received_at'],
                'status' => 'unread',
                'priority' => $priority,
                'labels' => $emailData['labels'] ?? null,
                'is_gdpr_related' => $isGdprRelated,
                'category' => $category,
            ]);

            // Process attachments if any
            if (!empty($emailData['attachments'])) {
                $this->processAttachments($email, $emailData['attachments']);
            }

            return true;

        } catch (\Exception $e) {
            Log::error("Error processing email for company {$company->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Process and save email attachments.
     */
    protected function processAttachments(CompanyEmail $email, array $attachments): void
    {
        foreach ($attachments as $attachment) {
            try {
                // Generate unique filename
                $filename = uniqid() . '_' . $attachment['name'];
                $storagePath = 'email-documents/' . $email->company_id . '/' . $filename;

                // Save file to storage (in real implementation, you'd get the actual file content)
                // For demo purposes, we'll create a placeholder file
                Storage::put($storagePath, 'Attachment content for: ' . $attachment['name']);

                // Create EmailDocument record
                \App\Models\EmailDocument::create([
                    'company_email_id' => $email->id,
                    'filename' => $filename,
                    'original_name' => $attachment['name'],
                    'mime_type' => $attachment['mime_type'] ?? 'application/octet-stream',
                    'size' => $attachment['size'] ?? 0,
                    'storage_path' => $storagePath,
                ]);

            } catch (\Exception $e) {
                Log::error("Error processing attachment for email {$email->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Send a reply to an email.
     */
    public function sendReply(CompanyEmail $email, User $user, string $replyBody, array $attachments = []): bool
    {
        try {
            // Create reply email data
            $replyData = [
                'to_email' => $email->from_email,
                'to_name' => $email->from_name,
                'subject' => $this->formatReplySubject($email->subject),
                'body' => $this->formatReplyBody($email, $replyBody, $user),
                'attachments' => $attachments,
                'in_reply_to' => $email->email_id,
                'references' => $email->thread_id,
            ];

            // Send the email
            $sent = $this->sendEmail($replyData);

            if ($sent) {
                // Mark original email as replied
                $email->update([
                    'user_id' => $user->id,
                    'status' => 'replied',
                    'replied_at' => now(),
                ]);

                // Log the reply
                Log::info("Reply sent for email {$email->id} by user {$user->id}");
            }

            return $sent;

        } catch (\Exception $e) {
            Log::error("Error sending reply for email {$email->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a new email.
     */
    public function sendEmail(array $emailData): bool
    {
        try {
            // In production, you would use Laravel's Mail facade or external email service
            // For demo purposes, we'll simulate email sending
            $this->simulateEmailSending($emailData);

            Log::info("Email sent to: {$emailData['to_email']}");
            return true;

        } catch (\Exception $e) {
            Log::error("Error sending email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Determine if an email is GDPR-related.
     */
    protected function isGdprRelatedEmail(string $subject, string $body): bool
    {
        $gdprKeywords = [
            'gdpr', 'data protection', 'privacy', 'consent', 'data subject',
            'right to be forgotten', 'data portability', 'data breach',
            'personal data', 'processing', 'controller', 'processor',
            'data protection officer', 'dpo', 'artificial intelligence',
            'ai', 'machine learning', 'automated decision making'
        ];

        $content = strtolower($subject . ' ' . strip_tags($body));

        foreach ($gdprKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine email priority.
     */
    protected function determinePriority(string $subject, string $body): string
    {
        $urgentKeywords = ['urgent', 'immediate', 'asap', 'emergency', 'critical'];
        $highKeywords = ['important', 'priority', 'attention', 'deadline'];

        $content = strtolower($subject . ' ' . strip_tags($body));

        foreach ($urgentKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return 'urgent';
            }
        }

        foreach ($highKeywords as $keyword) {
            if (str_contains($content, $keyword)) {
                return 'high';
            }
        }

        return 'normal';
    }

    /**
     * Determine email category.
     */
    protected function determineCategory(string $subject, string $body): string
    {
        $content = strtolower($subject . ' ' . strip_tags($body));

        if (str_contains($content, 'complaint') || str_contains($content, 'grievance')) {
            return 'complaint';
        }

        if (str_contains($content, 'request') || str_contains($content, 'inquiry') || str_contains($content, 'question')) {
            return 'inquiry';
        }

        if (str_contains($content, 'notification') || str_contains($content, 'update') || str_contains($content, 'inform')) {
            return 'notification';
        }

        return 'general';
    }

    /**
     * Format reply subject.
     */
    protected function formatReplySubject(string $originalSubject): string
    {
        if (!str_starts_with(strtolower($originalSubject), 're:')) {
            return 'Re: ' . $originalSubject;
        }

        return $originalSubject;
    }

    /**
     * Format reply body with original email quoted.
     */
    protected function formatReplyBody(CompanyEmail $email, string $replyBody, User $user): string
    {
        $originalBody = strip_tags($email->body_plain ?: $email->body);

        return $replyBody . "\n\n" .
               "--- Original Message ---\n" .
               "From: {$email->from_name} <{$email->from_email}>\n" .
               "Date: {$email->received_at->format('Y-m-d H:i:s')}\n" .
               "Subject: {$email->subject}\n\n" .
               $originalBody;
    }

    /**
     * Simulate email fetching for demo purposes.
     */
    protected function simulateEmailFetch(Company $company, int $maxResults): array
    {
        $emails = [];

        for ($i = 0; $i < min($maxResults, 10); $i++) {
            $emails[] = [
                'email_id' => 'email_' . $company->id . '_' . time() . '_' . $i,
                'thread_id' => 'thread_' . $company->id . '_' . $i,
                'from_email' => 'sender' . $i . '@example.com',
                'from_name' => 'Sender ' . $i,
                'subject' => $this->generateSampleSubject($i),
                'body' => $this->generateSampleBody($i),
                'body_plain' => $this->generateSampleBody($i),
                'received_at' => now()->subHours(rand(1, 168)),
                'attachments' => rand(0, 3) > 0 ? [['name' => 'document.pdf', 'size' => rand(100, 5000)]] : null,
                'headers' => ['X-Mailer' => 'Sample Mailer'],
                'labels' => ['INBOX'],
            ];
        }

        return $emails;
    }

    /**
     * Generate sample email subjects.
     */
    protected function generateSampleSubject(int $index): string
    {
        $subjects = [
            'GDPR Compliance Inquiry',
            'Data Protection Request',
            'Privacy Policy Update',
            'Right to be Forgotten Request',
            'Data Breach Notification',
            'Consent Management Question',
            'Personal Data Processing Inquiry',
            'Data Subject Rights Request',
            'Privacy Impact Assessment',
            'Data Transfer Agreement'
        ];

        return $subjects[$index % count($subjects)];
    }

    /**
     * Generate sample email bodies.
     */
    protected function generateSampleBody(int $index): string
    {
        $bodies = [
            'I am writing to inquire about your GDPR compliance procedures and how you handle personal data processing.',
            'We have received a request from a data subject regarding their right to be forgotten. Please advise on the process.',
            'Could you please provide information about your data retention policies and procedures?',
            'We need to update our privacy policy and would like to understand your current data processing activities.',
            'There has been a potential data breach and we need to notify the relevant authorities. Please provide guidance.',
            'We are implementing new consent management procedures and would like to ensure compliance with GDPR requirements.',
            'A customer has requested access to their personal data. What is the process for handling such requests?',
            'We are considering using artificial intelligence for data processing. What are the GDPR implications?',
            'Please provide information about your data protection officer and their contact details.',
            'We need to transfer data to a third country. What safeguards should we implement?'
        ];

        return $bodies[$index % count($bodies)];
    }

    /**
     * Simulate email sending for demo purposes.
     */
    protected function simulateEmailSending(array $emailData): void
    {
        // In production, this would use Laravel Mail or external service
        Log::info("Simulating email send to: {$emailData['to_email']}");

        // Simulate processing time
        usleep(100000); // 0.1 seconds
    }

    /**
     * Get email statistics for a company.
     */
    public function getEmailStats(Company $company): array
    {
        $emails = $company->emails();

        return [
            'total' => $emails->count(),
            'unread' => $emails->where('status', 'unread')->count(),
            'read' => $emails->where('status', 'read')->count(),
            'replied' => $emails->where('status', 'replied')->count(),
            'gdpr_related' => $emails->where('is_gdpr_related', true)->count(),
            'high_priority' => $emails->where('priority', 'high')->count(),
            'urgent_priority' => $emails->where('priority', 'urgent')->count(),
        ];
    }

    /**
     * Test email connection for a company with a specific provider.
     */
    public function testConnection(Company $company, $provider, array $credentials): array
    {
        try {
            if ($provider->usesOAuth()) {
                return $this->testOAuthConnection($company, $provider, $credentials);
            } else {
                return $this->testImapConnection($company, $provider, $credentials);
            }
        } catch (\Exception $e) {
            Log::error("Connection test failed for company {$company->id}: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Test IMAP connection.
     */
    protected function testImapConnection(Company $company, $provider, array $credentials): array
    {
        try {
            $host = $provider->name === 'custom'
                ? ($credentials['custom_settings']['imap_host'] ?? '')
                : $provider->imap_host;

            $port = $provider->name === 'custom'
                ? ($credentials['custom_settings']['imap_port'] ?? 993)
                : $provider->imap_port;

            $encryption = $provider->name === 'custom'
                ? ($credentials['custom_settings']['imap_encryption'] ?? 'ssl')
                : $provider->imap_encryption;

            $username = $credentials['username'];
            $password = $credentials['password'] ?? '';

            if (empty($host) || empty($port) || empty($username) || empty($password)) {
                return ['success' => false, 'error' => 'Missing required connection parameters'];
            }

            // Build connection string
            $connectionString = "{{$host}:{$port}";
            if ($encryption) {
                $connectionString .= "/{$encryption}";
            }
            $connectionString .= "}INBOX";

            // Test connection using PHP's imap functions
            $connection = @imap_open($connectionString, $username, $password, 0, 1);

            if ($connection === false) {
                $errors = imap_errors();
                $errorMessage = $errors ? implode(', ', $errors) : 'Unknown connection error';
                return ['success' => false, 'error' => $errorMessage];
            }

            // Test basic operations
            $mailboxInfo = imap_mailboxmsginfo($connection);
            $messageCount = $mailboxInfo->Nmsgs ?? 0;

            imap_close($connection);

            return [
                'success' => true,
                'message' => "Connection successful. Found {$messageCount} messages in inbox.",
                'message_count' => $messageCount
            ];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Test OAuth connection.
     */
    protected function testOAuthConnection(Company $company, $provider, array $credentials): array
    {
        try {
            $oauthToken = $credentials['oauth_token'] ?? '';

            if (empty($oauthToken)) {
                return ['success' => false, 'error' => 'OAuth token is required'];
            }

            // Test OAuth token validity by making a test API call
            if ($provider->name === 'gmail') {
                return $this->testGmailOAuthConnection($oauthToken);
            } elseif ($provider->name === 'microsoft') {
                return $this->testMicrosoftOAuthConnection($oauthToken);
            } else {
                return ['success' => false, 'error' => 'OAuth not supported for this provider'];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Test Gmail OAuth connection.
     */
    protected function testGmailOAuthConnection(string $accessToken): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Accept' => 'application/json',
            ])->get('https://gmail.googleapis.com/gmail/v1/users/me/profile');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => "Gmail OAuth connection successful. Email: {$data['emailAddress']}",
                    'email' => $data['emailAddress']
                ];
            } else {
                return ['success' => false, 'error' => 'Gmail API request failed: ' . $response->body()];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Gmail OAuth test failed: ' . $e->getMessage()];
        }
    }

    /**
     * Test Microsoft OAuth connection.
     */
    protected function testMicrosoftOAuthConnection(string $accessToken): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Accept' => 'application/json',
            ])->get('https://graph.microsoft.com/v1.0/me');

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => "Microsoft OAuth connection successful. Email: {$data['mail']}",
                    'email' => $data['mail']
                ];
            } else {
                return ['success' => false, 'error' => 'Microsoft Graph API request failed: ' . $response->body()];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Microsoft OAuth test failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get OAuth authorization URL for a provider.
     */
    public function getOAuthUrl($provider, Company $company): string
    {
        if ($provider->name === 'gmail') {
            return $this->getGmailOAuthUrl($provider, $company);
        } elseif ($provider->name === 'microsoft') {
            return $this->getMicrosoftOAuthUrl($provider, $company);
        } else {
            throw new \Exception('OAuth not supported for this provider');
        }
    }

    /**
     * Get Gmail OAuth URL.
     */
    protected function getGmailOAuthUrl($provider, Company $company): string
    {
        $clientId = $provider->oauth_client_id;
        $redirectUri = $provider->oauth_redirect_uri;
        $scopes = implode(' ', $provider->oauth_scopes ?? []);
        $state = base64_encode(json_encode(['company_id' => $company->id, 'provider_id' => $provider->id]));

        return "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);
    }

    /**
     * Get Microsoft OAuth URL.
     */
    protected function getMicrosoftOAuthUrl($provider, Company $company): string
    {
        $clientId = $provider->oauth_client_id;
        $redirectUri = $provider->oauth_redirect_uri;
        $scopes = implode(' ', $provider->oauth_scopes ?? []);
        $state = base64_encode(json_encode(['company_id' => $company->id, 'provider_id' => $provider->id]));

        return "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'scope' => $scopes,
            'response_type' => 'code',
            'state' => $state,
        ]);
    }

    /**
     * Handle OAuth callback.
     */
    public function handleOAuthCallback(string $code, string $state, Company $company): array
    {
        try {
            $stateData = json_decode(base64_decode($state), true);

            if (!$stateData || $stateData['company_id'] != $company->id) {
                return ['success' => false, 'error' => 'Invalid state parameter'];
            }

            $provider = \App\Models\EmailProvider::find($stateData['provider_id']);
            if (!$provider) {
                return ['success' => false, 'error' => 'Provider not found'];
            }

            if ($provider->name === 'gmail') {
                return $this->handleGmailOAuthCallback($code, $provider, $company);
            } elseif ($provider->name === 'microsoft') {
                return $this->handleMicrosoftOAuthCallback($code, $provider, $company);
            } else {
                return ['success' => false, 'error' => 'OAuth not supported for this provider'];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Handle Gmail OAuth callback.
     */
    protected function handleGmailOAuthCallback(string $code, $provider, Company $company): array
    {
        try {
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $provider->oauth_client_id,
                'client_secret' => $provider->oauth_client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $provider->oauth_redirect_uri,
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                // Store tokens in session for the form
                session([
                    'oauth_tokens' => [
                        'access_token' => $tokenData['access_token'],
                        'refresh_token' => $tokenData['refresh_token'] ?? null,
                        'expires_in' => $tokenData['expires_in'] ?? null,
                    ]
                ]);

                return ['success' => true, 'message' => 'OAuth authentication successful'];
            } else {
                return ['success' => false, 'error' => 'Failed to exchange code for token: ' . $response->body()];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Gmail OAuth callback failed: ' . $e->getMessage()];
        }
    }

    /**
     * Handle Microsoft OAuth callback.
     */
    protected function handleMicrosoftOAuthCallback(string $code, $provider, Company $company): array
    {
        try {
            $response = Http::post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'client_id' => $provider->oauth_client_id,
                'client_secret' => $provider->oauth_client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $provider->oauth_redirect_uri,
            ]);

            if ($response->successful()) {
                $tokenData = $response->json();

                // Store tokens in session for the form
                session([
                    'oauth_tokens' => [
                        'access_token' => $tokenData['access_token'],
                        'refresh_token' => $tokenData['refresh_token'] ?? null,
                        'expires_in' => $tokenData['expires_in'] ?? null,
                    ]
                ]);

                return ['success' => true, 'message' => 'OAuth authentication successful'];
            } else {
                return ['success' => false, 'error' => 'Failed to exchange code for token: ' . $response->body()];
            }

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Microsoft OAuth callback failed: ' . $e->getMessage()];
        }
    }
}
