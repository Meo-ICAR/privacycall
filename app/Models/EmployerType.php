<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployerType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
