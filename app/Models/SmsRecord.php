<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsRecord extends Model
{
    protected $fillable = [
        'campaign_name',
        'type',
        'message',
        'recipient_phone',
        'recipient_name',
        'status',
        'sms_provider',
        'provider_message_id',
        'error_message',
        'cost',
        'sent_by',
        'order_id',
        'customer_id',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get the user who sent the SMS.
     */
    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get the order associated with the SMS.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer associated with the SMS.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Scope for filtering by SMS type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for bulk SMS campaigns.
     */
    public function scopeBulk($query)
    {
        return $query->where('type', 'bulk');
    }

    /**
     * Scope for single SMS.
     */
    public function scopeSingle($query)
    {
        return $query->where('type', 'single');
    }
}
