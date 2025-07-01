<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait TenantScoped
{
    /**
     * Scope a query to the current user's company if not superadmin.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeTenant($query)
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('superadmin')) {
            $query->where($this->getTable() . '.company_id', $user->company_id);
        }
        return $query;
    }
}
