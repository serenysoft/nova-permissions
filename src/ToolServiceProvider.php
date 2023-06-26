<?php

namespace Sereny\NovaPermissions;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Sereny\NovaPermissions\Nova\Permission;
use Sereny\NovaPermissions\Nova\Role;

class ToolServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Filesystem $filesystem)
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_permission_tables.php.stub' => $this->getMigrationFileName($filesystem),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../database/seeders/RolesAndPermissionsSeeder.php.stub' => $this->app->databasePath() . "/seeders/RolesAndPermissionsSeeder.php",
        ], 'seeders');

        $this->app->booted(function () {
            $this->routes();
        });

        Role::$model = config('permission.models.role');
        Permission::$model = config('permission.models.permission');

        Nova::serving(function (ServingNova $event) {
            //
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Nova::router(['nova', Authenticate::class], 'nova-permissions')
            ->group(__DIR__.'/../routes/inertia.php');

        Route::middleware(['nova', Authenticate::class])
            ->prefix('sereny/nova-permissions')
            ->group(__DIR__.'/../routes/api.php');
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param  Filesystem $filesystem
     * @return string
     */
    protected function getMigrationFileName(Filesystem $filesystem)
    {
        $timestamp = date('Y_m_d_His');

        return Collection::make($this->app->databasePath() . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem) {
                return $filesystem->glob($path . '*_create_permission_tables.php');
            })->push($this->app->databasePath() . "/migrations/{$timestamp}_create_permission_tables.php")
            ->first();
    }
}
