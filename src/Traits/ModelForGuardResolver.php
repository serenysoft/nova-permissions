<?php

namespace Sereny\NovaPermissions\Traits;

/**
 *
 */
trait ModelForGuardResolver {

    /**
     * The callback that should be used to resolve user model.
     *
     * @var \Closure|null
     */
    public static $resolveModelForGuardCallback;

    /**
     * Determines the guard model class
     *
     * @return class-string
     */
    public function modelForGuard()
    {
        return ModelForGuardResolver::$resolveModelForGuardCallback
            ? call_user_func(ModelForGuardResolver::$resolveModelForGuardCallback)
            : getModelForGuard($this->guard_name);
    }
}
