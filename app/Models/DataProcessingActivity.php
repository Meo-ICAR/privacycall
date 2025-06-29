<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\Traits\HasVersioning;
use Carbon\Carbon;
use App\Models\ThirdCountryTransfer;

class DataProcessingActivity extends Model
{
    use HasFactory, SoftDeletes, HasVersioning;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'data_controller_name',
        'data_controller_contact_email',
        'data_controller_contact_phone',
        'dpo_name',
        'dpo_email',
        'dpo_phone',
        'processing_method',
        'data_sources',
        'data_flows',
        'data_storage_locations',
        'risk_assessment_date',
        'risk_assessment_methodology',
        'risk_mitigation_measures',
        'supervisory_authority',
        'supervisory_authority_contact',
        'compliance_status',
        'last_compliance_review_date',
        'next_compliance_review_date',
        'supporting_documents',
        'privacy_notice_version',
        'privacy_notice_date',
        'processing_volume',
        'processing_frequency',
        'last_activity_review_date',
        'parent_activity_id',
        'related_activities',
        'processable_type', // Company, Employee, Customer, Supplier
        'processable_id',
        'activity_name',
        'activity_description',
        'processing_purpose',
        'legal_basis', // consent, contract, legal_obligation, vital_interests, public_task, legitimate_interests
        'data_categories', // personal_data, sensitive_data, special_categories
        'data_subjects', // employees, customers, suppliers, visitors
        'data_recipients', // internal, external, third_parties
        'third_country_transfers',
        'retention_period',
        'security_measures',
        'risk_assessment_level', // low, medium, high
        'data_protection_ia_required',
        'data_protection_ia_date',
        'data_protection_officer_consulted',
        'data_protection_officer_consultation_date',
        'start_date',
        'end_date',
        'is_active',
        'notes',
        'version',
        'version_id',
        'is_latest_version',
        'version_created_at',
        'version_created_by',
        'version_notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_categories' => 'array',
        'data_subjects' => 'array',
        'data_recipients' => 'array',
        'third_country_transfers' => 'array',
        'security_measures' => 'array',
        'data_storage_locations' => 'array',
        'supporting_documents' => 'array',
        'related_activities' => 'array',
        'data_protection_ia_date' => 'datetime',
        'data_protection_officer_consultation_date' => 'datetime',
        'risk_assessment_date' => 'date',
        'last_compliance_review_date' => 'date',
        'next_compliance_review_date' => 'date',
        'privacy_notice_date' => 'date',
        'last_activity_review_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'data_protection_ia_required' => 'boolean',
        'data_protection_officer_consulted' => 'boolean',
        'is_active' => 'boolean',
        'retention_period' => 'integer',
        'is_latest_version' => 'boolean',
        'version_created_at' => 'datetime',
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
     * Get the company that owns the data processing activity.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent processable model (Company, Employee, Customer, Supplier).
     */
    public function processable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the parent activity if this is a sub-activity.
     */
    public function parentActivity(): BelongsTo
    {
        return $this->belongsTo(DataProcessingActivity::class, 'parent_activity_id');
    }

    /**
     * Get the related activities.
     */
    public function relatedActivities()
    {
        return $this->belongsToMany(DataProcessingActivity::class, 'related_activities');
    }

    /**
     * Get the DPIAs associated with this activity.
     */
    public function dataProtectionIAs()
    {
        return $this->hasMany(DataProtectionIA::class);
    }

    /**
     * Get the third country transfers associated with this activity.
     */
    public function thirdCountryTransfers()
    {
        return $this->hasMany(ThirdCountryTransfer::class);
    }

    /**
     * Get the data processing agreements associated with this activity.
     */
    public function dataProcessingAgreements()
    {
        return $this->hasMany(DataProcessingAgreement::class);
    }

    /**
     * Scope a query to only include active activities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by legal basis.
     */
    public function scopeWithLegalBasis($query, $basis)
    {
        return $query->where('legal_basis', $basis);
    }

    /**
     * Scope a query to filter by risk level.
     */
    public function scopeWithRiskLevel($query, $level)
    {
        return $query->where('risk_assessment_level', $level);
    }

    /**
     * Scope a query to filter by processing purpose.
     */
    public function scopeWithPurpose($query, $purpose)
    {
        return $query->where('processing_purpose', $purpose);
    }

    /**
     * Scope a query to filter by compliance status.
     */
    public function scopeWithComplianceStatus($query, $status)
    {
        return $query->where('compliance_status', $status);
    }

    /**
     * Scope a query to filter by overdue compliance reviews.
     */
    public function scopeOverdueForComplianceReview($query)
    {
        return $query->where('next_compliance_review_date', '<', now());
    }

    /**
     * Check if activity requires Data Protection Impact Assessment (DPIA).
     */
    public function requiresDpia(): bool
    {
        return $this->data_protection_ia_required;
    }

    /**
     * Check if activity involves third country transfers.
     */
    public function hasThirdCountryTransfers(): bool
    {
        return !empty($this->third_country_transfers);
    }

    /**
     * Check if activity involves sensitive data.
     */
    public function involvesSensitiveData(): bool
    {
        return in_array('sensitive_data', $this->data_categories ?? []);
    }

    /**
     * Check if activity is compliant.
     */
    public function isCompliant(): bool
    {
        return $this->compliance_status === 'compliant';
    }

    /**
     * Check if activity is overdue for compliance review.
     */
    public function isOverdueForComplianceReview(): bool
    {
        return $this->next_compliance_review_date && $this->next_compliance_review_date->isPast();
    }

    /**
     * Get the duration of the activity in days.
     */
    public function getDurationInDaysAttribute(): ?int
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInDays($this->end_date);
    }

    /**
     * Check if activity is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get the compliance status color for UI.
     */
    public function getComplianceStatusColorAttribute(): string
    {
        $colors = [
            'compliant' => 'green',
            'non_compliant' => 'red',
            'under_review' => 'yellow',
        ];

        return $colors[$this->compliance_status] ?? 'gray';
    }

    /**
     * Get the risk level color for UI.
     */
    public function getRiskLevelColorAttribute(): string
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'very_high' => 'red',
        ];

        return $colors[$this->risk_assessment_level] ?? 'gray';
    }

    /**
     * Get the days until next compliance review.
     */
    public function getDaysUntilComplianceReviewAttribute(): ?int
    {
        if (!$this->next_compliance_review_date) {
            return null;
        }

        return now()->diffInDays($this->next_compliance_review_date, false);
    }

    /**
     * Get the documents associated with this activity.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Get the version that created this activity.
     */
    public function versionCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'version_created_by');
    }

    /**
     * Scope a query to only include latest versions.
     */
    public function scopeLatestVersion($query)
    {
        return $query->where('is_latest_version', true);
    }

    /**
     * Scope a query to only include specific version.
     */
    public function scopeByVersion($query, string $version)
    {
        return $query->where('version', $version);
    }

    /**
     * Create a new version of this activity.
     */
    public function createNewVersion(array $data = []): self
    {
        // Marca la versione corrente come non più recente
        $this->update(['is_latest_version' => false]);

        // Crea una nuova versione
        $newVersion = $this->replicate();
        $newVersion->fill(array_merge([
            'version' => $this->incrementVersion(),
            'is_latest_version' => true,
            'version_created_at' => now(),
            'version_created_by' => auth()->id(),
        ], $data));

        $newVersion->save();

        return $newVersion;
    }

    /**
     * Increment the version number.
     */
    private function incrementVersion(): string
    {
        $parts = explode('.', $this->version);
        $major = (int) $parts[0];
        $minor = (int) $parts[1];
        $patch = (int) $parts[2];

        return ($major + 1) . '.0.0';
    }

    /**
     * Get the version history for this activity.
     */
    public function getVersionHistory()
    {
        return self::where('activity_name', $this->activity_name)
            ->where('company_id', $this->company_id)
            ->orderBy('version', 'desc')
            ->get();
    }

    /**
     * Check if this is the latest version.
     */
    public function isLatestVersion(): bool
    {
        return $this->is_latest_version;
    }

    /**
     * Get the version display name.
     */
    public function getVersionDisplayAttribute(): string
    {
        return "v{$this->version}";
    }

    /**
     * Override the entity type for change logging.
     */
    protected function getEntityType(): string
    {
        return 'data_processing_activity';
    }

    /**
     * Override the entity name for change logging.
     */
    protected function getEntityName(): string
    {
        return $this->activity_name;
    }

    /**
     * Override the version identifier field.
     */
    protected function getVersionIdentifierField(): string
    {
        return 'activity_name';
    }
}
