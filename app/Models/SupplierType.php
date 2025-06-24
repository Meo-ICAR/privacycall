<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon'];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}
