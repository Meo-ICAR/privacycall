<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Carbon\Carbon;
use App\Models\TenantScoped;

class DataBreach extends Model
{
    use HasFactory, SoftDeletes, TenantScoped;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'breach_number',
        'breach_type',
        'severity',
        'status',
        'detection_date',
        'notification_date',
        'dpa_notification_date',
        'affected_data_subjects_count',
        'affected_data_categories',
        'breach_description',
        'containment_measures',
        'remediation_actions',
        'lessons_learned',
        'impact_assessment',
        'individuals_notified',
        'individuals_notification_date',
        'notification_method',
        'dpa_notified',
        'dpa_notification_details',
        'investigation_findings',
        'corrective_actions',
        'preventive_measures',
        'estimated_financial_impact',
        'legal_implications',
        'insurance_claims',
        'is_active',
        'notes'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'affected_data_categories' => 'array',
        'detection_date' => 'datetime',
        'notification_date' => 'datetime',
        'dpa_notification_date' => 'datetime',
        'individuals_notification_date' => 'datetime',
        'individuals_notified' => 'boolean',
        'dpa_notified' => 'boolean',
        'is_active' => 'boolean',
        'estimated_financial_impact' => 'decimal:2',
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
     * Get the company that owns the data breach.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the documents associated with this breach.
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    /**
     * Scope a query to only include active breaches.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by severity.
     */
    public function scopeWithSeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by breach type.
     */
    public function scopeWithType($query, $type)
    {
        return $query->where('breach_type', $type);
    }

    /**
     * Check if breach requires DPA notification.
     */
    public function requiresDpaNotification(): bool
    {
        return in_array($this->severity, ['high', 'critical']) ||
               ($this->affected_data_subjects_count && $this->affected_data_subjects_count > 100);
    }

    /**
     * Check if breach requires individual notification.
     */
    public function requiresIndividualNotification(): bool
    {
        return $this->severity === 'critical' ||
               ($this->affected_data_subjects_count && $this->affected_data_subjects_count > 10);
    }

    /**
     * Get the time since detection.
     */
    public function getTimeSinceDetectionAttribute(): string
    {
        return $this->detection_date->diffForHumans();
    }

    /**
     * Check if breach is within 72-hour notification window.
     */
    public function isWithinNotificationWindow(): bool
    {
        return $this->detection_date->addHours(72)->isFuture();
    }

    /**
     * Get the notification deadline.
     */
    public function getNotificationDeadlineAttribute(): Carbon
    {
        return $this->detection_date->addHours(72);
    }

    /**
     * Check if breach is overdue for notification.
     */
    public function isOverdueForNotification(): bool
    {
        return !$this->dpa_notified && $this->requiresDpaNotification() &&
               $this->detection_date->addHours(72)->isPast();
    }

    /**
     * Get the breach type display name.
     */
    public function getBreachTypeDisplayAttribute(): string
    {
        $types = [
            'unauthorized_access' => 'Unauthorized Access',
            'data_loss' => 'Data Loss',
            'system_failure' => 'System Failure',
            'human_error' => 'Human Error',
        ];

        return $types[$this->breach_type] ?? $this->breach_type;
    }

    /**
     * Get the severity color for UI.
     */
    public function getSeverityColorAttribute(): string
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
        ];

        return $colors[$this->severity] ?? 'gray';
    }

    /**
     * Get the status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'detected' => 'red',
            'investigating' => 'yellow',
            'contained' => 'blue',
            'resolved' => 'green',
            'closed' => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($breach) {
            if (empty($breach->breach_number)) {
                $breach->breach_number = 'BR-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
