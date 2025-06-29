<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'subsupplier_id',
        'company_id',
        'status',
        'justification',
        'review_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function subsupplier()
    {
        return $this->belongsTo(Subsupplier::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
