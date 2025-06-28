<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'legal_name',
        'registration_number',
        'vat_number',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'signature',
        'email',
        'website',
        'company_type', // employer, customer, supplier
        'industry',
        'size', // small, medium, large
        'gdpr_consent_date',
        'data_retention_period',
        'data_processing_purpose',
        'data_controller_contact',
        'data_protection_officer',
        'is_active',
        'notes',
        'logo_url',
        'holding_id',
        'type',
        'gdpr_compliant',
        'data_retention_policy',
        'address'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gdpr_consent_date' => 'datetime',
        'is_active' => 'boolean',
        'data_retention_period' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the users associated with this company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the employees associated with this company.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the customers associated with this company.
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the suppliers associated with this company.
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    /**
     * Get the data processing activities for this company.
     */
    public function dataProcessingActivities(): HasMany
    {
        return $this->hasMany(DataProcessingActivity::class);
    }

    /**
     * Get the consent records for this company.
     */
    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }

    /**
     * Get the representatives associated with this company.
     */
    public function representatives(): HasMany
    {
        return $this->hasMany(Representative::class);
    }

    /**
     * Scope a query to only include active companies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by company type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('company_type', $type);
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
               $this->gdpr_consent_date->diffInDays(now()) <= 365; // Consent valid for 1 year
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(\App\Models\Document::class, 'documentable');
    }

    public function employerType()
    {
        return $this->belongsTo(EmployerType::class);
    }

    public function holding()
    {
        return $this->belongsTo(Holding::class);
    }
}
