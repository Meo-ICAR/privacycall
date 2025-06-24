<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'role', // superadmin, admin, manager, employee, customer
        'is_active',

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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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
        ];
    }

    /**
     * Get the company that owns the user.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee record associated with this user.
     */
    public function employee(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the customer record associated with this user.
     */
    public function customer(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * Get the data processing activities for this user.
     */
    public function dataProcessingActivities(): HasMany
    {
        return $this->hasMany(DataProcessingActivity::class, 'processable_id')
                    ->where('processable_type', User::class);
    }

    /**
     * Get the consent records for this user.
     */
    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class, 'consentable_id')
                    ->where('consentable_type', User::class);
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeWithRole($query, $role)
    {
        return $query->where('role', $role);
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
     * Check if user has requested right to be forgotten.
     */
    public function hasRequestedRightToBeForgotten(): bool
    {
        return $this->right_to_be_forgotten_requested;
    }

    /**
     * Check if user has requested data portability.
     */
    public function hasRequestedDataPortability(): bool
    {
        return $this->data_portability_requested;
    }

    /**
     * Check if user is a superadmin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'manager']);
    }

    /**
     * Check if user has permission to manage companies.
     */
    public function canManageCompanies(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Check if user has permission to manage users.
     */
    public function canManageUsers(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    /**
     * Check if user has permission to view GDPR data.
     */
    public function canViewGdprData(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'manager']);
    }

    /**
     * Check if user has permission to manage GDPR settings.
     */
    public function canManageGdprSettings(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }
}
