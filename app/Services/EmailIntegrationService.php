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
     * Fetch emails for a company's data protection officer.
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
            $skippedCount = 0;

            foreach ($emails as $emailData) {
                if ($this->processEmail($company, $emailData)) {
                    $processedCount++;
                } else {
                    $skippedCount++;
                }
            }

            // Update sync status
            $company->updateEmailSyncStatus(true);

            Log::info("Email fetch completed for company {$company->id}: {$processedCount} processed, {$skippedCount} skipped");
            return [
                'success' => true,
                'processed' => $processedCount,
                'skipped' => $skippedCount,
                'total' => count($emails)
            ];

        } catch (\Exception $e) {
            // Update sync status with error
            $company->updateEmailSyncStatus(false, $e->getMessage());

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
            // Check if email already exists by email_id (primary check)
            $existingEmail = CompanyEmail::where('email_id', $emailData['email_id'])
                ->where('company_id', $company->id)
                ->first();

            if ($existingEmail) {
                Log::info("Email already exists with email_id: {$emailData['email_id']}");
                return false; // Email already processed
            }

            // Additional duplicate check using message_id, from_email, subject, and received_at
            if (!empty($emailData['headers']['message_id'])) {
                $duplicateCheck = CompanyEmail::where('company_id', $company->id)
                    ->where('from_email', $emailData['from_email'])
                    ->where('subject', $emailData['subject'])
                    ->where('received_at', $emailData['received_at'])
                    ->whereJsonContains('headers->message_id', $emailData['headers']['message_id'])
                    ->first();

                if ($duplicateCheck) {
                    Log::info("Duplicate email detected by message_id: {$emailData['headers']['message_id']}");
                    return false; // Email already processed
                }
            }

            // Determine if email is GDPR-related
            // TODO: Implement GDPR detection logic if needed
            $isGdprRelated = false; // Default to false for now

            // Quick fix: set default values for priority and category
            $priority = 'normal'; // Default to normal priority
            $category = 'general'; // Default to general category

            // Create email record
            $email = CompanyEmail::create([
                'company_id' => $company->id,
                'email_id' => $emailData['email_id'],
                'thread_id' => $emailData['thread_id'] ?? null,
                'from_email' => $emailData['from_email'],
                'from_name' => $emailData['from_name'] ?? null,
                'to_email' => $company->data_protection_officer,
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

            Log::info("Successfully processed new email: {$emailData['email_id']} from {$emailData['from_email']}");
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
    public function sendEmail(array $emailData, ?Company $company = null): bool
    {
        try {
            // If no company provided, try to get from authenticated user
            if (!$company) {
                $user = auth()->user();
                $company = $user ? $user->company : null;
            }

            // If company has email configured, use it
            if ($company && $company->hasEmailConfigured()) {
                // Get the email provider and credentials
                $provider = $company->emailProvider;
                $credentials = $company->getEmailCredentials();

                if ($provider && $credentials) {
                    // Send email based on provider type
                    if ($provider->supportsSmtp()) {
                        return $this->sendEmailViaSmtp($emailData, $company, $provider, $credentials);
                    } elseif ($provider->supportsApi()) {
                        return $this->sendEmailViaApi($emailData, $company, $provider, $credentials);
                    } else {
                        Log::warning("No supported sending method for provider: {$provider->name}, falling back to default mail");
                    }
                } else {
                    Log::warning("Email provider or credentials not found for company: {$company->id}, falling back to default mail");
                }
            } else {
                Log::info("Email not configured for company: " . ($company ? $company->id : 'no company') . ", using default mail configuration");
            }

            // Fallback to Laravel's default mail configuration
            return $this->sendEmailViaDefaultMail($emailData, $company);

        } catch (\Exception $e) {
            Log::error("Error sending email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email using Laravel's default mail configuration.
     */
    protected function sendEmailViaDefaultMail(array $emailData, ?Company $company): bool
    {
        try {
            // Use Laravel's default mail configuration
            Mail::raw($emailData['body'], function ($message) use ($emailData, $company) {
                $message->to($emailData['to_email'], $emailData['to_name'] ?? null)
                        ->subject($emailData['subject']);

                // Set from address
                if ($company && $company->data_protection_officer) {
                    $message->from($company->data_protection_officer, $company->name);

                    // BCC to sender's own email to appear in webmail (if enabled)
                    if ($company->isBccToSelfEnabled()) {
                        $message->bcc($company->data_protection_officer, $company->name);
                    }
                } else {
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                }

                // Add attachments if any
                if (!empty($emailData['attachments'])) {
                    foreach ($emailData['attachments'] as $attachment) {
                        if (isset($attachment['path']) && Storage::exists($attachment['path'])) {
                            $message->attach(Storage::path($attachment['path']), [
                                'as' => $attachment['name'],
                                'mime' => $attachment['mime_type'] ?? 'application/octet-stream',
                            ]);
                        }
                    }
                }
            });

            Log::info("Email sent via default mail configuration to: {$emailData['to_email']}");
            return true;

        } catch (\Exception $e) {
            Log::error("Default mail sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email via SMTP.
     */
    protected function sendEmailViaSmtp(array $emailData, Company $company, $provider, array $credentials): bool
    {
        try {
            // Get SMTP configuration
            $smtpConfig = $provider->getSmtpConfig();

            // Use custom settings if provider is custom
            if ($provider->name === 'custom') {
                $customSettings = $credentials['custom_settings'] ?? [];
                $smtpConfig = [
                    'host' => $customSettings['smtp_host'] ?? $smtpConfig['host'],
                    'port' => $customSettings['smtp_port'] ?? $smtpConfig['port'],
                    'encryption' => $customSettings['smtp_encryption'] ?? $smtpConfig['encryption'],
                    'auth_required' => $smtpConfig['auth_required'],
                    'timeout' => $smtpConfig['timeout'],
                    'verify_ssl' => $smtpConfig['verify_ssl'],
                ];
            }

            // Configure Laravel Mail with SMTP settings (using correct format)
            $config = [
                'transport' => 'smtp',
                'host' => $smtpConfig['host'],
                'port' => $smtpConfig['port'],
                'encryption' => $smtpConfig['encryption'],
                'username' => $credentials['username'],
                'password' => $credentials['password'] ?? '',
                'timeout' => $smtpConfig['timeout'],
                'local_domain' => parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST),
            ];

            // Create a temporary mail configuration
            $tempConfig = config('mail');
            $tempConfig['mailers']['temp_smtp'] = $config;
            $tempConfig['default'] = 'temp_smtp';

            // Set the temporary configuration
            config(['mail' => $tempConfig]);

            // Send the email using Laravel Mail
            Mail::raw($emailData['body'], function ($message) use ($emailData, $company) {
                $message->to($emailData['to_email'], $emailData['to_name'] ?? null)
                        ->subject($emailData['subject'])
                        ->from($company->data_protection_officer, $company->name);

                // BCC to sender's own email to appear in webmail (if enabled)
                if ($company->isBccToSelfEnabled()) {
                    $message->bcc($company->data_protection_officer, $company->name);
                }

                // Add attachments if any
                if (!empty($emailData['attachments'])) {
                    foreach ($emailData['attachments'] as $attachment) {
                        if (isset($attachment['path']) && Storage::exists($attachment['path'])) {
                            $message->attach(Storage::path($attachment['path']), [
                                'as' => $attachment['name'],
                                'mime' => $attachment['mime_type'] ?? 'application/octet-stream',
                            ]);
                        }
                    }
                }
            });

            Log::info("Email sent via SMTP to: {$emailData['to_email']} from company {$company->id}");
            return true;

        } catch (\Exception $e) {
            Log::error("SMTP email sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email via API (Gmail, Microsoft Graph, etc.).
     */
    protected function sendEmailViaApi(array $emailData, Company $company, $provider, array $credentials): bool
    {
        try {
            if ($provider->name === 'gmail') {
                return $this->sendEmailViaGmailApi($emailData, $company, $credentials);
            } elseif ($provider->name === 'microsoft') {
                return $this->sendEmailViaMicrosoftApi($emailData, $company, $credentials);
            } else {
                Log::error("API sending not supported for provider: {$provider->name}");
                return false;
            }
        } catch (\Exception $e) {
            Log::error("API email sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email via Gmail API.
     */
    protected function sendEmailViaGmailApi(array $emailData, Company $company, array $credentials): bool
    {
        try {
            $accessToken = $credentials['oauth_token'] ?? '';

            if (empty($accessToken)) {
                Log::error("Gmail OAuth token not found for company: {$company->id}");
                return false;
            }

            // Create email message
            $message = $this->createGmailMessage($emailData, $company);

            // Encode the message
            $encodedMessage = base64_encode($message);
            $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);

            // Send via Gmail API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->post('https://gmail.googleapis.com/gmail/v1/users/me/messages/send', [
                'raw' => $encodedMessage
            ]);

            if ($response->successful()) {
                Log::info("Email sent via Gmail API to: {$emailData['to_email']} from company {$company->id}");
                return true;
            } else {
                Log::error("Gmail API error: " . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Gmail API sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email via Microsoft Graph API.
     */
    protected function sendEmailViaMicrosoftApi(array $emailData, Company $company, array $credentials): bool
    {
        try {
            $accessToken = $credentials['oauth_token'] ?? '';

            if (empty($accessToken)) {
                Log::error("Microsoft OAuth token not found for company: {$company->id}");
                return false;
            }

            // Prepare email data for Microsoft Graph
            $emailPayload = [
                'message' => [
                    'subject' => $emailData['subject'],
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $emailData['body']
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $emailData['to_email'],
                                'name' => $emailData['to_name'] ?? null
                            ]
                        ]
                    ]
                ],
                'saveToSentItems' => true
            ];

            // Add BCC if enabled
            if ($company->isBccToSelfEnabled()) {
                $emailPayload['message']['bccRecipients'] = [
                    [
                        'emailAddress' => [
                            'address' => $company->data_protection_officer,
                            'name' => $company->name
                        ]
                    ]
                ];
            }

            // Add attachments if any
            if (!empty($emailData['attachments'])) {
                $emailPayload['message']['attachments'] = [];
                foreach ($emailData['attachments'] as $attachment) {
                    if (isset($attachment['path']) && Storage::exists($attachment['path'])) {
                        $fileContent = base64_encode(Storage::get($attachment['path']));
                        $emailPayload['message']['attachments'][] = [
                            '@odata.type' => '#microsoft.graph.fileAttachment',
                            'name' => $attachment['name'],
                            'contentType' => $attachment['mime_type'] ?? 'application/octet-stream',
                            'contentBytes' => $fileContent
                        ];
                    }
                }
            }

            // Send via Microsoft Graph API
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ])->post('https://graph.microsoft.com/v1.0/me/sendMail', $emailPayload);

            if ($response->successful()) {
                Log::info("Email sent via Microsoft Graph API to: {$emailData['to_email']} from company {$company->id}");
                return true;
            } else {
                Log::error("Microsoft Graph API error: " . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            Log::error("Microsoft Graph API sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create Gmail message format.
     */
    protected function createGmailMessage(array $emailData, Company $company): string
    {
        $boundary = uniqid();

        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: multipart/mixed; boundary="' . $boundary . '"',
            'From: ' . $company->name . ' <' . $company->data_protection_officer . '>',
            'To: ' . ($emailData['to_name'] ? $emailData['to_name'] . ' <' . $emailData['to_email'] . '>' : $emailData['to_email']),
            'Subject: ' . $emailData['subject'],
            'Date: ' . date('r'),
        ];

        // Add BCC if enabled
        if ($company->isBccToSelfEnabled()) {
            $headers[] = 'Bcc: ' . $company->data_protection_officer; // BCC to sender for webmail visibility
        }

        $message = implode("\r\n", $headers) . "\r\n\r\n";

        // Add text body
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $emailData['body'] . "\r\n\r\n";

        // Add attachments if any
        if (!empty($emailData['attachments'])) {
            foreach ($emailData['attachments'] as $attachment) {
                if (isset($attachment['path']) && Storage::exists($attachment['path'])) {
                    $fileContent = base64_encode(Storage::get($attachment['path']));
                    $message .= "--{$boundary}\r\n";
                    $message .= "Content-Type: " . ($attachment['mime_type'] ?? 'application/octet-stream') . "; name=\"" . $attachment['name'] . "\"\r\n";
                    $message .= "Content-Disposition: attachment; filename=\"" . $attachment['name'] . "\"\r\n";
                    $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
                    $message .= chunk_split($fileContent, 76, "\r\n") . "\r\n";
                }
            }
        }

        $message .= "--{$boundary}--\r\n";

        return $message;
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
            $skippedCount = 0;

            // Determine the cutoff date for fetching emails
            $cutoffDate = $company->email_last_sync ? $company->email_last_sync->subMinutes(5) : now()->subDays(7);
            Log::info("Fetching emails newer than: {$cutoffDate->format('Y-m-d H:i:s')} for company {$company->id}");

            // Fetch emails (start from newest)
            for ($i = $totalMessages; $i > max(1, $totalMessages - $maxResults); $i--) {
                try {
                    // Get email headers first to check date
                    $headers = imap_headerinfo($connection, $i);
                    if (!$headers) {
                        continue;
                    }

                    // Check if email is newer than cutoff date
                    $emailDate = $headers->date ? strtotime($headers->date) : time();
                    if ($emailDate < $cutoffDate->timestamp) {
                        Log::info("Skipping email {$i} - older than cutoff date");
                        $skippedCount++;
                        continue;
                    }

                    $emailData = $this->fetchEmailFromImap($connection, $i, $company, $headers);
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

            Log::info("Email fetch completed for company {$company->id}: {$processedCount} processed, {$skippedCount} skipped (older than cutoff)");
            return $emails;

        } catch (\Exception $e) {
            Log::error("Error fetching emails from IMAP for company {$company->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch a single email from IMAP connection.
     */
    protected function fetchEmailFromImap($connection, int $messageNumber, Company $company, $headers = null): ?array
    {
        try {
            // Get email headers if not provided
            if (!$headers) {
                $headers = imap_headerinfo($connection, $messageNumber);
                if (!$headers) {
                    return null;
                }
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

            // Generate unique email ID using message_id if available, otherwise fallback to message number
            $messageId = $headers->message_id ?? null;
            if ($messageId) {
                // Clean the message_id to make it safe for use as an ID
                $cleanMessageId = preg_replace('/[^a-zA-Z0-9._-]/', '_', $messageId);
                $emailId = 'imap_' . $company->id . '_' . $cleanMessageId;
            } else {
                // Fallback to message number if no message_id is available
                $emailId = 'imap_' . $company->id . '_msg_' . $messageNumber;
            }

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
