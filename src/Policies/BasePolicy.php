<?php

namespace Sereny\NovaPermissions\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class BasePolicy {

    use HandlesAuthorization;

    /**
     * @var string
     */
    protected $key;

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(Model $user)
    {
        return $this->hasPermissionTo($user, 'create');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $user
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function delete(Model $user, $model)
    {
        return $this->hasPermissionTo($user, 'delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $user
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function forceDelete(Model $user, $model)
    {
        return $this->hasPermissionTo($user, 'forceDelete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $user
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function restore(Model $user, $model)
    {
        return $this->hasPermissionTo($user, 'restore');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $user
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function update(Model $user, $model)
    {
        return $this->hasPermissionTo($user, 'update');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $user
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return mixed
     */
    public function view(Model $user, $model)
    {
        return $this->hasPermissionTo($user, 'view');
    }

    /**
     * Determine whether the user can view any model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $user
     */
    public function viewAny(Model $user)
    {
        return $this->hasPermissionTo($user, 'viewAny');
    }

    /**
     * Build permission name
     *
     * @param string $name
     * @return string
     */
    protected function buildPermission(string $name)
    {
        return $name . ucfirst($this->key);
    }

    /**
     * Checks if user has permission
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @return bool
     */
    protected function hasPermissionTo($user, $permission)
    {
        return (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) 
            || $user->hasPermissionTo($this->buildPermission($permission));
    }
}
