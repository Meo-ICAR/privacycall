<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'subject',
        'body',
        'variables',
        'is_active',
        'category'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the template.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get available variables for this template.
     */
    public function getAvailableVariables(): array
    {
        return $this->variables ?? [
            'supplier_name' => 'Supplier Name',
            'supplier_email' => 'Supplier Email',
            'supplier_phone' => 'Supplier Phone',
            'company_name' => 'Your Company Name',
            'user_name' => 'Current User Name',
            'current_date' => 'Current Date',
            'custom_message' => 'Custom Message'
        ];
    }

    /**
     * Replace variables in the template with actual values.
     */
    public function replaceVariables(array $data): string
    {
        $body = $this->body;

        foreach ($data as $key => $value) {
            $body = str_replace("{{" . $key . "}}", $value, $body);
        }

        return $body;
    }
}
