<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockLog extends Model
{
    protected $fillable = [
        'product_id',
        'agent_id',
        'quantity_changed',
        'action',
        'comment',
        'created_by',
    ];

    protected $casts = [
        'quantity_changed' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the product that owns the stock log.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the agent that owns the stock log.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the user who created the stock log.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the action badge class based on the action type.
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match($this->action) {
            'Add Stock' => 'bg-success',
            'Manual Adjustment' => 'bg-primary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the quantity change display with appropriate sign and color.
     */
    public function getQuantityDisplayAttribute(): string
    {
        if ($this->quantity_changed > 0) {
            return '<span class="text-success">+' . $this->quantity_changed . '</span>';
        } elseif ($this->quantity_changed < 0) {
            return '<span class="text-danger">' . $this->quantity_changed . '</span>';
        }
        return '<span class="text-muted">0</span>';
    }
}
