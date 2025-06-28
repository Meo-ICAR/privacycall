<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Representative extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'disclosure_subscriptions',
        'last_disclosure_date',
        'is_active',
        'notes',
        'email_notifications',
        'sms_notifications',
        'preferred_contact_method',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'disclosure_subscriptions' => 'array',
        'last_disclosure_date' => 'datetime',
        'is_active' => 'boolean',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
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
     * Get the company that owns the representative.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the full name of the representative.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Scope a query to only include active representatives.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by company.
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Check if representative is subscribed to a specific disclosure type.
     */
    public function isSubscribedTo($disclosureType): bool
    {
        return in_array($disclosureType, $this->disclosure_subscriptions ?? []);
    }

    /**
     * Add a disclosure subscription.
     */
    public function addDisclosureSubscription($disclosureType): void
    {
        $subscriptions = $this->disclosure_subscriptions ?? [];
        if (!in_array($disclosureType, $subscriptions)) {
            $subscriptions[] = $disclosureType;
            $this->update(['disclosure_subscriptions' => $subscriptions]);
        }
    }

    /**
     * Remove a disclosure subscription.
     */
    public function removeDisclosureSubscription($disclosureType): void
    {
        $subscriptions = $this->disclosure_subscriptions ?? [];
        $subscriptions = array_filter($subscriptions, fn($type) => $type !== $disclosureType);
        $this->update(['disclosure_subscriptions' => array_values($subscriptions)]);
    }

    /**
     * Get the disclosure subscription summary.
     */
    public function getDisclosureSummaryAttribute(): array
    {
        $subscriptions = $this->disclosure_subscriptions ?? [];
        $lastDate = $this->last_disclosure_date;

        return [
            'total_subscriptions' => count($subscriptions),
            'subscription_types' => $subscriptions,
            'last_disclosure_date' => $lastDate?->format('Y-m-d H:i:s'),
            'days_since_last_disclosure' => $lastDate ? $lastDate->diffInDays(now()) : null,
        ];
    }

    /**
     * Update the last disclosure date.
     */
    public function updateLastDisclosureDate(): void
    {
        $this->update(['last_disclosure_date' => now()]);
    }
}
