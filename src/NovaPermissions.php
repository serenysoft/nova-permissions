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
use Sereny\NovaPermissions\Traits\ModelForGuardResolver;

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
     * @var bool
     */
    public $menuDisabled = false;


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

        Nova::mix('nova-permissions', __DIR__.'/../dist/mix-manifest.json');
    }

    /**
     * Set the role resource class name
     *
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return $this
     */
    public function roleResource($resourceClass)
    {
        $this->roleResource = $resourceClass;
        return $this;
    }

     /**
     * Set the permission resource class name
     *
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return $this
     */
    public function permissionResource($resourceClass)
    {
        $this->permissionResource = $resourceClass;
        return $this;
    }

    /**
     * Set the role policy class name
     *
     * @param  class-string $rolePolicy
     * @return $this
     */
    public function rolePolicy($rolePolicy)
    {
        $this->rolePolicy = $rolePolicy;
        return $this;
    }

     /**
     * Set the permission policy class name
     *
     * @param  class-string $permissionPolicy
     * @return $this
     */
    public function permissionPolicy($permissionPolicy)
    {
        $this->permissionPolicy = $permissionPolicy;
        return $this;
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
    public function resolveModelForGuardUsing($callback)
    {
        ModelForGuardState::$resolveModelForGuardCallback = $callback;
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
     * If the default menu should be available
     *
     * @return $this
     */
    public function disableMenu()
    {
        $this->menuDisabled = true;
        $this->roleResource::$displayInNavigation = $this->menuDisabled;
        $this->permissionResource::$displayInNavigation = $this->menuDisabled;
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
        if ($this->menuDisabled) {
            return [];
        }

        $itens = [$this->createMenuItem($this->roleResource)];

        if ($this->displayPermissions) {
            $itens[] = $this->createMenuItem($this->permissionResource);
        }

        return MenuSection::make(__('Roles & Permissions'), $itens)
            ->icon('shield-check')
            ->collapsable();
    }

    /**
     * @param  bool $disabled
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return void
     */
    protected function createMenuItem($resourceClass)
    {
        return MenuItem::make($resourceClass::label())
            ->path('/resources/'.$resourceClass::uriKey());
    }
}
