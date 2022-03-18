<?php

namespace App\Providers;

use App\Models\Permission;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            Permission::get()->map(function ($permission) {
                //Define custom can function
                Gate::define($permission->slug, function($user) use ($permission) {
                    return $user->hasPermission($permission);
                });
            });
        } catch(Exception $e){
            report($e);
        }
    }
}
