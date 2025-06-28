<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class CompanyEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'email_id',
        'thread_id',
        'from_email',
        'from_name',
        'to_email',
        'subject',
        'body',
        'body_plain',
        'attachments',
        'headers',
        'received_at',
        'read_at',
        'replied_at',
        'status',
        'priority',
        'labels',
        'notes',
        'is_gdpr_related',
        'category'
    ];

    protected $casts = [
        'attachments' => 'array',
        'headers' => 'array',
        'labels' => 'array',
        'received_at' => 'datetime',
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'is_gdpr_related' => 'boolean',
    ];

    /**
     * Get the company that owns the email.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who handled the email.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all emails in the same thread.
     */
    public function threadEmails(): HasMany
    {
        return $this->hasMany(CompanyEmail::class, 'thread_id', 'thread_id');
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by company.
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope to filter by GDPR-related emails.
     */
    public function scopeGdprRelated($query)
    {
        return $query->where('is_gdpr_related', true);
    }

    /**
     * Scope to filter by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter unread emails.
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope to filter read emails.
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope to filter replied emails.
     */
    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    /**
     * Mark email as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }

    /**
     * Mark email as replied.
     */
    public function markAsReplied(): void
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now()
        ]);
    }

    /**
     * Archive email.
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Get email age in human readable format.
     */
    public function getAgeAttribute(): string
    {
        return $this->received_at->diffForHumans();
    }

    /**
     * Check if email is urgent.
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    /**
     * Check if email is high priority.
     */
    public function isHighPriority(): bool
    {
        return in_array($this->priority, ['high', 'urgent']);
    }

    /**
     * Get email excerpt (first 150 characters).
     */
    public function getExcerptAttribute(): string
    {
        $text = strip_tags($this->body_plain ?: $this->body);
        return strlen($text) > 150 ? substr($text, 0, 150) . '...' : $text;
    }

    /**
     * Get attachment count.
     */
    public function getAttachmentCountAttribute(): int
    {
        return count($this->attachments ?? []);
    }

    /**
     * Check if email has attachments.
     */
    public function hasAttachments(): bool
    {
        return $this->attachment_count > 0;
    }

    /**
     * Get sender display name.
     */
    public function getSenderDisplayNameAttribute(): string
    {
        return $this->from_name ?: $this->from_email;
    }

    /**
     * Check if email is from today.
     */
    public function isFromToday(): bool
    {
        return $this->received_at->isToday();
    }

    /**
     * Check if email is from this week.
     */
    public function isFromThisWeek(): bool
    {
        return $this->received_at->isThisWeek();
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'unread' => 'bg-blue-100 text-blue-800',
            'read' => 'bg-gray-100 text-gray-800',
            'replied' => 'bg-green-100 text-green-800',
            'archived' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get priority badge class.
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-gray-100 text-gray-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function documents()
    {
        return $this->hasMany(EmailDocument::class);
    }

    public function replyAttachments()
    {
        return $this->hasMany(EmailReplyAttachment::class);
    }
}
