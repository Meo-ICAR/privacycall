<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataRemovalRequestAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_removal_request_id',
        'user_id',
        'action',
        'notes',
    ];

    public function dataRemovalRequest(): BelongsTo
    {
        return $this->belongsTo(DataRemovalRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
