<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'type',
        'date',
        'duration',
        'provider',
        'location',
        'customer_id',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the training.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_training')
            ->withPivot('attended', 'completed', 'score', 'notes')
            ->withTimestamps();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
