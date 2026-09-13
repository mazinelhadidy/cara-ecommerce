<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENT = 'percent';

    protected $fillable = [
        'code',
        'name',
        'type',
        'discount_type',
        'value',
        'discount_value',
        'minimum_order_amount',
        'is_active',
        'starts_at',
        'expires_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isAvailableFor(float $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return $subtotal >= (float) $this->minimum_order_amount;
    }

    public function discountFor(float $subtotal): float
    {
        if (! $this->isAvailableFor($subtotal)) {
            return 0.0;
        }

        $discount = $this->type === self::TYPE_PERCENT
            ? $subtotal * min((float) $this->value, 100) / 100
            : (float) $this->value;

        return round(min($discount, $subtotal), 2);
    }
}
