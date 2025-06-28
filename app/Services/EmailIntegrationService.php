<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\EmailDocument;
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
        if (!$company->hasEmailConfigured()) {
            Log::warning("Email not configured for company: {$company->id}");
            return ['success' => false, 'error' => 'Email is not configured for this company.'];
        }

        try {
            // Get the email provider and credentials
            $provider = $company->emailProvider;
            $credentials = $company->getEmailCredentials();

            if (!$provider || !$credentials) {
                Log::warning("Email provider or credentials not found for company: {$company->id}");
                return ['success' => false, 'error' => 'Email provider or credentials not configured.'];
            }

            // Fetch real emails from IMAP server
            $emails = $this->fetchEmailsFromImap($company, $provider, $credentials, $maxResults);

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

                // Save the actual attachment data to storage
                if (isset($attachment['data'])) {
                    Storage::put($storagePath, $attachment['data']);
                } else {
                    // Fallback: create a placeholder if no data is available
                    Log::warning("No attachment data available for: " . $attachment['name']);
                    Storage::put($storagePath, 'Attachment data not available for: ' . $attachment['name']);
                }

                // Create EmailDocument record
                \App\Models\EmailDocument::create([
                    'company_email_id' => $email->id,
                    'filename' => $filename,
                    'original_name' => $attachment['name'],
                    'mime_type' => $attachment['mime_type'] ?? 'application/octet-stream',
                    'size' => $attachment['size'] ?? 0,
                    'storage_path' => $storagePath,
                ]);

                Log::info("Successfully processed attachment: {$attachment['name']} for email {$email->id}");

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
            $hasAttachments = rand(0, 3) > 0;
            $attachments = null;

            if ($hasAttachments) {
                $attachmentCount = rand(1, 3);
                $attachments = [];

                for ($j = 0; $j < $attachmentCount; $j++) {
                    $attachmentTypes = [
                        ['name' => 'document.pdf', 'mime_type' => 'application/pdf', 'size' => rand(50000, 500000)],
                        ['name' => 'contract.docx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'size' => rand(30000, 200000)],
                        ['name' => 'data_request.xlsx', 'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'size' => rand(20000, 150000)],
                        ['name' => 'consent_form.pdf', 'mime_type' => 'application/pdf', 'size' => rand(40000, 300000)],
                        ['name' => 'privacy_policy.html', 'mime_type' => 'text/html', 'size' => rand(5000, 50000)],
                        ['name' => 'gdpr_compliance.txt', 'mime_type' => 'text/plain', 'size' => rand(1000, 10000)],
                    ];

                    $attachments[] = $attachmentTypes[array_rand($attachmentTypes)];
                }
            }

            $emails[] = [
                'email_id' => 'email_' . $company->id . '_' . time() . '_' . $i,
                'thread_id' => 'thread_' . $company->id . '_' . $i,
                'from_email' => 'sender' . $i . '@example.com',
                'from_name' => 'Sender ' . $i,
                'subject' => $this->generateSampleSubject($i),
                'body' => $this->generateSampleBody($i),
                'body_plain' => $this->generateSampleBody($i),
                'received_at' => now()->subHours(rand(1, 168)),
                'attachments' => $attachments,
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
        try {
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
        } catch (\Exception $e) {
            Log::error("Error getting email stats for company {$company->id}: " . $e->getMessage());

            // Fallback to direct query
            return [
                'total' => CompanyEmail::where('company_id', $company->id)->count(),
                'unread' => CompanyEmail::where('company_id', $company->id)->where('status', 'unread')->count(),
                'read' => CompanyEmail::where('company_id', $company->id)->where('status', 'read')->count(),
                'replied' => CompanyEmail::where('company_id', $company->id)->where('status', 'replied')->count(),
                'gdpr_related' => CompanyEmail::where('company_id', $company->id)->where('is_gdpr_related', true)->count(),
                'high_priority' => CompanyEmail::where('company_id', $company->id)->where('priority', 'high')->count(),
                'urgent_priority' => CompanyEmail::where('company_id', $company->id)->where('priority', 'urgent')->count(),
            ];
        }
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

            // Check if IMAP extension is available
            if (!function_exists('imap_open')) {
                return ['success' => false, 'error' => 'IMAP extension is not available. Please install php-imap extension.'];
            }

            if (!function_exists('imap_errors') || !function_exists('imap_mailboxmsginfo') || !function_exists('imap_close')) {
                return ['success' => false, 'error' => 'Required IMAP functions are not available. Please check php-imap extension installation.'];
            }

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

    /**
     * Fetch real emails from IMAP server.
     */
    protected function fetchEmailsFromImap(Company $company, $provider, array $credentials, int $maxResults): array
    {
        try {
            $host = $provider->imap_host;
            $port = $provider->imap_port;
            $encryption = $provider->imap_encryption;
            $username = $credentials['username'];
            $password = $credentials['password'] ?? '';

            if (empty($host) || empty($port) || empty($username) || empty($password)) {
                throw new \Exception('Missing required IMAP connection parameters');
            }

            // Build connection string
            $connectionString = "{{$host}:{$port}";
            if ($encryption) {
                $connectionString .= "/{$encryption}";
            }
            $connectionString .= "}INBOX";

            // Check if IMAP extension is available
            if (!function_exists('imap_open')) {
                throw new \Exception('IMAP extension is not available');
            }

            // Connect to IMAP server
            $connection = @imap_open($connectionString, $username, $password, 0, 1);

            if ($connection === false) {
                $errors = imap_errors();
                $errorMessage = $errors ? implode(', ', $errors) : 'Unknown connection error';
                throw new \Exception('IMAP connection failed: ' . $errorMessage);
            }

            // Get mailbox info
            $mailboxInfo = imap_mailboxmsginfo($connection);
            $totalMessages = $mailboxInfo->Nmsgs ?? 0;

            Log::info("Found {$totalMessages} messages in mailbox for company {$company->id}");

            $emails = [];
            $processedCount = 0;

            // Fetch emails (start from newest)
            for ($i = $totalMessages; $i > max(1, $totalMessages - $maxResults); $i--) {
                try {
                    $emailData = $this->fetchEmailFromImap($connection, $i, $company);
                    if ($emailData) {
                        $emails[] = $emailData;
                        $processedCount++;
                    }
                } catch (\Exception $e) {
                    Log::warning("Error fetching email {$i} for company {$company->id}: " . $e->getMessage());
                    continue;
                }
            }

            imap_close($connection);

            Log::info("Successfully fetched {$processedCount} emails for company {$company->id}");
            return $emails;

        } catch (\Exception $e) {
            Log::error("Error fetching emails from IMAP for company {$company->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch a single email from IMAP connection.
     */
    protected function fetchEmailFromImap($connection, int $messageNumber, Company $company): ?array
    {
        try {
            // Get email headers
            $headers = imap_headerinfo($connection, $messageNumber);
            if (!$headers) {
                return null;
            }

            // Get email body
            $body = imap_body($connection, $messageNumber);
            if ($body === false) {
                return null;
            }

            // Parse email structure
            $structure = imap_fetchstructure($connection, $messageNumber);

            // Extract text and HTML parts
            $textBody = '';
            $htmlBody = '';
            $attachments = [];

            if ($structure) {
                $this->parseEmailStructure($connection, $messageNumber, $structure, $textBody, $htmlBody, $attachments);

                // Log attachment count
                if (!empty($attachments)) {
                    Log::info("Email {$messageNumber} has " . count($attachments) . " attachments");
                }
            }

            // Use HTML body if available, otherwise use text body
            $body = !empty($htmlBody) ? $htmlBody : $textBody;

            // Generate unique email ID
            $emailId = 'imap_' . $company->id . '_' . $messageNumber . '_' . time();

            // Extract email data
            $emailData = [
                'email_id' => $emailId,
                'thread_id' => $headers->message_id ?? null,
                'from_email' => $headers->from[0]->mailbox . '@' . $headers->from[0]->host ?? '',
                'from_name' => $headers->from[0]->personal ?? null,
                'subject' => $headers->subject ?? 'No Subject',
                'body' => $body,
                'body_plain' => $textBody,
                'received_at' => $headers->date ? date('Y-m-d H:i:s', strtotime($headers->date)) : now(),
                'attachments' => $attachments,
                'headers' => [
                    'message_id' => $headers->message_id ?? null,
                    'in_reply_to' => $headers->in_reply_to ?? null,
                    'references' => $headers->references ?? null,
                    'date' => $headers->date ?? null,
                ],
                'labels' => ['INBOX'],
            ];

            return $emailData;

        } catch (\Exception $e) {
            Log::warning("Error parsing email {$messageNumber}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse email structure to extract text, HTML, and attachments.
     */
    protected function parseEmailStructure($connection, int $messageNumber, $structure, &$textBody, &$htmlBody, &$attachments, $partNumber = ''): void
    {
        if ($structure->type == 0) {
            // Text part
            $data = imap_fetchbody($connection, $messageNumber, $partNumber ?: 1);

            if (strtolower($structure->subtype) == 'html') {
                $htmlBody = $this->decodeEmailBody($data, $structure);
            } else {
                $textBody = $this->decodeEmailBody($data, $structure);
            }
        } elseif ($structure->type == 1) {
            // Multipart
            if (isset($structure->parts)) {
                foreach ($structure->parts as $index => $part) {
                    $newPartNumber = $partNumber ? $partNumber . '.' . ($index + 1) : ($index + 1);
                    $this->parseEmailStructure($connection, $messageNumber, $part, $textBody, $htmlBody, $attachments, $newPartNumber);
                }
            }
        } elseif ($structure->type == 2) {
            // Attachments (application, image, audio, video, etc.)
            $data = imap_fetchbody($connection, $messageNumber, $partNumber ?: 1);
            $filename = $this->getAttachmentFilename($structure);

            if ($filename) {
                $decodedData = $this->decodeEmailBody($data, $structure);
                $attachments[] = [
                    'name' => $filename,
                    'mime_type' => $structure->subtype ?? 'application/octet-stream',
                    'size' => strlen($decodedData),
                    'data' => $decodedData,
                ];

                Log::info("Found attachment: {$filename} (size: " . strlen($decodedData) . " bytes)");
            }
        } elseif ($structure->type == 3) {
            // Application (treat as attachment)
            $data = imap_fetchbody($connection, $messageNumber, $partNumber ?: 1);
            $filename = $this->getAttachmentFilename($structure);

            if ($filename) {
                $decodedData = $this->decodeEmailBody($data, $structure);
                $attachments[] = [
                    'name' => $filename,
                    'mime_type' => $structure->subtype ?? 'application/octet-stream',
                    'size' => strlen($decodedData),
                    'data' => $decodedData,
                ];

                Log::info("Found application attachment: {$filename} (size: " . strlen($decodedData) . " bytes)");
            }
        } elseif ($structure->type == 4) {
            // Audio (treat as attachment)
            $data = imap_fetchbody($connection, $messageNumber, $partNumber ?: 1);
            $filename = $this->getAttachmentFilename($structure);

            if ($filename) {
                $decodedData = $this->decodeEmailBody($data, $structure);
                $attachments[] = [
                    'name' => $filename,
                    'mime_type' => $structure->subtype ?? 'audio/octet-stream',
                    'size' => strlen($decodedData),
                    'data' => $decodedData,
                ];

                Log::info("Found audio attachment: {$filename} (size: " . strlen($decodedData) . " bytes)");
            }
        } elseif ($structure->type == 5) {
            // Image (treat as attachment)
            $data = imap_fetchbody($connection, $messageNumber, $partNumber ?: 1);
            $filename = $this->getAttachmentFilename($structure);

            if ($filename) {
                $decodedData = $this->decodeEmailBody($data, $structure);
                $attachments[] = [
                    'name' => $filename,
                    'mime_type' => $structure->subtype ?? 'image/octet-stream',
                    'size' => strlen($decodedData),
                    'data' => $decodedData,
                ];

                Log::info("Found image attachment: {$filename} (size: " . strlen($decodedData) . " bytes)");
            }
        } elseif ($structure->type == 6) {
            // Video (treat as attachment)
            $data = imap_fetchbody($connection, $messageNumber, $partNumber ?: 1);
            $filename = $this->getAttachmentFilename($structure);

            if ($filename) {
                $decodedData = $this->decodeEmailBody($data, $structure);
                $attachments[] = [
                    'name' => $filename,
                    'mime_type' => $structure->subtype ?? 'video/octet-stream',
                    'size' => strlen($decodedData),
                    'data' => $decodedData,
                ];

                Log::info("Found video attachment: {$filename} (size: " . strlen($decodedData) . " bytes)");
            }
        } elseif ($structure->type == 7) {
            // Other (treat as attachment)
            $data = imap_fetchbody($connection, $messageNumber, $partNumber ?: 1);
            $filename = $this->getAttachmentFilename($structure);

            if ($filename) {
                $decodedData = $this->decodeEmailBody($data, $structure);
                $attachments[] = [
                    'name' => $filename,
                    'mime_type' => $structure->subtype ?? 'application/octet-stream',
                    'size' => strlen($decodedData),
                    'data' => $decodedData,
                ];

                Log::info("Found other attachment: {$filename} (size: " . strlen($decodedData) . " bytes)");
            }
        }
    }

    /**
     * Decode email body based on encoding.
     */
    protected function decodeEmailBody(string $data, $structure): string
    {
        $encoding = $structure->encoding ?? 0;

        switch ($encoding) {
            case 3: // BASE64
                return base64_decode($data);
            case 4: // QUOTED-PRINTABLE
                return quoted_printable_decode($data);
            default:
                return $data;
        }
    }

    /**
     * Get attachment filename from structure.
     */
    protected function getAttachmentFilename($structure): ?string
    {
        // Check dparameters first (disposition parameters)
        if (isset($structure->dparameters)) {
            foreach ($structure->dparameters as $param) {
                if (strtolower($param->attribute) == 'filename') {
                    return $this->decodeFilename($param->value);
                }
            }
        }

        // Check parameters (content-type parameters)
        if (isset($structure->parameters)) {
            foreach ($structure->parameters as $param) {
                if (strtolower($param->attribute) == 'name') {
                    return $this->decodeFilename($param->value);
                }
            }
        }

        // Check if there's a name property directly on the structure
        if (isset($structure->name)) {
            return $this->decodeFilename($structure->name);
        }

        // Generate a fallback filename based on MIME type
        if (isset($structure->subtype)) {
            $extension = $this->getExtensionFromMimeType($structure->subtype);
            return 'attachment_' . uniqid() . '.' . $extension;
        }

        return null;
    }

    /**
     * Decode filename that might be encoded (RFC 2231, RFC 2047, etc.)
     */
    protected function decodeFilename(string $filename): string
    {
        // Handle RFC 2231 encoding
        if (preg_match('/^([^=]+)\*[0-9]*\*?=(.+)$/', $filename, $matches)) {
            $filename = $matches[2];
        }

        // Handle quoted-printable encoding
        if (strpos($filename, '=?') !== false) {
            $filename = mb_decode_mimeheader($filename);
        }

        // Handle URL encoding
        if (strpos($filename, '%') !== false) {
            $filename = urldecode($filename);
        }

        // Remove quotes if present
        $filename = trim($filename, '"\'');

        // Clean up the filename
        $filename = preg_replace('/[^\w\-\.]/', '_', $filename);

        return $filename;
    }

    /**
     * Get file extension from MIME type.
     */
    protected function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeToExt = [
            'pdf' => 'pdf',
            'msword' => 'doc',
            'vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'vnd.ms-excel' => 'xls',
            'vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'vnd.ms-powerpoint' => 'ppt',
            'vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'jpeg' => 'jpg',
            'png' => 'png',
            'gif' => 'gif',
            'bmp' => 'bmp',
            'svg+xml' => 'svg',
            'mpeg' => 'mpg',
            'mp4' => 'mp4',
            'avi' => 'avi',
            'wav' => 'wav',
            'mp3' => 'mp3',
            'zip' => 'zip',
            'rar' => 'rar',
            '7z' => '7z',
            'tar' => 'tar',
            'gz' => 'gz',
            'txt' => 'txt',
            'html' => 'html',
            'xml' => 'xml',
            'json' => 'json',
            'csv' => 'csv',
        ];

        return $mimeToExt[$mimeType] ?? 'bin';
    }

    /**
     * Check if IMAP extension is available and properly configured.
     */
    public function checkImapConfiguration(): array
    {
        $issues = [];

        // Check if IMAP extension is loaded
        if (!extension_loaded('imap')) {
            $issues[] = 'IMAP extension is not loaded. Please install php-imap extension.';
        }

        // Check if imap functions are available
        if (!function_exists('imap_open')) {
            $issues[] = 'IMAP functions are not available. Please check PHP configuration.';
        }

        // Check if mbstring extension is loaded (needed for filename decoding)
        if (!extension_loaded('mbstring')) {
            $issues[] = 'MBString extension is not loaded. This may affect attachment filename decoding.';
        }

        return [
            'available' => empty($issues),
            'issues' => $issues
        ];
    }

    /**
     * Get attachment processing statistics.
     */
    public function getAttachmentStats(Company $company): array
    {
        $totalEmails = CompanyEmail::where('company_id', $company->id)->count();
        $emailsWithAttachments = CompanyEmail::where('company_id', $company->id)
            ->whereNotNull('attachments')
            ->where('attachments', '!=', '[]')
            ->count();

        $totalAttachments = EmailDocument::whereHas('companyEmail', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->count();

        return [
            'total_emails' => $totalEmails,
            'emails_with_attachments' => $emailsWithAttachments,
            'total_attachments' => $totalAttachments,
            'attachment_rate' => $totalEmails > 0 ? round(($emailsWithAttachments / $totalEmails) * 100, 2) : 0,
        ];
    }
}
