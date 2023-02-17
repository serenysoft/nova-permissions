<?php

namespace Sereny\NovaPermissions\Traits;

use Sereny\NovaPermissions\ModelForGuardState;

trait ModelForGuardResolver {

    /**
     * Determines the guard model class
     *
     * @return class-string
     */
    public function modelForGuard()
    {
        return ModelForGuardState::$resolveModelForGuardCallback
            ? call_user_func(ModelForGuardState::$resolveModelForGuardCallback)
            : getModelForGuard($this->guard_name);
    }
}
