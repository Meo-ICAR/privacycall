<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ComplianceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'mandator_id',
        'request_type',
        'request_scope',
        'status',
        'priority',
        'subject',
        'message',
        'requested_documents',
        'provided_documents',
        'requested_deadline',
        'scheduled_date',
        'scheduled_time',
        'meeting_type',
        'meeting_link',
        'meeting_location',
        'notes',
        'follow_up_dates',
        'last_follow_up',
        'completed_at',
        'response_sent',
        'response_sent_at',
        'response_message',
        'documents_uploaded',
        'documents_uploaded_at',
        'documents_count',
        'compliance_score',
        'risk_level',
        'compliance_findings',
        'required_actions',
        'assigned_to',
    ];

    protected $casts = [
        'requested_documents' => 'array',
        'provided_documents' => 'array',
        'follow_up_dates' => 'array',
        'compliance_findings' => 'array',
        'required_actions' => 'array',
        'requested_deadline' => 'date',
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'last_follow_up' => 'datetime',
        'completed_at' => 'datetime',
        'response_sent_at' => 'datetime',
        'documents_uploaded_at' => 'datetime',
        'response_sent' => 'boolean',
        'documents_uploaded' => 'boolean',
        'compliance_score' => 'integer',
    ];

    /**
     * Get the company being audited.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the mandator requesting the audit.
     */
    public function mandator(): BelongsTo
    {
        return $this->belongsTo(Mandator::class);
    }

    /**
     * Get the user assigned to handle this request.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by request type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    /**
     * Scope to filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter by risk level.
     */
    public function scopeByRiskLevel($query, $riskLevel)
    {
        return $query->where('risk_level', $riskLevel);
    }

    /**
     * Scope for pending requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in progress requests.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for overdue requests.
     */
    public function scopeOverdue($query)
    {
        return $query->where('requested_deadline', '<', now())
            ->where('status', '!=', 'completed');
    }

    /**
     * Scope for upcoming requests.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(30));
    }

    /**
     * Scope for high priority requests.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high')
            ->orWhere('priority', 'urgent');
    }

    /**
     * Scope for requests requiring response.
     */
    public function scopeRequiresResponse($query)
    {
        return $query->where('response_sent', false)
            ->where('status', '!=', 'completed');
    }

    /**
     * Scope for requests requiring documents.
     */
    public function scopeRequiresDocuments($query)
    {
        return $query->where('documents_uploaded', false)
            ->where('status', '!=', 'completed');
    }

    /**
     * Mark request as in progress.
     */
    public function markAsInProgress()
    {
        $this->update([
            'status' => 'in_progress',
            'last_follow_up' => now(),
        ]);
    }

    /**
     * Mark request as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark response as sent.
     */
    public function markResponseSent($message = null)
    {
        $this->update([
            'response_sent' => true,
            'response_sent_at' => now(),
            'response_message' => $message,
        ]);
    }

    /**
     * Mark documents as uploaded.
     */
    public function markDocumentsUploaded($documents = [])
    {
        $this->update([
            'documents_uploaded' => true,
            'documents_uploaded_at' => now(),
            'provided_documents' => $documents,
            'documents_count' => count($documents),
        ]);
    }

    /**
     * Check if request is overdue.
     */
    public function isOverdue()
    {
        return $this->requested_deadline &&
               $this->requested_deadline < now() &&
               $this->status !== 'completed';
    }

    /**
     * Check if response is overdue.
     */
    public function isResponseOverdue()
    {
        return !$this->response_sent &&
               $this->requested_deadline &&
               $this->requested_deadline < now();
    }

    /**
     * Check if documents are overdue.
     */
    public function isDocumentsOverdue()
    {
        return !$this->documents_uploaded &&
               $this->requested_deadline &&
               $this->requested_deadline < now();
    }

    /**
     * Get days until deadline.
     */
    public function getDaysUntilDeadline()
    {
        if (!$this->requested_deadline) {
            return null;
        }

        return now()->diffInDays($this->requested_deadline, false);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClass()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'overdue' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get priority badge class.
     */
    public function getPriorityBadgeClass()
    {
        return match($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get risk level badge class.
     */
    public function getRiskLevelBadgeClass()
    {
        return match($this->risk_level) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'critical' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
