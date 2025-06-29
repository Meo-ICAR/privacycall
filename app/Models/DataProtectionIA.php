<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;

class DataProtectionIA extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'data_protection_i_as';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'data_processing_activity_id',
        'dpia_number',
        'assessment_date',
        'assessment_status',
        'risk_level',
        'processing_purpose',
        'data_categories_processed',
        'data_subjects_affected',
        'necessity_and_proportionality',
        'risk_mitigation_measures',
        'residual_risks',
        'dpo_opinion',
        'stakeholder_consultation',
        'approval_date',
        'approved_by',
        'review_frequency',
        'next_review_date',
        'methodology_used',
        'identified_risks',
        'risk_assessment_criteria',
        'consultation_findings',
        'recommendations',
        'implementation_plan',
        'supervisory_authority_consultation_required',
        'supervisory_consultation_date',
        'supervisory_authority_feedback',
        'is_active',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_categories_processed' => 'array',
        'data_subjects_affected' => 'array',
        'assessment_date' => 'date',
        'approval_date' => 'date',
        'next_review_date' => 'date',
        'supervisory_consultation_date' => 'date',
        'supervisory_authority_consultation_required' => 'boolean',
        'is_active' => 'boolean',
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
     * Get the company that owns the DPIA.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the data processing activity associated with this DPIA.
     */
    public function dataProcessingActivity(): BelongsTo
    {
        return $this->belongsTo(DataProcessingActivity::class);
    }

    /**
     * Get the user who approved the DPIA.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the documents associated with this DPIA.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Scope a query to only include active DPIAs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by assessment status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('assessment_status', $status);
    }

    /**
     * Scope a query to filter by risk level.
     */
    public function scopeWithRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    /**
     * Scope a query to filter by overdue reviews.
     */
    public function scopeOverdueForReview($query)
    {
        return $query->where('next_review_date', '<', now());
    }

    /**
     * Check if DPIA requires supervisory authority consultation.
     */
    public function requiresSupervisoryConsultation(): bool
    {
        return $this->supervisory_authority_consultation_required ||
               $this->risk_level === 'very_high';
    }

    /**
     * Check if DPIA is overdue for review.
     */
    public function isOverdueForReview(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    /**
     * Check if DPIA is approved.
     */
    public function isApproved(): bool
    {
        return $this->assessment_status === 'approved';
    }

    /**
     * Check if DPIA is completed.
     */
    public function isCompleted(): bool
    {
        return in_array($this->assessment_status, ['completed', 'approved']);
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

        return $colors[$this->risk_level] ?? 'gray';
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'draft' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            'approved' => 'green',
            'rejected' => 'red',
            'under_review' => 'yellow',
        ];

        return $colors[$this->assessment_status] ?? 'gray';
    }

    /**
     * Get the days until next review.
     */
    public function getDaysUntilReviewAttribute(): ?int
    {
        if (!$this->next_review_date) {
            return null;
        }

        return now()->diffInDays($this->next_review_date, false);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($dpia) {
            if (empty($dpia->dpia_number)) {
                $dpia->dpia_number = 'DPIA-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
