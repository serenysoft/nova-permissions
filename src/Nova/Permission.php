<?php
namespace Sereny\NovaPermissions\Nova;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property static class-string $model
 */
class Permission extends Resource
{

    /**
     * Indicates if the guard name field should be available.
     *
     * @var bool
     */
    public static $showRoles = true;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = null;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = SpatiePermission::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        $guardOptions = $this->guardOptions($request);
        $userResource = Nova::resourceForModel(getModelForGuard($this->guard_name));

        return [
            ID::make('Id', 'id')
                ->rules('required')
                ->hideFromIndex()
            ,
            Text::make(__('Name'), 'name')
                ->rules(['required', 'string', 'max:255'])
                ->creationRules('unique:' . config('permission.table_names.permissions'))
                ->updateRules('unique:' . config('permission.table_names.permissions') . ',name,{{resourceId}}'),

            Text::make(__('Group'), 'group'),

            Select::make(__('Guard Name'), 'guard_name')
                ->options($guardOptions->toArray())
                ->rules(['required', Rule::in($guardOptions)])
                ->canSee(function ($request) {
                    return static::$showGuardName;
                })
                ->default($this->defaultGuard($guardOptions)),

            BelongsToMany::make(__('Roles'), 'roles', Role::class)
                ->canSee(function ($request) {
                    return static::$showRoles;
                }),

            MorphToMany::make($userResource::label(), 'users', $userResource)
                ->searchable()
                ->canSee(function ($request) {
                    return static::$showUsers;
                }),
        ];
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('Permissions');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('Permission');
    }

}
