<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AuditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'supplier_id',
        'audit_type',
        'audit_scope',
        'status',
        'priority',
        'subject',
        'message',
        'requested_documents',
        'received_documents',
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
        // New supplier audit specific fields
        'compliance_score',
        'risk_level',
        'audit_frequency',
        'next_audit_date',
        'audit_findings',
        'corrective_actions',
        'audit_cost',
        'audit_duration_hours',
        'auditor_assigned',
        'supplier_response_deadline',
        'supplier_response_received',
        'audit_report_url',
        'certification_status',
        'certification_expiry_date',
    ];

    protected $casts = [
        'requested_documents' => 'array',
        'received_documents' => 'array',
        'follow_up_dates' => 'array',
        'requested_deadline' => 'date',
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'last_follow_up' => 'datetime',
        'completed_at' => 'datetime',
        'next_audit_date' => 'date',
        'audit_findings' => 'array',
        'corrective_actions' => 'array',
        'supplier_response_received' => 'boolean',
        'certification_expiry_date' => 'date',
        'compliance_score' => 'integer',
        'audit_cost' => 'decimal:2',
        'audit_duration_hours' => 'decimal:2',
    ];

    /**
     * Get the company that owns the audit request.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the audit request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the supplier being audited.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the auditor assigned to this audit.
     */
    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_assigned');
    }

    /**
     * Get audit history for this supplier.
     */
    public function auditHistory(): HasMany
    {
        return $this->hasMany(AuditRequest::class, 'supplier_id', 'supplier_id')
            ->where('id', '!=', $this->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by audit type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('audit_type', $type);
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
     * Scope for pending audits.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for in progress audits.
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope for overdue audits.
     */
    public function scopeOverdue($query)
    {
        return $query->where('requested_deadline', '<', now())
            ->where('status', '!=', 'completed');
    }

    /**
     * Scope for upcoming audits.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(30));
    }

    /**
     * Scope for high risk audits.
     */
    public function scopeHighRisk($query)
    {
        return $query->where('risk_level', 'high')
            ->orWhere('risk_level', 'critical');
    }

    /**
     * Scope for audits requiring follow-up.
     */
    public function scopeRequiresFollowUp($query)
    {
        return $query->where('status', 'in_progress')
            ->where('last_follow_up', '<', now()->subDays(7));
    }

    /**
     * Mark audit as in progress.
     */
    public function markAsInProgress()
    {
        $this->update([
            'status' => 'in_progress',
            'last_follow_up' => now(),
        ]);
    }

    /**
     * Mark audit as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Calculate compliance score based on findings.
     */
    public function calculateComplianceScore()
    {
        if (empty($this->audit_findings)) {
            return 100;
        }

        $totalFindings = count($this->audit_findings);
        $criticalFindings = collect($this->audit_findings)
            ->where('severity', 'critical')
            ->count();
        $majorFindings = collect($this->audit_findings)
            ->where('severity', 'major')
            ->count();
        $minorFindings = collect($this->audit_findings)
            ->where('severity', 'minor')
            ->count();

        // Scoring algorithm: Critical = -20, Major = -10, Minor = -5
        $score = 100 - ($criticalFindings * 20) - ($majorFindings * 10) - ($minorFindings * 5);

        return max(0, $score);
    }

    /**
     * Determine risk level based on compliance score and audit type.
     */
    public function determineRiskLevel()
    {
        $score = $this->compliance_score ?? $this->calculateComplianceScore();

        if ($score >= 90) return 'low';
        if ($score >= 70) return 'medium';
        if ($score >= 50) return 'high';
        return 'critical';
    }

    /**
     * Get audit frequency recommendation.
     */
    public function getRecommendedAuditFrequency()
    {
        $riskLevel = $this->risk_level ?? $this->determineRiskLevel();

        switch ($riskLevel) {
            case 'low': return 'annual';
            case 'medium': return 'semi_annual';
            case 'high': return 'quarterly';
            case 'critical': return 'monthly';
            default: return 'annual';
        }
    }

    /**
     * Check if audit is overdue.
     */
    public function isOverdue()
    {
        return $this->requested_deadline &&
               $this->requested_deadline < now() &&
               $this->status !== 'completed';
    }

    /**
     * Check if supplier response is overdue.
     */
    public function isSupplierResponseOverdue()
    {
        return $this->supplier_response_deadline &&
               $this->supplier_response_deadline < now() &&
               !$this->supplier_response_received;
    }

    /**
     * Get days until deadline.
     */
    public function getDaysUntilDeadline()
    {
        if (!$this->requested_deadline) return null;

        return now()->diffInDays($this->requested_deadline, false);
    }

    /**
     * Get audit duration in hours.
     */
    public function getAuditDuration()
    {
        if (!$this->audit_duration_hours) return null;

        $hours = floor($this->audit_duration_hours);
        $minutes = round(($this->audit_duration_hours - $hours) * 60);

        return "{$hours}h {$minutes}m";
    }

    /**
     * Get audit cost formatted.
     */
    public function getFormattedCost()
    {
        if (!$this->audit_cost) return null;

        return '€' . number_format($this->audit_cost, 2);
    }

    /**
     * Get certification status with expiry info.
     */
    public function getCertificationStatus()
    {
        if (!$this->certification_status) return 'Not Certified';

        if ($this->certification_expiry_date) {
            $daysUntilExpiry = now()->diffInDays($this->certification_expiry_date, false);

            if ($daysUntilExpiry < 0) {
                return 'Expired (' . abs($daysUntilExpiry) . ' days ago)';
            } elseif ($daysUntilExpiry <= 30) {
                return 'Expiring Soon (' . $daysUntilExpiry . ' days)';
            } else {
                return 'Valid (Expires ' . $this->certification_expiry_date->format('M d, Y') . ')';
            }
        }

        return $this->certification_status;
    }
}
