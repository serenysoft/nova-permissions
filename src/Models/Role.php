<?php

namespace Sereny\NovaPermissions\Models;

use Sereny\NovaPermissions\Traits\SupportsRole;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use SupportsRole;
}
