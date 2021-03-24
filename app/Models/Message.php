<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        1 => "Event",
        2 => "Commande",
        3 => "Martins Marteau",
        4 => "Site",
        5 => "Confidentialité"
    ];

    protected $fillable = [
        'subject', 'category', 'email', 'content', 'sender_ip'
    ];

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeResponded(Builder $query): Builder
    {
        return $query->where('responded', true);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNotResponded(Builder $query): Builder
    {
        return $query->where('responded', false);
    }

    /**
     * @return BelongsTo
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
