<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'client_id',
    'default_location_id',
    'name_en',
    'name_kh',
    'username',
    'password',
    'pos_pin',
    'is_active',
])]
#[Hidden(['password', 'pos_pin', 'remember_token'])]
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password'  => 'hashed',
            'pos_pin'   => 'hashed',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function defaultLocation(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'default_location_id');
    }

    /** Extra locations (user_locations table) besides the default one. */
    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(StockLocation::class, 'user_locations', 'user_id', 'location_id')
            ->withTimestamps();
    }
}
