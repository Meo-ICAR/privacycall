<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcessingRegisterVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'version_number',
        'version_name',
        'version_description',
        'status',
        'effective_date',
        'expiry_date',
        'register_data',
        'activities_summary',
        'compliance_summary',
        'changes_log',
        'created_by',
        'approved_by',
        'approved_at',
        'approval_notes',
        'is_current',
        'notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'expiry_date' => 'date',
        'register_data' => 'array',
        'activities_summary' => 'array',
        'compliance_summary' => 'array',
        'changes_log' => 'array',
        'approved_at' => 'datetime',
        'is_current' => 'boolean',
    ];

    /**
     * Get the company that owns the version.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created the version.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the version.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the changes associated with this version.
     */
    public function changes(): HasMany
    {
        return $this->hasMany(ProcessingRegisterChange::class);
    }

    /**
     * Scope a query to only include current versions.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to only include active versions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include draft versions.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include archived versions.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Get the next version number.
     */
    public static function getNextVersionNumber(int $companyId): string
    {
        $lastVersion = self::where('company_id', $companyId)
            ->orderBy('version_number', 'desc')
            ->first();

        if (!$lastVersion) {
            return '1.0.0';
        }

        $parts = explode('.', $lastVersion->version_number);
        $major = (int) $parts[0];
        $minor = (int) $parts[1];
        $patch = (int) $parts[2];

        return ($major + 1) . '.0.0';
    }

    /**
     * Create a new version from the current state.
     */
    public static function createFromCurrentState(int $companyId, array $data = []): self
    {
        $versionNumber = self::getNextVersionNumber($companyId);

        // Archivia la versione corrente
        self::where('company_id', $companyId)
            ->where('is_current', true)
            ->update([
                'is_current' => false,
                'status' => 'superseded',
                'expiry_date' => now(),
            ]);

        // Crea la nuova versione
        return self::create(array_merge([
            'company_id' => $companyId,
            'version_number' => $versionNumber,
            'status' => 'draft',
            'is_current' => true,
            'created_by' => auth()->id(),
        ], $data));
    }

    /**
     * Approve the version.
     */
    public function approve(int $approvedBy, string $notes = null): bool
    {
        return $this->update([
            'status' => 'active',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'effective_date' => now(),
        ]);
    }

    /**
     * Archive the version.
     */
    public function archive(): bool
    {
        return $this->update([
            'status' => 'archived',
            'expiry_date' => now(),
        ]);
    }

    /**
     * Get the version display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->version_name ?: "Versione {$this->version_number}";
    }

    /**
     * Get the version status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Bozza',
            'active' => 'Attiva',
            'archived' => 'Archiviata',
            'superseded' => 'Sostituita',
            default => 'Sconosciuto',
        };
    }

    /**
     * Get the version status color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'yellow',
            'active' => 'green',
            'archived' => 'gray',
            'superseded' => 'red',
            default => 'gray',
        };
    }
}
