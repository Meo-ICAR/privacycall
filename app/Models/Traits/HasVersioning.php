<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProcessingRegisterChange;

trait HasVersioning
{
    /**
     * Boot the trait.
     */
    protected static function bootHasVersioning()
    {
        static::creating(function ($model) {
            if (!isset($model->version)) {
                $model->version = '1.0.0';
            }
            if (!isset($model->is_latest_version)) {
                $model->is_latest_version = true;
            }
            if (!isset($model->version_created_at)) {
                $model->version_created_at = now();
            }
            if (!isset($model->version_created_by)) {
                $model->version_created_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            // Se ci sono modifiche significative, crea una nuova versione
            if ($model->shouldCreateNewVersion()) {
                $model->createNewVersionFromUpdate();
            }
        });
    }

    /**
     * Get the version that created this entity.
     */
    public function versionCreatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'version_created_by');
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
     * Create a new version of this entity.
     */
    public function createNewVersion(array $data = [], string $reason = null): self
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

        // Registra la modifica
        $this->logVersionChange('created', $newVersion, $reason);

        return $newVersion;
    }

    /**
     * Create a new version from an update.
     */
    protected function createNewVersionFromUpdate(): void
    {
        $oldValues = $this->getOriginal();
        $newValues = $this->getAttributes();

        // Crea una nuova versione
        $newVersion = $this->createNewVersion();

        // Registra la modifica
        $this->logVersionChange('updated', $newVersion, 'Aggiornamento automatico', $oldValues, $newValues);
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
     * Get the version history for this entity.
     */
    public function getVersionHistory()
    {
        $identifierField = $this->getVersionIdentifierField();

        return static::where($identifierField, $this->$identifierField)
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
     * Determine if a new version should be created.
     */
    protected function shouldCreateNewVersion(): bool
    {
        $significantFields = $this->getSignificantFields();

        foreach ($significantFields as $field) {
            if ($this->isDirty($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the fields that trigger version creation.
     */
    protected function getSignificantFields(): array
    {
        return [
            'activity_name',
            'activity_description',
            'processing_purpose',
            'legal_basis',
            'data_categories',
            'data_subjects',
            'retention_period',
            'security_measures',
            'risk_assessment_level',
            'is_active',
        ];
    }

    /**
     * Get the field used to identify versions of the same entity.
     */
    protected function getVersionIdentifierField(): string
    {
        return 'activity_name'; // Override in specific models if needed
    }

    /**
     * Log a version change.
     */
    protected function logVersionChange(
        string $changeType,
        self $newVersion,
        string $reason = null,
        array $oldValues = null,
        array $newValues = null
    ): void {
        $entityType = $this->getEntityType();

        ProcessingRegisterChange::create([
            'company_id' => $this->company_id,
            'change_type' => $changeType,
            'entity_type' => $entityType,
            'entity_id' => $newVersion->id,
            'entity_name' => $this->getEntityName(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'change_description' => "Creata nuova versione {$newVersion->version}",
            'change_reason' => $reason,
            'impact_level' => $this->determineImpactLevel($oldValues, $newValues),
            'requires_review' => $this->requiresReview($oldValues, $newValues),
            'requires_approval' => $this->requiresApproval($oldValues, $newValues),
            'changed_by' => auth()->id(),
        ]);
    }

    /**
     * Get the entity type for change logging.
     */
    protected function getEntityType(): string
    {
        return 'data_processing_activity'; // Override in specific models
    }

    /**
     * Get the entity name for change logging.
     */
    protected function getEntityName(): string
    {
        return $this->activity_name ?? $this->name ?? 'Unknown';
    }

    /**
     * Determine the impact level of changes.
     */
    protected function determineImpactLevel(array $oldValues = null, array $newValues = null): string
    {
        if (!$oldValues || !$newValues) {
            return 'low';
        }

        $criticalFields = ['legal_basis', 'data_categories', 'security_measures'];
        $highFields = ['processing_purpose', 'retention_period', 'risk_assessment_level'];
        $mediumFields = ['activity_description', 'data_subjects'];

        foreach ($criticalFields as $field) {
            if (isset($oldValues[$field]) && isset($newValues[$field]) && $oldValues[$field] !== $newValues[$field]) {
                return 'critical';
            }
        }

        foreach ($highFields as $field) {
            if (isset($oldValues[$field]) && isset($newValues[$field]) && $oldValues[$field] !== $newValues[$field]) {
                return 'high';
            }
        }

        foreach ($mediumFields as $field) {
            if (isset($oldValues[$field]) && isset($newValues[$field]) && $oldValues[$field] !== $newValues[$field]) {
                return 'medium';
            }
        }

        return 'low';
    }

    /**
     * Determine if changes require review.
     */
    protected function requiresReview(array $oldValues = null, array $newValues = null): bool
    {
        $impactLevel = $this->determineImpactLevel($oldValues, $newValues);
        return in_array($impactLevel, ['high', 'critical']);
    }

    /**
     * Determine if changes require approval.
     */
    protected function requiresApproval(array $oldValues = null, array $newValues = null): bool
    {
        $impactLevel = $this->determineImpactLevel($oldValues, $newValues);
        return $impactLevel === 'critical';
    }
}
