<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessingRegC extends Model
{
    use HasFactory;

    protected $table = 'processing_reg_cs';

    protected $fillable = [
        'company_id',
        'processing_register_version_id',
        'change_type',
        'entity_type',
        'entity_id',
        'entity_name',
        'old_values',
        'new_values',
        'change_description',
        'change_reason',
        'impact_level',
        'requires_review',
        'requires_approval',
        'changed_by',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'review_status',
        'is_approved',
        'approved_at',
        'approved_by',
        'approval_notes',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'requires_review' => 'boolean',
        'requires_approval' => 'boolean',
        'reviewed_at' => 'datetime',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the company that owns the change.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the version associated with the change.
     */
    public function version(): BelongsTo
    {
        return $this->belongsTo(ProcessingRegisterVersion::class, 'processing_register_version_id');
    }

    /**
     * Get the user who made the change.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get the user who reviewed the change.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the user who approved the change.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include pending changes.
     */
    public function scopePending($query)
    {
        return $query->where('review_status', 'pending');
    }

    /**
     * Scope a query to only include approved changes.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include changes that require review.
     */
    public function scopeRequiresReview($query)
    {
        return $query->where('requires_review', true);
    }

    /**
     * Scope a query to only include changes that require approval.
     */
    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }

    /**
     * Scope a query to only include changes by entity type.
     */
    public function scopeByEntityType($query, string $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to only include changes by impact level.
     */
    public function scopeByImpactLevel($query, string $impactLevel)
    {
        return $query->where('impact_level', $impactLevel);
    }

    /**
     * Get the change type display name.
     */
    public function getChangeTypeDisplayAttribute(): string
    {
        return match($this->change_type) {
            'created' => 'Creazione',
            'updated' => 'Modifica',
            'deleted' => 'Eliminazione',
            'status_changed' => 'Cambio Status',
            default => 'Sconosciuto',
        };
    }

    /**
     * Get the entity type display name.
     */
    public function getEntityTypeDisplayAttribute(): string
    {
        return match($this->entity_type) {
            'data_processing_activity' => 'Attività di Trattamento',
            'data_breach' => 'Violazione Dati',
            'dpia' => 'DPIA',
            'third_country_transfer' => 'Trasferimento Paese Terzo',
            'data_processing_agreement' => 'Accordo di Trattamento',
            'data_subject_rights_request' => 'Richiesta Diritti Interessato',
            'company' => 'Azienda',
            default => 'Sconosciuto',
        };
    }

    /**
     * Get the impact level display name.
     */
    public function getImpactLevelDisplayAttribute(): string
    {
        return match($this->impact_level) {
            'low' => 'Basso',
            'medium' => 'Medio',
            'high' => 'Alto',
            'critical' => 'Critico',
            default => 'Sconosciuto',
        };
    }

    /**
     * Get the impact level color.
     */
    public function getImpactLevelColorAttribute(): string
    {
        return match($this->impact_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the review status display name.
     */
    public function getReviewStatusDisplayAttribute(): string
    {
        return match($this->review_status) {
            'pending' => 'In Attesa',
            'approved' => 'Approvato',
            'rejected' => 'Rifiutato',
            default => 'Sconosciuto',
        };
    }

    /**
     * Get the review status color.
     */
    public function getReviewStatusColorAttribute(): string
    {
        return match($this->review_status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    /**
     * Approve the change.
     */
    public function approve(int $approvedBy, string $notes = null): bool
    {
        return $this->update([
            'is_approved' => true,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Review the change.
     */
    public function review(int $reviewedBy, string $status, string $notes = null): bool
    {
        return $this->update([
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'review_status' => $status,
            'review_notes' => $notes,
        ]);
    }

    /**
     * Get the differences between old and new values.
     */
    public function getDifferencesAttribute(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $differences = [];

        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;

            if ($oldValue !== $newValue) {
                $differences[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $differences;
    }
}
