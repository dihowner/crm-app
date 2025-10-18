<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
        'company_name',
        'commission_rate',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
    ];

    /**
     * Check if agent is active
     */
    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    /**
     * Get the orders for the agent.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the payment records for the agent.
     */
    public function paymentRecords(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    /**
     * Get the inventory for the agent.
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
