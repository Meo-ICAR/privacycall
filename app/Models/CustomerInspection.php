<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'inspection_date',
        'notes',
        'status',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'customer_inspection_employee')
            ->withPivot('position', 'hire_date')
            ->withTimestamps();
    }
}
