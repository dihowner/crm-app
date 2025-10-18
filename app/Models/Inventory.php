<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'agent_id',
        'quantity',
        'low_stock_threshold',
        'cost_price',
        'selling_price',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns the inventory.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the agent that owns the inventory.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the stock movements for the inventory.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Check if inventory is low stock.
     */
    public function isLowStock()
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    /**
     * Scope for low stock inventory.
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= low_stock_threshold');
    }
}
