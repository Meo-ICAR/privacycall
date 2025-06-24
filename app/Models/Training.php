<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'date',
        'duration',
        'provider',
        'location',
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_training')
            ->withPivot('attended', 'completed', 'score', 'notes')
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
