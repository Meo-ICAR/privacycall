<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'hire_date',
        'termination_date',
        'salary',
        'employment_type', // full_time, part_time, contract, temporary
        'work_location',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',

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

        'is_active',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
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
        'salary' => 'decimal:2',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'salary',
        'emergency_contact_phone',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the company that owns the employee.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user account associated with this employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the data processing activities for this employee.
     */
    public function dataProcessingActivities(): HasMany
    {
        return $this->hasMany(DataProcessingActivity::class);
    }

    /**
     * Get the consent records for this employee.
     */
    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }

    /**
     * Scope a query to only include active employees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by employment type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Get the full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
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
     * Check if employee has requested right to be forgotten.
     */
    public function hasRequestedRightToBeForgotten(): bool
    {
        return $this->right_to_be_forgotten_requested;
    }

    /**
     * Check if employee has requested data portability.
     */
    public function hasRequestedDataPortability(): bool
    {
        return $this->data_portability_requested;
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(\App\Models\Document::class, 'documentable');
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Training::class, 'employee_training')
            ->withPivot('attended', 'completed', 'score', 'notes')
            ->withTimestamps();
    }
}
