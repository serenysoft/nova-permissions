<?php

namespace Sereny\NovaPermissions\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Sereny\NovaPermissions\Traits\ModelForGuardResolver;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;

class Role extends SpatieRole
{
    use ModelForGuardResolver;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['prepared_permissions'];

    /**
     * @return mixed
     */
    public function getPreparedPermissionsAttribute()
    {
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            $this->modelForGuard(),
            'model',
            config('permission.table_names.model_has_roles'),
            PermissionRegistrar::$pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }
}
