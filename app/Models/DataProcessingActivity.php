<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DataProcessingActivity extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
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
        'data_protection_impact_assessment_required',
        'data_protection_impact_assessment_date',
        'data_protection_officer_consulted',
        'data_protection_officer_consultation_date',
        'start_date',
        'end_date',
        'is_active',
        'notes'
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
        'data_protection_impact_assessment_date' => 'datetime',
        'data_protection_officer_consultation_date' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'data_protection_impact_assessment_required' => 'boolean',
        'data_protection_officer_consulted' => 'boolean',
        'is_active' => 'boolean',
        'retention_period' => 'integer',
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
     * Check if activity requires Data Protection Impact Assessment (DPIA).
     */
    public function requiresDpia(): bool
    {
        return $this->data_protection_impact_assessment_required;
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

    public function documents(): MorphMany
    {
        return $this->morphMany(\App\Models\Document::class, 'documentable');
    }
}
