<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\EmailProvider;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Get company and provider
    $company = Company::find(1);
    $provider = EmailProvider::where('name', 'aruba')->first();

    if (!$company || !$provider) {
        echo "Company or provider not found\n";
        exit(1);
    }

    $credentials = $company->getEmailCredentials();

    echo "Testing ARUBA IMAP connection...\n";
    echo "Host: {$provider->imap_host}\n";
    echo "Port: {$provider->imap_port}\n";
    echo "Encryption: {$provider->imap_encryption}\n";
    echo "Username: {$credentials['username']}\n";
    echo "Email: {$credentials['email_address']}\n";
    echo "Password: " . str_repeat('*', strlen($credentials['password'])) . "\n\n";

    // Build connection string
    $connectionString = "{{$provider->imap_host}:{$provider->imap_port}";
    if ($provider->imap_encryption) {
        $connectionString .= "/{$provider->imap_encryption}";
    }
    $connectionString .= "}INBOX";

    echo "Connection string: {$connectionString}\n\n";

    // Test connection
    $mailbox = @imap_open($connectionString, $credentials['username'], $credentials['password'], 0, 1);

    if ($mailbox) {
        echo "✅ Connection successful!\n";

        // Get mailbox info
        $info = imap_mailboxmsginfo($mailbox);
        echo "Total messages: " . $info->Nmsgs . "\n";
        echo "Recent messages: " . $info->Recent . "\n";
        echo "Unread messages: " . $info->Unread . "\n";

        imap_close($mailbox);
    } else {
        echo "❌ Connection failed!\n";
        $errors = imap_errors();
        if ($errors) {
            echo "IMAP errors:\n";
            foreach ($errors as $error) {
                echo "  - {$error}\n";
            }
        }

        $alerts = imap_alerts();
        if ($alerts) {
            echo "IMAP alerts:\n";
            foreach ($alerts as $alert) {
                echo "  - {$alert}\n";
            }
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
