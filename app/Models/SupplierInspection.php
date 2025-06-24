<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'supplier_id',
        'inspection_date',
        'notes',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
