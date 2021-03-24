<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name', 'description', 'image', 'priority', 'available', 'price', 'points', 'stock'
    ];

    protected $casts = [
        'available' => 'boolean'
    ];

    /**
     * @return HasMany
     */
    public function stockOperations(): HasMany
    {
        return $this->hasMany(StockOperation::class);
    }

    /**
     * @return HasMany
     */
    public function ordering(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('available', '=', 1);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }
}
