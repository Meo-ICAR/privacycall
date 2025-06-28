<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailReplyAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_email_id',
        'email_log_id',
        'user_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'storage_path',
    ];

    public function companyEmail(): BelongsTo
    {
        return $this->belongsTo(CompanyEmail::class);
    }

    public function emailLog(): BelongsTo
    {
        return $this->belongsTo(EmailLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
