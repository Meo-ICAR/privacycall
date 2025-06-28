<?php

namespace Database\Factories;

use App\Models\EmailProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailProvider>
 */
class EmailProviderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailProvider::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'display_name' => $this->faker->company() . ' Email',
            'type' => 'imap',
            'icon' => null,
            'color' => $this->faker->hexColor(),
            'imap_host' => $this->faker->domainName(),
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'pop3_host' => $this->faker->domainName(),
            'pop3_port' => 995,
            'pop3_encryption' => 'ssl',
            'smtp_host' => $this->faker->domainName(),
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'smtp_auth_required' => true,
            'api_endpoint' => null,
            'api_version' => null,
            'oauth_client_id' => null,
            'oauth_client_secret' => null,
            'oauth_redirect_uri' => null,
            'oauth_scopes' => null,
            'timeout' => 30,
            'verify_ssl' => true,
            'auth_type' => 'password',
            'settings' => null,
            'is_active' => true,
            'description' => $this->faker->sentence(),
            'setup_instructions' => $this->faker->paragraph(),
        ];
    }

    /**
     * Configure the factory for Gmail provider.
     */
    public function gmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'gmail',
            'display_name' => 'Gmail',
            'type' => 'gmail_api',
            'icon' => 'gmail.png',
            'color' => '#EA4335',
            'imap_host' => 'imap.gmail.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp.gmail.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'api_endpoint' => 'https://gmail.googleapis.com',
            'api_version' => 'v1',
            'oauth_client_id' => config('services.google.client_id'),
            'oauth_client_secret' => config('services.google.client_secret'),
            'oauth_redirect_uri' => config('app.url') . '/auth/google/callback',
            'oauth_scopes' => [
                'https://www.googleapis.com/auth/gmail.readonly',
                'https://www.googleapis.com/auth/gmail.send',
                'https://www.googleapis.com/auth/gmail.modify'
            ],
            'auth_type' => 'oauth',
            'description' => 'Google Gmail with OAuth2 authentication',
            'setup_instructions' => 'To use Gmail, you need to enable 2-factor authentication and create an App Password, or use OAuth2 authentication.',
        ]);
    }

    /**
     * Configure the factory for Microsoft provider.
     */
    public function microsoft(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'microsoft',
            'display_name' => 'Microsoft 365 / Outlook',
            'type' => 'microsoft_graph',
            'icon' => 'microsoft.png',
            'color' => '#0078D4',
            'imap_host' => 'outlook.office365.com',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'smtp_host' => 'smtp.office365.com',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'api_endpoint' => 'https://graph.microsoft.com',
            'api_version' => 'v1.0',
            'oauth_client_id' => config('services.microsoft.client_id'),
            'oauth_client_secret' => config('services.microsoft.client_secret'),
            'oauth_redirect_uri' => config('app.url') . '/auth/microsoft/callback',
            'oauth_scopes' => [
                'https://graph.microsoft.com/Mail.Read',
                'https://graph.microsoft.com/Mail.Send',
                'https://graph.microsoft.com/Mail.ReadWrite'
            ],
            'auth_type' => 'oauth',
            'description' => 'Microsoft 365 and Outlook.com with Microsoft Graph API',
            'setup_instructions' => 'To use Microsoft 365, you need to register an application in Azure AD and configure the necessary permissions.',
        ]);
    }

    /**
     * Configure the factory for OVH provider.
     */
    public function ovh(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'ovh',
            'display_name' => 'OVH',
            'type' => 'imap',
            'icon' => 'ovh.png',
            'color' => '#123F6D',
            'imap_host' => 'ssl0.ovh.net',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'pop3_host' => 'ssl0.ovh.net',
            'pop3_port' => 995,
            'pop3_encryption' => 'ssl',
            'smtp_host' => 'ssl0.ovh.net',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'auth_type' => 'password',
            'description' => 'OVH email hosting with IMAP/POP3/SMTP support',
            'setup_instructions' => 'Use your OVH email address and password. Make sure IMAP is enabled in your OVH control panel.',
        ]);
    }

    /**
     * Configure the factory for Aruba provider.
     */
    public function aruba(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'aruba',
            'display_name' => 'Aruba',
            'type' => 'imap',
            'icon' => 'aruba.png',
            'color' => '#00A3E0',
            'imap_host' => 'imap.aruba.it',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'pop3_host' => 'pop3.aruba.it',
            'pop3_port' => 995,
            'pop3_encryption' => 'ssl',
            'smtp_host' => 'smtp.aruba.it',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'auth_type' => 'password',
            'description' => 'Aruba email hosting with IMAP/POP3/SMTP support',
            'setup_instructions' => 'Use your Aruba email address and password. Make sure IMAP is enabled in your Aruba control panel.',
        ]);
    }

    /**
     * Configure the factory for Libero provider.
     */
    public function libero(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'libero',
            'display_name' => 'Libero',
            'type' => 'imap',
            'icon' => 'libero.png',
            'color' => '#FF6B35',
            'imap_host' => 'imap.libero.it',
            'imap_port' => 993,
            'imap_encryption' => 'ssl',
            'pop3_host' => 'pop3.libero.it',
            'pop3_port' => 995,
            'pop3_encryption' => 'ssl',
            'smtp_host' => 'smtp.libero.it',
            'smtp_port' => 587,
            'smtp_encryption' => 'tls',
            'auth_type' => 'password',
            'description' => 'Libero email with IMAP/POP3/SMTP support',
            'setup_instructions' => 'Use your Libero email address and password. You may need to enable "Less secure app access" or use an App Password.',
        ]);
    }

    /**
     * Configure the factory for custom provider.
     */
    public function custom(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'custom',
            'display_name' => 'Custom IMAP',
            'type' => 'imap',
            'icon' => 'custom.png',
            'color' => '#6B7280',
            'imap_host' => null,
            'imap_port' => null,
            'imap_encryption' => null,
            'pop3_host' => null,
            'pop3_port' => null,
            'pop3_encryption' => null,
            'smtp_host' => null,
            'smtp_port' => null,
            'smtp_encryption' => null,
            'auth_type' => 'password',
            'description' => 'Custom IMAP server configuration',
            'setup_instructions' => 'Enter your custom IMAP server details. You can configure host, port, and encryption settings.',
        ]);
    }

    /**
     * Configure the factory for inactive provider.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Configure the factory for OAuth provider.
     */
    public function oauth(): static
    {
        return $this->state(fn (array $attributes) => [
            'auth_type' => 'oauth',
            'oauth_client_id' => $this->faker->uuid(),
            'oauth_client_secret' => $this->faker->sha256(),
            'oauth_redirect_uri' => config('app.url') . '/auth/callback',
            'oauth_scopes' => ['read', 'write'],
        ]);
    }
}
