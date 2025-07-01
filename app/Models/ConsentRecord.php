<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\TenantScoped;

class ConsentRecord extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'consentable_type', // Company, Employee, Customer, Supplier
        'consentable_id',
        'consent_type', // data_processing, marketing, third_party_sharing, data_retention
        'consent_status', // granted, withdrawn, expired, pending
        'consent_method', // web_form, email, phone, in_person, document
        'consent_date',
        'withdrawal_date',
        'expiry_date',
        'consent_version',
        'consent_text',
        'consent_language',
        'ip_address',
        'user_agent',
        'consent_source', // website, mobile_app, call_center, in_store
        'consent_channel', // online, offline, phone, email
        'consent_evidence', // screenshot, document, audio_recording, video_recording
        'consent_evidence_file',
        'consent_notes',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'consent_date' => 'datetime',
        'withdrawal_date' => 'datetime',
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'ip_address',
        'user_agent',
        'consent_evidence_file',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the company that owns the consent record.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent consentable model (Company, Employee, Customer, Supplier).
     */
    public function consentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include active consent records.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by consent type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('consent_type', $type);
    }

    /**
     * Scope a query to filter by consent status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('consent_status', $status);
    }

    /**
     * Scope a query to filter by consent method.
     */
    public function scopeWithMethod($query, $method)
    {
        return $query->where('consent_method', $method);
    }

    /**
     * Scope a query to get valid consents.
     */
    public function scopeValid($query)
    {
        return $query->where('consent_status', 'granted')
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('withdrawal_date');
                    });
    }

    /**
     * Scope a query to get expired consents.
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('consent_status', 'expired')
              ->orWhere('expiry_date', '<', now());
        });
    }

    /**
     * Scope a query to get withdrawn consents.
     */
    public function scopeWithdrawn($query)
    {
        return $query->where('consent_status', 'withdrawn')
                    ->whereNotNull('withdrawal_date');
    }

    /**
     * Check if consent is currently valid.
     */
    public function isValid(): bool
    {
        if ($this->consent_status !== 'granted') {
            return false;
        }

        if ($this->withdrawal_date) {
            return false;
        }

        if ($this->expiry_date && now()->gt($this->expiry_date)) {
            return false;
        }

        return true;
    }

    /**
     * Check if consent has expired.
     */
    public function hasExpired(): bool
    {
        return $this->expiry_date && now()->gt($this->expiry_date);
    }

    /**
     * Check if consent has been withdrawn.
     */
    public function hasBeenWithdrawn(): bool
    {
        return $this->consent_status === 'withdrawn' && $this->withdrawal_date;
    }

    /**
     * Get the duration of consent in days.
     */
    public function getConsentDurationInDaysAttribute(): ?int
    {
        if (!$this->consent_date) {
            return null;
        }

        $endDate = $this->withdrawal_date ?? $this->expiry_date ?? now();
        return $this->consent_date->diffInDays($endDate);
    }

    /**
     * Get days until consent expires.
     */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Check if consent is about to expire (within 30 days).
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->getDaysUntilExpiryAttribute() <= 30 && $this->getDaysUntilExpiryAttribute() > 0;
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(\App\Models\Document::class, 'documentable');
    }
}
