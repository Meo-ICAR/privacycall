<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'type',
        'icon',
        'color',
        'imap_host',
        'imap_port',
        'imap_encryption',
        'pop3_host',
        'pop3_port',
        'pop3_encryption',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_auth_required',
        'api_endpoint',
        'api_version',
        'oauth_client_id',
        'oauth_client_secret',
        'oauth_redirect_uri',
        'oauth_scopes',
        'timeout',
        'verify_ssl',
        'auth_type',
        'settings',
        'is_active',
        'description',
        'setup_instructions'
    ];

    protected $casts = [
        'oauth_scopes' => 'array',
        'settings' => 'array',
        'smtp_auth_required' => 'boolean',
        'verify_ssl' => 'boolean',
        'is_active' => 'boolean',
        'imap_port' => 'integer',
        'pop3_port' => 'integer',
        'smtp_port' => 'integer',
        'timeout' => 'integer',
    ];

    /**
     * Get the companies using this email provider.
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Scope to filter active providers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get IMAP configuration.
     */
    public function getImapConfig(): array
    {
        return [
            'host' => $this->imap_host,
            'port' => $this->imap_port,
            'encryption' => $this->imap_encryption,
            'timeout' => $this->timeout,
            'verify_ssl' => $this->verify_ssl,
        ];
    }

    /**
     * Get POP3 configuration.
     */
    public function getPop3Config(): array
    {
        return [
            'host' => $this->pop3_host,
            'port' => $this->pop3_port,
            'encryption' => $this->pop3_encryption,
            'timeout' => $this->timeout,
            'verify_ssl' => $this->verify_ssl,
        ];
    }

    /**
     * Get SMTP configuration.
     */
    public function getSmtpConfig(): array
    {
        return [
            'host' => $this->smtp_host,
            'port' => $this->smtp_port,
            'encryption' => $this->smtp_encryption,
            'auth_required' => $this->smtp_auth_required,
            'timeout' => $this->timeout,
            'verify_ssl' => $this->verify_ssl,
        ];
    }

    /**
     * Get API configuration.
     */
    public function getApiConfig(): array
    {
        return [
            'endpoint' => $this->api_endpoint,
            'version' => $this->api_version,
            'oauth_client_id' => $this->oauth_client_id,
            'oauth_client_secret' => $this->oauth_client_secret,
            'oauth_redirect_uri' => $this->oauth_redirect_uri,
            'oauth_scopes' => $this->oauth_scopes,
            'timeout' => $this->timeout,
        ];
    }

    /**
     * Check if provider supports IMAP.
     */
    public function supportsImap(): bool
    {
        return !empty($this->imap_host) && !empty($this->imap_port);
    }

    /**
     * Check if provider supports POP3.
     */
    public function supportsPop3(): bool
    {
        return !empty($this->pop3_host) && !empty($this->pop3_port);
    }

    /**
     * Check if provider supports SMTP.
     */
    public function supportsSmtp(): bool
    {
        return !empty($this->smtp_host) && !empty($this->smtp_port);
    }

    /**
     * Check if provider supports API.
     */
    public function supportsApi(): bool
    {
        return !empty($this->api_endpoint);
    }

    /**
     * Check if provider uses OAuth.
     */
    public function usesOAuth(): bool
    {
        return $this->auth_type === 'oauth' && !empty($this->oauth_client_id);
    }

    /**
     * Get provider icon URL.
     */
    public function getIconUrlAttribute(): string
    {
        if ($this->icon) {
            return asset('storage/email-providers/' . $this->icon);
        }

        // Return default icon based on provider name
        $defaultIcons = [
            'gmail' => 'fab fa-google',
            'microsoft' => 'fab fa-microsoft',
            'outlook' => 'fab fa-microsoft',
            'yahoo' => 'fab fa-yahoo',
            'ovh' => 'fas fa-server',
            'aruba' => 'fas fa-server',
            'libero' => 'fas fa-envelope',
        ];

        $providerKey = strtolower($this->name);
        return $defaultIcons[$providerKey] ?? 'fas fa-envelope';
    }

    /**
     * Get provider color for UI.
     */
    public function getColorAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Return default colors based on provider name
        $defaultColors = [
            'gmail' => '#EA4335',
            'microsoft' => '#0078D4',
            'outlook' => '#0078D4',
            'yahoo' => '#720E9E',
            'ovh' => '#123F6D',
            'aruba' => '#00A3E0',
            'libero' => '#FF6B35',
        ];

        $providerKey = strtolower($this->name);
        return $defaultColors[$providerKey] ?? '#6B7280';
    }

    /**
     * Get connection string for IMAP.
     */
    public function getImapConnectionString(): string
    {
        if (!$this->supportsImap()) {
            return '';
        }

        $encryption = $this->imap_encryption ? '/' . $this->imap_encryption : '';
        return "{{$this->imap_host}:{$this->imap_port}{$encryption}}";
    }

    /**
     * Get connection string for POP3.
     */
    public function getPop3ConnectionString(): string
    {
        if (!$this->supportsPop3()) {
            return '';
        }

        $encryption = $this->pop3_encryption ? '/' . $this->pop3_encryption : '';
        return "{{$this->pop3_host}:{$this->pop3_port}{$encryption}}";
    }

    /**
     * Get connection string for SMTP.
     */
    public function getSmtpConnectionString(): string
    {
        if (!$this->supportsSmtp()) {
            return '';
        }

        $encryption = $this->smtp_encryption ? '/' . $this->smtp_encryption : '';
        return "{{$this->smtp_host}:{$this->smtp_port}{$encryption}}";
    }

    /**
     * Get setup instructions with placeholders replaced.
     */
    public function getSetupInstructionsWithPlaceholders(): string
    {
        $instructions = $this->setup_instructions ?? '';

        // Replace common placeholders
        $replacements = [
            '{imap_host}' => $this->imap_host ?? 'N/A',
            '{imap_port}' => $this->imap_port ?? 'N/A',
            '{imap_encryption}' => $this->imap_encryption ?? 'N/A',
            '{pop3_host}' => $this->pop3_host ?? 'N/A',
            '{pop3_port}' => $this->pop3_port ?? 'N/A',
            '{pop3_encryption}' => $this->pop3_encryption ?? 'N/A',
            '{smtp_host}' => $this->smtp_host ?? 'N/A',
            '{smtp_port}' => $this->smtp_port ?? 'N/A',
            '{smtp_encryption}' => $this->smtp_encryption ?? 'N/A',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $instructions);
    }

    /**
     * Get provider capabilities.
     */
    public function getCapabilities(): array
    {
        $capabilities = [];

        if ($this->supportsImap()) {
            $capabilities[] = 'IMAP';
        }

        if ($this->supportsPop3()) {
            $capabilities[] = 'POP3';
        }

        if ($this->supportsSmtp()) {
            $capabilities[] = 'SMTP';
        }

        if ($this->supportsApi()) {
            $capabilities[] = 'API';
        }

        if ($this->usesOAuth()) {
            $capabilities[] = 'OAuth';
        }

        return $capabilities;
    }

    /**
     * Check if provider is configured for a specific company.
     */
    public function isConfiguredForCompany(Company $company): bool
    {
        return $company->email_provider_id === $this->id && $company->email_configured;
    }
}
