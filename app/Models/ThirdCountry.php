<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThirdCountry extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_name',
        'country_code',
        'adequacy_decision',
        'adequacy_decision_date',
        'adequacy_decision_reference',
        'adequacy_decision_details',
        'risk_level',
        'risk_assessment',
        'data_protection_laws',
        'supervisory_authority',
        'contact_information',
        'is_active',
        'sort_order',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_third_country')->withPivot('reason')->withTimestamps();
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_third_country')->withPivot('reason')->withTimestamps();
    }
}
