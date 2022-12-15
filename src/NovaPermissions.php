<?php

namespace Sereny\NovaPermissions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Sereny\NovaPermissions\Nova\Permission;
use Sereny\NovaPermissions\Nova\Resource;
use Sereny\NovaPermissions\Nova\Role;
use Sereny\NovaPermissions\Policies\PermissionPolicy;
use Sereny\NovaPermissions\Policies\RolePolicy;

class NovaPermissions extends Tool
{
    /**
     * @var class-string
     */
    public $permissionResource = Permission::class;

    /**
     * @var class-string
     */
    public $roleResource = Role::class;

    /**
     * @var class-string
     */
    public $rolePolicy = RolePolicy::class;

    /**
     * @var class-string
     */
    public $permissionPolicy = PermissionPolicy::class;

    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::resources([
            $this->roleResource,
            $this->permissionResource,
        ]);

        Gate::policy(config('permission.models.permission'), $this->permissionPolicy);
        Gate::policy(config('permission.models.role'), $this->rolePolicy);

        Nova::script('nova-permissions', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-permissions', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Set a callback that should be used to define wich guard names will be available
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function resolveGuardsUsing($callback)
    {
        Resource::$resolveGuardsCallback = $callback;
        return $this;
    }

    /**
     * Specify that the guard name should be hidden from role resource.
     *
     * @return $this
     */
    public function hideGuardNameFromRole()
    {
        $this->roleResource::$showGuardName = false;
        return $this;
    }

    /**
     * Specify that the guard name should be hidden from permission resource.
     *
     * @return $this
     */
    public function hideGuardNameFromPermission()
    {
        $this->permissionResource::$showGuardName = false;
        return $this;
    }

    /**
     * Specify that the users relationship should be hidden from permission resource.
     *
     * @return $this
     */
    public function hideUsersFromPermission()
    {
        $this->permissionResource::$showUsers = false;
        return $this;
    }

    /**
     * Specify that the roles relationship should be hidden from permission resource.
     *
     * @return $this
     */
    public function hideRolesFromPermission()
    {
        $this->permissionResource::$showRoles = false;
        return $this;
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuSection::make(__('Permissions'), [
            $this->makeMenuItem($this->roleResource),
            $this->makeMenuItem($this->permissionResource)
        ])->icon('shield-check');
    }

    /**
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return void
     */
    protected function makeMenuItem($resourceClass)
    {
        return MenuItem::make($resourceClass::label())
            ->path('/resources/'.$resourceClass::uriKey())
            ->canSee(function ($request) use ($resourceClass) {
                return $resourceClass::authorizedToViewAny($request);
            });
    }
}
