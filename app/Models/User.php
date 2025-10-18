<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
        'two_factor_secret',
        'two_factor_enabled',
        'password_changed_at',
        'must_change_password',
        'login_history',
        'is_active',
        'phone',
        'max_orders_per_day',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'password_changed_at' => 'datetime',
            'login_history' => 'array',
            'two_factor_enabled' => 'boolean',
            'must_change_password' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the role that belongs to the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the orders assigned to this user.
     */
    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'assigned_to');
    }

    /**
     * Get the orders assigned to this user (alias for compatibility).
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'assigned_to');
    }

    /**
     * Get the payment records created by this user.
     */
    public function paymentRecords()
    {
        return $this->hasMany(PaymentRecord::class, 'recorded_by');
    }

    /**
     * Get the SMS records sent by this user.
     */
    public function smsRecords()
    {
        return $this->hasMany(SmsRecord::class, 'sent_by');
    }

    /**
     * Get the order status history created by this user.
     */
    public function orderStatusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class, 'changed_by');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission)
    {
        if (!$this->role) {
            return false;
        }

        $permissions = $this->role->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin()
    {
        return $this->role && $this->role->slug === 'admin';
    }

    /**
     * Check if user has CSR role.
     */
    public function isCSR()
    {
        return $this->role && $this->role->slug === 'csr';
    }

    /**
     * Check if user has Logistic Manager role.
     */
    public function isLogisticManager()
    {
        return $this->role && $this->role->slug === 'logistic_manager';
    }
}
