<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    protected $dates = [
        'shipped_at',
        'paid_at',
        'created_at',
        'updated_at'
    ];

    protected $fillable = ['status', 'total_price', 'shipping_address', 'method_payment', 'phone', 'total_points'];

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasMany<MessageDelivery>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(MessageDelivery::class);
    }

    /**
     * @return BelongsTo<User>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User>
     */
    public function deliveryDriver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNoDriver(Builder $query): Builder
    {
        return $query->whereDoesntHave('deliveryDriver')->where('status', config('ordering.status.PENDING'));
    }

    public function scopeByDriver(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'deliveryDriver',
            function (Builder $query) use ($user) {
                return $query->where('id', $user->id);
            }
        )->where('status', config('ordering.status.IN_DELIVERY'));
    }

    public function scopeByCustomer(Builder $query, User $user): Builder
    {
        return $query->whereHas(
            'deliveryDriver',
            function (Builder $query) use ($user) {
                return $query->where('customer_id', $user->id);
            }
        );
    }

    public function scopeDelivered(Builder $query): Builder
    {
        return $query->where('status', config('ordering.status.DELIVERED'));
    }

    public function selfUpdateTotals()
    {
        $dispatcher = Order::getEventDispatcher();
        Order::unsetEventDispatcher();
        $this->total_points = 0;
        $this->total_price  = 0;
        $order              = $this;
        $this->items()->each(
            function (OrderItem $orderItem) use (&$order) {
                $order->total_price  += $orderItem->quantity * $orderItem->product->price;
                $order->total_points += $orderItem->quantity * $orderItem->product->points;
            }
        );
        $this->save();
        Order::setEventDispatcher($dispatcher);
    }

}
