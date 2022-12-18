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
     * @var bool
     */
    public $displayPermissions = true;

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
     * Set a callback that should be used to define the user model
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function resolveUserModelUsing($callback)
    {
        Resource::$resolveUserModelCallback = $callback;
        return $this;
    }

    /**
     * Determines the hidden fields from Role
     *
     * @param string[] $fields
     * @return $this
     */
    public function hideFieldsFromRole($fields)
    {
        $this->roleResource::$hiddenFields = $fields;
        return $this;
    }

    /**
     * Determines the hidden fields from Permission
     *
     * @param string[] $fields
     * @return $this
     */
    public function hideFieldsFromPermission($fields)
    {
        $this->permissionResource::$hiddenFields = $fields;
        return $this;
    }

    /**
     * Determines if the permission resource is disabled from menu
     *
     * @param bool $value
     * @return $this
     */
    public function disablePermissions()
    {
        $this->displayPermissions = false;
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
        return MenuSection::make(__('Roles & Permissions'), [
            $this->makeMenuItem($this->roleResource),
            $this->makeMenuItem($this->permissionResource, $this->displayPermissions)
        ])->icon('shield-check');
    }

    /**
     * @param  bool $disabled
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return void
     */
    protected function makeMenuItem($resourceClass, $displayInNavigation = true)
    {
        return MenuItem::make($resourceClass::label())
            ->path('/resources/'.$resourceClass::uriKey())
            ->canSee(function ($request) use ($resourceClass, $displayInNavigation) {
                return $displayInNavigation && $resourceClass::authorizedToViewAny($request);
            });
    }
}
