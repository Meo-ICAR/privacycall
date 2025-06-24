<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'supplier_number',
        'name',
        'legal_name',
        'registration_number',
        'vat_number',
        'contact_person_name',
        'contact_person_email',
        'contact_person_phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'supplier_type', // goods, services, both
        'supplier_category', // primary, secondary, emergency
        'supplier_status', // active, inactive, suspended, approved, pending
        'supplier_since',
        'last_order_date',
        'total_orders',
        'total_spent',
        'payment_terms',
        'credit_limit',
        'bank_account_info',
        'tax_info',

        // GDPR Compliance Fields
        'gdpr_consent_date',
        'data_processing_consent',
        'third_party_sharing_consent',
        'data_retention_consent',
        'right_to_be_forgotten_requested',
        'right_to_be_forgotten_date',
        'data_portability_requested',
        'data_portability_date',
        'data_processing_purpose',
        'data_retention_period',
        'data_processing_agreement_signed',
        'data_processing_agreement_date',

        'is_active',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'supplier_since' => 'date',
        'last_order_date' => 'date',
        'gdpr_consent_date' => 'datetime',
        'right_to_be_forgotten_date' => 'datetime',
        'data_portability_date' => 'datetime',
        'data_processing_agreement_date' => 'datetime',
        'data_processing_consent' => 'boolean',
        'third_party_sharing_consent' => 'boolean',
        'data_retention_consent' => 'boolean',
        'right_to_be_forgotten_requested' => 'boolean',
        'data_portability_requested' => 'boolean',
        'data_processing_agreement_signed' => 'boolean',
        'is_active' => 'boolean',
        'total_orders' => 'integer',
        'total_spent' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'data_retention_period' => 'integer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'bank_account_info',
        'tax_info',
        'credit_limit',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the company that owns the supplier.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the purchase orders for this supplier.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get the data processing activities for this supplier.
     */
    public function dataProcessingActivities(): HasMany
    {
        return $this->hasMany(DataProcessingActivity::class);
    }

    /**
     * Get the consent records for this supplier.
     */
    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by supplier type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('supplier_type', $type);
    }

    /**
     * Scope a query to filter by supplier status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('supplier_status', $status);
    }

    /**
     * Scope a query to filter by supplier category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('supplier_category', $category);
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
     * Check if supplier has signed data processing agreement.
     */
    public function hasSignedDataProcessingAgreement(): bool
    {
        return $this->data_processing_agreement_signed &&
               $this->data_processing_agreement_date;
    }

    /**
     * Check if supplier has requested right to be forgotten.
     */
    public function hasRequestedRightToBeForgotten(): bool
    {
        return $this->right_to_be_forgotten_requested;
    }

    /**
     * Check if supplier has requested data portability.
     */
    public function hasRequestedDataPortability(): bool
    {
        return $this->data_portability_requested;
    }

    /**
     * Calculate average order value.
     */
    public function getAverageOrderValueAttribute(): float
    {
        return $this->total_orders > 0 ? $this->total_spent / $this->total_orders : 0;
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(\App\Models\Document::class, 'documentable');
    }

    public function customerInspections(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\CustomerInspection::class, 'customer_inspection_supplier')->withTimestamps();
    }
}
