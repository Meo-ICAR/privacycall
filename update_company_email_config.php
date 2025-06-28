<?php

require_once 'vendor/autoload.php';

use App\Models\Company;
use App\Models\EmailProvider;
use Illuminate\Support\Facades\Crypt;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Find company ID 1
    $company = Company::find(1);
    if (!$company) {
        echo "Error: Company ID 1 not found.\n";
        exit(1);
    }

    // Find ARUBA email provider
    $provider = EmailProvider::where('name', 'aruba')->first();
    if (!$provider) {
        echo "Error: ARUBA email provider not found.\n";
        exit(1);
    }

    echo "Updating email configuration for company: {$company->name}\n";
    echo "Email provider: {$provider->display_name}\n";

    // Prepare credentials
    $credentials = [
        'username' => 'Privacy@noemisrls.it',
        'email_address' => 'Privacy@noemisrls.it',
        'password' => 'N03m1Priv24@'
    ];

    // Update company with email configuration
    $company->update([
        'email_provider_id' => $provider->id,
        'data_controller_contact' => 'Privacy@noemisrls.it',
        'email_configured' => true,
        'email_sync_error' => null,
    ]);

    // Set email credentials using the proper method
    $company->setEmailCredentials($credentials);

    echo "Email configuration updated successfully!\n";
    echo "Email address: Privacy@noemisrls.it\n";
    echo "Username: Privacy@noemisrls.it\n";
    echo "Provider: {$provider->display_name}\n";
    echo "Configuration status: " . ($company->email_configured ? 'Configured' : 'Not configured') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
