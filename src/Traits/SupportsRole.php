<?php

namespace Sereny\NovaPermissions\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Sereny\NovaPermissions\Traits\ModelForGuardResolver;
use Spatie\Permission\PermissionRegistrar;

trait SupportsRole
{
    use ModelForGuardResolver;

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            $this->modelForGuard(),
            'model',
            config('permission.table_names.model_has_roles'),
            app(PermissionRegistrar::class)->pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }
}
