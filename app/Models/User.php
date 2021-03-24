<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }

    /**
     * @param String $permission
     *
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->named($permission)->exists()
               || $this->roles()
                       ->with('permissions')
                       ->get()
                       ->reject(
                           function ($role) use ($permission) {
                               return ! $role->permissions()->named($permission)->exists();
                           }
                       )->count() > 0;
    }

    /**
     * @param string $roleName
     *
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->get()->reject(
                function ($role) use ($roleName) {
                    return $role->name != $roleName;
                }
            )->count() > 0;
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeNoPendingOrder(Builder $query): Builder
    {
        return $query->whereDoesntHave(
            'orders',
            function (Builder $query) {
                return $query->whereIn(
                    'status',
                    [
                        config('ordering.status.NOT_COMPLETED'),
                        config('ordering.status.PENDING'),
                        config('ordering.status.IN_DELIVERY')
                    ]
                );
            }
        );
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'author_id');
    }

    /**
     * @return int
     */
    public function totalPoints(): int
    {
        $points = 0;

        $this->orders()->each(
            function (Order $order) use (&$points) {
                if ($order->status == config('ordering.status.DELIVERED')) {
                    $points += $order->total_points;
                }
            }
        );

        return $points;
    }
}
