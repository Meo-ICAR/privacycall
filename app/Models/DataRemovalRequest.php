<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;

class DataRemovalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'mandator_id',
        'requested_by_user_id',
        'request_number',
        'request_type',
        'status',
        'priority',
        'reason_for_removal',
        'data_categories_to_remove',
        'retention_justification',
        'legal_basis_for_retention',
        'request_date',
        'due_date',
        'review_date',
        'completion_date',
        'review_notes',
        'rejection_reason',
        'completion_notes',
        'data_removal_method',
        'identity_verified',
        'verification_method',
        'verification_notes',
        'gdpr_compliant',
        'compliance_notes',
        'notify_third_parties',
        'third_party_notification_details',
        'reviewed_by_user_id',
        'completed_by_user_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'due_date' => 'date',
        'review_date' => 'date',
        'completion_date' => 'date',
        'identity_verified' => 'boolean',
        'gdpr_compliant' => 'boolean',
        'notify_third_parties' => 'boolean',
        'data_categories_to_remove' => 'array',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function mandator(): BelongsTo
    {
        return $this->belongsTo(Mandator::class);
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_user_id');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function auditLogs()
    {
        return $this->hasMany(DataRemovalRequestAuditLog::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'in_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                    ->where('due_date', '>', now())
                    ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Accessors
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getIsDueSoonAttribute(): bool
    {
        return $this->due_date && $this->due_date->diffInDays(now()) <= 7 && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        return $this->due_date ? $this->due_date->diffInDays(now()) : null;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_review' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            'completed' => 'gray',
            'cancelled' => 'gray',
            default => 'gray'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray'
        };
    }

    // Methods
    public function generateRequestNumber(): string
    {
        $prefix = 'DRR';
        $year = now()->format('Y');
        $month = now()->format('m');

        $lastRequest = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRequest ? (intval(substr($lastRequest->request_number, -4)) + 1) : 1;

        return sprintf('%s%s%s%04d', $prefix, $year, $month, $sequence);
    }

    public function markAsInReview(User $user): void
    {
        $this->update([
            'status' => 'in_review',
            'reviewed_by_user_id' => $user->id,
            'review_date' => now(),
        ]);
    }

    public function approve(User $user, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by_user_id' => $user->id,
            'review_date' => now(),
            'review_notes' => $notes,
        ]);
    }

    public function reject(User $user, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => $user->id,
            'review_date' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function complete(User $user, string $method, string $notes = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_by_user_id' => $user->id,
            'completion_date' => now(),
            'data_removal_method' => $method,
            'completion_notes' => $notes,
        ]);

        // Update customer's right to be forgotten status
        if ($this->customer) {
            $this->customer->update([
                'right_to_be_forgotten_requested' => true,
                'right_to_be_forgotten_date' => now(),
            ]);
        }
    }

    public function cancel(User $user, string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'reviewed_by_user_id' => $user->id,
            'review_date' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function isUrgent(): bool
    {
        return $this->priority === 'urgent' ||
               ($this->due_date && $this->due_date->diffInDays(now()) <= 3);
    }

    public function canBeProcessed(): bool
    {
        return in_array($this->status, ['pending', 'in_review', 'approved']);
    }

    public function requiresReview(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function logAction($action, $userId = null, $notes = null)
    {
        $this->auditLogs()->create([
            'user_id' => $userId,
            'action' => $action,
            'notes' => $notes,
        ]);
    }

    // Boot method to auto-generate request number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->request_number)) {
                $model->request_number = $model->generateRequestNumber();
            }
            if (empty($model->request_date)) {
                $model->request_date = now();
            }
        });
    }
}
