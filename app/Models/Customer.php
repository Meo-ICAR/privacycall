<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'customer_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'customer_type', // individual, business
        'customer_status', // active, inactive, suspended
        'customer_since',
        'last_purchase_date',
        'total_purchases',
        'preferred_contact_method',
        'marketing_preferences',

        // GDPR Compliance Fields
        'gdpr_consent_date',
        'data_processing_consent',
        'marketing_consent',
        'third_party_sharing_consent',
        'data_retention_consent',
        'right_to_be_forgotten_requested',
        'right_to_be_forgotten_date',
        'data_portability_requested',
        'data_portability_date',
        'data_processing_purpose',
        'data_retention_period',

        // Consent Acquisition Fields
        'consent_method',
        'consent_source',
        'consent_channel',
        'consent_evidence',
        'consent_evidence_file',
        'consent_text',
        'consent_language',
        'consent_version',
        'ip_address',
        'user_agent',

        'is_active',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'customer_since' => 'date',
        'last_purchase_date' => 'date',
        'gdpr_consent_date' => 'datetime',
        'right_to_be_forgotten_date' => 'datetime',
        'data_portability_date' => 'datetime',
        'data_processing_consent' => 'boolean',
        'marketing_consent' => 'boolean',
        'third_party_sharing_consent' => 'boolean',
        'data_retention_consent' => 'boolean',
        'right_to_be_forgotten_requested' => 'boolean',
        'data_portability_requested' => 'boolean',
        'is_active' => 'boolean',
        'total_purchases' => 'decimal:2',
        'data_retention_period' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'date_of_birth',
        'total_purchases',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the company that owns the customer.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user account associated with this customer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders for this customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the data processing activities for this customer.
     */
    public function dataProcessingActivities(): HasMany
    {
        return $this->hasMany(DataProcessingActivity::class);
    }

    /**
     * Get the consent records for this customer.
     */
    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by customer type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('customer_type', $type);
    }

    /**
     * Scope a query to filter by customer status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('customer_status', $status);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->address_line_1;

        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }

        $address .= ', ' . $this->city;

        if ($this->state) {
            $address .= ', ' . $this->state;
        }

        $address .= ' ' . $this->postal_code . ', ' . $this->country;

        return $address;
    }

    /**
     * Check if GDPR consent is valid.
     */
    public function hasValidGdprConsent(): bool
    {
        return $this->gdpr_consent_date &&
               $this->gdpr_consent_date->diffInDays(now()) <= 365;
    }

    /**
     * Check if customer has requested right to be forgotten.
     */
    public function hasRequestedRightToBeForgotten(): bool
    {
        return $this->right_to_be_forgotten_requested;
    }

    /**
     * Check if customer has requested data portability.
     */
    public function hasRequestedDataPortability(): bool
    {
        return $this->data_portability_requested;
    }

    /**
     * Calculate customer age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(\App\Models\Document::class, 'documentable');
    }

    public function customerType()
    {
        return $this->belongsTo(CustomerType::class);
    }
}
