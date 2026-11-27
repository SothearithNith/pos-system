<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Register in config/permission.php:
 *   'models' => ['role' => App\Models\Role::class, ...],
 *   'teams'  => false,
 */
class Role extends SpatieRole
{
    protected $casts = [
        'is_system'   => 'boolean',
        'is_platform' => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Roles a client may see and assign:
     * the shared default roles + its own custom roles (never the platform role).
     */
    public function scopeAvailableFor(Builder $query, ?int $clientId): Builder
    {
        return $query->where('is_active', true)
            ->where('is_platform', false)
            ->where(fn (Builder $q) => $q->whereNull('client_id')->orWhere('client_id', $clientId));
    }

    /** Default roles cannot be edited or deleted by clients. */
    public function isEditable(): bool
    {
        return ! $this->is_system;
    }
}
