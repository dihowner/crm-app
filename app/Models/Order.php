<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'product_id',
        'assigned_to',
        'assigned_at',
        'agent_id',
        'source',
        'quantity',
        'unit_price',
        'total_price',
        'status',
        'scheduled_delivery_date',
        'tracking_number',
        'notes',
        'callback_reminder',
        'payment_status',
        'amount_paid',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'scheduled_delivery_date' => 'datetime',
        'assigned_at' => 'datetime',
        'callback_reminder' => 'datetime',
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product that belongs to the order.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user assigned to the order.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the agent assigned to the order.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the order status history.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get the payment records for the order.
     */
    public function paymentRecords(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    /**
     * Get the SMS records for the order.
     */
    public function smsRecords(): HasMany
    {
        return $this->hasMany(SmsRecord::class);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }


    /**
     * Scope for today's orders.
     */
    public function scopeTodays($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for overdue orders.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
                    ->whereNotNull('scheduled_delivery_date')
                    ->where('scheduled_delivery_date', '<', now());
    }
}
