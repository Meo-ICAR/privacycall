<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subsupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'company_id',
        'service_description',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function authorizationRequests()
    {
        return $this->hasMany(AuthorizationRequest::class);
    }
}
