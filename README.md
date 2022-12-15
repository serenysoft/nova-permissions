# Laravel Nova Grouped Permissions

A Laravel Nova Tool that allows you to group your Permissions into Groups and attach it to Users. It uses Spatie's laravel-permission.

We have a Migration, Seed, Policy and Resource ready for a good Authorization Experience.

1. [Installation](#Installation)
2. [Permissions with Groups](#permissions-with-groups)
    * [Index view](#index-view)
    * [Detail View](#detail-view)
    * [Edit View](#edit-view)
    * [Database Seeding](#database-seeding)
    * [Create a Model Policy](#create-a-model-policy)
    * [Super Admin](#super-admin)
    * [Scope Resource for User](#scope-resource-for-user)
3. [Customization](#customization)
4. [Credits](#credits)


## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require sereny/nova-permissions
```

Publish the Migration with the following command:

```bash
php artisan vendor:publish --provider="Sereny\NovaPermissions\ToolServiceProvider" --tag="migrations"
```

Migrate the Database:

```bash
php artisan migrate
```

Next up, you must register the tool with Nova. This is typically done in the `tools` method of the `NovaServiceProvider`.

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools()
{
    return [
        // ...
        new \Sereny\NovaPermissions\NovaPermissions(),
    ];
}
```

If you want to hide the tool from certain users, you can write your custom logic for the ability to see the tool:

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools()
{
    return [
        // ...
        (new \Sereny\NovaPermissions\NovaPermissions())->canSee(function ($request) {
            return $request->user()->isSuperAdmin();
        }),
    ];
}


```

Finally, add `MorphToMany` fields to you `app/Nova/User` resource:

```php
// ...
use Laravel\Nova\Fields\MorphToMany;

public function fields(Request $request)
{
    return [
        // ...
        MorphToMany::make('Roles', 'roles', \Eminiarts\NovaPermissions\Nova\Role::class),
        MorphToMany::make('Permissions', 'permissions', \Eminiarts\NovaPermissions\Nova\Permission::class),
    ];
}
```

Add the Spatie\Permission\Traits\HasRoles trait to your User model(s):

```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    // ...
}
```

A new menu item called **Roles & Permissions** will appear in your Nova app after installing this package.

## Permissions with Groups

### Index View

![image](/.github/images/role-index.png)

### Detail View

![image](/.github/images/role-detail.png)

### Edit View

![image](/.github/images/role-edit.png)


### Database Seeding

Publish our Seeder with the following command:

```
php artisan vendor:publish --provider="Sereny\NovaPermissions\ToolServiceProvider" --tag="seeds"
```


This is just an example on how you could seed your Database with Roles and Permissions. Modify `RolesAndPermissionsSeeder.php` in `database/seeds`. List all your Models you want to have Permissions for in the `$collection` Array and change the email for the Super-Admin:

```php
<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $collection = collect([
            'invoices',
            'clients',
            'contacts',
            'payments',
            'teams',
            'users',
            'roles',
            // ... your own models/permission you want to crate
        ]);

        $collection->each(function ($item, $key) {
            // create permissions for each collection item
            Permission::create(['group' => $item, 'name' => 'view ' . $item]);
            Permission::create(['group' => $item, 'name' => 'view own ' . $item]);
            Permission::create(['group' => $item, 'name' => 'manage ' . $item]);
            Permission::create(['group' => $item, 'name' => 'manage own ' . $item]);
            Permission::create(['group' => $item, 'name' => 'restore ' . $item]);
            Permission::create(['group' => $item, 'name' => 'forceDelete ' . $item]);
        });

        // Create a Super-Admin Role and assign all permissions to it
        $role = Role::create(['name' => 'super-admin']);
        $role->givePermissionTo(Permission::all());

        // Give User Super-Admin Role
        $user = App\User::whereEmail('your@email.com')->first(); // enter your email here
        $user->assignRole('super-admin');
    }
}
```

Now you can seed the Database. Add `$this->call(RolesAndPermissionsSeeder::class);` to the `DatabaseSeeder`.

> **Note**: If this doesn't work, run `composer dumpautoload` to autoload the Seeder.

### Create a Model Policy

You can extend `Sereny\NovaPermissions\Policies\BasePolicy` and have a very clean Model Policy that works with Nova.

For Example: Create a new Contact Policy with `php artisan make:policy ContactPolicy` with the following code:

```php
<?php

namespace App\Policies;

use Sereny\NovaPermissions\Policies\BasePolicy;

class ContactPolicy extends BasePolicy
{
    /**
     * The Permission key the Policy corresponds to.
     *
     * @var string
     */
    public $key = 'contact';
}
```
It should now work as exptected. Just create a Role, modify its Permissions and the Policy should take care of the rest.

> **Note**: Don't forget to add your Policy to your `$policies` in `App\Providers\AuthServiceProvider`.

> **Note**: Only extend the Policy if you have created your Permissions according to our Seeding Example. Otherwise, make sure to have `viewAnyContact, viewContact, createContact, updateContact, deleteContact, restoreContact,  destroyContact` as Permissions in your Table in order to extend our Policy.

### Super Admin

A Super Admin can do everything. If you extend our Policy, make sure to add a `isSuperAdmin()` Function to your `App\User` Model:

```php
<?php

namespace App;

class User {

    /**
     * Determines if the User is a Super admin
     * @return null
    */
    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }
}
```

## Customization

```php
// in app/Providers/NovaServiceProvider.php

// ...

public function tools()
{
    return [
        // ...
        \Sereny\NovaPermissions\NovaPermissions::make()
            ->hideGuardNameFromRole()
            ->hideGuardNameFromPermission()
            ->hideUsersFromPermission()
            ->hideRolesFromPermission()
            ->resolveGuardsUsing(function($request) {
                return [ 'web' ];
            })
    ];
}
```

## Credits

This Package is inspired by [eminiarts/nova-permissions](https://github.com/eminiarts/nova-permissions).
A huge thanks goes to Spatie [spatie/laravel-permission](https://github.com/spatie/laravel-permission) for their amazing work!
