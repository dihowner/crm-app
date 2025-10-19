<?php

namespace App\Models;

use App\Helpers\PhoneNumberHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp_number',
        'state',
        'address',
        'city',
        'postal_code',
        'status',
        'notes',
        'last_order_date',
        'total_orders',
        'total_spent',
    ];

    protected $casts = [
        'last_order_date' => 'datetime',
        'total_spent' => 'decimal:2',
    ];

    /**
     * Set the phone attribute with country code conversion
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = PhoneNumberHelper::convertToCountryCode($value);
    }

    /**
     * Set the whatsapp_number attribute with country code conversion
     */
    public function setWhatsappNumberAttribute($value)
    {
        $this->attributes['whatsapp_number'] = PhoneNumberHelper::convertToCountryCode($value);
    }

    /**
     * Get the orders for the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the SMS records for the customer.
     */
    public function smsRecords(): HasMany
    {
        return $this->hasMany(SmsRecord::class);
    }

    /**
     * Get delivered orders count.
     */
    public function getDeliveredOrdersCountAttribute()
    {
        return $this->orders()->where('status', 'delivered')->count();
    }

    /**
     * Get the latest order for the customer.
     */
    public function latestOrder()
    {
        return $this->hasOne(Order::class)->latest();
    }
}
