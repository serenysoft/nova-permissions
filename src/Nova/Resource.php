<?php

namespace Sereny\NovaPermissions\Nova;

use Laravel\Nova\Resource as NovaResource;

abstract class Resource extends NovaResource
{
    /**
     * Indicates if the guard name field should be available.
     *
     * @var bool
     */
    public static $showGuardName = true;

    /**
     * Indicates if the guard name field should be available.
     *
     * @var bool
     */
    public static $showUsers = true;

    /**
     * The callback that should be used to resolve guards that can be used.
     *
     * @var \Closure|null
     */
    public static $resolveGuardsCallback;

    /**
     * Get the available guards.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string
     */
    protected function guards($request)
    {
        if (static::$resolveGuardsCallback) {
            return call_user_func(static::$resolveGuardsCallback, $request);
        }

        return array_keys(config('auth.guards'));
    }

    /**
     * When is available only one guard the default value is the first element.
     *
     * @param \Illuminate\Support\Collection  $options
     * @return string
     */
    protected function defaultGuard($options)
    {
        return $options->count() === 1 ? $options->first() : null;
    }

    /**
     * Get mapped guard options.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    protected function guardOptions($request)
    {
        return collect($this->guards($request))->mapWithKeys(function ($value) {
            return [$value => __($value)];
        });
    }

}
