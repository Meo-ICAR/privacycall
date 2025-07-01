<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\TenantScoped;

class Mandator extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

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
        'agent_company_id',
        'gdpr_representative_id',
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
        // GDPR service agreement fields
        'service_agreement_number',
        'service_start_date',
        'service_end_date',
        'service_status',
        'service_type',
        // GDPR compliance tracking
        'compliance_score',
        'last_gdpr_audit_date',
        'next_gdpr_audit_date',
        'gdpr_maturity_level',
        'risk_level',
        // GDPR service scope
        'gdpr_services_provided',
        'gdpr_requirements',
        'applicable_regulations',
        // Communication preferences for GDPR matters
        'gdpr_reporting_frequency',
        'gdpr_reporting_format',
        'gdpr_reporting_recipients',
        // GDPR incident management
        'last_data_incident_date',
        'data_incidents_count',
        'incident_response_plan',
        // GDPR training and awareness
        'last_gdpr_training_date',
        'next_gdpr_training_date',
        'employees_trained_count',
        'gdpr_training_required',
        // GDPR documentation
        'privacy_policy_updated',
        'privacy_policy_last_updated',
        'data_processing_register_maintained',
        'data_breach_procedures_established',
        'data_subject_rights_procedures_established',
        // GDPR deadlines and reminders
        'upcoming_gdpr_deadlines',
        'next_review_date',
        'gdpr_notes',
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
        // GDPR service agreement fields
        'service_start_date' => 'date',
        'service_end_date' => 'date',
        // GDPR compliance tracking
        'last_gdpr_audit_date' => 'date',
        'next_gdpr_audit_date' => 'date',
        // GDPR service scope
        'gdpr_services_provided' => 'array',
        'applicable_regulations' => 'array',
        // Communication preferences for GDPR matters
        'gdpr_reporting_recipients' => 'array',
        // GDPR incident management
        'last_data_incident_date' => 'date',
        // GDPR training and awareness
        'last_gdpr_training_date' => 'date',
        'next_gdpr_training_date' => 'date',
        'gdpr_training_required' => 'boolean',
        // GDPR documentation
        'privacy_policy_updated' => 'boolean',
        'privacy_policy_last_updated' => 'date',
        'data_processing_register_maintained' => 'boolean',
        'data_breach_procedures_established' => 'boolean',
        'data_subject_rights_procedures_established' => 'boolean',
        // GDPR deadlines and reminders
        'upcoming_gdpr_deadlines' => 'array',
        'next_review_date' => 'date',
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
     * Get the agent company (your company providing GDPR services).
     */
    public function agentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'agent_company_id');
    }

    /**
     * Get the GDPR representative from your company.
     */
    public function gdprRepresentative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gdpr_representative_id');
    }

    /**
     * Get the disclosure types this mandator is subscribed to.
     */
    public function disclosureTypes(): BelongsToMany
    {
        return $this->belongsToMany(DisclosureType::class);
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
     * Scope a query to only include active service agreements.
     */
    public function scopeActiveServices($query)
    {
        return $query->where('service_status', 'active');
    }

    /**
     * Scope a query to filter by service type.
     */
    public function scopeByServiceType($query, $type)
    {
        return $query->where('service_type', $type);
    }

    /**
     * Scope a query to filter by GDPR maturity level.
     */
    public function scopeByMaturityLevel($query, $level)
    {
        return $query->where('gdpr_maturity_level', $level);
    }

    /**
     * Scope a query to filter by risk level.
     */
    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    /**
     * Scope a query to get mandators with expiring service agreements.
     */
    public function scopeServiceExpiringSoon($query, $days = 30)
    {
        return $query->where('service_end_date', '<=', now()->addDays($days))
                    ->where('service_status', 'active');
    }

    /**
     * Scope a query to get mandators needing GDPR training.
     */
    public function scopeNeedsTraining($query)
    {
        return $query->where('gdpr_training_required', true)
                    ->where(function ($q) {
                        $q->whereNull('next_gdpr_training_date')
                          ->orWhere('next_gdpr_training_date', '<=', now());
                    });
    }

    /**
     * Scope a query to get mandators with upcoming GDPR deadlines.
     */
    public function scopeWithUpcomingDeadlines($query, $days = 30)
    {
        return $query->whereNotNull('upcoming_gdpr_deadlines')
                    ->where('next_review_date', '<=', now()->addDays($days));
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
        $subscriptions = array_filter($subscriptions, function ($type) use ($disclosureType) {
            return $type !== $disclosureType;
        });
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

    /**
     * Get the service agreement days remaining.
     */
    public function getServiceDaysRemainingAttribute(): ?int
    {
        if (!$this->service_end_date) return null;
        return now()->diffInDays($this->service_end_date, false);
    }

    /**
     * Check if service agreement is expiring soon.
     */
    public function isServiceExpiringSoon(int $days = 30): bool
    {
        return $this->service_end_date &&
               $this->service_end_date->diffInDays(now(), false) <= $days &&
               $this->service_status === 'active';
    }

    /**
     * Check if GDPR training is overdue.
     */
    public function isTrainingOverdue(): bool
    {
        return $this->gdpr_training_required &&
               $this->next_gdpr_training_date &&
               $this->next_gdpr_training_date->isPast();
    }

    /**
     * Check if GDPR audit is overdue.
     */
    public function isAuditOverdue(): bool
    {
        return $this->next_gdpr_audit_date &&
               $this->next_gdpr_audit_date->isPast();
    }

    /**
     * Get GDPR compliance status.
     */
    public function getGdprComplianceStatusAttribute(): string
    {
        if (!$this->compliance_score) return 'not_assessed';

        if ($this->compliance_score >= 90) return 'excellent';
        if ($this->compliance_score >= 75) return 'good';
        if ($this->compliance_score >= 60) return 'fair';
        return 'poor';
    }

    /**
     * Check if all required GDPR documentation is in place.
     */
    public function hasCompleteGdprDocumentation(): bool
    {
        return $this->privacy_policy_updated &&
               $this->data_processing_register_maintained &&
               $this->data_breach_procedures_established &&
               $this->data_subject_rights_procedures_established;
    }

    /**
     * Get GDPR compliance score color for UI.
     */
    public function getComplianceScoreColorAttribute(): string
    {
        if (!$this->compliance_score) return 'gray';

        if ($this->compliance_score >= 90) return 'green';
        if ($this->compliance_score >= 75) return 'blue';
        if ($this->compliance_score >= 60) return 'yellow';
        return 'red';
    }

    /**
     * Get risk level color for UI.
     */
    public function getRiskLevelColorAttribute(): string
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'very_high' => 'red',
            default => 'gray'
        };
    }
}
