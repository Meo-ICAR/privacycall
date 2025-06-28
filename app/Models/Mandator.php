<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mandator extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mandators';

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
        'logo_url',
        'original_id',
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
     * Get the company that owns the mandator.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the original mandator (if this is a clone).
     */
    public function original(): BelongsTo
    {
        return $this->belongsTo(Mandator::class, 'original_id');
    }

    /**
     * Get all clones of this mandator.
     */
    public function clones(): HasMany
    {
        return $this->hasMany(Mandator::class, 'original_id');
    }

    /**
     * Check if this mandator is a clone.
     */
    public function isClone(): bool
    {
        return !is_null($this->original_id);
    }

    /**
     * Check if this mandator has clones.
     */
    public function hasClones(): bool
    {
        return $this->clones()->exists();
    }

    /**
     * Get the root mandator (original or self if not a clone).
     */
    public function getRootMandator(): Mandator
    {
        return $this->original_id ? $this->original : $this;
    }

    /**
     * Clone this mandator to another company.
     */
    public function cloneToCompany(int $targetCompanyId, array $overrides = []): Mandator
    {
        $cloneData = $this->toArray();

        // Remove fields that shouldn't be cloned
        unset($cloneData['id'], $cloneData['created_at'], $cloneData['updated_at'], $cloneData['deleted_at']);

        // Set the new company and original reference
        $cloneData['company_id'] = $targetCompanyId;
        $cloneData['original_id'] = $this->id;

        // Apply any overrides
        $cloneData = array_merge($cloneData, $overrides);

        // Ensure email is unique by adding a suffix if needed
        $originalEmail = $cloneData['email'];
        $counter = 1;
        while (Mandator::where('email', $cloneData['email'])->exists()) {
            $cloneData['email'] = $originalEmail . '_clone_' . $counter;
            $counter++;
        }

        return Mandator::create($cloneData);
    }

    /**
     * Get all related mandators (original + clones).
     */
    public function getAllRelated(): \Illuminate\Database\Eloquent\Collection
    {
        $root = $this->getRootMandator();
        return Mandator::where('id', $root->id)
            ->orWhere('original_id', $root->id)
            ->get();
    }

    /**
     * Get the full name of the mandator.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Scope a query to only include active mandators.
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
     * Scope a query to only include original mandators (not clones).
     */
    public function scopeOriginals($query)
    {
        return $query->whereNull('original_id');
    }

    /**
     * Scope a query to only include cloned mandators.
     */
    public function scopeClones($query)
    {
        return $query->whereNotNull('original_id');
    }

    /**
     * Check if mandator is subscribed to a specific disclosure type.
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

    /**
     * Get the logo URL or a default avatar.
     */
    public function getLogoUrlAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Return a default avatar based on initials
        $initials = strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name ?? '', 0, 1));
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&color=7C3AED&background=EBF4FF";
    }
}
