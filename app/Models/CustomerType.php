<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
