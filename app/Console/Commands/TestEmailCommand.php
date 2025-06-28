<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailIntegrationService;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email} {--subject=Test Email} {--body=This is a test email from PrivacyCall}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $subject = $this->option('subject');
        $body = $this->option('body');

        $this->info("Testing email sending to: {$email}");
        $this->info("Subject: {$subject}");
        $this->info("Body: {$body}");

        try {
            // Get company 1 which has email configured
            $company = \App\Models\Company::find(1);
            if (!$company) {
                $this->error("Company 1 not found!");
                return 1;
            }

            $this->info("Using company: {$company->name} (ID: {$company->id})");
            $this->info("Provider: " . ($company->emailProvider ? $company->emailProvider->name : 'None'));
            $this->info("Configured: " . ($company->email_configured ? 'Yes' : 'No'));

            // Test 1: EmailIntegrationService with company 1
            $this->info("\n1. Testing EmailIntegrationService with company 1...");
            $emailService = new EmailIntegrationService();
            $emailData = [
                'to_email' => $email,
                'to_name' => 'Test User',
                'subject' => $subject,
                'body' => $body,
                'attachments' => []
            ];

            $result = $emailService->sendEmail($emailData, $company);

            if ($result) {
                $this->info("✓ EmailIntegrationService test completed successfully");
            } else {
                $this->error("✗ EmailIntegrationService test failed");
            }

            // Test 2: Direct Laravel Mail with company's SMTP settings
            $this->info("\n2. Testing direct Laravel Mail with company SMTP...");
            if ($company->hasEmailConfigured()) {
                $provider = $company->emailProvider;
                $credentials = $company->getEmailCredentials();

                // Configure Laravel Mail with company's SMTP settings
                $config = [
                    'transport' => 'smtp',
                    'host' => $provider->smtp_host,
                    'port' => $provider->smtp_port,
                    'encryption' => $provider->smtp_encryption,
                    'username' => $credentials['username'],
                    'password' => $credentials['password'] ?? '',
                    'timeout' => $provider->timeout ?? 30,
                    'local_domain' => parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST),
                ];

                // Create a temporary mail configuration
                $tempConfig = config('mail');
                $tempConfig['mailers']['company_smtp'] = $config;
                $tempConfig['default'] = 'company_smtp';

                // Set the temporary configuration
                config(['mail' => $tempConfig]);

                Mail::raw($body, function ($message) use ($email, $subject, $company) {
                    $message->to($email)
                            ->subject($subject)
                            ->from($company->data_controller_contact, $company->name);
                });

                $this->info("✓ Direct Laravel Mail with company SMTP test completed");
            } else {
                $this->warn("Company email not configured, skipping direct SMTP test");
            }

            $this->info("\nEmail tests completed!");

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
