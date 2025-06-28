<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Services\EmailIntegrationService;
use Illuminate\Support\Facades\Log;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $emailService = new EmailIntegrationService();

    // Check IMAP configuration
    echo "=== IMAP Configuration Check ===\n";
    $imapConfig = $emailService->checkImapConfiguration();
    if ($imapConfig['available']) {
        echo "✓ IMAP is properly configured\n";
    } else {
        echo "✗ IMAP configuration issues found:\n";
        foreach ($imapConfig['issues'] as $issue) {
            echo "  - {$issue}\n";
        }
        exit(1);
    }

    // Find company ID 1
    $company = Company::find(1);
    if (!$company) {
        echo "Error: Company ID 1 not found.\n";
        exit(1);
    }

    echo "\n=== Company Email Configuration ===\n";
    echo "Company: {$company->name}\n";
    echo "Email configured: " . ($company->hasEmailConfigured() ? 'Yes' : 'No') . "\n";

    if (!$company->hasEmailConfigured()) {
        echo "Error: Email is not configured for this company.\n";
        exit(1);
    }

    $provider = $company->emailProvider;
    echo "Email provider: {$provider->display_name}\n";

    // Get attachment statistics
    echo "\n=== Current Attachment Statistics ===\n";
    $stats = $emailService->getAttachmentStats($company);
    echo "Total emails: {$stats['total_emails']}\n";
    echo "Emails with attachments: {$stats['emails_with_attachments']}\n";
    echo "Total attachments: {$stats['total_attachments']}\n";
    echo "Attachment rate: {$stats['attachment_rate']}%\n";

    // Test email fetching
    echo "\n=== Testing Email Fetch ===\n";
    $result = $emailService->fetchEmailsForCompany($company, 5);

    if ($result['success']) {
        echo "✓ Successfully processed {$result['processed']} emails\n";

        // Get updated statistics
        $newStats = $emailService->getAttachmentStats($company);
        echo "\n=== Updated Attachment Statistics ===\n";
        echo "Total emails: {$newStats['total_emails']}\n";
        echo "Emails with attachments: {$newStats['emails_with_attachments']}\n";
        echo "Total attachments: {$newStats['total_attachments']}\n";
        echo "Attachment rate: {$newStats['attachment_rate']}%\n";

        // Show recent attachments
        $recentAttachments = \App\Models\EmailDocument::whereHas('companyEmail', function ($query) use ($company) {
            $query->where('company_id', $company->id);
        })->orderBy('created_at', 'desc')->limit(5)->get();

        if ($recentAttachments->count() > 0) {
            echo "\n=== Recent Attachments ===\n";
            foreach ($recentAttachments as $attachment) {
                echo "- {$attachment->original_name} ({$attachment->mime_type}, " . number_format($attachment->size / 1024, 1) . " KB)\n";
            }
        } else {
            echo "\nNo attachments found in recent emails.\n";
        }

    } else {
        echo "✗ Error fetching emails: {$result['error']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
